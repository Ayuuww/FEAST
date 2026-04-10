<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if user is logged in and is superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// --- ✅ 1. HANDLE MODAL FORM SUBMISSION (UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
  $original_id = $_POST['old_idnumber'];
  $new_id = trim($_POST['idnumber']);
  $position = !empty($_POST['position']) ? trim($_POST['position']) : null;
  $faculty_rank = !empty($_POST['faculty_rank']) ? trim($_POST['faculty_rank']) : null;
  $status = trim($_POST['status']);
  $new_password = trim($_POST['new_password']);

  $main_college = !empty($_POST['main_college']) ? trim($_POST['main_college']) : null;

  // ✅ FIX: Fallback to existing programs if the UI dropdown fails to submit them
  $programs_post = $_POST['main_program'] ?? [];
  $fallback_programs = $_POST['fallback_programs'] ?? '';

  if (empty($programs_post) && !empty($fallback_programs)) {
    // Rebuild the array from the hidden comma-separated fallback field
    $programs_post = explode(', ', $fallback_programs);
  }

  if ($new_id === '') {
    $_SESSION['msg'] = "ID number is required.";
    $_SESSION['msg_type'] = "danger";
    header("Location: superadmin-adminlist.php");
    exit();
  }

  // Check for duplicate ID against OTHER admins
  $chk = $conn->prepare("SELECT idnumber FROM admin WHERE idnumber = ? AND idnumber != ?");
  $chk->bind_param("ss", $new_id, $original_id);
  $chk->execute();
  if ($chk->get_result()->num_rows > 0) {
    $_SESSION['msg'] = "ID number already exists for another admin.";
    $_SESSION['msg_type'] = "danger";
    header("Location: superadmin-adminlist.php");
    exit();
  }
  $chk->close();

  if (!is_array($programs_post)) {
    $programs_post = [$programs_post];
  }

  // Clean out empty values
  $valid_programs = [];
  foreach ($programs_post as $p) {
    if (trim($p) !== '') {
      $valid_programs[] = trim($p);
    }
  }

  // Server-Side Validation: Ensure Non-Deans can only have 1 program maximum
  if ($position && stripos($position, 'Dean') === false && count($valid_programs) > 1) {
    $valid_programs = [$valid_programs[0]];
  }

  // Mother Program
  $mother_program = !empty($valid_programs) ? $valid_programs[0] : null;

  // Hash the new password if provided
  $final_password = null;
  if (!empty($new_password)) {
    $final_password = password_hash($new_password, PASSWORD_BCRYPT);
  }

  $conn->begin_transaction();

  try {
    // A. Update ADMIN table
    if ($final_password) {
      $update_admin = $conn->prepare("UPDATE admin SET idnumber = ?, position = ?, faculty_rank = ?, status = ?, password = ? WHERE idnumber = ?");
      $update_admin->bind_param("ssssss", $new_id, $position, $faculty_rank, $status, $final_password, $original_id);
    } else {
      $update_admin = $conn->prepare("UPDATE admin SET idnumber = ?, position = ?, faculty_rank = ?, status = ? WHERE idnumber = ?");
      $update_admin->bind_param("sssss", $new_id, $position, $faculty_rank, $status, $original_id);
    }
    $update_admin->execute();

    // B. Update FACULTY table 
    if ($final_password) {
      $update_faculty = $conn->prepare("UPDATE faculty SET idnumber = ?, faculty_rank = ?, college = ?, program = ?, password = ? WHERE idnumber = ?");
      $update_faculty->bind_param("ssssss", $new_id, $faculty_rank, $main_college, $mother_program, $final_password, $original_id);
    } else {
      $update_faculty = $conn->prepare("UPDATE faculty SET idnumber = ?, faculty_rank = ?, college = ?, program = ? WHERE idnumber = ?");
      $update_faculty->bind_param("sssss", $new_id, $faculty_rank, $main_college, $mother_program, $original_id);
    }
    $update_faculty->execute();

    // C. Update admin_college mappings
    $del = $conn->prepare("DELETE FROM admin_college WHERE admin_idnumber = ? OR admin_idnumber = ?");
    $del->bind_param("ss", $original_id, $new_id);
    $del->execute();
    $del->close();

    if ($main_college) {
      $ins = $conn->prepare("INSERT IGNORE INTO admin_college (admin_idnumber, college_name, program_name) VALUES (?, ?, ?)");
      $unique_programs = array_unique($valid_programs);

      if (empty($unique_programs)) {
        $null_prog = null;
        $ins->bind_param("sss", $new_id, $main_college, $null_prog);
        $ins->execute();
      } else {
        foreach ($unique_programs as $prog) {
          $ins->bind_param("sss", $new_id, $main_college, $prog);
          $ins->execute();
        }
      }
      $ins->close();
    }

    $conn->commit();
    $_SESSION['update_success'] = "Admin record updated successfully!";
  } catch (Exception $e) {
    $conn->rollback();
    $_SESSION['msg'] = "Update failed: " . $e->getMessage();
    $_SESSION['msg_type'] = "danger";
  }
  header("Location: superadmin-adminlist.php");
  exit();
}

// --- ✅ 2. FETCH DATA FOR LIST & MODAL ---
$query = "
  SELECT 
    a.idnumber,
    a.first_name,
    a.mid_name,
    a.last_name,
    a.faculty_rank,
    a.position,
    a.status,
    GROUP_CONCAT(DISTINCT ad.college_name ORDER BY ad.college_name SEPARATOR ', ') AS college,
    GROUP_CONCAT(DISTINCT NULLIF(ad.program_name, '') ORDER BY ad.program_name SEPARATOR ', ') AS programs
  FROM admin a
  LEFT JOIN admin_college ad ON a.idnumber = ad.admin_idnumber
  WHERE a.role = 'admin'
  GROUP BY a.idnumber
  ORDER BY a.last_name ASC
";
$result = mysqli_query($conn, $query);

$college_programs = [];
$dept_res = $conn->query("SELECT DISTINCT college_name FROM adds WHERE college_name IS NOT NULL AND college_name != '' ORDER BY college_name ASC");
while ($r = $dept_res->fetch_assoc()) {
  $college_programs[$r['college_name']] = [];
}
$prog_res = $conn->query("SELECT college_name, program_name FROM adds WHERE college_name IS NOT NULL AND program_name IS NOT NULL AND program_name != '' ORDER BY college_name, program_name");
while ($r = $prog_res->fetch_assoc()) {
  if (isset($college_programs[$r['college_name']])) {
    $college_programs[$r['college_name']][] = $r['program_name'];
  }
}

$admin_assignments_map = [];
$ac_res = $conn->query("SELECT admin_idnumber, college_name, program_name FROM admin_college");
while ($row = $ac_res->fetch_assoc()) {
  $id = $row['admin_idnumber'];
  $col = $row['college_name'];
  $prog = $row['program_name'];

  if (!isset($admin_assignments_map[$id])) $admin_assignments_map[$id] = [];
  if (!isset($admin_assignments_map[$id][$col])) $admin_assignments_map[$id][$col] = [];
  if (!empty($prog)) {
    $admin_assignments_map[$id][$col][] = $prog;
  }
}

$positions = [];
$ranks = [];
$res_pos = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL AND position_name != '' ORDER BY position_name ASC");
while ($row = $res_pos->fetch_assoc()) {
  $positions[] = $row['position_name'];
}
$res_rank = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != '' ORDER BY rank_name ASC");
while ($row = $res_rank->fetch_assoc()) {
  $ranks[] = $row['rank_name'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <link rel="stylesheet" href="assets/css/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .choices {
      width: 100% !important;
      margin-bottom: 0;
    }

    .choices__inner {
      width: 100% !important;
      border-radius: 0.375rem;
      border: 1px solid #dee2e6;
      background-color: #fff;
      padding: 0.375rem 0.75rem;
      min-height: calc(3.5rem + 2px);
    }

    .choices__list--multiple .choices__item {
      background-color: #198754;
      border: 1px solid #146c43;
      border-radius: 4px;
    }

    .choices__list--dropdown {
      z-index: 1055 !important;
    }
  </style>
</head>

<body>
  <?php include 'superadmin-header.php'; ?>
  <?php include 'superadmin-sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <div>
        <h1>Admin List</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
            <li class="breadcrumb-item">List</li>
            <li class="breadcrumb-item active">Admin List</li>
          </ol>
        </nav>
      </div>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">

              <?php if (isset($_SESSION['update_success'])): ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: <?= json_encode($_SESSION['update_success']) ?>,
                      timer: 2000,
                      showConfirmButton: false
                    });
                  });
                </script>
              <?php unset($_SESSION['update_success']);
              endif; ?>

              <?php if (isset($_SESSION['msg'])): ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                      icon: '<?= $_SESSION['msg_type'] === 'danger' ? 'error' : 'info' ?>',
                      title: 'Notice',
                      text: <?= json_encode($_SESSION['msg']) ?>
                    });
                  });
                </script>
              <?php unset($_SESSION['msg'], $_SESSION['msg_type']);
              endif; ?>

              <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                <div class="d-flex align-items-center gap-2">
                  <label for="pageSize" class="mb-0 fw-semibold text-muted">Show</label>
                  <select id="pageSize" class="form-select form-select-sm w-auto">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="999999">All</option>
                  </select>
                  <span class="mb-0 fw-semibold text-muted">entries</span>
                </div>
                <div class="search-box position-relative" style="width: 300px;">
                  <input type="text" id="customAdminSearch" class="form-control" placeholder="Search ID, Name, College...">
                  <i class="bi bi-search position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); color: #6c757d;"></i>
                </div>
              </div>

              <table id="adminTable" class="table table-hover align-middle">
                <thead class="table-light text-center">
                  <tr>
                    <th>ID Number</th>
                    <th>Full Name</th>
                    <th>Faculty Rank</th>
                    <th>College</th>
                    <th>Programs</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody class="text-center">
                  <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['idnumber']); ?></td>
                      <td class="text-capitalize">
                        <?= htmlspecialchars($row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name']); ?>
                      </td>
                      <td><?= htmlspecialchars($row['faculty_rank'] ?? '—'); ?></td>
                      <td>
                        <?php if (!empty($row['college'])): ?>
                          <?php foreach (explode(', ', $row['college']) as $dept): ?>
                            <span><?= htmlspecialchars($dept); ?></span><br>
                          <?php endforeach; ?>
                          <?php else: ?>—<?php endif; ?>
                      </td>
                      <td>
                        <?php if (!empty($row['programs'])): ?>
                          <?php foreach (explode(', ', $row['programs']) as $prog): ?>
                            <span><?= htmlspecialchars($prog); ?></span><br>
                          <?php endforeach; ?>
                          <?php else: ?>—<?php endif; ?>
                      </td>
                      <td><?= htmlspecialchars($row['position'] ?? '—'); ?></td>
                      <td>
                        <?php if ($row['status'] === 'active'): ?>
                          <span class="badge bg-success">Active</span>
                        <?php else: ?>
                          <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-warning btn-sm edit-btn"
                          data-bs-toggle="modal"
                          data-bs-target="#editAdminModal"
                          data-id="<?= htmlspecialchars($row['idnumber']); ?>"
                          data-fname="<?= htmlspecialchars($row['first_name']); ?>"
                          data-mname="<?= htmlspecialchars($row['mid_name']); ?>"
                          data-lname="<?= htmlspecialchars($row['last_name']); ?>"
                          data-rank="<?= htmlspecialchars($row['faculty_rank']); ?>"
                          data-pos="<?= htmlspecialchars($row['position']); ?>"
                          data-college="<?= htmlspecialchars($row['college']); ?>"
                          data-programs="<?= htmlspecialchars($row['programs']); ?>"
                          data-status="<?= htmlspecialchars($row['status']); ?>">
                          <i class="bi bi-pencil-square"></i> Edit
                        </button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>

              <div class="d-flex justify-content-end mt-3">
                <nav aria-label="Table navigation">
                  <ul class="pagination pagination-sm" id="paginationControls"></ul>
                </nav>
              </div>

            </div>
          </div>

        </div>
      </div>
    </section>
  </main>

  <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAdminModalLabel">Edit Admin Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" id="editAdminForm" class="row g-3 px-3 py-2">

          <input type="hidden" name="old_idnumber" id="modal_old_idnumber">
          <input type="hidden" name="update_admin" value="1">
          <input type="hidden" name="fallback_programs" id="modal_fallback_programs">

          <div class="col-md-6 mt-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="modal_fullname" disabled>
              <label>Full Name</label>
            </div>
          </div>

          <div class="col-md-6 mt-3">
            <div class="form-floating">
              <input type="text" class="form-control" name="idnumber" id="modal_idnumber" required>
              <label>ID Number (Editable)</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select class="form-select" name="position" id="modal_position" required>
                <option value="" disabled>Select Position</option>
                <?php foreach ($positions as $p): ?>
                  <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
                <?php endforeach; ?>
              </select>
              <label>Position</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select class="form-select" name="faculty_rank" id="modal_rank">
                <option value="">-- No Specific Rank --</option>
                <?php foreach ($ranks as $rank): ?>
                  <option value="<?= htmlspecialchars($rank) ?>"><?= htmlspecialchars($rank) ?></option>
                <?php endforeach; ?>
              </select>
              <label>Faculty Rank (Optional)</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select class="form-select" name="status" id="modal_status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <label>Status</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating position-relative">
              <input type="password" name="new_password" id="modal_password" class="form-control" placeholder="New Password">
              <label>New Password (Leave blank to keep current)</label>
              <span class="toggle-password" onclick="toggleModalPassword()" style="position:absolute; top:50%; right:15px; transform:translateY(-50%); cursor:pointer; font-size: 1.1rem; color: #6c757d;">
                <i class="bi bi-eye-fill" id="toggleModalPasswordIcon"></i>
              </span>
            </div>
          </div>

          <div class="col-12 mt-4">
            <hr class="text-muted">
            <h6 class="fw-bold text-secondary mb-3">Department & Program Assignment</h6>
          </div>

          <div class="col-12">
            <div class="form-floating">
              <select class="form-select" name="main_college" id="main_college" required>
                <option value="" disabled selected>-- Select College --</option>
              </select>
              <label>College</label>
            </div>
          </div>

          <div class="col-12 mt-3">
            <label class="form-label fw-bold mb-1" style="font-size: 0.95rem; color: #444;">
              Assign Program(s) <span id="program_hint"></span>
            </label>

            <select class="form-control" name="main_program[]" id="main_program" multiple></select>

            <div class="alert border-0 shadow-sm mt-3" id="primary_program_notice" style="display: none; border-left: 4px solid #0d6efd !important; background-color: #f8f9fa;">
              <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-3 text-primary me-3"></i>
                <div id="notice_text" style="font-size: 0.9rem; color: #333;"></div>
              </div>
            </div>
          </div>

          <div class="modal-footer border-0 p-0 mt-3 pb-3 w-100 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success px-4">Update Admin</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/choices.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    const collegePrograms = <?= json_encode($college_programs, JSON_UNESCAPED_UNICODE) ?>;
    const allColleges = Object.keys(collegePrograms);
    const adminDataMap = <?= json_encode($admin_assignments_map, JSON_UNESCAPED_UNICODE) ?>;

    document.addEventListener('DOMContentLoaded', () => {

      const mainDeptSelect = document.getElementById('main_college');
      const positionSelect = document.getElementById('modal_position');
      const mainProgramSelect = document.getElementById('main_program');

      const programHint = document.getElementById('program_hint');
      const noticeDiv = document.getElementById('primary_program_notice');
      const noticeText = document.getElementById('notice_text');

      let programChoicesInstance = null;
      let currentAdminId = null;

      allColleges.forEach(dept => {
        mainDeptSelect.add(new Option(dept, dept));
      });

      function updateProgramDropdown() {
        const dept = mainDeptSelect.value;
        const position = positionSelect.value || '';
        const isDean = position.toLowerCase().includes('dean');

        if (programChoicesInstance) {
          programChoicesInstance.destroy();
          programChoicesInstance = null;
        }

        mainProgramSelect.innerHTML = '';

        if (dept) {
          noticeDiv.style.display = "block";
          if (isDean) {
            programHint.innerText = "— Deans can select multiple";
            programHint.className = "text-success fw-normal";
            noticeText.innerHTML = "<strong>Note for Deans:</strong> You can assign multiple programs. <br><span class='text-danger fw-bold'>Important:</span> The very <strong>FIRST</strong> program you select will be permanently set as their primary/mother program in the Faculty system.";
          } else {
            programHint.innerText = "— Limited to 1 program";
            programHint.className = "text-secondary fw-normal";
            noticeText.innerHTML = "<strong>Note:</strong> Non-Deans can only be assigned to 1 program. This program will be directly linked to their Faculty account.";
          }
        } else {
          noticeDiv.style.display = "none";
          programHint.innerText = "";
        }

        programChoicesInstance = new Choices(mainProgramSelect, {
          removeItemButton: true,
          searchEnabled: true,
          shouldSort: false,
          itemSelectText: '',
          maxItemCount: isDean ? -1 : 1,
          placeholderValue: isDean ? 'Click to select Program(s)...' : 'Click to select a Program...'
        });

        const programs = collegePrograms[dept] || [];
        const savedAssignments = adminDataMap[currentAdminId] || {};
        const savedProgramsForDept = savedAssignments[dept] || [];

        const choicesData = programs.map(p => ({
          value: p,
          label: p,
          selected: savedProgramsForDept.includes(p)
        }));

        programChoicesInstance.setChoices(choicesData, 'value', 'label', true);
      }

      mainDeptSelect.addEventListener('change', updateProgramDropdown);
      positionSelect.addEventListener('change', updateProgramDropdown);

      const editButtons = document.querySelectorAll('.edit-btn');
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          currentAdminId = this.getAttribute('data-id');
          const fname = this.getAttribute('data-fname');
          const mname = this.getAttribute('data-mname');
          const lname = this.getAttribute('data-lname');
          const rank = this.getAttribute('data-rank');
          const pos = this.getAttribute('data-pos');
          const collegeString = this.getAttribute('data-college');
          const programString = this.getAttribute('data-programs');
          const status = this.getAttribute('data-status');

          const fullName = `${fname} ${mname} ${lname}`.replace(/\s+/g, ' ').trim();

          document.getElementById('modal_fullname').value = fullName;
          document.getElementById('modal_old_idnumber').value = currentAdminId;
          document.getElementById('modal_idnumber').value = currentAdminId;
          document.getElementById('modal_position').value = pos || '';
          document.getElementById('modal_rank').value = rank || '';
          document.getElementById('modal_status').value = status || 'active';
          document.getElementById('modal_password').value = '';

          // Set the fallback hidden input
          document.getElementById('modal_fallback_programs').value = programString && programString !== "—" ? programString : "";

          let mainCollege = "";
          if (collegeString && collegeString !== "—") {
            let cols = collegeString.split(", ");
            if (cols.length > 0) mainCollege = cols[0];
          }

          if (allColleges.includes(mainCollege)) {
            mainDeptSelect.value = mainCollege;
          } else {
            mainDeptSelect.value = "";
          }

          updateProgramDropdown();
        });
      });

      document.getElementById("editAdminForm").addEventListener("submit", function(e) {
        e.preventDefault();
        Swal.fire({
          title: "Confirm Update",
          text: "Are you sure you want to save these changes?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Yes, Update",
          cancelButtonText: "Cancel",
          confirmButtonColor: "#198754"
        }).then((result) => {
          if (result.isConfirmed) {
            this.submit();
          }
        });
      });

      let currentPage = 1;
      let rowsPerPage = 10;

      function updateTable() {
        let filterValue = document.getElementById('customAdminSearch').value.toLowerCase();
        let tableRows = Array.from(document.querySelectorAll('#adminTable tbody tr'));

        let filteredRows = tableRows.filter(row => {
          return row.textContent.toLowerCase().includes(filterValue);
        });

        tableRows.forEach(row => row.style.display = 'none');

        let totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (currentPage > totalPages) currentPage = totalPages || 1;

        let start = (currentPage - 1) * rowsPerPage;
        let end = start + rowsPerPage;

        filteredRows.slice(start, end).forEach(row => {
          row.style.display = '';
        });

        renderPaginationControls(totalPages);
      }

      function renderPaginationControls(totalPages) {
        const paginationContainer = document.getElementById('paginationControls');
        paginationContainer.innerHTML = '';
        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
          let li = document.createElement('li');
          li.className = `page-item ${i === currentPage ? 'active' : ''}`;
          let a = document.createElement('a');
          a.className = 'page-link';
          a.href = 'javascript:void(0);';
          a.innerText = i;
          a.addEventListener('click', () => {
            currentPage = i;
            updateTable();
          });
          li.appendChild(a);
          paginationContainer.appendChild(li);
        }
      }

      document.getElementById('customAdminSearch').addEventListener('keyup', () => {
        currentPage = 1;
        updateTable();
      });

      document.getElementById('pageSize').addEventListener('change', function() {
        rowsPerPage = parseInt(this.value);
        currentPage = 1;
        updateTable();
      });

      updateTable();
    });

    function toggleModalPassword() {
      const passwordInput = document.getElementById("modal_password");
      const icon = document.getElementById("toggleModalPasswordIcon");
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("bi-eye-fill");
        icon.classList.add("bi-eye-slash-fill");
      } else {
        passwordInput.type = "password";
        icon.classList.remove("bi-eye-slash-fill");
        icon.classList.add("bi-eye-fill");
      }
    }
  </script>

</body>

</html>
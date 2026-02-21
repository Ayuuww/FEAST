<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if user is logged in and is registrar
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// --- ✅ 1. HANDLE MODAL FORM SUBMISSION (UPDATE & DEMOTE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_superadmin'])) {
  $original_id = $_POST['old_idnumber'];
  $new_id = trim($_POST['idnumber']);
  $position = !empty($_POST['position']) ? trim($_POST['position']) : null;
  $faculty_rank = !empty($_POST['faculty_rank']) ? trim($_POST['faculty_rank']) : null;
  $status = trim($_POST['status']);
  $new_password = trim($_POST['new_password']);
  $new_role = trim($_POST['role']); // Handle Demotions

  $main_college = !empty($_POST['main_college']) ? trim($_POST['main_college']) : null;

  // Fallback program array handler for Choices.js
  $programs_post = $_POST['main_program'] ?? [];
  $fallback_programs = $_POST['fallback_programs'] ?? '';
  if (empty($programs_post) && !empty($fallback_programs)) {
    $programs_post = explode(', ', $fallback_programs);
  }

  if ($new_id === '') {
    $_SESSION['msg'] = "ID number is required.";
    $_SESSION['msg_type'] = "danger";
    header("Location: register-superadminlist.php");
    exit();
  }

  // Check for duplicate ID against OTHER superadmins
  $chk = $conn->prepare("SELECT idnumber FROM superadmin WHERE idnumber = ? AND idnumber != ?");
  $chk->bind_param("ss", $new_id, $original_id);
  $chk->execute();
  if ($chk->get_result()->num_rows > 0) {
    $_SESSION['msg'] = "ID number already exists for another Super Admin.";
    $_SESSION['msg_type'] = "danger";
    header("Location: register-superadminlist.php");
    exit();
  }
  $chk->close();

  if (!is_array($programs_post)) {
    $programs_post = [$programs_post];
  }

  // Clean out empty values to satisfy foreign keys
  $valid_programs = [];
  foreach ($programs_post as $p) {
    if (trim($p) !== '') {
      $valid_programs[] = trim($p);
    }
  }

  // Ensure Non-Deans can only have 1 program maximum
  if ($position && stripos($position, 'Dean') === false && count($valid_programs) > 1) {
    $valid_programs = [$valid_programs[0]];
  }

  $mother_program = !empty($valid_programs) ? $valid_programs[0] : null;

  // Hash new password or grab existing
  $final_password = null;
  if (!empty($new_password)) {
    $final_password = password_hash($new_password, PASSWORD_BCRYPT);
  } else {
    $getPw = $conn->query("SELECT password FROM superadmin WHERE idnumber = '$original_id'");
    if ($getPw->num_rows > 0) {
      $final_password = $getPw->fetch_assoc()['password'];
    }
  }

  $conn->begin_transaction();

  try {
    // 🔹 CASE 1: MAINTAIN AS SUPERADMIN
    if ($new_role === 'superadmin') {
      $upd_sa = $conn->prepare("UPDATE superadmin SET idnumber = ?, position = ?, faculty_rank = ?, status = ?, password = ?, college = ?, program = ? WHERE idnumber = ?");
      $upd_sa->bind_param("ssssssss", $new_id, $position, $faculty_rank, $status, $final_password, $main_college, $mother_program, $original_id);
      $upd_sa->execute();

      // Sync base faculty table
      $upd_fac = $conn->prepare("UPDATE faculty SET idnumber = ?, faculty_rank = ?, status = ?, password = ?, college = ?, program = ? WHERE idnumber = ?");
      $upd_fac->bind_param("sssssss", $new_id, $faculty_rank, $status, $final_password, $main_college, $mother_program, $original_id);
      $upd_fac->execute();

      $_SESSION['update_success'] = "Super Admin record updated successfully!";
    }
    // 🔹 CASE 2: DEMOTE TO ADMIN
    elseif ($new_role === 'admin') {
      // 1. Fetch current profile details to migrate them
      $fetch = $conn->query("SELECT first_name, mid_name, last_name FROM superadmin WHERE idnumber = '$original_id'")->fetch_assoc();

      // 2. Delete from superadmin table
      $conn->query("DELETE FROM superadmin WHERE idnumber = '$original_id'");

      // 3. Insert into Admin Table
      $ins_admin = $conn->prepare("INSERT INTO admin (idnumber, first_name, mid_name, last_name, password, position, faculty_rank, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'admin', ?)");
      $ins_admin->bind_param("ssssssss", $new_id, $fetch['first_name'], $fetch['mid_name'], $fetch['last_name'], $final_password, $position, $faculty_rank, $status);
      $ins_admin->execute();

      // 4. Update Faculty Mirror Record
      $upd_fac = $conn->prepare("UPDATE faculty SET idnumber = ?, faculty_rank = ?, status = ?, password = ?, college = ?, program = ?, role = 'admin' WHERE idnumber = ?");
      $upd_fac->bind_param("sssssss", $new_id, $faculty_rank, $status, $final_password, $main_college, $mother_program, $original_id);
      $upd_fac->execute();

      // 5. Setup Admin_College mappings securely
      if ($main_college) {
        $ins_map = $conn->prepare("INSERT IGNORE INTO admin_college (admin_idnumber, college_name, program_name) VALUES (?, ?, ?)");
        $unique_programs = array_unique($valid_programs);
        if (empty($unique_programs)) {
          $empty_p = ''; // Mapping table doesn't allow NULL
          $ins_map->bind_param("sss", $new_id, $main_college, $empty_p);
          $ins_map->execute();
        } else {
          foreach ($unique_programs as $p) {
            $ins_map->bind_param("sss", $new_id, $main_college, $p);
            $ins_map->execute();
          }
        }
      }
      $_SESSION['update_success'] = "Successfully demoted to Admin role. Access migrated.";
    }

    $conn->commit();
  } catch (Exception $e) {
    $conn->rollback();
    $_SESSION['msg'] = "Update failed: " . $e->getMessage();
    $_SESSION['msg_type'] = "danger";
  }
  header("Location: register-superadminlist.php");
  exit();
}

// --- ✅ 2. FETCH DATA FOR LIST & MODAL ---

// A. Fetch Superadmin List
$query = "
  SELECT 
    idnumber,
    first_name,
    mid_name,
    last_name,
    faculty_rank,
    college,
    program,
    position,
    status
  FROM superadmin
  ORDER BY last_name ASC
";
$result = mysqli_query($conn, $query);

// B. Fetch Dropdown Data
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
  <?php include 'register-header.php'; ?>
  <?php include 'register-sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <div>
        <h1>Super Admin List</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
            <li class="breadcrumb-item">List</li>
            <li class="breadcrumb-item active">Super Admin List</li>
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
                    <th>Program</th>
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
                      <td><?= htmlspecialchars($row['college'] ?? '—'); ?></td>
                      <td><?= htmlspecialchars($row['program'] ?? '—'); ?></td>
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
                          data-program="<?= htmlspecialchars($row['program']); ?>"
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
          <h5 class="modal-title" id="editAdminModalLabel">Edit Super Admin Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" id="editAdminForm" class="row g-3 px-3 py-2">

          <input type="hidden" name="old_idnumber" id="modal_old_idnumber">
          <input type="hidden" name="update_superadmin" value="1">
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
              <select class="form-select" name="role" id="modal_role" required>
                <option value="superadmin">Super Admin</option>
                <option value="admin">Change to Admin</option>
              </select>
              <label>Administrative Role</label>
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
              <label>Status <span class="text-danger" style="font-size:0.75rem;">(Set Inactive to become Faculty)</span></label>
            </div>
          </div>

          <div class="col-md-12">
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
            <button type="submit" class="btn btn-success px-4">Save Changes</button>
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

    document.addEventListener('DOMContentLoaded', () => {
      const mainDeptSelect = document.getElementById('main_college');
      const positionSelect = document.getElementById('modal_position');
      const roleSelect = document.getElementById('modal_role');
      const mainProgramSelect = document.getElementById('main_program');

      const programHint = document.getElementById('program_hint');
      const noticeDiv = document.getElementById('primary_program_notice');
      const noticeText = document.getElementById('notice_text');

      let programChoicesInstance = null;
      let prefilledProgram = null;

      // 1. Populate College Dropdown
      allColleges.forEach(dept => {
        mainDeptSelect.add(new Option(dept, dept));
      });

      // 2. Initialize dynamic Program Dropdown based on Role
      function updateProgramDropdown() {
        const dept = mainDeptSelect.value;
        const position = positionSelect.value || '';
        const role = roleSelect.value;
        const isDean = position.toLowerCase().includes('dean');

        // Deans can have multiple programs IF they are demoted to Admin. 
        // Standard Superadmins technically only have 1 mother program in their table.
        const allowMultiple = isDean && role === 'admin';

        if (programChoicesInstance) {
          programChoicesInstance.destroy();
          programChoicesInstance = null;
        }

        mainProgramSelect.innerHTML = '';

        // --- Update UI Hint and Alert Box ---
        if (dept) {
          noticeDiv.style.display = "block";
          if (allowMultiple) {
            programHint.innerText = "— Deans can select multiple";
            programHint.className = "text-success fw-normal";
            noticeText.innerHTML = "<strong>Note for Deans:</strong> You can assign multiple programs. <br><span class='text-danger fw-bold'>Important:</span> The very <strong>FIRST</strong> program you select will be permanently set as their primary/mother program in the Faculty system.";
          } else {
            programHint.innerText = "— Limited to 1 program";
            programHint.className = "text-secondary fw-normal";
            noticeText.innerHTML = "<strong>Note:</strong> Superadmins and non-Deans can only be assigned to 1 program. This program will be directly linked to their Faculty account.";
          }
        } else {
          noticeDiv.style.display = "none";
          programHint.innerText = "";
        }

        // --- Re-initialize Choices.js safely ---
        programChoicesInstance = new Choices(mainProgramSelect, {
          removeItemButton: true,
          searchEnabled: true,
          shouldSort: false,
          itemSelectText: '',
          maxItemCount: allowMultiple ? -1 : 1,
          placeholderValue: allowMultiple ? 'Click to select Program(s)...' : 'Click to select a Program...'
        });

        // Load programs for the selected college
        const programs = collegePrograms[dept] || [];

        const choicesData = programs.map(p => ({
          value: p,
          label: p,
          selected: p === prefilledProgram
        }));

        programChoicesInstance.setChoices(choicesData, 'value', 'label', true);
        prefilledProgram = null;
      }

      // Listeners
      mainDeptSelect.addEventListener('change', updateProgramDropdown);
      positionSelect.addEventListener('change', updateProgramDropdown);
      roleSelect.addEventListener('change', updateProgramDropdown);

      // --- 3. EDIT BUTTON CLICK POPULATION ---
      const editButtons = document.querySelectorAll('.edit-btn');
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const fname = this.getAttribute('data-fname');
          const mname = this.getAttribute('data-mname');
          const lname = this.getAttribute('data-lname');
          const rank = this.getAttribute('data-rank');
          const pos = this.getAttribute('data-pos');
          const collegeString = this.getAttribute('data-college');
          const programString = this.getAttribute('data-program');
          const status = this.getAttribute('data-status');

          const fullName = `${fname} ${mname} ${lname}`.replace(/\s+/g, ' ').trim();

          document.getElementById('modal_fullname').value = fullName;
          document.getElementById('modal_old_idnumber').value = id;
          document.getElementById('modal_idnumber').value = id;
          document.getElementById('modal_position').value = pos || '';
          document.getElementById('modal_rank').value = rank || '';
          document.getElementById('modal_status').value = status || 'active';
          document.getElementById('modal_role').value = 'superadmin';
          document.getElementById('modal_password').value = '';

          document.getElementById('modal_fallback_programs').value = programString && programString !== "—" ? programString : "";

          let mainCollege = "";
          if (collegeString && collegeString !== "—") {
            let cols = collegeString.split(", ");
            if (cols.length > 0) mainCollege = cols[0];
          }

          if (allColleges.includes(mainCollege)) {
            mainDeptSelect.value = mainCollege;
            prefilledProgram = programString && programString !== "—" ? programString : null;
          } else {
            mainDeptSelect.value = "";
          }

          updateProgramDropdown();
        });
      });

      // --- 4. FORM SUBMIT CONFIRMATION ---
      document.getElementById("editAdminForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const selectedRole = roleSelect.value;
        let warningTitle = "Confirm Update";
        let warningText = "Are you sure you want to save these changes?";
        let btnColor = "#198754";

        if (selectedRole === 'admin') {
          warningTitle = "Confirm Demotion";
          warningText = "You are about to demote this user to Admin. They will lose Super Admin access. Proceed?";
          btnColor = "#ffc107";
        }

        Swal.fire({
          title: warningTitle,
          text: warningText,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Yes, Update",
          cancelButtonText: "Cancel",
          confirmButtonColor: btnColor
        }).then((result) => {
          if (result.isConfirmed) {
            this.submit();
          }
        });
      });

      // --- 5. SEARCH & PAGINATION LOGIC ---
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

    // --- 6. TOGGLE PASSWORD VISIBILITY ---
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
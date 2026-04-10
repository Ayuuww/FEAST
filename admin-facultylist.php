<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if admin is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

// --- ✅ FETCH ALL DYNAMIC DATA FOR DROPDOWNS ---
$positions_result = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL AND position_name != '' ORDER BY position_name ASC");

$rankQuery = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != '' ORDER BY rank_name ASC");
$facultyRanks = [];
while ($row = $rankQuery->fetch_assoc()) {
  $facultyRanks[] = $row['rank_name'];
}

$adds_data_result = $conn->query("
    SELECT DISTINCT college_name, program_name 
    FROM adds 
    WHERE college_name IS NOT NULL AND college_name != '' 
    ORDER BY college_name, program_name ASC
");
$collegePrograms = [];
while ($row = $adds_data_result->fetch_assoc()) {
  $college = $row['college_name'];
  $program = $row['program_name'];
  if (!isset($collegePrograms[$college])) {
    $collegePrograms[$college] = [];
  }
  if ($program && !in_array($program, $collegePrograms[$college])) {
    $collegePrograms[$college][] = $program;
  }
}

// --- ✅ HANDLE MODAL FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_faculty'])) {
  $faculty_id = $_POST['old_idnumber'];
  $new_idnumber = $_POST['idnumber'];
  $new_status = $_POST['status'];
  $new_role_trigger = $_POST['role'];
  $new_faculty_rank = !empty($_POST['faculty_rank']) ? trim($_POST['faculty_rank']) : null;
  $new_password = trim($_POST['new_password']);

  $main_college = !empty($_POST['main_college']) ? trim($_POST['main_college']) : null;
  $programs_post = $_POST['main_program'] ?? [];

  if ($new_idnumber === '') {
    $_SESSION['msg'] = "ID number is required.";
    $_SESSION['msg_type'] = "danger";
    header("Location: admin-facultylist.php");
    exit();
  }

  // Check for duplicate ID against OTHER faculty
  $chk = $conn->prepare("SELECT idnumber FROM faculty WHERE idnumber = ? AND idnumber != ?");
  $chk->bind_param("ss", $new_idnumber, $faculty_id);
  $chk->execute();
  if ($chk->get_result()->num_rows > 0) {
    $_SESSION['msg'] = "ID number already exists.";
    $_SESSION['msg_type'] = "danger";
    header("Location: admin-facultylist.php");
    exit();
  }
  $chk->close();

  // Fetch the current faculty base data for password handling
  $stmt = $conn->prepare("SELECT * FROM faculty WHERE idnumber = ?");
  $stmt->bind_param("s", $faculty_id);
  $stmt->execute();
  $faculty = $stmt->get_result()->fetch_assoc();

  if ($faculty) {
    // Hash the new password if provided, otherwise keep the old hashed password
    $final_password = !empty($new_password) ? password_hash($new_password, PASSWORD_BCRYPT) : $faculty['password'];

    // Safely format the programs array
    if (!is_array($programs_post)) {
      $programs_post = [$programs_post];
    }
    $valid_programs = [];
    foreach ($programs_post as $p) {
      if (trim($p) !== '') {
        $valid_programs[] = trim($p);
      }
    }

    $position = !empty($_POST['position']) ? trim($_POST['position']) : null;

    // Ensure Non-Deans can only have 1 program maximum
    if ($position && stripos($position, 'Dean') === false && count($valid_programs) > 1) {
      $valid_programs = [$valid_programs[0]];
    }

    $mother_program = !empty($valid_programs) ? $valid_programs[0] : null;

    $conn->begin_transaction();

    try {
      // --- 🔹 PROMOTE TO ADMIN LOGIC ---
      if ($new_role_trigger === 'admin') {
        $checkAdmin = $conn->prepare("SELECT idnumber FROM admin WHERE idnumber = ?");
        $checkAdmin->bind_param("s", $faculty_id);
        $checkAdmin->execute();

        if ($checkAdmin->get_result()->num_rows > 0) {
          $_SESSION['promotion_message'] = "This faculty is already an Admin. Please manage them in the Admin list.";
        } else {
          // Insert Admin 
          $insertAdmin = $conn->prepare("INSERT INTO admin (idnumber, first_name, mid_name, last_name, password, position, faculty_rank, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'admin', ?)");
          $insertAdmin->bind_param("ssssssss", $new_idnumber, $faculty['first_name'], $faculty['mid_name'], $faculty['last_name'], $final_password, $position, $new_faculty_rank, $new_status);
          $insertAdmin->execute();

          // Update original faculty record 
          $updateFaculty = $conn->prepare("UPDATE faculty SET idnumber = ?, status = ?, faculty_rank = ?, college = ?, program = ?, password = ?, role = 'admin' WHERE idnumber = ?");
          $updateFaculty->bind_param("sssssss", $new_idnumber, $new_status, $new_faculty_rank, $main_college, $mother_program, $final_password, $faculty_id);
          $updateFaculty->execute();

          // Insert into admin_college mappings safely
          if ($main_college) {
            $stmt_dept = $conn->prepare("INSERT IGNORE INTO admin_college (admin_idnumber, college_name, program_name) VALUES (?, ?, ?)");
            $unique_programs = array_unique($valid_programs);

            if (empty($unique_programs)) {
              $empty_prog = '';
              $stmt_dept->bind_param("sss", $new_idnumber, $main_college, $empty_prog);
              $stmt_dept->execute();
            } else {
              foreach ($unique_programs as $prog) {
                $prog_val = trim($prog);
                $stmt_dept->bind_param("sss", $new_idnumber, $main_college, $prog_val);
                $stmt_dept->execute();
              }
            }
            $stmt_dept->close();
          }
          $_SESSION['promotion_message'] = "Faculty successfully promoted to Admin!";
        }
      }

      // --- 🔹 PROMOTE TO SUPERADMIN LOGIC ---
      elseif ($new_role_trigger === 'superadmin') {
        $checkSuper = $conn->prepare("SELECT idnumber FROM superadmin WHERE idnumber = ?");
        $checkSuper->bind_param("s", $faculty_id);
        $checkSuper->execute();

        if ($checkSuper->get_result()->num_rows > 0) {
          $_SESSION['promotion_message'] = "Faculty is already a Superadmin.";
        } else {
          // Insert Superadmin
          $insertSuper = $conn->prepare("INSERT INTO superadmin (idnumber, first_name, mid_name, last_name, password, role, college, program, faculty_rank, position, status) VALUES (?, ?, ?, ?, ?, 'superadmin', ?, ?, ?, ?, ?)");
          $status = 'active';
          $insertSuper->bind_param("ssssssssss", $new_idnumber, $faculty['first_name'], $faculty['mid_name'], $faculty['last_name'], $final_password, $main_college, $mother_program, $new_faculty_rank, $position, $status);
          $insertSuper->execute();

          // Update original faculty record
          $updateFaculty = $conn->prepare("UPDATE faculty SET idnumber = ?, college = ?, program = ?, faculty_rank = ?, password = ?, role = 'superadmin' WHERE idnumber = ?");
          $updateFaculty->bind_param("ssssss", $new_idnumber, $main_college, $mother_program, $new_faculty_rank, $final_password, $faculty_id);
          $updateFaculty->execute();

          $_SESSION['promotion_message'] = "Faculty successfully promoted to Superadmin!";
        }
      }

      // --- 🔹 STANDARD FACULTY UPDATE LOGIC ---
      else {
        // Just update Faculty details normally (College and Program are now completely editable)
        $stmt = $conn->prepare("UPDATE faculty SET idnumber = ?, status = ?, faculty_rank = ?, college = ?, program = ?, password = ? WHERE idnumber = ?");
        $stmt->bind_param("sssssss", $new_idnumber, $new_status, $new_faculty_rank, $main_college, $mother_program, $final_password, $faculty_id);
        $stmt->execute();

        $_SESSION['update_success'] = "Faculty updated successfully!";
      }

      $conn->commit();
    } catch (Exception $e) {
      $conn->rollback();
      $_SESSION['msg'] = "Database Error: " . $e->getMessage();
      $_SESSION['msg_type'] = "error";
    }
  }

  header("Location: admin-facultylist.php");
  exit();
}

// --- ✅ FETCH FACULTY BASED ON ADMIN'S ASSIGNED COLLEGE(S) ---
$admin_id = $_SESSION['idnumber'];
$query = "
    SELECT f.* FROM faculty f
    INNER JOIN (
        SELECT DISTINCT college_name 
        FROM admin_college 
        WHERE admin_idnumber = ?
    ) ac ON f.college = ac.college_name
    WHERE f.role = 'faculty' 
    ORDER BY f.last_name ASC
";
$stmt_fac = $conn->prepare($query);
$stmt_fac->bind_param("s", $admin_id);
$stmt_fac->execute();
$result = $stmt_fac->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="assets/css/choices.min.css" />
  <style>
    /* Choices.js Bootstrap UI Fixes */
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

  <?php include 'admin-header.php'; ?>
  <?php include 'admin-sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>List of Faculty Members</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Faculty</li>
          <li class="breadcrumb-item active">List</li>
        </ol>
      </nav>
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
                      title: 'Updated!',
                      text: <?= json_encode($_SESSION['update_success']) ?>,
                      timer: 2000,
                      showConfirmButton: false
                    });
                  });
                </script>
              <?php unset($_SESSION['update_success']);
              endif; ?>

              <?php if (isset($_SESSION['promotion_message'])): ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                      icon: 'success',
                      title: 'Promotion Status',
                      text: <?= json_encode($_SESSION['promotion_message']) ?>,
                      timer: 2500,
                      showConfirmButton: false
                    });
                  });
                </script>
              <?php unset($_SESSION['promotion_message']);
              endif; ?>

              <?php if (isset($_SESSION['msg'])): ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                      icon: '<?= isset($_SESSION['msg_type']) && $_SESSION['msg_type'] === 'error' ? 'error' : 'info' ?>',
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
                  <input type="text" id="customFacultySearch" class="form-control" placeholder="Search ID, Name, College...">
                  <i class="bi bi-search position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); color: #6c757d;"></i>
                </div>
              </div>

              <table class="table table-hover align-middle" id="facultyTable">
                <thead class="table-light text-center">
                  <tr>
                    <th>ID Number</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Academic Rank</th>
                    <th>College</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th width="120px">Action</th>
                  </tr>
                </thead>
                <tbody class="text-center">
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['idnumber']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['first_name']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['mid_name']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['last_name']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['faculty_rank'] ?? '—'); ?></td>
                      <td class="text-uppercase"><?= htmlspecialchars($row['college'] ?? '—'); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['program'] ?? '—'); ?></td>
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
                          data-bs-target="#editFacultyModal"
                          data-id="<?= htmlspecialchars($row['idnumber']); ?>"
                          data-fname="<?= htmlspecialchars($row['first_name']); ?>"
                          data-mname="<?= htmlspecialchars($row['mid_name']); ?>"
                          data-lname="<?= htmlspecialchars($row['last_name']); ?>"
                          data-college="<?= htmlspecialchars($row['college'] ?? ''); ?>"
                          data-program="<?= htmlspecialchars($row['program'] ?? ''); ?>"
                          data-rank="<?= htmlspecialchars($row['faculty_rank'] ?? ''); ?>"
                          data-status="<?= htmlspecialchars($row['status']); ?>"
                          data-role="<?= htmlspecialchars($row['role']); ?>">
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

  <div class="modal fade" id="editFacultyModal" tabindex="-1" aria-labelledby="editFacultyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editFacultyModalLabel">Edit Faculty Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" id="editFacultyForm" class="row g-3 px-3 py-2">

          <input type="hidden" name="old_idnumber" id="modal_old_idnumber">
          <input type="hidden" name="update_faculty" value="1">

          <div class="col-md-6 mt-3">
            <div class="form-floating">
              <input type="text" class="form-control" name="idnumber" id="modal_idnumber" required>
              <label class="form-label">ID Number</label>
            </div>
          </div>

          <div class="col-md-6 mt-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="modal_fullname" disabled>
              <label class="form-label">Full Name</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select name="role" id="modal_role" class="form-select" required>
                <option value="faculty">Faculty</option>
                <option value="admin">Promote to Admin</option>
                <option value="superadmin">Promote to Superadmin</option>
              </select>
              <label class="form-label">Role</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select name="status" id="modal_status" class="form-select" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <label class="form-label">Current Status</label>
            </div>
          </div>

          <div class="col-md-12 mb-2">
            <div class="form-floating">
              <select class="form-select" name="faculty_rank" id="modal_rank">
                <option value="">-- Select Rank --</option>
                <?php foreach ($facultyRanks as $rank): ?>
                  <option value="<?= htmlspecialchars($rank) ?>"><?= htmlspecialchars($rank) ?></option>
                <?php endforeach; ?>
              </select>
              <label>Update Faculty Rank</label>
            </div>
          </div>

          <div id="admin-options" style="display: none;" class="row g-3 m-0 p-0">
            <hr>
            <h6 class="text-primary fw-bold">Admin Promotion Details</h6>

            <div class="col-md-12 mb-3">
              <div class="form-floating">
                <select class="form-select" name="position" id="position">
                  <option value="" disabled selected>-- Select Position --</option>
                  <?php $positions_result->data_seek(0); ?>
                  <?php while ($row = $positions_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['position_name']) ?>"><?= htmlspecialchars($row['position_name']) ?></option>
                  <?php endwhile; ?>
                </select>
                <label for="position">Position</label>
              </div>
            </div>
          </div>

          <div class="col-12 mt-2">
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

          <div class="col-md-12 mb-3 mt-3">
            <div class="form-floating position-relative">
              <input type="password" name="new_password" id="modal_password" class="form-control" placeholder="New Password">
              <label for="modal_password">New Password (Leave blank to keep current)</label>

              <span class="toggle-password" onclick="toggleModalPassword()"
                style="position:absolute; top:50%; right:15px; transform:translateY(-50%); cursor:pointer; font-size: 1.1rem; color: #6c757d;">
                <i class="bi bi-eye-fill" id="toggleModalPasswordIcon"></i>
              </span>
            </div>
          </div>

          <div class="modal-footer border-0 p-0 mt-2 pb-3 w-100 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success px-4">Save Changes</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/choices.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    const collegePrograms = <?= json_encode($collegePrograms) ?>;
    const allcollege = Object.keys(collegePrograms);

    document.addEventListener('DOMContentLoaded', function() {

      // --- MODAL POPULATION LOGIC ---
      const roleSelect = document.getElementById('modal_role');
      const adminOptions = document.getElementById('admin-options');
      const positionSelect = document.getElementById('position');

      const mainDeptSelect = document.getElementById('main_college');
      const mainProgramSelect = document.getElementById('main_program');

      const programHint = document.getElementById('program_hint');
      const noticeDiv = document.getElementById('primary_program_notice');
      const noticeText = document.getElementById('notice_text');

      let programChoicesInstance = null;
      let prefillProgram = null;

      // Populate Main College Dropdown
      allcollege.forEach(dept => {
        mainDeptSelect.add(new Option(dept, dept));
      });

      // Initialize dynamic Program Dropdown based on Position Role
      function updateProgramDropdown() {
        const dept = mainDeptSelect.value;
        const position = positionSelect.value || '';
        const role = roleSelect.value;
        const isDean = position.toLowerCase().includes('dean');

        // Deans can have multiple programs IF they are promoted to Admin.
        const allowMultiple = isDean && role === 'admin';

        if (programChoicesInstance) {
          programChoicesInstance.destroy();
          programChoicesInstance = null;
        }
        mainProgramSelect.innerHTML = '';

        // Update UI Hint and Alert Box
        if (dept) {
          noticeDiv.style.display = "block";
          if (allowMultiple) {
            programHint.innerText = "— Deans can select multiple";
            programHint.className = "text-success fw-normal";
            noticeText.innerHTML = "<strong>Note for Deans:</strong> You can assign multiple programs. <br><span class='text-danger fw-bold'>Important:</span> The very <strong>FIRST</strong> program you select will be permanently set as their primary/mother program in the Faculty system.";
          } else {
            programHint.innerText = "— Limited to 1 program";
            programHint.className = "text-secondary fw-normal";
            noticeText.innerHTML = "<strong>Note:</strong> Regular Faculty and non-Deans can only be assigned to 1 program.";
          }
        } else {
          noticeDiv.style.display = "none";
          programHint.innerText = "";
        }

        // Re-initialize Choices.js safely
        programChoicesInstance = new Choices(mainProgramSelect, {
          removeItemButton: true,
          searchEnabled: true,
          shouldSort: false,
          itemSelectText: '',
          maxItemCount: allowMultiple ? -1 : 1, // -1 means infinite, 1 means single
          placeholderValue: allowMultiple ? 'Click to select Program(s)...' : 'Click to select a Program...'
        });

        // Load programs for the selected college
        const programs = collegePrograms[dept] || [];
        const choicesData = programs.map(p => ({
          value: p,
          label: p,
          selected: p === prefillProgram // Auto-select if they already have one
        }));

        programChoicesInstance.setChoices(choicesData, 'value', 'label', true);
        prefillProgram = null; // Clear it out after loading
      }

      // Listeners for dynamic updates
      mainDeptSelect.addEventListener('change', updateProgramDropdown);
      positionSelect.addEventListener('change', updateProgramDropdown);

      // Toggle Admin Options block
      function toggleAdminOptions() {
        if (roleSelect.value === 'admin' || roleSelect.value === 'superadmin') {
          adminOptions.style.display = 'flex';
          positionSelect.setAttribute('required', 'required');
        } else {
          adminOptions.style.display = 'none';
          positionSelect.removeAttribute('required');
        }
        updateProgramDropdown(); // Refresh UI in case of role change
      }
      roleSelect.addEventListener('change', toggleAdminOptions);

      // --- EDIT BUTTON CLICK TO OPEN MODAL ---
      const editButtons = document.querySelectorAll('.edit-btn');
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const fname = this.getAttribute('data-fname');
          const mname = this.getAttribute('data-mname');
          const lname = this.getAttribute('data-lname');
          const college = this.getAttribute('data-college');
          const program = this.getAttribute('data-program');
          const rank = this.getAttribute('data-rank');
          const status = this.getAttribute('data-status');
          const role = this.getAttribute('data-role');

          const fullName = `${fname} ${mname} ${lname}`.replace(/\s+/g, ' ').trim();

          // Clear Password
          document.getElementById('modal_password').value = '';

          document.getElementById('modal_old_idnumber').value = id;
          document.getElementById('modal_idnumber').value = id;
          document.getElementById('modal_fullname').value = fullName;
          document.getElementById('modal_status').value = status || 'active';
          document.getElementById('modal_rank').value = rank || '';
          roleSelect.value = role;

          // Pre-fill fields based on their current standard info
          positionSelect.value = "";
          if (allcollege.includes(college)) {
            mainDeptSelect.value = college;
            prefillProgram = program;
          } else {
            mainDeptSelect.value = "";
          }

          toggleAdminOptions();
        });
      });

      // Submit Confirmation
      document.getElementById("editFacultyForm").addEventListener("submit", function(e) {
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

      // --- SEARCH & PAGINATION LOGIC ---
      let currentPage = 1;
      let rowsPerPage = 10;

      function updateTable() {
        let filterValue = document.getElementById('customFacultySearch').value.toLowerCase();
        let tableRows = Array.from(document.querySelectorAll('#facultyTable tbody tr'));

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

      document.getElementById('customFacultySearch').addEventListener('keyup', () => {
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

    // Toggle Password Visibility in Modal
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
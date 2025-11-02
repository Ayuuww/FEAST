<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if superadmin is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Check if faculty ID is provided
if (!isset($_GET['id'])) {
  echo "Faculty ID is missing.";
  exit();
}

$faculty_id = $_GET['id'];

// Fetch faculty data
$stmt = $conn->prepare("SELECT * FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();

if (!$faculty && $_SERVER["REQUEST_METHOD"] != "POST") {
  echo "Faculty not found.";
  exit();
}

// --- Fetch all data for dropdowns ---
$positions_result = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL AND position_name != '' ORDER BY position_name ASC");

$rankQuery = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != '' ORDER BY rank_name ASC");
$facultyRanks = [];
while ($row = $rankQuery->fetch_assoc()) {
  $facultyRanks[] = $row['rank_name'];
}

// Fetch departments and programs for dynamic dropdowns
$adds_data_result = $conn->query("
    SELECT DISTINCT department_name, program_name 
    FROM adds 
    WHERE department_name IS NOT NULL AND department_name != '' 
    ORDER BY department_name, program_name ASC
");
$departmentPrograms = [];
while ($row = $adds_data_result->fetch_assoc()) {
  $department = $row['department_name'];
  $program = $row['program_name'];
  if (!isset($departmentPrograms[$department])) {
    $departmentPrograms[$department] = [];
  }
  if ($program && !in_array($program, $departmentPrograms[$department])) {
    $departmentPrograms[$department][] = $program;
  }
}
// --- End Data Fetching ---


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $faculty_id = $_GET['id'];
  $new_status = $_POST['status'];
  $new_role_trigger = $_POST['role']; // This is just the trigger
  $new_faculty_rank = $_POST['faculty_rank'] ?? null;

  // Re-fetch faculty data
  $stmt = $conn->prepare("SELECT * FROM faculty WHERE idnumber = ?");
  $stmt->bind_param("s", $faculty_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $faculty = $result->fetch_assoc();

  if (!$faculty) {
    echo "Faculty not found.";
    exit();
  }

  // --- ✅ REBUILT 'Promote to Admin' Logic ---
  if ($new_role_trigger === 'admin') {

    // Check if already an admin
    $checkAdmin = $conn->prepare("SELECT idnumber FROM admin WHERE idnumber = ?");
    $checkAdmin->bind_param("s", $faculty_id);
    $checkAdmin->execute();
    $adminResult = $checkAdmin->get_result();

    if ($adminResult->num_rows > 0) {
      $_SESSION['msg'] = "This faculty is already an Admin. Their details have been updated instead.";
      // Fall through to the 'else' logic to perform an update
    } else {
      // Not an admin, so let's create them
      $position = $_POST['position'] ?? NULL;

      // ✅ Get Main Dept/Program
      $main_department = $_POST['main_department'] ?? NULL;
      $main_program = $_POST['main_program'] ?? ''; // Default to ''

      // ✅ Get *Additional* (optional) assignments
      $departments = $_POST['departments'] ?? [];
      $programs_by_dept = $_POST['programs'] ?? [];

      // ✅ Use an array to track added entries and prevent duplicates
      $added_assignments = [];

      $conn->begin_transaction();
      try {
        // 1. Insert into 'admin' table
        // 9 columns, 9 VALUES (8 '?' + 1 'admin')
        $insertAdmin = $conn->prepare("
                    INSERT INTO admin (idnumber, first_name, mid_name, last_name, password, position, faculty_rank, role, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'admin', ?)
                ");
        // 8 variables bind to 8 's'
        $insertAdmin->bind_param(
          "ssssssss",
          $faculty['idnumber'],
          $faculty['first_name'],
          $faculty['mid_name'],
          $faculty['last_name'],
          $faculty['password'],
          $position,
          $new_faculty_rank,
          $new_status
        );
        $insertAdmin->execute();

        // 2. UPDATE the existing faculty record with new main dept/program
        $updateFaculty = $conn->prepare("UPDATE faculty SET status = ?, faculty_rank = ?, department = ?, program = ? WHERE idnumber = ?");
        $updateFaculty->bind_param("sssss", $new_status, $new_faculty_rank, $main_department, $main_program, $faculty_id);
        $updateFaculty->execute();


        // 3. Prepare statement for 'admin_departments'
        $stmt_dept = $conn->prepare("INSERT INTO admin_departments (admin_idnumber, department_name, program_name) VALUES (?, ?, ?)");

        // 4. Insert the MAIN department/program assignment first
        $stmt_dept->bind_param("sss", $faculty_id, $main_department, $main_program);
        $stmt_dept->execute();
        $added_assignments[$main_department . "::" . $main_program] = true; // Track it

        // 5. Loop and insert *ADDITIONAL* assignments
        foreach ($departments as $dept_name) {
          if (isset($programs_by_dept[$dept_name]) && !empty($programs_by_dept[$dept_name])) {
            // Case 1: Dept has programs
            foreach ($programs_by_dept[$dept_name] as $prog_name) {
              $key = $dept_name . "::" . $prog_name;
              if (isset($added_assignments[$key])) continue; // Skip duplicate

              $stmt_dept->bind_param("sss", $faculty_id, $dept_name, $prog_name);
              $stmt_dept->execute();
              $added_assignments[$key] = true;
            }
          } else {
            // Case 2: Dept selected, but no programs
            $prog_name_empty = '';
            $key = $dept_name . "::" . $prog_name_empty;
            if (isset($added_assignments[$key])) continue; // Skip duplicate

            $stmt_dept->bind_param("sss", $faculty_id, $dept_name, $prog_name_empty);
            $stmt_dept->execute();
            $added_assignments[$key] = true;
          }
        }

        // All good, commit the changes
        $conn->commit();
        $_SESSION['success_message'] = "Faculty successfully promoted to Admin!";
        header("Location: superadmin-adminlist.php"); // Redirect to admin list
        exit();
      } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        if ($e->getCode() == 1062) {
          $_SESSION['msg'] = "Error: A user with this ID already exists in the admin table.";
        } else {
          $_SESSION['msg'] = "Database Error: " . $e->getMessage();
        }
        header("Location: superadmin-editfaculty.php?id=$faculty_id");
        exit();
      }
    }
  }

  // This 'else' block runs if $new_role_trigger is 'faculty' OR if they were already an admin
  try {
    $stmt = $conn->prepare("UPDATE faculty SET status = ?, faculty_rank = ? WHERE idnumber = ?");
    $stmt->bind_param("sss", $new_status, $new_faculty_rank, $faculty_id);
    $stmt->execute();

    // Also sync to admin table if this faculty is also an admin
    $checkAdmin = $conn->prepare("SELECT idnumber FROM admin WHERE idnumber = ?");
    $checkAdmin->bind_param("s", $faculty_id);
    $checkAdmin->execute();
    $adminResult = $checkAdmin->get_result();

    if ($adminResult->num_rows > 0) {
      $updateAdmin = $conn->prepare("UPDATE admin SET status = ?, faculty_rank = ? WHERE idnumber = ?");
      $updateAdmin->bind_param("sss", $new_status, $new_faculty_rank, $faculty_id);
      $updateAdmin->execute();
    }

    $success = "Faculty updated successfully!";

    // Re-fetch updated faculty data
    $stmt = $conn->prepare("SELECT * FROM faculty WHERE idnumber = ?");
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculty = $result->fetch_assoc();
  } catch (mysqli_sql_exception $e) {
    $_SESSION['msg'] = "Database Error: " . $e->getMessage();
    header("Location: superadmin-editfaculty.php?id=$faculty_id");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="assets/js/choices.min.css" />
</head>

<body>

  <?php include 'superadmin-header.php' ?>
  <?php include 'superadmin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Faculty Status</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="superadmin-facultylist.php">Faculty</a></li>
          <li class="breadcrumb-item">List</li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Faculty Information</h5>

              <?php if (isset($success)): ?>
                <script>
                  Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: <?= json_encode($success) ?>,
                    timer: 2000,
                    showConfirmButton: false
                  });
                </script>
              <?php endif; ?>
              <?php if (isset($_SESSION['msg'])): ?>
                <script>
                  Swal.fire({
                    icon: 'info',
                    title: 'Notice',
                    text: <?= json_encode($_SESSION['msg']) ?>,
                    confirmButtonColor: '#3085d6'
                  });
                </script>
                <?php unset($_SESSION['msg']); ?>
              <?php endif; ?>
              <?php if ($faculty): ?>
                <form method="POST" class="row g-3">
                  <div class="col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['mid_name'] . ' ' . $faculty['last_name']); ?>" disabled>
                      <label class="form-label">Full Name</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($faculty['idnumber']); ?>" disabled>
                      <label class="form-label">ID Number</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($faculty['department']); ?>" disabled>
                      <label class="form-label">Current Main Department</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($faculty['program']); ?>" disabled>
                      <label class="form-label">Current Main Program</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <select name="role" class="form-select" required>
                        <option value="faculty" <?php if ($faculty['role'] == 'faculty') echo 'selected'; ?>>Faculty</option>
                        <option value="admin">Promote to Admin</option>
                      </select>
                      <label class="form-label">Role</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class=" form-floating">
                      <select name="status" class="form-select" required>
                        <option value="active" <?php if ($faculty['status'] == 'active') echo 'selected'; ?>>Active</option>
                        <option value="inactive" <?php if ($faculty['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                      </select>
                      <label class="form-label">Current Status</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($faculty['faculty_rank'] ?? 'Not Set'); ?>" disabled>
                      <label class="form-label">Current Faculty Rank</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <select class="form-select" name="faculty_rank">
                        <option value="" <?php if (empty($faculty['faculty_rank'])) echo 'selected'; ?>>-- Select Rank --</option>
                        <?php foreach ($facultyRanks as $rank): ?>
                          <option value="<?= htmlspecialchars($rank) ?>" <?= ($faculty['faculty_rank'] ?? '') === $rank ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rank) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <label>Update Faculty Rank</label>
                    </div>
                  </div>

                  <div id="admin-options" style="display: none;" class="row g-3">
                    <hr>
                    <h6 class="text-primary fw-bold">Admin Promotion Details</h6>

                    <div class="col-md-12">
                      <div class="form-floating">
                        <select class="form-select" name="position" id="position">
                          <option value="" disabled selected>-- Select Position --</option>
                          <?php $positions_result->data_seek(0); // Reset result pointer 
                          ?>
                          <?php while ($row = $positions_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['position_name']) ?>"><?= htmlspecialchars($row['position_name']) ?></option>
                          <?php endwhile; ?>
                        </select>
                        <label for="position">Position</label>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-floating">
                        <select class="form-select" name="main_department" id="main_department">
                          <option value="" disabled selected>-- Select Main Department --</option>
                        </select>
                        <label>New Main Department</label>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-floating">
                        <select class="form-select" name="main_program" id="main_program">
                          <option value="" selected>-- Select Main Program (if any) --</option>
                        </select>
                        <label>New Main Program</label>
                      </div>
                    </div>

                    <div class="col-12" id="department_div">
                      <label for="department" class="form-label fw-bold">Assign *Additional* Department(s) (Optional)</label>
                      <select name="departments[]" id="department" multiple></select>
                    </div>

                    <div class="col-12 mt-3" id="program_container"></div>
                    <hr>
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-success">Update Faculty</button>
                    <a href="superadmin-facultylist.php" class="btn btn-secondary">Back</a>
                  </div>

                </form>
              <?php else: ?>
                <div class="alert alert-info">Faculty not found or may have been moved.</div>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/choices.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // Pass PHP array to JavaScript
    const departmentPrograms = <?= json_encode($departmentPrograms) ?>;
    const allDepartments = Object.keys(departmentPrograms);

    document.addEventListener('DOMContentLoaded', function() {
      const roleSelect = document.querySelector('select[name="role"]');
      const adminOptions = document.getElementById('admin-options');

      // --- Elements for Admin Options ---
      const adminPosition = document.getElementById('position');
      const mainDeptSelect = document.getElementById('main_department');
      const mainProgramSelect = document.getElementById('main_program');
      const departmentElement = document.getElementById('department');
      const programContainer = document.getElementById('program_container');

      // --- Populate Main Department Dropdown ---
      allDepartments.forEach(dept => {
        const opt = new Option(dept, dept);
        mainDeptSelect.appendChild(opt.cloneNode(true));
      });

      // --- When main department changes, load its programs ---
      mainDeptSelect.addEventListener('change', function() {
        const dept = this.value;
        const programs = departmentPrograms[dept] || [];
        mainProgramSelect.innerHTML = `<option value="" selected>-- Select Main Program (if any) --</option>`;
        programs.forEach(p => {
          const opt = new Option(p, p);
          mainProgramSelect.appendChild(opt);
        });
      });

      // --- Initialize Choices.js for *additional* departments ---
      const departmentChoices = new Choices(departmentElement, {
        removeItemButton: true,
        placeholderValue: 'Select additional department(s)...',
        searchPlaceholderValue: 'Search departments...',
      });

      // Populate *additional* department choices
      departmentChoices.setChoices(
        allDepartments.map(d => ({
          value: d,
          label: d
        })),
        'value', 'label', true
      );

      // Handle *Additional* Department selection to show programs
      departmentElement.addEventListener('change', function() {
        const selectedDepartments = Array.from(departmentElement.selectedOptions).map(opt => opt.value);
        programContainer.innerHTML = ''; // clear previous

        selectedDepartments.forEach(dep => {
          const programs = departmentPrograms[dep] || [];
          if (programs.length > 0) {
            const div = document.createElement('div');
            div.classList.add('mt-3', 'p-3', 'border', 'rounded');
            div.innerHTML = `
                            <label class="form-label fw-bold text-primary">Programs under ${dep}</label>
                            <select name="programs[${dep}][]" id="program_${dep.replace(/\s+/g, '_')}" multiple></select>
                        `;
            programContainer.appendChild(div);

            const programSelect = div.querySelector('select');
            const programChoices = new Choices(programSelect, {
              removeItemButton: true,
              placeholderValue: `Select program(s) for ${dep}...`,
            });

            programChoices.setChoices(
              programs.map(p => ({
                value: p,
                label: p
              })),
              'value', 'label', true
            );
          }
        });
      });

      // --- Function to toggle Admin Options ---
      function toggleAdminOptions() {
        if (roleSelect.value === 'admin') {
          adminOptions.style.display = 'block';
          adminPosition.setAttribute('required', 'required');
          mainDeptSelect.setAttribute('required', 'required');
          // mainProgramSelect is optional
          // departmentElement (additional) is optional
        } else {
          adminOptions.style.display = 'none';
          adminPosition.removeAttribute('required');
          mainDeptSelect.removeAttribute('required');
        }
      }

      // --- Event Listeners ---
      roleSelect.addEventListener('change', toggleAdminOptions);
      // Run on page load
      toggleAdminOptions();
    });
  </script>

</body>

</html>
<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a registrar
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// ✅ Fetch dropdown data
$positions_result = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL AND position_name != '' ORDER BY position_name ASC");
$ranks_result = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != '' ORDER BY rank_name ASC");

// ✅ Fetch departments and programs properly (FIXED SPACES)
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <?php include 'register-header.php' ?>
  <?php include 'register-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Admin Creation</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Admin</li>
          <li class="breadcrumb-item active">Add New Admin</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">

              <?php if (isset($_SESSION['success_message'])): ?>
                <script>
                  Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= htmlspecialchars($_SESSION['success_message']) ?>',
                    confirmButtonColor: '#28a745'
                  });
                </script>
                <?php unset($_SESSION['success_message']); ?>
              <?php endif; ?>

              <?php if (isset($_SESSION['error_message'])): ?>
                <script>
                  Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?= htmlspecialchars($_SESSION['error_message']) ?>',
                    confirmButtonColor: '#dc3545'
                  });
                </script>
                <?php unset($_SESSION['error_message']); ?>
              <?php endif; ?>

              <h5 class="card-title text-center">Create New Admin</h5>
              <form class="row g-3" method="post" action="admincreation.php">

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="idnumber" class="form-control" id="idnumber" placeholder="ID Number" pattern="^[0-9\-]+$" required>
                    <label for="idnumber">ID Number</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                    <label>First Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="mid_name" class="form-control" placeholder="Middle Name" required>
                    <label>Middle Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    <label>Last Name</label>
                  </div>
                </div>

                <input type="hidden" name="password" value="ILOVEDMMMSU">

                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" name="position" required>
                      <option value="" disabled selected>-- Select Position --</option>
                      <?php while ($row = $positions_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['position_name']) ?>"><?= htmlspecialchars($row['position_name']) ?></option>
                      <?php endwhile; ?>
                    </select>
                    <label>Position</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" name="faculty_rank" required>
                      <option value="" disabled selected>-- Select Faculty Rank --</option>
                      <?php while ($rank = $ranks_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($rank['rank_name']) ?>"><?= htmlspecialchars($rank['rank_name']) ?></option>
                      <?php endwhile; ?>
                    </select>
                    <label>Faculty Rank</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" name="main_department" id="main_department" required>
                      <option value="" disabled selected>-- Select Main College --</option>
                    </select>
                    <label>Main College</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" name="main_program" id="main_program">
                      <option value="" selected>-- Select Main Program (if any) --</option>
                    </select>
                    <label>Main Program</label>
                  </div>
                </div>

                <div class="col-12">
                  <label class="form-label fw-bold">Assign *Additional* College(s) (Optional)</label>
                  <select name="departments[]" id="department" multiple></select>
                </div>

                <div class="col-12 mt-3" id="program_container"></div>

                <div class="col-4 offset-4 mt-4">
                  <button class="btn btn-success w-100" name="submit" type="submit">Create Admin Account</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php' ?>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/choices.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    const departmentPrograms = <?= json_encode($departmentPrograms) ?>;

    document.addEventListener('DOMContentLoaded', function() {
      const departmentSelect = document.getElementById('department');
      const mainDeptSelect = document.getElementById('main_department');
      const mainProgramSelect = document.getElementById('main_program');
      const programContainer = document.getElementById('program_container');

      // Populate all departments (for main + multi-select)
      const allDepartments = Object.keys(departmentPrograms);
      allDepartments.forEach(dept => {
        const opt = new Option(dept, dept);
        mainDeptSelect.appendChild(opt);
      });

      // ✅ When main department changes, load its programs
      mainDeptSelect.addEventListener('change', function() {
        const dept = this.value;
        const programs = departmentPrograms[dept] || [];
        // ✅ FIX: Updated placeholder
        mainProgramSelect.innerHTML = `<option value="" selected>-- Select Main Program (if any) --</option>`;
        programs.forEach(p => {
          const opt = new Option(p, p);
          mainProgramSelect.appendChild(opt);
        });
      });

      // ✅ Choices.js for multiple department selection
      const deptChoices = new Choices(departmentSelect, {
        removeItemButton: true,
        placeholderValue: 'Select additional departments...'
      });

      deptChoices.setChoices(
        allDepartments.map(d => ({
          value: d,
          label: d
        })),
        'value',
        'label',
        true
      );

      // ✅ Dynamic programs under selected departments
      departmentSelect.addEventListener('change', function() {
        programContainer.innerHTML = '';
        const selectedDepartments = Array.from(departmentSelect.selectedOptions).map(opt => opt.value);

        selectedDepartments.forEach(dept => {
          const programs = departmentPrograms[dept] || [];
          if (programs.length > 0) {
            const div = document.createElement('div');
            div.classList.add('mt-3', 'p-3', 'border', 'rounded');
            div.innerHTML = `
                        <label class="form-label fw-bold text-primary">Programs under ${dept}</label>
                        <select name="programs[${dept}][]" multiple></select>
                    `;
            programContainer.appendChild(div);

            const select = div.querySelector('select');
            const programChoices = new Choices(select, {
              removeItemButton: true,
              placeholderValue: `Select program(s) for ${dept}...`
            });

            programChoices.setChoices(
              programs.map(p => ({
                value: p,
                label: p
              })),
              'value',
              'label',
              true
            );
          }
        });
      });
    });
  </script>
</body>

</html>
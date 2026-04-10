<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// ✅ Fetch dropdown data
$positions_result = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL AND position_name != '' ORDER BY position_name ASC");
$ranks_result = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != '' ORDER BY rank_name ASC");

// ✅ Fetch college and programs
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <link rel="stylesheet" href="assets/css/choices.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    /* Make Choices.js flush with Bootstrap styling */
    .choices[data-type*="select-multiple"] .choices__inner {
      border-radius: 0.375rem;
      border: 1px solid #dee2e6;
      background-color: #fff;
      padding: 0.375rem 0.75rem;
      min-height: calc(3.5rem + 2px);
    }

    .choices__list--multiple .choices__item {
      background-color: #198754;
      /* Bootstrap Success Green */
      border: 1px solid #146c43;
      border-radius: 4px;
    }
  </style>
</head>

<body>
  <?php include 'superadmin-header.php' ?>
  <?php include 'superadmin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Admin Creation</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Admin</li>
          <li class="breadcrumb-item active">Add New Admin</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-7">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">

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

              <h4 class="card-title text-center mb-4">Create New Admin Account</h4>

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
                    <select class="form-select" name="position" id="position" required>
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
                      <div id="notice_text" style="font-size: 0.9rem; color: #333;">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 mt-4 text-center">
                  <button class="btn btn-success px-5 py-2 w-100 fw-bold shadow-sm" name="submit" type="submit">Create Admin Account</button>
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
    const collegePrograms = <?= json_encode($collegePrograms) ?>;

    document.addEventListener('DOMContentLoaded', function() {
      const positionSelect = document.getElementById('position');
      const mainDeptSelect = document.getElementById('main_college');
      const mainProgramSelect = document.getElementById('main_program');
      const programHint = document.getElementById('program_hint');
      const noticeDiv = document.getElementById('primary_program_notice');
      const noticeText = document.getElementById('notice_text');

      let programChoicesInstance = null;

      // 1. Populate College Dropdown
      const allcollege = Object.keys(collegePrograms);
      allcollege.forEach(dept => {
        mainDeptSelect.add(new Option(dept, dept));
      });

      // 2. Initialize dynamic Program Dropdown based on Role
      function updateProgramDropdown() {
        const dept = mainDeptSelect.value;
        const position = positionSelect.value || '';
        const isDean = position.toLowerCase().includes('dean');

        if (programChoicesInstance) {
          programChoicesInstance.destroy();
        }

        // --- Update UI Hint and Alert Box ---
        if (dept) {
          noticeDiv.style.display = "block";
          if (isDean) {
            programHint.innerText = "— Deans can select multiple";
            programHint.className = "text-success fw-normal";
            noticeText.innerHTML = "<strong>Note for Deans:</strong> You can assign multiple programs. <br><span class='text-danger fw-bold'>Important:</span> The very <strong>FIRST</strong> program you select will be set as their primary/mother program in the Faculty system.";
          } else {
            programHint.innerText = "— Limited to 1 program";
            programHint.className = "text-secondary fw-normal";
            noticeText.innerHTML = "<strong>Note:</strong> Non-Deans can only be assigned to 1 program. This program will be directly linked to their Faculty account.";
          }
        } else {
          noticeDiv.style.display = "none";
          programHint.innerText = "";
        }

        // --- Safely Re-initialize Choices.js ---
        const selectEl = document.getElementById('main_program');
        programChoicesInstance = new Choices(selectEl, {
          removeItemButton: true,
          searchEnabled: true,
          shouldSort: false,
          maxItemCount: isDean ? -1 : 1, // -1 means infinite, 1 means single
          placeholderValue: isDean ? 'Click to select Program(s)...' : 'Click to select a Program...'
        });

        // Load programs for the selected college
        const programs = collegePrograms[dept] || [];
        const choicesData = programs.map(p => ({
          value: p,
          label: p
        }));

        programChoicesInstance.setChoices(choicesData, 'value', 'label', true);
      }

      // Listeners
      mainDeptSelect.addEventListener('change', updateProgramDropdown);
      positionSelect.addEventListener('change', updateProgramDropdown);

      // Force UI to initialize nicely on page load
      updateProgramDropdown();
    });
  </script>
</body>

</html>
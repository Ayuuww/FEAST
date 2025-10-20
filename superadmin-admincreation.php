<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// --- Fetch all data for form dropdowns once ---
$positions_result = $conn->query("SELECT position_name FROM adds WHERE position_name IS NOT NULL AND position_name != '' ORDER BY position_name ASC");
$departments_result = $conn->query("SELECT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != '' ORDER BY department_name ASC");
$ranks_result = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != '' ORDER BY rank_name ASC");
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <?php include 'head.php' ?>
</head>

<body>
  <?php include 'superadmin-header.php' ?>
  <?php include 'superadmin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Admin</h1>
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
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title text-center">Create New Admin</h5>
              <form class="row g-3 needs-validation" method="post" action="admincreation.php">

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="idnumber" class="form-control" id="idnumber" placeholder="ID Number" pattern="^[0-9\-]+$" required>
                    <label for="idnumber" class="form-label">ID Number</label>
                    <div class="invalid-feedback">Please enter a valid ID number (numbers and hyphens only).</div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="first_name" class="form-control" id="first_name" placeholder="First Name" required>
                    <label for="first_name" class="form-label">First Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="mid_name" class="form-control" id="mid_name" placeholder="Middle Name" required>
                    <label for="mid_name" class="form-label">Middle Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Last Name" required>
                    <label for="last_name" class="form-label">Last Name</label>
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
                    <label for="position">Position</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" name="is_faculty" id="is_faculty" required>
                      <option value="" disabled selected>Is this admin also a faculty?</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                    <label for="is_faculty">Also a Faculty?</label>
                  </div>
                </div>

                <div class="col-6" id="department_div" style="display:none;">
                  <label for="department" class="form-label fw-bold">Assign Department(s)</label>
                  <select name="departments[]" id="department" multiple>
                    <?php while ($row = $departments_result->fetch_assoc()): ?>
                      <option value="<?= htmlspecialchars($row['department_name']) ?>"><?= htmlspecialchars($row['department_name']) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <div class="col-6" id="faculty_rank_div" style="display:none;">
                  <label for="faculty_rank" class="form-label fw-bold">Faculty Rank</label>
                  <select class="form-select" name="faculty_rank" id="faculty_rank">
                    <option value="" disabled selected>-- Select Faculty Rank --</option>
                    <?php while ($rank = $ranks_result->fetch_assoc()): ?>
                      <option value="<?= htmlspecialchars($rank['rank_name']) ?>"><?= htmlspecialchars($rank['rank_name']) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>


                <div class="col-4 offset-4 mt-4">
                  <button class="btn btn-success w-100" name="submit" type="submit">Create Account</button>
                </div>
              </form>
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
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Choices.js for the department dropdown
      const departmentElement = document.getElementById('department');
      const choices = new Choices(departmentElement, {
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Click to select departments...',
        searchPlaceholderValue: 'Search departments...',
      });

      // Logic to show/hide faculty-specific fields
      const facultySelect = document.getElementById('is_faculty');
      const departmentDiv = document.getElementById('department_div');
      const facultyRankDiv = document.getElementById('faculty_rank_div');
      const facultyRankSelect = document.getElementById('faculty_rank');

      facultySelect.addEventListener('change', function() {
        if (this.value === 'Yes') {
          departmentDiv.style.display = 'block';
          facultyRankDiv.style.display = 'block';
          facultyRankSelect.setAttribute('required', 'required');
        } else {
          departmentDiv.style.display = 'none';
          facultyRankDiv.style.display = 'none';
          facultyRankSelect.removeAttribute('required');
        }
      });

      // SweetAlert for messages
      <?php if (isset($_SESSION['msg'])): ?>
        Swal.fire({
          icon: '<?= $_SESSION['msg_type'] ?? 'info' ?>',
          title: <?= json_encode($_SESSION['msg']) ?>,
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true
        });
        <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
      <?php endif; ?>
    });
  </script>
</body>

</html>
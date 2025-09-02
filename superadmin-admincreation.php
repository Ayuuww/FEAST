<?php

session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Fetch super admin details
$query = "SELECT * FROM superadmin";



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Admin Creation </title>
  <?php include 'header.php' ?>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard">Home</a></li>
          <li class="breadcrumb-item ">Admin</li>
          <li class="breadcrumb-item active">Add New Admin</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <!-- Admin Creation Section -->
    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">

              <?php if (isset($_SESSION['msg'])): ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                      icon: '<?= $_SESSION['msg_type'] === 'success' ? 'success' : 'info' ?>',
                      title: '<?= htmlspecialchars($_SESSION['msg']) ?>',
                      showConfirmButton: false,
                      timer: 1500,
                      timerProgressBar: true
                    });
                  });
                </script>
                <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
              <?php endif; ?>

              <h5 class="card-title text-center">Create New Admin</h5>
              <form class="row g-3 needs-validation" novalidate method="post" action="admincreation.php">

                <!-- ID Number -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="idnumber" class="form-control" id="idnumber" placeholder="ID Number" pattern="^[0-9\-]+$" required>
                    <label for="idnumber" class="form-label">ID Number</label>
                    <div class="invalid-feedback">Please, enter a valid ID number (only numbers and hyphens are allowed)!</div>
                  </div>
                </div>

                <!-- First Name -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                    <label class="form-label">First Name</label>
                  </div>
                </div>

                <!-- Middle Name -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="mid_name" class="form-control" placeholder="Middle Name" required>
                    <label class="form-label">Middle Name</label>
                  </div>
                </div>

                <!-- Last Name -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    <label class="form-label">Last Name</label>
                  </div>
                </div>

                <!-- Password -->
                <input type="text" name="password" value="ILOVEDMMMSU" class="form-control" placeholder="ILOVEDMMMSU" id="yourPassword" hidden>


                <!-- Confirm Password
                      <div class="col-md-6">
                          <div class="form-floating">
                              <input type="password" name="password" class="form-control" placeholder="Confirm Password" id="conpass" onkeyup='checkpass();' required>
                              <div class="invalid-feedback" id="mess">Password do not match</div>
                              <label class="form-label">Confirm Password</label>
                          </div>
                      </div> -->

                <!-- Position -->
                <div class="col-md-4">
                  <div class="form-floating">
                    <select class="form-select" name="position" required>
                      <option value="" disabled selected>-- Select Position --</option>
                      <?php
                      $positions = mysqli_query($conn, "SELECT position_name FROM adds WHERE position_name IS NOT NULL AND position_name != ''");
                      while ($row = mysqli_fetch_assoc($positions)) {
                        echo '<option value="' . htmlspecialchars($row['position_name']) . '">' . htmlspecialchars($row['position_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label for="position">Position</label>
                  </div>
                </div>

                <!-- Department -->
                <div class="col-md-4">
                  <div class="form-floating">
                    <select class="form-select" name="department" required>
                      <option value="" disabled selected>Select Department</option>
                      <?php
                      $departments = mysqli_query($conn, "SELECT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != ''");
                      while ($row = mysqli_fetch_assoc($departments)) {
                        echo '<option value="' . htmlspecialchars($row['department_name']) . '">' . htmlspecialchars($row['department_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label for="department">Department</label>
                  </div>
                </div>

                <!-- Faculty Rank (hidden by default, shown if faculty=yes) -->
                <div class="col-md-4 " id="">
                  <div class="form-floating">
                    <select class="form-select" name="faculty_rank" id="">
                      <option value="" selected disabled>-- Select Rank --</option>
                      <?php
                      $rank_query = mysqli_query($conn, "SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != ''");
                      while ($rank = mysqli_fetch_assoc($rank_query)) {
                        echo '<option value="' . $rank['rank_name'] . '">' . $rank['rank_name'] . '</option>';
                      }
                      ?>
                    </select>
                    <label for="faculty_rank">Faculty Rank</label>
                  </div>
                </div>

                <!-- Submit -->
                <div class="col-4 offset-4">
                  <button class="btn btn-success w-100" name="submit" id="create" type="submit">Create Account</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Admin Creation Section -->

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php' ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script data-cfasync="false" src="assets/js/email-decode.min.js"></script>
  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- <script>
      document.addEventListener('DOMContentLoaded', function () {
        const facultyDropdown = document.getElementById('faculty');
        const facultyRankContainer = document.getElementById('facultyRankContainer');

        facultyDropdown.addEventListener('change', function () {
          if (facultyDropdown.value.toLowerCase() === 'yes') {
            facultyRankContainer.style.display = 'block';
          } else {
            facultyRankContainer.style.display = 'none';
            document.getElementById('faculty_rank').selectedIndex = 0; // reset selection
          }
        });
      });
    </script> -->

  <?php if (isset($_SESSION['msg'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: '<?= $_SESSION['msg_type'] === 'success' ? 'success' : 'info' ?>',
          title: <?= json_encode($_SESSION['msg']) ?>,
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true
        });
      });
    </script>
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
  <?php endif; ?>

</body>

</html>
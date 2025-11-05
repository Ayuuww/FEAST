<?php

session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a registrar
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// Create a new super admin account


?>

<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

</head>

<body>

  <?php include 'register-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'register-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Super Admin Creation</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Super Admin</li>
          <li class="breadcrumb-item active">Add New Super Admin</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <!-- Super Admin Creation Section -->
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

              <h5 class="card-title text-center">Create New Super Admin</h5>
              <form class="row g-3 needs-validation" novalidate method="post" action="registercreation.php">

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="idnumber" class="form-control" id="idnumber" placeholder="ID Number" pattern="^[0-9\-]+$" required>
                    <label for="idnumber" class="form-label">ID Number</label>
                    <div class="invalid-feedback">Please, enter a valid ID number (only numbers and hyphens are allowed)!</div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                    <label class="form-label">First Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="mid_name" class="form-control" placeholder="Middle Name" required>
                    <label class="form-label">Middle Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    <label class="form-label">Last Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" name="position" required>
                      <option value="" disabled selected>-- Select Position --</option>
                      <?php
                      $positions = mysqli_query($conn, "SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL AND position_name != ''");
                      while ($row = mysqli_fetch_assoc($positions)) {
                        echo '<option value="' . htmlspecialchars($row['position_name']) . '">' . htmlspecialchars($row['position_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label for="position">Position</label>
                  </div>
                </div>


                <div class="col-md-6" id="department_div">
                  <div class="form-floating">
                    <select class="form-select" name="department" id="department">
                      <option value="" disabled selected>Select Department</option>
                      <?php
                      $departments = mysqli_query($conn, "SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != ''");
                      while ($row = mysqli_fetch_assoc($departments)) {
                        echo '<option value="' . htmlspecialchars($row['department_name']) . '">' . htmlspecialchars($row['department_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label for="department">Department</label>
                  </div>
                </div>


                <div class="col-md-6" id="program_div">
                  <div class="form-floating">
                    <select class="form-select" name="program" id="program">
                      <option value="" disabled selected>Select Program</option>
                    </select>
                    <label for="program">Program</label>
                  </div>
                </div>


                <div class="col-md-6" id="faculty_rank_div">
                  <div class="form-floating">
                    <select class="form-select" name="faculty_rank" id="faculty_rank">
                      <option value="" disabled selected>-- Select Rank --</option>
                      <?php
                      $rank_query = mysqli_query($conn, "SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != ''");
                      while ($rank = mysqli_fetch_assoc($rank_query)) {
                        echo '<option value="' . htmlspecialchars($rank['rank_name']) . '">' . htmlspecialchars($rank['rank_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label for="faculty_rank">Faculty Rank</label>
                  </div>
                </div>

                <input type="hidden" name="password" value="ILOVEDMMMSU">

                <div class="col-4 offset-4">
                  <button class="btn btn-success w-100" name="submit" type="submit">Create Account</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Super Admin Creation Section -->

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php' ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
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

  <?php if (isset($_SESSION['msg'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: <?= json_encode($_SESSION['msg_type'] ?? 'info') ?>,
          title: <?= json_encode($_SESSION['msg']) ?>,
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true
        });
      });
    </script>
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
  <?php endif; ?>

  <script src="jquery/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#department').on('change', function() {
        var department = $(this).val();
        if (department) {
          $.ajax({
            type: 'POST',
            url: 'fetch_programs.php',
            data: {
              department: department
            },
            success: function(html) {
              $('#program').html(html);
            }
          });
        } else {
          $('#program').html('<option value="" disabled selected>Select Program</option>');
        }
      });
    });
  </script>

</body>

</html>
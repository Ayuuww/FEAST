<?php

session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Create a new super admin account



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / SuperAdmin Creation</title>
  <?php include 'header.php' ?>
</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Super Admin Creation</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Super Admin</li>
          <li class="breadcrumb-item active">Add New Super Admin</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <!-- Super Admin Creation Section -->
    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6 ">
          <div class="card">
            <div class="card-body ">

              <h5 class="card-title text-center">Create New Super Admin</h5>
              <form class="row g-3 needs-validation" novalidate method="post" action="superadmincreation.php">

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
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="password" name="pass" class="form-control" placeholder="Password" id="password" minlength="8" required>
                    <label class="form-label">Password</label>
                    <div class="invalid-feedback">Password must be at least 8 characters!</div>
                  </div>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="password" name="password" class="form-control" placeholder="Confirm Password" id="conpass" onkeyup='checkpass();' required>
                    <div class="invalid-feedback" id="mess">Password do not match</div>
                    <label class="form-label">Confirm Password</label>
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

  <script>
    var checkpass = function() {

      if (document.getElementById('password').value == document.getElementById('conpass').value) {
        document.getElementById('mess').style.display = 'none';
        document.getElementById('conpass').style.borderColor = 'green';

      } else {
        document.getElementById('mess').style.display = 'block';
        document.getElementById('conpass').style.borderColor = 'red';
      }

    }
  </script>

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


</body>

</html>
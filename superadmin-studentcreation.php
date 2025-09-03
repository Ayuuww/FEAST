<?php

session_start();
include 'conn/conn.php'; // Connection to the database

$departments = mysqli_query($conn, "SELECT id, department_name FROM adds WHERE department_name IS NOT NULL AND department_name != ''");
$sections = mysqli_query($conn, "SELECT id, section_name FROM adds WHERE section_name IS NOT NULL AND section_name != ''");

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
        
  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->
   
</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Student</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard">Home</a></li>
          <li class="breadcrumb-item ">Student</li>
          <li class="breadcrumb-item active">Add New Student</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->


    <!-- Admin Creation Section -->
    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title text-center">Create New Student</h5>
              <form class="row g-3 needs-validation" novalidate method="post" action="studentcreation.php">

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
                <input type="hidden" name="password" value="ILOVEDMMMSU">

                <!-- Department -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <select name="department" class="form-select" required>
                      <option disabled selected>Select Department</option>
                      <?php
                      $result = mysqli_query($conn, "SELECT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != ''");
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . htmlspecialchars($row['department_name']) . '">' . htmlspecialchars($row['department_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label for="department">Department</label>
                  </div>
                </div>


                <!-- Section -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <select name="section" class="form-select" required>
                      <option disabled selected>Select Section</option>
                      <?php
                      $result = mysqli_query($conn, "SELECT section_name FROM adds WHERE section_name IS NOT NULL AND section_name != ''");
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . htmlspecialchars($row['section_name']) . '">' . htmlspecialchars($row['section_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label for="section">Section</label>
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
  <?php include 'footer.php'?>
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
  <!--  -->

  <?php if (isset($_SESSION['msg'])): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          icon: '<?= $_SESSION['msg_type'] === 'success' ? 'success' : ($_SESSION['msg_type'] === 'danger' ? 'error' : ($_SESSION['msg_type'] === 'warning' ? 'warning' : 'info')) ?>',
          title: '<?= addslashes($_SESSION['msg']) ?>',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
        });
      });
    </script>
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
  <?php endif; ?>

</body>

</html>
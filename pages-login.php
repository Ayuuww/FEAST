<?php
session_start();
include 'conn/conn.php';// Connection to the database

// Display messaeges if set
if (isset($_SESSION['msg'])) {
    echo "<script>alert('" . $_SESSION['msg'] . "');</script>";
    unset($_SESSION['msg']);
  }


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST - Login</title>
  <?php include 'header.php'?>

  <style>
    @media (max-width: 576px) {
      .card {
        padding: 1rem !important;
      }
      .card-body {
        padding: 1.5rem !important;
      }
    }
  </style>


</head>

<body>

  <main>
    <!-- Main Section -->
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
    <div class="container-fluid">
      <div class="row h-100 flex-column flex-md-row">
        
        <!-- Left side: Logo  -->
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center bg-light text-center">
          <img src="pics/DMMMSUlogosignup.png" alt="Logo" class="logo-img mb-4">
          <h4 class="fst-italic"><strong>FEAST DMMMSU-NLUC</strong></h4>
        </div>
        
        <!-- Right side: Login form -->
        <div class="col-md-6 d-flex flex-column justify-content-center align-items-center bg-light">
          <div class="w-100 px-3 px-sm-5 px-md-0" style="max-width: 400px; width: 100%;">


            <div class="card shadow-lg">
              <div class="card-body">
                <h5 class="card-title text-center pb-0 fs-4">Login Your Account</h5>

                <form class="row g-3 needs-validation" novalidate method="post" action="login.php">

                  <!-- ID Number -->
                  <div class="col-12">
                    <div class="form-floating">
                      <input type="text" name="idnumber" class="form-control" placeholder="ID Number" id="idnumber" pattern="^[0-9\-]+$" required>
                      <div class="invalid-feedback">Please, enter a valid ID number (only numbers and hyphens are allowed)!</div>
                      <label for="floatingID" >ID Number</label>
                    </div>
                  </div>

                  
                  <!-- Password -->
                  <div class="col-12 position-relative">
                    <div class="form-floating">
                      <input type="password" name="password" class="form-control" placeholder="Password" id="password" required>
                      <label for="floatingPassword">Password</label>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <!-- Show Password Toggle -->
                    <button type="button"
                            class="position-absolute top-50 end-0 translate-middle-y me-2"
                            style="border: none; background: transparent; z-index: 2;"
                            onclick="togglePassword()" tabindex="-1">
                      <i id="eyeIcon" class="bi bi-eye-slash fs-5"></i>
                    </button>
                  </div>


                  <!-- Remember Me -->
                   <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                      </div>
                    </div>

                  <!-- Login -->
                  <div class="col-12">
                    <button class="btn btn-success w-100" name="login">Login</button>
                  </div>

                  <!-- <div class="col-12 text-center">
                    <p class="small">Don't have account? <a href="pages-register.php">Create an account</a></p>
                  </div> -->

                </form>
              </div>
            </div>

          </div>
        </div>

        
      </div>
    </div>
    </section><!-- End Registration Section -->
</main>


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
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      const icon = document.getElementById("eyeIcon");

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      } else {
        passwordInput.type = "password";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      }
    }
  </script>


</body>

</html>

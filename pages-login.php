<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Display messaeges if set
// if (isset($_SESSION['msg'])) {
//     echo "<script>alert('" . $_SESSION['msg'] . "');</script>";
//     unset($_SESSION['msg']);
//   }


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST - Login</title>
  <?php include 'header.php' ?>

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
    <section class="section login min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
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

                  <?php if (isset($_SESSION['msg'])): ?>
                    <script>
                      document.addEventListener("DOMContentLoaded", function() {
                        let message = <?= json_encode($_SESSION['msg']) ?>;
                        let icon = 'error';
                        let title = 'Login Failed';

                        // Customize based on message content
                        if (message.toLowerCase().includes('inactive')) {
                          title = 'Account Inactive';
                        }

                        Swal.fire({
                          icon: icon,
                          title: title,
                          text: message,
                          confirmButtonColor: '#3085d6'
                        });
                      });
                    </script>
                    <?php
                    unset($_SESSION['msg']);
                    unset($_SESSION['login_failed']);
                    ?>
                  <?php endif; ?>

                  <form class="row g-3 needs-validation" novalidate method="post" action="login.php">

                    <!-- ID Number -->
                    <div class="col-md-12">
                      <div class="form-floating">
                        <input type="text" name="idnumber" class="form-control" id="idnumber"
                          value="<?= (isset($_COOKIE['remember_idnumber']) && !isset($_SESSION['login_failed'])) ? $_COOKIE['remember_idnumber'] : '' ?>"
                          placeholder="ID Number" pattern="^[0-9\-]+$" required>
                        <div class="invalid-feedback">Please, enter a valid ID number (only numbers and hyphens are allowed)!</div>
                        <label for="floatingID">ID Number</label>
                      </div>
                    </div>

                    <!-- Password -->
                    <div class="col-md-12 position-relative">
                      <div class="form-floating">
                        <input type="password" name="password" class="form-control" id="password"
                          value="<?= (isset($_COOKIE['remember_password']) && !isset($_SESSION['login_failed'])) ? $_COOKIE['remember_password'] : '' ?>"
                          <?= (isset($_SESSION['login_failed'])) ? '' : '' ?>
                          placeholder="Password" required>
                        <label for="floatingPassword">Password</label>
                        <div class="invalid-feedback">Please enter your password!</div>
                      </div>

                      <!-- Remember Me -->
                      <div class="col-12 mb-3 mt-3">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe"
                            <?= isset($_COOKIE['remember_idnumber']) ? 'checked' : '' ?>>
                          <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                      </div>

                      <!-- Login -->
                      <div class="col-12">
                        <button class="btn btn-success w-100" name="login">Login</button>
                      </div>

                      <!-- <div class="col-12 text-center">
                      <a href="reset-password.php">Forgot your password?</a>
                    </div> -->


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
    document.addEventListener("DOMContentLoaded", function() {
      const alert = document.querySelector(".alert-dismissible");
      if (alert) {
        setTimeout(() => {
          // Bootstrap fade-out
          alert.classList.remove("show");
          alert.classList.add("fade");
          // Optional: remove completely from DOM
          setTimeout(() => alert.remove(), 500);
        }, 4000); // 4 seconds
      }
    });
  </script>

</body>

</html>
<?php unset($_SESSION['login_failed']); ?>
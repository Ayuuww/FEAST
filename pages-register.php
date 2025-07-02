<?php
session_start(); 
include 'conn/conn.php';// Connection to the database

// Display message if set
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

  <title>FEAST - Register</title>
  <?php include 'header.php'?>

  <style>
    @media (max-width: 576px) {
      .card {
        padding: 1rem !important;
      }
      .card-body {
        padding: 1.5rem !important;
      }
      .logo-img {
        width: 200px;
      }
    }
  </style>
  
</head>

<body>

  <main><!-- Main Section -->
  
  <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
    <div class="container-fluid">
      <div class="row h-100 flex-column flex-md-row">

      <!-- Right side: Logo -->
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center bg-light text-center">
          <img src="pics/DMMMSUlogosignup.png" alt="Logo" width="300" class="logo-img mb-4">
          <h4 class="fst-italic"><strong>FEAST DMMMSU-NLUC</strong></h4>
        </div>
        
        <!-- Left side: Registration form -->
        <div class="col-md-6 d-flex flex-column justify-content-center align-items-center bg-light">
          <div class="w-100 px-3 px-sm-5 px-md-0" style="max-width: 500px;">

            <div class="card shadow-lg">
              <div class="card-body">
                <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>

                <form class="row g-3 needs-validation" novalidate method="post" action="register.php">
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

                  <!-- Email -->
                  <div class="col-12">
                      <div class="form-floating">
                          <input type="email" name="email" class="form-control" placeholder="Email" id="yourEmail" required>
                          <label for="yourEmail" class="form-label">Email</label>
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

                  <!-- Department -->
                  <div class="col-md-6">
                      <div class="form-floating">
                          <select class="form-select" name="department" required>
                              <option value="" disabled selected>Select Department</option>
                              <option value="CIS">CIS</option>
                              <option value="CAS">CAS</option>
                              <option value="CVM">CVM</option>
                          </select>
                          <label for="department">Department</label>
                      </div>
                  </div>

                  <!-- Status -->
                  <div class="col-md-6">
                      <div class="form-floating">
                          <select class="form-select" name="role" required>
                              <option value="" disabled selected>Select Role</option>
                              <option value="student">Student</option>
                              <option value="faculty">Faculty</option>
                          </select>
                      <label class="form-label">Role</label>
                  </div>
                  </div>

                  <!-- Terms -->
                  <div class="col-12">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="terms" id="acceptTerms" required>
                      <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
                    </div>
                  </div>

                  <!-- Submit -->
                  <div class="col-12">
                    <button class="btn btn-success w-100" name="submit" id="create" type="submit">Create Account</button>
                  </div>

                  <div class="col-12 text-center">
                    <p class="small">Already have an account? <a href="pages-login.php">Log in</a></p>
                  </div>

                </form>
              </div>
            </div>

          </div>
        </div>

        
      </div>
    </div>
  </section><!-- End Registration Section -->
</main><!-- End #main -->

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
    } 
    else
     {
      document.getElementById('mess').style.display = 'block';
      document.getElementById('conpass').style.borderColor = 'red';
      }

    }


  </script>


</body>

</html>

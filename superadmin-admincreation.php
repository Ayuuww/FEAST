<?php 

session_start();
include 'conn/conn.php';// Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

// Display message if set
if(isset($_SESSION['msg'])) {
    echo "<script>alert('".$_SESSION['msg']."');</script>";
    unset($_SESSION['msg']);
}

// Fetch super admin details
$query = "SELECT * FROM superadmin";



?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>FEAST / Admin Creation  </title>
    <?php include 'header.php'?>
  </head>

  <body>

    <?php include 'superadmin-header.php'?>

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-dashboard.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li><!-- End Dashboard Nav -->

        <!-- Subject Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-subjectlist.php" >
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-subjectadding.php">
                <i class="bi bi-circle"></i><span>Add Subject</span>
              </a>
            </li>
          </ul>
        </li><!-- End Subject Nav -->

        <!-- Student Subject Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-studentsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Assign Subject</span>
          </a>
        </li><!-- End Student Subject Nav -->

        <!-- Reports Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
            <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="reports" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-individualreport.php" >
                <i class="bi bi-circle"></i><span>Invidiual Report</span>
              </a>
            </li>
            <li>
              <a href="superadmin-acknowledgementreport.php">
                <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
              </a>
            </li>
          </ul>
        </li><!-- End Reports Nav -->

        <!-- Evaluation Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#evaluation" data-bs-toggle="collapse" href="#">
            <i class="ri-settings-4-line"></i><span>Evaluation</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="evaluation" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-evaluationsetting.php" >
                <i class="bi bi-circle"></i><span>Setting</span>
              </a>
            </li>
            <li>
              <a href="superadmin-evaluationswitch.php">
                <i class="bi bi-circle"></i><span>On/Off</span>
              </a>
            </li>
          </ul>
        </li><!-- End Evalutaion Nav -->

        <li class="nav-heading">Account Management</li>

        <!-- Faculty Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-people-fill"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-facultylist.php">
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-facultycreation.php">
                <i class="bi bi-circle"></i><span>Add New Student</span>
              </a>
            </li>
          </ul>
        </li><!-- End Faculty Nav -->
        
        <!-- Student Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-people"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-studentlist.php">
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-studentcreation.php">
                <i class="bi bi-circle"></i><span>Add New Student</span>
              </a>
            </li>
          </ul>
        </li><!-- End Student Nav -->

        <!-- Admin Nav -->
        <li class="nav-item">
          <a class="nav-link collapse" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="admin-nav" class="nav-content collapse show " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-adminlist.php" >
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-admincreation.php" class="active">
                <i class="bi bi-circle"></i><span>Add New Admin</span>
              </a>
            </li>
          </ul>
        </li><!-- End Admin Nav -->

        <!-- Super Admin Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person-fill"></i><span>Super Admin</span><i
              class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-superadminlist.php">
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-superadmincreation.php">
                <i class="bi bi-circle"></i><span>Add New SuperAdmin</span>
              </a>
            </li>
          </ul>
        </li><!-- End Super Admin Nav -->

        <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-user-profile.php">
            <i class="bi bi-person"></i>
            <span>Profile</span>
          </a>
        </li><!-- End Profile Page Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
          </a>
        </li><!-- End Sign Out Page Nav -->


      </ul>

    </aside><!-- End Sidebar-->

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
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Create New Admin</h5>
                    <form class="row g-3 needs-validation" novalidate method="post" action="admincreation.php">

                      <!-- ID Number -->
                      <div class="col-md-3">
                        <div class="form-floating">
                          <input type="text" name="idnumber" class="form-control" id="idnumber" placeholder="ID Number" pattern="^[0-9\-]+$" required>
                          <label for="idnumber" class="form-label">ID Number</label>
                          <div class="invalid-feedback">Please, enter a valid ID number (only numbers and hyphens are allowed)!</div>
                        </div>
                      </div>

                      <!-- First Name -->
                      <div class="col-md-3">
                          <div class="form-floating">
                              <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                              <label class="form-label">First Name</label>
                          </div>
                      </div>

                      <!-- Middle Name -->
                      <div class="col-md-3">
                          <div class="form-floating">
                              <input type="text" name="mid_name" class="form-control" placeholder="Middle Name" required>
                              <label class="form-label">Middle Name</label>
                          </div>
                      </div>

                      <!-- Last Name -->
                      <div class="col-md-3">
                          <div class="form-floating">
                              <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                              <label class="form-label">Last Name</label>
                          </div>
                      </div>

                      <!-- Email -->
                      <div class="col-md-4">
                          <div class="form-floating">
                              <input type="email" name="email" class="form-control" placeholder="Email" id="yourEmail" required>
                              <label for="yourEmail" class="form-label">Email</label>
                          </div>
                      </div>

                      <!-- Password -->
                      <div class="col-md-2">
                          <div class="form-floating">
                              <input type="text" name="password" value="ILOVEDMMMSU" class="form-control" placeholder="ILOVEDMMMSU" id="yourPassword" readonly>
                              <label for="yourEmail" class="form-label">Default Password</label>
                          </div>
                      </div>

                      <!-- Confirm Password
                      <div class="col-md-3">
                          <div class="form-floating">
                              <input type="password" name="password" class="form-control" placeholder="Confirm Password" id="conpass" onkeyup='checkpass();' required>
                              <div class="invalid-feedback" id="mess">Password do not match</div>
                              <label class="form-label">Confirm Password</label>
                          </div>
                      </div> -->

                      <!-- Department -->
                      <div class="col-md-4">
                          <div class="form-floating">
                              <select class="form-select" name="position" required>
                                  <option value="" disabled selected>-- Select Position --</option>
                                  <option value="Vice-President">Vice-President</option>
                                  <option value="Chancelor">Chancelor</option>
                                  <option value="Campus-Administrator">Campus-Administrator</option>
                                  <option value="Dean">Dean</option>
                                  <option value="Director">Director</option>
                              </select>
                              <label for="department">Position</label>
                          </div>
                      </div>

                      <!-- Department -->
                      <div class="col-md-2">
                          <div class="form-floating">
                              <select class="form-select" name="department" required>
                                  <option value="" disabled selected>Select Department</option>
                                  <option value="CIS">CIS</option>
                                  <option value="CAS">CAS</option>
                                  <option value="CVM">CVM</option>
                                  <option value="CAFF">CAFF</option>
                              </select>
                              <label for="department">Department</label>
                          </div>
                      </div>

                      <!-- Is it Faculty? -->
                      <div class="col-md-2 offset-5">
                          <div class="form-floating">
                              <select class="form-select" name="faculty" required>
                                  <option value="" disabled selected>-- Select --</option>
                                  <option value="yes">Yes</option>
                                  <option value="no">No</option>
                              </select>
                              <label for="department">Is it Faculty Member?</label>
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
    <footer id="footer" class="footer">
      <div class="copyright">
        &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </footer><!-- End Footer -->

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

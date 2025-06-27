<?php 

session_start();
include 'conn/conn.php';// Connection to the database

// Display message if set
if(isset($_SESSION['msg'])) {
    echo "<script>alert('".$_SESSION['msg']."');</script>";
    unset($_SESSION['msg']);
}



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

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

      <?php include 'logo.php'?><!-- End Logo -->

      <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="POST" action="#">
          <input type="text" name="query" placeholder="Search" title="Enter search keyword">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
      </div><!-- End Search Bar -->

      <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

          <li class="nav-item d-block d-lg-none">
            <a class="nav-link nav-icon search-bar-toggle " href="#">
              <i class="bi bi-search"></i>
            </a>
          </li><!-- End Search Icon-->

          <li class="nav-item dropdown pe-3">

            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
              <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
              <span class="d-none d-md-block dropdown-toggle ps-2">K. Anderson</span>
            </a><!-- End Profile Iamge Icon -->

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
              <li class="dropdown-header">
                <h6>Kevin Anderson</h6>
                <span>Web Designer</span>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>

              <li>
                <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                  <i class="bi bi-person"></i>
                  <span>My Profile</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>

              <li>
                <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                  <i class="bi bi-gear"></i>
                  <span>Account Settings</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>

              <li>
                <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                  <i class="bi bi-question-circle"></i>
                  <span>Need Help?</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>

              <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
                </a>
              </li>

            </ul><!-- End Profile Dropdown Items -->
          </li><!-- End Profile Nav -->

        </ul>
      </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

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

        <!-- Report Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-reports.php">
            <i class="bi bi-journal-text"></i>
            <span>Reports</span>
          </a>
        </li><!-- End Report Nav -->

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
              <a href="superadmin-facultyapproval.php">
                <i class="bi bi-circle"></i><span>Approval</span>
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
              <a href="superadmin-studentapproval.php">
                <i class="bi bi-circle"></i><span>Approval</span>
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

        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-gem"></i><span>Icons</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="icons-bootstrap.html">
                <i class="bi bi-circle"></i><span>Bootstrap Icons</span>
              </a>
            </li>
            <li>
              <a href="icons-remix.html">
                <i class="bi bi-circle"></i><span>Remix Icons</span>
              </a>
            </li>
            <li>
              <a href="icons-boxicons.html">
                <i class="bi bi-circle"></i><span>Boxicons</span>
              </a>
            </li>
          </ul>
        </li><!-- End Icons Nav -->

        <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="users-profile.html">
            <i class="bi bi-person"></i>
            <span>Profile</span>
          </a>
        </li><!-- End Profile Page Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="pages-faq.html">
            <i class="bi bi-question-circle"></i>
            <span>F.A.Q</span>
          </a>
        </li><!-- End F.A.Q Page Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="pages-contact.html">
            <i class="bi bi-envelope"></i>
            <span>Contact</span>
          </a>
        </li><!-- End Contact Page Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="pages-register.html">
            <i class="bi bi-card-list"></i>
            <span>Register</span>
          </a>
        </li><!-- End Register Page Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="pages-login.html">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Login</span>
          </a>
        </li><!-- End Login Page Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="pages-error-404.html">
            <i class="bi bi-dash-circle"></i>
            <span>Error 404</span>
          </a>
        </li><!-- End Error 404 Page Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="pages-blank.html">
            <i class="bi bi-file-earmark"></i>
            <span>Blank</span>
          </a>
        </li><!-- End Blank Page Nav -->

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
                      <div class="col-6">
                          <div class="form-floating">
                              <input type="email" name="email" class="form-control" placeholder="Email" id="yourEmail" required>
                              <label for="yourEmail" class="form-label">Email</label>
                          </div>
                      </div>

                      <!-- Password -->
                      <div class="col-md-3">
                          <div class="form-floating">
                              <input type="password" name="pass" class="form-control" placeholder="Password" id="password" minlength="8" required>
                              <label class="form-label">Password</label>
                              <div class="invalid-feedback">Password must be at least 8 characters!</div>
                          </div>
                      </div>

                      <!-- Confirm Password -->
                      <div class="col-md-3">
                          <div class="form-floating">
                              <input type="password" name="password" class="form-control" placeholder="Confirm Password" id="conpass" onkeyup='checkpass();' required>
                              <div class="invalid-feedback" id="mess">Password do not match</div>
                              <label class="form-label">Confirm Password</label>
                          </div>
                      </div>

                      <!-- Department -->
                      <div class="col-4 offset-4">
                          <div class="form-floating">
                              <select class="form-select" name="department" required>
                                  <option value="" disabled selected>Select Department</option>
                                  <option value="cis">CIS</option>
                                  <option value="cas">CAS</option>
                                  <option value="cvm">CVM</option>
                              </select>
                              <label for="department">Department</label>
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

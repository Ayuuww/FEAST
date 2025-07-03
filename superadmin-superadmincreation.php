<?php

session_start();
include 'conn/conn.php';// Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

// Create a new super admin account

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

  <title>FEAST / SuperAdmin Creation</title>
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
            <a href="superadmin-facultycreation.php">
              <i class="bi bi-circle"></i><span>Add New Faculty</span>
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
        <a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="admin-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-adminlist.php" >
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-admincreation.php">
              <i class="bi bi-circle"></i><span>Add New Admin</span>
            </a>
          </li>
        </ul>
      </li><!-- End Admin Nav -->

      <!-- Super Admin Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-fill"></i><span>Super Admin</span><i
            class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-superadminlist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-superadmincreation.php" class="active">
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
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Create New Super Admin</h5>
                <form class="row g-3 needs-validation" novalidate method="post" action="superadmincreation.php">

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

      if (document.getElementById('password').value           == document.getElementById('conpass').value) {
        document.getElementById('mess').style.display         = 'none';
        document.getElementById('conpass').style.borderColor  = 'green';
        
      } 
      else
      {
        document.getElementById('mess').style.display         = 'block';
        document.getElementById('conpass').style.borderColor  = 'red';
        }

      }



    </script>

</body>

</html>

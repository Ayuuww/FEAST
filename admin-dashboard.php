<<?php
session_start();
include 'conn/conn.php'; // DB connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    header("Location: pages-login.php");
    exit();
}

// 

$admin_id = $_SESSION['idnumber'];

// Get admin's department
$dept_query = "SELECT department FROM admin WHERE idnumber = ?";
$stmt = $conn->prepare($dept_query);
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$department = $row['department'] ?? '';

// Count faculty in same department
$faculty_query = "SELECT COUNT(*) AS total FROM faculty WHERE department = ? AND status = 'active'";
$stmt = $conn->prepare($faculty_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$faculty_result = $stmt->get_result();
$faculty_row = $faculty_result->fetch_assoc();
$totalfaculty = $faculty_row['total'] ?? 0;

// Count students in same department
$student_query = "SELECT COUNT(*) AS total FROM student WHERE department = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$student_result = $stmt->get_result();
$student_row = $student_result->fetch_assoc();
$totalstudent = $student_row['total'] ?? 0;

?>


<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Home </title>

  <?php include 'header.php' ?>
  

  </head>
  <body>
    
    <?php include 'admin-header.php'?>
    
    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link" href="admin-dashboard.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li><!-- End Dashboard Nav -->

        <!-- Evaluate Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="admin-evaluate.php" >
                <i class="bi bi-circle"></i><span>Form</span>
              </a>
            </li>
            <li>
              <a href="admin-evaluatedfaculty.php" >
                <i class="bi bi-circle"></i><span>Evaluated Faculty</span>
              </a>
            </li>
          </ul>
        </li><!-- End Evaluate Nav -->

        <!-- Subject Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#subject" data-bs-toggle="collapse" href="#">
            <i class="ri-book-line"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="subject" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="admin-subjectlist.php" >
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="admin-subjectadding.php">
                <i class="bi bi-circle"></i><span>Add Subject</span>
              </a>
            </li>
          </ul>
        </li><!-- End Subject Nav -->

        <!-- Student Subject Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-studentsubject.php">
            <i class="ri-book-fill"></i>
            <span>Assign Subject</span>
          </a>
        </li><!-- End Student Subject Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-evaluatedsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Subject Evaluated</span>
          </a>
        </li><!-- End Profile Nav -->

        <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-user-profile.php">
            <i class="bi bi-person"></i>
            <span>Profile</span>
          </a>
        </li><!-- End Profile Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
          </a>
        </li><!-- End Sign out Nav -->


      </ul>

    </aside><!-- End Sidebar-->

    <main id="main" class="main">

      <div class="pagetitle">
        <h1>Dashboard</h1>

        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section dashboard">
        <div class="row">
            

            <!-- Total Faculty Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card ">
                    
                    <div class="card-body">
                    <h5 class="card-title">Total<span> | Faculty Members</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <img src="icons/teacher.png" alt="Faculty Icon" class="img-fluid" style="max-height: 50px;">
                            </div>
                            <div class="ps-3">
                            <h6><?php echo number_format($totalfaculty); ?></h6>

                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- End Total Faculty Card -->

            <!-- Total Student Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card shadow-sm">
                    <div class="card-body">
                    <h5 class="card-title">Total<span> | Students</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <img src="icons/students.png" alt="Student Icon" class="img-fluid" style="max-height: 50px;">
                            </div>
                            <div class="ps-3">
                            <h6><?php echo number_format($totalstudent); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Total Student Card -->

        </div>
      </section>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <?php include'footer.php'?>
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

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

  </body>

</html>

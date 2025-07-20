<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a student
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
  header("Location: pages-login.php");
  exit();
}

$query = "SELECT * FROM student WHERE role = 'student'";
$result = mysqli_query($conn, $query);


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Home </title>

  <?php include 'header.php' ?>

  <style>
    .welcome-box {
      background: linear-gradient(to right, #4CAF50, #0e3f10ff);
      animation: slideFadeIn 1s ease-out;
    }

    @keyframes slideFadeIn {
      0% {
        opacity: 0;
        transform: translateY(-30px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animated {
      animation-duration: 1s;
      animation-fill-mode: both;
    }

    .fadeInDown {
      animation-name: fadeInDown;
    }

    .fadeInUp {
      animation-name: fadeInUp;
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>


</head>

<body>
  <?php include 'student-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="student-dashboard.php">
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
            <a href="student-evaluate.php">
              <i class="bi bi-circle"></i><span>Form</span>
            </a>
          </li>
          <li>
            <a href="student-evaluatedsubject.php">
              <i class="bi bi-circle"></i><span>Evaluated Subject</span>
            </a>
          </li>
        </ul>
      </li><!-- End Evaluate Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="student-user-profile.php">
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
          <li class="breadcrumb-item"><a href="student-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <?php
      $student_name = 'Student';
      $idnumber = $_SESSION['idnumber'] ?? '';
      // Fetch student name from DB
      $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM student WHERE idnumber = ?");
      $stmt->bind_param("s", $idnumber);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $student_name = $student['first_name'] . ' ' . $student['mid_name'] . ' ' . $student['last_name'];
      }
      ?>

      <div class="col-12">
        <div class="welcome-box p-5 text-center text-white rounded shadow-lg">
          <h1 class="animated fadeInDown">Welcome, <span class="text-warning"><?= htmlspecialchars($student_name) ?></span>!</h1>
          <p class="lead animated fadeInUp mt-2">We're glad to have you here. Start evaluating your professors or view your evaluated subjects.</p>
        </div>
      </div>

    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php' ?>
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
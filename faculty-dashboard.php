<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a student
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
  header("Location: pages-login.php");
  exit();
}

// Top rated subject
$chartLabels = [];
$chartValues = [];

$faculty_id = $_SESSION['idnumber']; // currently logged-in faculty

$subjectQuery = "
    SELECT subject_title, COUNT(*) AS total_ratings 
    FROM evaluation 
    WHERE faculty_id = ?
    GROUP BY subject_title 
    ORDER BY total_ratings DESC 
    LIMIT 5
";

$stmt = $conn->prepare($subjectQuery);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

$chartLabels = [];
$chartValues = [];
while ($row = $result->fetch_assoc()) {
  $chartLabels[] = $row['subject_title'];
  $chartValues[] = $row['total_ratings'];
}

// Top Rated Subjects by Section (for this faculty)
$sectionQuery = "
    SELECT student_section, COUNT(*) AS total_ratings
    FROM evaluation
    WHERE faculty_id = ?
    GROUP BY student_section
    ORDER BY total_ratings DESC
    LIMIT 5
";

$stmt2 = $conn->prepare($sectionQuery);
$stmt2->bind_param("s", $faculty_id);
$stmt2->execute();
$sectionResult = $stmt2->get_result();

$sectionLabels = [];
$sectionValues = [];

while ($row = $sectionResult->fetch_assoc()) {
  $sectionLabels[] = $row['studdent_section'];
  $sectionValues[] = $row['total_ratings'];
}

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
  <?php include 'faculty-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="faculty-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <!-- Evaluate Nav -->
      <!-- <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="faculty-peer-evaluate.php" >
                <i class="bi bi-circle"></i><span>Form</span>
              </a>
            </li>
            <li>
              <a href="faculty-peer-evaluatedpeer.php" >
                <i class="bi bi-circle"></i><span>Evaluated Peer</span>
              </a>
            </li>
          </ul>
        </li>End Evaluate Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="faculty-evaluatedsubject.php">
          <i class="bi bi-book-fill"></i>
          <span>Subject</span>
        </a>
      </li><!-- End Subject Nav -->

      <!-- <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-records.php">
            <i class="ri-record-circle-fill"></i>
            <span>Records</span>
          </a>
        </li>End Records Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="faculty-user-profile.php">
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
          <li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <?php
      $faculty_name = 'Faculty';
      $idnumber = $_SESSION['idnumber'] ?? '';
      // Fetch student name from DB
      $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
      $stmt->bind_param("s", $idnumber);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $faculty_name = $student['first_name'] . ' ' . $student['mid_name'] . ' ' . $student['last_name'];
      }
      ?>

      <div class="col-12">
        <div class="welcome-box p-5 text-center text-white rounded shadow-lg">
          <h1 class="animated fadeInDown">Welcome, <span class="text-warning"><?= htmlspecialchars($faculty_name) ?></span>!</h1>
          <p class="lead animated fadeInUp mt-2">We’re glad to have you here. View your evaluation insights and performance summary below.</p>
        </div>
      </div>

      <!-- Top Rated Subject -->
      <div class="row mt-4">
        <div class="col-lg-6">
          <div class="card shadow">
            <div class="card-body">
              <h5 class="card-title">Top Rated Subjects (Handled by You)</h5>
              <canvas id="ratedSubjectsChart" style="height: 400px;"></canvas>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const ctx = document.getElementById("ratedSubjectsChart").getContext("2d");

                  new Chart(ctx, {
                    type: "bar",
                    data: {
                      labels: <?= json_encode($chartLabels) ?>,
                      datasets: [{
                        label: 'No. of Ratings',
                        data: <?= json_encode($chartValues) ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                      }]
                    },
                    options: {
                      responsive: true,
                      scales: {
                        y: {
                          beginAtZero: true,
                          ticks: {
                            precision: 0
                          }
                        }
                      }
                    }
                  });
                });
              </script><!-- end of chart -->
            </div>
          </div>
        </div>

        <!-- Top Rated by Section -->
        <div class="col-lg-6 mt-4 mt-lg-0">
          <div class="card shadow">
            <div class="card-body">
              <h5 class="card-title">Top Rated by Section (Your Subjects)</h5>
              <canvas id="ratedSectionChart" style="height: 400px;"></canvas>
            </div>
          </div>
        </div>
        <script>
          document.addEventListener("DOMContentLoaded", () => {
            const sectionCtx = document.getElementById("ratedSectionChart").getContext("2d");

            new Chart(sectionCtx, {
              type: "bar",
              data: {
                labels: <?= json_encode($sectionLabels) ?>,
                datasets: [{
                  label: 'No. of Ratings',
                  data: <?= json_encode($sectionValues) ?>,
                  backgroundColor: 'rgba(255, 159, 64, 0.7)',
                  borderColor: 'rgba(255, 159, 64, 1)',
                  borderWidth: 1
                }]
              },
              options: {
                responsive: true,
                scales: {
                  y: {
                    beginAtZero: true,
                    ticks: {
                      precision: 0
                    }
                  }
                }
              }
            });
          });
        </script>
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
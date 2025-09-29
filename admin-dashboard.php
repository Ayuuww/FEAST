<?php
session_start();
if (isset($_SESSION['access_denied'])) {
  $access_denied = $_SESSION['access_denied'];
  unset($_SESSION['access_denied']);
}
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

// Count total evaluations from students
$student_eval_query = "
  SELECT COUNT(*) AS total FROM evaluation e
  JOIN faculty f ON e.faculty_id = f.idnumber
  WHERE f.department = ?";

$stmt = $conn->prepare($student_eval_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();
$student_eval_count = $result->fetch_assoc()['total'] ?? 0;

// Count total evaluations from admins
$admin_eval_query = "
  SELECT COUNT(*) AS total FROM admin_evaluation ae
  JOIN faculty f ON ae.evaluatee_id = f.idnumber
  WHERE f.department = ? ";

$stmt = $conn->prepare($admin_eval_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();
$admin_eval_count = $result->fetch_assoc()['total'] ?? 0;

// Combine both
$total_evaluations = $student_eval_count + $admin_eval_count;

/// Student evaluations trend (uses created_at)
$eval_trend_query = "
  SELECT DATE_FORMAT(e.created_at, '%Y-%m') AS eval_month, COUNT(*) AS total
  FROM evaluation e
  JOIN faculty f ON e.faculty_id = f.idnumber
  WHERE f.department = ?
  GROUP BY eval_month
  ORDER BY eval_month ASC
";
$stmt = $conn->prepare($eval_trend_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

$months = [];
$eval_counts = [];
while ($row = $result->fetch_assoc()) {
  $months[] = $row['eval_month'] . "-01";
  $eval_counts[] = (int)$row['total'];
}

// Admin evaluations trend (uses evaluation_date instead of created_at!)
$admin_eval_trend_query = "
  SELECT DATE_FORMAT(ae.evaluation_date, '%Y-%m') AS eval_month, COUNT(*) AS total
  FROM admin_evaluation ae
  JOIN faculty f ON ae.evaluatee_id = f.idnumber
  WHERE f.department = ?
  GROUP BY eval_month
  ORDER BY eval_month ASC
";
$stmt = $conn->prepare($admin_eval_trend_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

$admin_months = [];
$admin_eval_counts = [];
while ($row = $result->fetch_assoc()) {
  $admin_months[] = $row['eval_month'] . "-01";
  $admin_eval_counts[] = (int)$row['total'];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

</head>

<body>

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->

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
        <div class="col-xxl-4 col-md-4">
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
        <div class="col-xxl-4 col-md-4">
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

        <!-- Total Evaluation Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Total<span> | Evaluations</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <img src="icons/evaluation.png" alt="Evaluation Icon" class="img-fluid" style="max-height: 50px;">
                </div>
                <div class="ps-3">
                  <h6><?php echo number_format($total_evaluations); ?></h6>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Total Evaluation Card -->

        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Evaluation Trends <span>| Monthly</span></h5>
              <div id="evalTrendChart"></div>
            </div>
          </div>
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

  <?php if (isset($access_denied)): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: 'Access Denied',
          text: <?= json_encode($access_denied) ?>,
          confirmButtonText: 'OK',
          confirmButtonColor: '#d33',
          footer: 'Need help? Contact Superadmin'
        }).then(() => {
          window.location.href = "admin-dashboard.php"; // ✅ redirect stays on dashboard
        });
      });
    </script>
  <?php endif; ?>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#evalTrendChart"), {
        series: [{
          name: 'Student Evaluations',
          data: <?= json_encode($eval_counts) ?>
        }, {
          name: 'Admin Evaluations',
          data: <?= json_encode($admin_eval_counts) ?>
        }],
        chart: {
          height: 350,
          type: 'line',
          toolbar: {
            show: false
          }
        },
        markers: {
          size: 4
        },
        colors: ['#4154f1', '#ff771d'], // blue for students, orange for admins
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.3,
            opacityTo: 0.4,
            stops: [0, 90, 100]
          }
        },
        dataLabels: {
          enabled: true
        },
        stroke: {
          curve: 'smooth',
          width: 2
        },
        xaxis: {
          type: 'datetime',
          categories: <?= json_encode($months) ?>
        },
        tooltip: {
          x: {
            format: 'MMM yyyy'
          }
        }
      }).render();
    });
  </script>


</body>

</html>
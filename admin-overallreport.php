<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get admin's department
$stmt = $conn->prepare("SELECT department FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_department);
$stmt->fetch();
$stmt->close();

// Get all faculty in this department
$query = $conn->prepare("
  SELECT idnumber, last_name, first_name, mid_name
  FROM faculty
  WHERE department = ?
  ORDER BY last_name ASC
");
$query->bind_param("s", $admin_department);
$query->execute();
$faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
$query->close();

// Initialize rows
$set_rows = '';
$sef_rows = '';
$overall_rows = '';

foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

  // SET data (Student evaluations)
  $set_result = $conn->query("
    SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating
    FROM evaluation
    WHERE faculty_id = '$fid'
  ")->fetch_assoc();

  $set_count = (int)$set_result['students'];
  $set_avg = $set_count ? number_format((float)$set_result['avg_rating'], 2) : '0.00';
  $set_rows .= "<tr><td>{$name}</td><td class='text-center'>{$set_count}</td><td class='text-center'>{$set_avg} %</td></tr>";

  // SEF data (Supervisor evaluations)
  $sef_result = $conn->query("
    SELECT COUNT(*) AS admins, AVG(computed_rating) AS avg_rating
    FROM admin_evaluation
    WHERE evaluatee_id = '$fid'
  ")->fetch_assoc();

  $sef_count = (int)$sef_result['admins'];
  $sef_avg = $sef_count ? number_format((float)$sef_result['avg_rating'], 2) : '0.00';
  $sef_rows .= "<tr><td>{$name}</td><td class='text-center'>{$sef_count}</td><td class='text-center'>{$sef_avg} %</td></tr>";

  // Overall Evaluation
  $set_avg_val = (float)$set_avg;
  $sef_avg_val = (float)$sef_avg;

  $overall_avg = ($set_count && $sef_count) ? number_format(($set_avg_val + $sef_avg_val) / 2, 2) : ($set_count ? $set_avg : ($sef_count ? $sef_avg : '0.00'));

  $overall_rows .= "<tr><td>{$name}</td><td class='text-center'>{$set_avg} %</td><td class='text-center'>{$sef_avg} %</td><td class='text-center'>{$overall_avg} %</td></tr>";
}
?>


<!DOCTYPE html>
<html>

<head>
   
  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

</head>

<body>
  <?php include 'admin-header.php'; ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Overall Faculty Evaluation Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Overall Report</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="container mt-4">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title text-center my-3">
                  Overall Evaluation Report – <?= htmlspecialchars($admin_department) ?>
                </h4>

                <!-- SET Table -->
                <h5 class="mt-4 mb-2">Student Evaluation of Teachers (SET)</h5>
                <div class="table-responsive mb-4">
                  <table class="table table-bordered table-hover">
                    <thead class="table-light text-center">
                      <tr>
                        <th>Faculty Name</th>
                        <th>No. of Student Evaluations</th>
                        <th>Average SET Rating</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?= $set_rows ?> <!-- This should be built in your PHP script -->
                    </tbody>
                  </table>
                </div>

                <!-- SEF Table -->
                <h5 class="mb-2">Supervisor Evaluation of Faculty (SEF)</h5>
                <div class="table-responsive mb-4">
                  <table class="table table-bordered table-hover">
                    <thead class="table-light text-center">
                      <tr>
                        <th>Faculty Name</th>
                        <th>No. of Supervisor Evaluations</th>
                        <th>Average SEF Rating</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?= $sef_rows ?> <!-- This should be built in your PHP script -->
                    </tbody>
                  </table>
                </div>

                <!-- Combined Overall Evaluation Table -->
                <h5 class="mb-2">Overall Evaluation (SET + SEF)</h5>
                <div class="table-responsive mb-4">
                  <table class="table table-bordered table-hover">
                    <thead class="table-light text-center">
                      <tr>
                        <th>Faculty Name</th>
                        <th>SET Avg (%)</th>
                        <th>SEF Avg (%)</th>
                        <th>Overall Average (%)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?= $overall_rows ?> <!-- Should be generated by combining SET & SEF averages -->
                    </tbody>
                  </table>
                </div>

                <div class="text-end mb-3">
                  <a href="admin-overallreport-print.php" class="btn btn-secondary" target="_blank">
                    <i class="bi bi-printer"></i> Print Report
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


  </main>

  <?php include 'footer.php'; ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
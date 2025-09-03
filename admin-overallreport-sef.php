<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];
$stmt = $conn->prepare("SELECT department FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_department);
$stmt->fetch();
$stmt->close();

// Fetch all faculty in this department
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

// Build table rows
$rows = '';
foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

  $r = $conn->query("
    SELECT COUNT(*) AS evals, AVG(computed_rating) AS avg_rating
    FROM admin_evaluation
    WHERE evaluatee_id = '$fid'
  ")->fetch_assoc();

  $count = (int)$r['evals'];
  $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

  $rows .= "<tr>
              <td>{$name}</td>
              <td>{$count}</td>
              <td>{$avg} %</td>
            </tr>";
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
  <?php include 'admin-header.php'; ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Supervisor Evaluation of Faculty Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Overall SEF Report</li>
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
                  Overall SEF Report – <?= htmlspecialchars($admin_department) ?>
                </h4>

                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="table-light">
                      <tr>
                        <th>Faculty Name</th>
                        <th>No. of Supervisor Evaluations</th>
                        <th>Average SEF Rating</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?= $rows ?>
                    </tbody>
                  </table>
                </div>

                <div class="text-end mt-3">
                  <a href="admin-overallreport-sef-print.php" class="btn btn-secondary" target="_blank">
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

  <!-- JS Vendor Files -->
  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>

</body>
</html>

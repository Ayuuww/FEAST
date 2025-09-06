<?php
session_start();
include 'conn/conn.php';

// Check if logged in as superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Default filters
$selected_department = isset($_GET['department']) ? $_GET['department'] : "";
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : "";
$selected_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : "";

// Build department dropdown
$dept_options = "";
$dept_query = $conn->query("SELECT DISTINCT department FROM faculty ORDER BY department ASC");
while ($row = $dept_query->fetch_assoc()) {
  $dept = $row['department'];
  $selected = ($dept === $selected_department) ? "selected" : "";
  $dept_options .= "<option value='$dept' $selected>$dept</option>";
}

// Build semester dropdown
$sem_options = "";
$sem_query = $conn->query("SELECT DISTINCT semester FROM evaluation ORDER BY semester ASC");
while ($row = $sem_query->fetch_assoc()) {
  $sem = $row['semester'];
  $selected = ($sem === $selected_semester) ? "selected" : "";
  $sem_options .= "<option value='$sem' $selected>$sem</option>";
}

// Build academic year dropdown
$year_options = "";
$year_query = $conn->query("SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
while ($row = $year_query->fetch_assoc()) {
  $year = $row['academic_year'];
  $selected = ($year === $selected_academic_year) ? "selected" : "";
  $year_options .= "<option value='$year' $selected>$year</option>";
}

// Fetch faculty for selected filters
$rows = "";
if (!empty($selected_department)) {
  $query = $conn->prepare("
      SELECT idnumber, last_name, first_name, mid_name
      FROM faculty
      WHERE department = ?
      ORDER BY last_name ASC
    ");
  $query->bind_param("s", $selected_department);
  $query->execute();
  $faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
  $query->close();

  foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    // Build condition for filters
    $where = "faculty_id = '$fid'";
    if (!empty($selected_semester)) {
      $where .= " AND semester = '" . $conn->real_escape_string($selected_semester) . "'";
    }
    if (!empty($selected_academic_year)) {
      $where .= " AND academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
    }

    $r = $conn->query("
          SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating
          FROM evaluation
          WHERE $where
        ")->fetch_assoc();

    $count = (int)$r['students'];
    $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

    $rows .= "<tr><td>{$name}</td><td>{$count}</td><td>{$avg} %</td></tr>";
  }
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

  <?php include 'superadmin-header.php'; ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->


  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Student Evaluation of Teacher (SET) Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Overall Report SET</li>
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
                  Overall SET Report
                  <?= !empty($selected_department) ? " – " . htmlspecialchars($selected_department) : "" ?>
                  <?= !empty($selected_semester) ? " | Semester: " . htmlspecialchars($selected_semester) : "" ?>
                  <?= !empty($selected_academic_year) ? " | AY: " . htmlspecialchars($selected_academic_year) : "" ?>
                </h4>

                <!-- Filters -->
                <form method="GET" class="mb-3">
                  <div class="row align-items-end">
                    <div class="col-md-3">
                      <label for="department" class="form-label">Select Department</label>
                      <select name="department" id="department" class="form-select">
                        <option value="">-- Choose Department --</option>
                        <?= $dept_options ?>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="semester" class="form-label">Select Semester</label>
                      <select name="semester" id="semester" class="form-select">
                        <option value="">-- All Semesters --</option>
                        <?= $sem_options ?>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="academic_year" class="form-label">Select Academic Year</label>
                      <select name="academic_year" id="academic_year" class="form-select">
                        <option value="">-- All Academic Years --</option>
                        <?= $year_options ?>
                      </select>
                    </div>
                    <div class="col-md-auto">
                      <button type="submit" class="btn btn-success w-100">Generate Report</button>
                    </div>
                  </div>
                </form><!-- End Filters -->
              
                <?php if (!empty($selected_department)) { ?>
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <thead class="table-light">
                        <tr>
                          <th>Faculty Name</th>
                          <th>No. of Student Evaluations</th>
                          <th>Average SET Rating</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?= $rows ?>
                      </tbody>
                    </table>
                  </div>

                  <div class="text-end mb-3">
                    <a href="superadmin-overallreport-set-print.php?department=<?= urlencode($selected_department) ?>&semester=<?= urlencode($selected_semester) ?>&academic_year=<?= urlencode($selected_academic_year) ?>"
                      class="btn btn-secondary" target="_blank">
                      <i class="bi bi-printer"></i> Print Report
                    </a>
                  </div>
                <?php } else { ?>
                  <p class="text-center text-muted">Please select a department to generate the report.</p>
                <?php } ?>

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- End #main -->

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
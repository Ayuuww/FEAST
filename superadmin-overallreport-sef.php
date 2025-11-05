<?php
session_start();
include 'conn/conn.php';

// Check if logged in as superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Default filters
$selected_department = $_GET['department'] ?? "";
$selected_program = $_GET['program'] ?? "";
$selected_semester = $_GET['semester'] ?? "";
$selected_academic_year = $_GET['academic_year'] ?? "";

// --- Build department dropdown (from faculty table) ---
$dept_options = "";
$dept_query = $conn->query("SELECT DISTINCT department FROM faculty ORDER BY department ASC");
while ($row = $dept_query->fetch_assoc()) {
  $dept = $row['department'];
  $selected = ($dept === $selected_department) ? "selected" : "";
  $dept_options .= "<option value='$dept' $selected>$dept</option>";
}

// --- Build program dropdown (depends on selected department) ---
$prog_options = "<option value=''>-- All Programs --</option>";
if (!empty($selected_department)) {
  $prog_stmt = $conn->prepare("SELECT DISTINCT program FROM faculty WHERE department = ? AND program != '' ORDER BY program ASC");
  $prog_stmt->bind_param("s", $selected_department);
  $prog_stmt->execute();
  $prog_result = $prog_stmt->get_result();
  while ($row = $prog_result->fetch_assoc()) {
    $prog = $row['program'];
    $selected = ($prog === $selected_program) ? "selected" : "";
    $prog_options .= "<option value='$prog' $selected>$prog</option>";
  }
  $prog_stmt->close();
}

// --- Build semester dropdown (from admin_evaluation table) ---
$sem_options = "";
$sem_query = $conn->query("SELECT DISTINCT semester FROM admin_evaluation ORDER BY semester ASC");
while ($row = $sem_query->fetch_assoc()) {
  $sem = $row['semester'];
  $selected = ($sem === $selected_semester) ? "selected" : "";
  $sem_options .= "<option value='$sem' $selected>$sem</option>";
}

// --- Build academic year dropdown (from admin_evaluation table) ---
$year_options = "";
$year_query = $conn->query("SELECT DISTINCT academic_year FROM admin_evaluation ORDER BY academic_year DESC");
while ($row = $year_query->fetch_assoc()) {
  $year = $row['academic_year'];
  $selected = ($year === $selected_academic_year) ? "selected" : "";
  $year_options .= "<option value='$year' $selected>$year</option>";
}

// --- Fetch faculty for selected filters ---
$rows = "";
if (!empty($selected_department)) {
  $faculty_sql = "SELECT idnumber, last_name, first_name, mid_name FROM faculty WHERE department = ?";
  $params = [$selected_department];
  $types = "s";

  if (!empty($selected_program)) {
    $faculty_sql .= " AND program = ?";
    $params[] = $selected_program;
    $types .= "s";
  }

  $faculty_sql .= " ORDER BY last_name ASC";

  $query = $conn->prepare($faculty_sql);
  $query->bind_param($types, ...$params);
  $query->execute();
  $faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
  $query->close();

  foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    // Build condition for filters
    $where = "evaluatee_id = '$fid'";
    if (!empty($selected_semester)) {
      $where .= " AND semester = '" . $conn->real_escape_string($selected_semester) . "'";
    }
    if (!empty($selected_academic_year)) {
      $where .= " AND academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
    }

    $r = $conn->query("
          SELECT COUNT(*) AS evaluations, AVG(computed_rating) AS avg_rating
          FROM admin_evaluation
          WHERE $where
        ")->fetch_assoc();

    $count = (int)$r['evaluations'];
    $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

    $rows .= "<tr><td>{$name}</td><td>{$count}</td><td>{$avg}</td></tr>";
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
      <h1>Supervisor Evaluation of Faculty (SEF) Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Overall Report SEF</li>
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
                  Overall SEF Report
                </h4>

                <!-- Filters -->
                <form method="GET" class="mb-3">
                  <div class="row align-items-end">
                    <!-- Department -->
                    <div class="col-md-3">
                      <label for="department" class="form-label">Select Department/College</label>
                      <select name="department" id="department" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Choose Department --</option>
                        <?= $dept_options ?>
                      </select>
                    </div>

                    <!-- Program -->
                    <div class="col-md-3">
                      <label for="program" class="form-label">Select Program</label>
                      <select name="program" id="program" class="form-select" <?= empty($selected_department) ? 'disabled' : '' ?>>
                        <?= $prog_options ?>
                      </select>
                    </div>

                    <!-- Semester -->
                    <div class="col-md-2">
                      <label for="semester" class="form-label">Select Semester</label>
                      <select name="semester" id="semester" class="form-select">
                        <option value="">-- All Semesters --</option>
                        <?= $sem_options ?>
                      </select>
                    </div>

                    <!-- Academic Year -->
                    <div class="col-md-2">
                      <label for="academic_year" class="form-label">Select Academic Year</label>
                      <select name="academic_year" id="academic_year" class="form-select">
                        <option value="">-- All Academic Years --</option>
                        <?= $year_options ?>
                      </select>
                    </div>

                    <div class="col-md-auto mt-3">
                      <button type="submit" class="btn btn-success w-100">Generate Report</button>
                    </div>
                  </div>
                </form>

                <?php if (!empty($selected_department)) { ?>
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

                  <div class="text-end mb-3">
                    <a href="superadmin-overallreport-sef-print.php?department=<?= urlencode($selected_department) ?>&program=<?= urlencode($selected_program) ?>&semester=<?= urlencode($selected_semester) ?>&academic_year=<?= urlencode($selected_academic_year) ?>"
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
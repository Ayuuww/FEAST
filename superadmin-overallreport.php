<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Default filters
$selected_department = isset($_GET['department']) ? $_GET['department'] : "";
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : "";
$selected_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : "";

// Build department dropdown (from faculty table)
$dept_options = "";
$dept_query = $conn->query("SELECT DISTINCT department FROM faculty ORDER BY department ASC");
while ($row = $dept_query->fetch_assoc()) {
  $dept = $row['department'];
  $selected = ($dept === $selected_department) ? "selected" : "";
  $dept_options .= "<option value='$dept' $selected>$dept</option>";
}

// Build semester dropdown (from evaluation/admin_evaluation)
$sem_options = "";
$sem_query = $conn->query("SELECT DISTINCT semester FROM evaluation UNION SELECT DISTINCT semester FROM admin_evaluation ORDER BY semester ASC");
while ($row = $sem_query->fetch_assoc()) {
  $sem = $row['semester'];
  $selected = ($sem === $selected_semester) ? "selected" : "";
  $sem_options .= "<option value='$sem' $selected>$sem</option>";
}

// Build academic year dropdown
$year_options = "";
$year_query = $conn->query("SELECT DISTINCT academic_year FROM evaluation UNION SELECT DISTINCT academic_year FROM admin_evaluation ORDER BY academic_year DESC");
while ($row = $year_query->fetch_assoc()) {
  $year = $row['academic_year'];
  $selected = ($year === $selected_academic_year) ? "selected" : "";
  $year_options .= "<option value='$year' $selected>$year</option>";
}

// Initialize rows
$set_rows = '';
$sef_rows = '';
$overall_rows = '';

if (!empty($selected_department)) {
  // Get all faculty in department
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

    // SET (Student evaluations)
    $where_set = "faculty_id = '$fid'";
    if (!empty($selected_semester)) {
      $where_set .= " AND semester = '" . $conn->real_escape_string($selected_semester) . "'";
    }
    if (!empty($selected_academic_year)) {
      $where_set .= " AND academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
    }
    $set_result = $conn->query("
      SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating
      FROM evaluation
      WHERE $where_set
    ")->fetch_assoc();
    $set_count = (int)$set_result['students'];
    $set_avg = $set_count ? number_format((float)$set_result['avg_rating'], 2) : '0.00';
    $set_rows .= "<tr><td>{$name}</td><td class='text-center'>{$set_count}</td><td class='text-center'>{$set_avg} %</td></tr>";

    // SEF (Supervisor evaluations)
    $where_sef = "evaluatee_id = '$fid'";
    if (!empty($selected_semester)) {
      $where_sef .= " AND semester = '" . $conn->real_escape_string($selected_semester) . "'";
    }
    if (!empty($selected_academic_year)) {
      $where_sef .= " AND academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
    }
    $sef_result = $conn->query("
      SELECT COUNT(*) AS admins, AVG(computed_rating) AS avg_rating
      FROM admin_evaluation
      WHERE $where_sef
    ")->fetch_assoc();
    $sef_count = (int)$sef_result['admins'];
    $sef_avg = $sef_count ? number_format((float)$sef_result['avg_rating'], 2) : '0.00';
    $sef_rows .= "<tr><td>{$name}</td><td class='text-center'>{$sef_count}</td><td class='text-center'>{$sef_avg} %</td></tr>";

    // Combined Overall
    $set_avg_val = (float)$set_avg;
    $sef_avg_val = (float)$sef_avg;
    $overall_avg = ($set_count && $sef_count)
      ? number_format(($set_avg_val + $sef_avg_val) / 2, 2)
      : ($set_count ? $set_avg : ($sef_count ? $sef_avg : '0.00'));
    $overall_rows .= "<tr>
                        <td>{$name}</td>
                        <td class='text-center'>{$set_avg}</td>
                        <td class='text-center'>{$sef_avg}</td>
                      </tr>";
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
      <h1>Overall Faculty Evaluation Report (SET + SEF)</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
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
                  Overall SET/SEF Report
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
                </form>

                <?php if (!empty($selected_department)) { ?>
                  
                  <!-- Overall Table -->
                  <h5 class="mb-2">Overall Evaluation (SET + SEF)</h5>
                  <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                      <thead class="table-light text-center">
                        <tr>
                          <th>Faculty Name</th>
                          <th>SET Avg</th>
                          <th>SEF Avg</th>
                        </tr>
                      </thead>
                      <tbody><?= $overall_rows ?></tbody>
                    </table>
                  </div>

                  <div class="text-end mb-3">
                    <a href="superadmin-overallreport-print.php?department=<?= urlencode($selected_department) ?>&semester=<?= urlencode($selected_semester) ?>&academic_year=<?= urlencode($selected_academic_year) ?>"
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

  <?php include 'footer.php'; ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
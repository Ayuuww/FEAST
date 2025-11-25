<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Default filters
$selected_college = isset($_GET['college']) ? $_GET['college'] : "";
$selected_program = isset($_GET['program']) ? $_GET['program'] : ""; // ✅ ADD THIS
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : "";
$selected_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : "";

// Build college dropdown (from faculty table)
$col_options = "";
$col_query = $conn->query("SELECT DISTINCT college FROM faculty ORDER BY college ASC");
while ($row = $col_query->fetch_assoc()) {
  $col = $row['college'];
  $selected = ($col === $selected_college) ? "selected" : "";
  $col_options .= "<option value='$col' $selected>$col</option>";
}

// --- Build program dropdown (depends on selected college) ---
$prog_options = "<option value=''>-- All Programs --</option>";
if (!empty($selected_college)) {
  $prog_stmt = $conn->prepare("SELECT DISTINCT program FROM faculty WHERE college = ? AND program != '' ORDER BY program ASC");
  $prog_stmt->bind_param("s", $selected_college);
  $prog_stmt->execute();
  $prog_result = $prog_stmt->get_result();
  while ($row = $prog_result->fetch_assoc()) {
    $prog = $row['program'];
    $selected = ($prog === $selected_program) ? "selected" : "";
    $prog_options .= "<option value='$prog' $selected>$prog</option>";
  }
  $prog_stmt->close();
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

if (!empty($selected_college)) {
  // Get all faculty in college
  // Get all faculty in college (and program, if selected)
  $faculty_sql = "SELECT idnumber, last_name, first_name, mid_name
         FROM faculty
         WHERE college = ?";
  $params = [$selected_college];
  $types = "s";

  // Add program filter if it exists
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
                  <?= !empty($selected_college) ? " – " . htmlspecialchars($selected_college) : "" ?>
                  <?= !empty($selected_semester) ? " | Semester: " . htmlspecialchars($selected_semester) : "" ?>
                  <?= !empty($selected_academic_year) ? " | AY: " . htmlspecialchars($selected_academic_year) : "" ?>
                </h4>

                <!-- Filters -->
                <form method="GET" class="mb-3">
                  <div class="row align-items-end g-3">
                    <div class="col-md-3">
                      <label for="college" class="form-label">Select College</label>
                      <select name="college" id="college" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Choose College --</option>
                        <?= $col_options ?>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="program" class="form-label">Select Program</label>
                      <select name="program" id="program" class="form-select" <?= empty($selected_college) ? 'disabled' : '' ?>>
                        <?= $prog_options ?>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label for="semester" class="form-label">Select Semester</label>
                      <select name="semester" id="semester" class="form-select">
                        <option value="">-- All Semesters --</option>
                        <?= $sem_options ?>
                      </select>
                    </div>
                    <div class="col-md-2">
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

                <?php if (!empty($selected_college)) { ?>

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
                    <a href="superadmin-overallreport-print.php?college=<?= urlencode($selected_college) ?>&program=<?= urlencode($selected_program) ?>&semester=<?= urlencode($selected_semester) ?>&academic_year=<?= urlencode($selected_academic_year) ?>"
                      class="btn btn-secondary" target="_blank">
                      <i class="bi bi-printer"></i> Print Report
                    </a>
                  </div>

                <?php } else { ?>
                  <p class="text-center text-muted">Please select a college to generate the report.</p>
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
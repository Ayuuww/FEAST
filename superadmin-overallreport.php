<?php
session_start();
include 'conn/conn.php';

// Check if logged in as superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// --- Filters ---
$selected_college = isset($_GET['college']) ? $_GET['college'] : "";
$selected_program = isset($_GET['program']) ? $_GET['program'] : "";
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : "";
$selected_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : "";

// --- Dropdowns ---
// Colleges
$col_options = "";
$col_query = $conn->query("SELECT DISTINCT college FROM faculty ORDER BY college ASC");
while ($row = $col_query->fetch_assoc()) {
  $col = $row['college'];
  $selected = ($col === $selected_college) ? "selected" : "";
  $col_options .= "<option value='$col' $selected>$col</option>";
}

// Programs (depends on selected college)
$prog_options = "<option value=''>-- All Programs --</option>";
if (!empty($selected_college)) {
  $stmt = $conn->prepare("SELECT DISTINCT program FROM faculty WHERE college = ? AND program != '' ORDER BY program ASC");
  $stmt->bind_param("s", $selected_college);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $prog = $row['program'];
    $selected = ($prog === $selected_program) ? "selected" : "";
    $prog_options .= "<option value='$prog' $selected>$prog</option>";
  }
  $stmt->close();
}

// Semesters (SET + SEF)
$sem_options = "";
$sem_query = $conn->query("SELECT DISTINCT semester FROM evaluation UNION SELECT DISTINCT semester FROM admin_evaluation ORDER BY semester ASC");
while ($row = $sem_query->fetch_assoc()) {
  $sem = $row['semester'];
  $selected = ($sem === $selected_semester) ? "selected" : "";
  $sem_options .= "<option value='$sem' $selected>$sem</option>";
}

// Academic years
$year_options = "";
$year_query = $conn->query("SELECT DISTINCT academic_year FROM evaluation UNION SELECT DISTINCT academic_year FROM admin_evaluation ORDER BY academic_year DESC");
while ($row = $year_query->fetch_assoc()) {
  $year = $row['academic_year'];
  $selected = ($year === $selected_academic_year) ? "selected" : "";
  $year_options .= "<option value='$year' $selected>$year</option>";
}

// --- Fetch faculty ---
$overall_rows = '';
$total_set_avg = 0;
$total_sef_avg = 0;
$faculty_with_set = 0;
$faculty_with_sef = 0;

if (!empty($selected_college)) {
  $faculty_sql = "SELECT idnumber, last_name, first_name, mid_name FROM faculty WHERE college = ?";
  $params = [$selected_college];
  $types = "s";

  if (!empty($selected_program)) {
    $faculty_sql .= " AND program = ?";
    $params[] = $selected_program;
    $types .= "s";
  }

  $faculty_sql .= " ORDER BY last_name ASC";

  $stmt = $conn->prepare($faculty_sql);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $faculties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt->close();

  foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    // SET
    $sql = "SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating FROM evaluation WHERE faculty_id = ?";
    $sql_types = "s";
    $sql_params = [$fid];
    if (!empty($selected_semester)) {
      $sql .= " AND semester = ?";
      $sql_params[] = $selected_semester;
      $sql_types .= "s";
    }
    if (!empty($selected_academic_year)) {
      $sql .= " AND academic_year = ?";
      $sql_params[] = $selected_academic_year;
      $sql_types .= "s";
    }
    $stmtEval = $conn->prepare($sql);
    $stmtEval->bind_param($sql_types, ...$sql_params);
    $stmtEval->execute();
    $set_result = $stmtEval->get_result()->fetch_assoc();
    $stmtEval->close();
    $set_count = (int)$set_result['students'];
    $set_avg_raw = $set_count ? (float)$set_result['avg_rating'] : 0.00;
    $set_avg_display = number_format($set_avg_raw, 2);

    // SEF
    $sql = "SELECT COUNT(*) AS admins, AVG(computed_rating) AS avg_rating FROM admin_evaluation WHERE evaluatee_id = ?";
    $sql_types = "s";
    $sql_params = [$fid];
    if (!empty($selected_semester)) {
      $sql .= " AND semester = ?";
      $sql_params[] = $selected_semester;
      $sql_types .= "s";
    }
    if (!empty($selected_academic_year)) {
      $sql .= " AND academic_year = ?";
      $sql_params[] = $selected_academic_year;
      $sql_types .= "s";
    }
    $stmtEval = $conn->prepare($sql);
    $stmtEval->bind_param($sql_types, ...$sql_params);
    $stmtEval->execute();
    $sef_result = $stmtEval->get_result()->fetch_assoc();
    $stmtEval->close();
    $sef_count = (int)$sef_result['admins'];
    $sef_avg_raw = $sef_count ? (float)$sef_result['avg_rating'] : 0.00;
    $sef_avg_display = number_format($sef_avg_raw, 2);

    // Totals for college averages
    if ($set_count > 0) {
      $total_set_avg += $set_avg_raw;
      $faculty_with_set++;
    }
    if ($sef_count > 0) {
      $total_sef_avg += $sef_avg_raw;
      $faculty_with_sef++;
    }

    $overall_rows .= "<tr>
        <td>{$name}</td>
        <td class='text-center'>{$set_avg_display}</td>
        <td class='text-center'>{$sef_avg_display}</td>
      </tr>";
  }
}

$final_set_average = ($faculty_with_set > 0) ? ($total_set_avg / $faculty_with_set) : 0;
$final_sef_average = ($faculty_with_sef > 0) ? ($total_sef_avg / $faculty_with_sef) : 0;

?>
<!DOCTYPE html>
<html>

<head>
  <?php include 'head.php'; ?>
</head>

<body>
  <?php include 'superadmin-header.php'; ?>
  <?php include 'superadmin-sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Overall Faculty Evaluation Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Overall Report SET & SEF</li>
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
                  Overall Evaluation Report – <?= htmlspecialchars($selected_college) ?>
                </h4>

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
                    <div class="col-md-auto mt-2">
                      <button type="submit" class="btn btn-success w-100">Generate Report</button>
                    </div>
                  </div>
                </form>

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
                    <tfoot>
                      <tr class="table-light fw-bold">
                        <td class="text-end">College Average:</td>
                        <td class="text-center"><?= number_format($final_set_average, 2) ?></td>
                        <td class="text-center"><?= number_format($final_sef_average, 2) ?></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div class="text-end mb-3">
                  <a href="superadmin-overallreport-print.php?college=<?= urlencode($selected_college) ?>&program=<?= urlencode($selected_program) ?>&semester=<?= urlencode($selected_semester) ?>&academic_year=<?= urlencode($selected_academic_year) ?>" class="btn btn-secondary" target="_blank">
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

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
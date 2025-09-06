<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Superadmin Info for "Prepared By"
$superadmin_id = $_SESSION['idnumber'];
$prep_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM superadmin WHERE idnumber = ?");
$prep_stmt->bind_param("s", $superadmin_id);
$prep_stmt->execute();
$prep_stmt->bind_result($prep_fname, $prep_mname, $prep_lname);
$prep_stmt->fetch();
$prep_stmt->close();
$prepared_by_name = trim("$prep_fname $prep_mname $prep_lname");

// Get and sanitize filter values
$faculty_id = $_GET['faculty_id'] ?? '';
$sem_filter = $_GET['semester'] ?? '';
$ay_filter = $_GET['academic_year'] ?? '';

// Variables to hold report data
$full_name = 'N/A';
$dept = 'N/A';
$rank = 'N/A';
$sem = 'N/A';
$sy = 'N/A';
$set_avg = '0.00';
$sef_avg = '0.00';
$evaluator_name = '';

if (!empty($faculty_id)) {
  // 1. Get Faculty Info
  $stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
  $stmt->bind_param("s", $faculty_id);
  $stmt->execute();
  $stmt->bind_result($fname, $mname, $lname, $dept_raw, $rank_raw);
  if ($stmt->fetch()) {
    $full_name = strtoupper(trim("$fname $mname $lname"));
    $dept = strtoupper($dept_raw);
    $rank = ucwords($rank_raw);
  }
  $stmt->close();

  // 2. Get Semester/Academic Year (filtered if available)
  $where_clause_ae = " WHERE evaluatee_id = ? ";
  $where_clause_e = " WHERE faculty_id = ? ";
  $params_ae = [$faculty_id];
  $params_e = [$faculty_id];
  $types_ae = "s";
  $types_e = "s";

  if (!empty($sem_filter)) {
    $where_clause_ae .= " AND semester = ? ";
    $where_clause_e .= " AND semester = ? ";
    $params_ae[] = $sem_filter;
    $params_e[] = $sem_filter;
    $types_ae .= "s";
    $types_e .= "s";
  }
  if (!empty($ay_filter)) {
    $where_clause_ae .= " AND academic_year = ? ";
    $where_clause_e .= " AND academic_year = ? ";
    $params_ae[] = $ay_filter;
    $params_e[] = $ay_filter;
    $types_ae .= "s";
    $types_e .= "s";
  }

  $stmt = $conn->prepare("SELECT semester, academic_year FROM admin_evaluation " . $where_clause_ae . " ORDER BY evaluation_date DESC LIMIT 1");
  $stmt->bind_param($types_ae, ...$params_ae);
  $stmt->execute();
  $stmt->bind_result($sem_eval, $ay_eval);
  if ($stmt->fetch()) {
    $sem = $sem_eval;
    $sy = $ay_eval;
  }
  $stmt->close();

  if ($sem === 'N/A') {
    $stmt = $conn->prepare("SELECT semester, academic_year FROM evaluation " . $where_clause_e . " ORDER BY id DESC LIMIT 1");
    $stmt->bind_param($types_e, ...$params_e);
    $stmt->execute();
    $stmt->bind_result($sem_eval, $ay_eval);
    if ($stmt->fetch()) {
      $sem = $sem_eval;
      $sy = $ay_eval;
    }
    $stmt->close();
  }

  // 3. Get SET and SEF Ratings
  $stmt = $conn->prepare("SELECT AVG(computed_rating) FROM evaluation " . $where_clause_e);
  $stmt->bind_param($types_e, ...$params_e);
  $stmt->execute();
  $stmt->bind_result($avg);
  if ($stmt->fetch()) {
    $set_avg = number_format($avg, 2);
  }
  $stmt->close();

  $stmt = $conn->prepare("SELECT AVG(computed_rating) FROM admin_evaluation " . $where_clause_ae);
  $stmt->bind_param($types_ae, ...$params_ae);
  $stmt->execute();
  $stmt->bind_result($avg);
  if ($stmt->fetch()) {
    $sef_avg = number_format($avg, 2);
  }
  $stmt->close();

  // 4. Determine Evaluator/Supervisor Name
  // A. First, find the latest evaluator from the admin_evaluation table
  $eval_stmt = $conn->prepare("SELECT evaluator_id FROM admin_evaluation WHERE evaluatee_id = ? ORDER BY evaluation_date DESC LIMIT 1");
  $eval_stmt->bind_param("s", $faculty_id);
  $eval_stmt->execute();
  $eval_stmt->bind_result($admin_id);
  $eval_stmt->fetch();
  $eval_stmt->close();

  $evaluator_name = 'N/A';
  $stmt_supervisor = $conn->prepare("SELECT first_name, mid_name, last_name, position
                              FROM admin
                              WHERE department = ?
                                AND (position LIKE 'Dean%' OR position LIKE 'Chair%' OR position LIKE 'Program Chair%')
                              ORDER BY 
                                CASE 
                                  WHEN position LIKE 'Dean%' THEN 1
                                  WHEN position LIKE 'Program Chair%' THEN 2
                                  WHEN position LIKE 'Chair%' THEN 3
                                  ELSE 4
                                END
                              LIMIT 1");
  if ($stmt_supervisor) {
    $stmt_supervisor->bind_param("s", $dept);
    $stmt_supervisor->execute();
    $stmt_supervisor->bind_result($sfn, $smn, $sln, $spos);
    if ($stmt_supervisor->fetch()) {
      $evaluator_name = strtoupper(trim("$sfn $smn $sln"));
    }
    $stmt_supervisor->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <style>
    .table th,
    .table td {
      text-align: left;
      vertical-align: top;
    }

    .signature-box {
      height: 60px;
      min-width: 250px;
      border-bottom: 1px solid #000;
    }

    .print-btn {
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <?php include 'superadmin-header.php' ?>
  <?php include 'superadmin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Faculty Evaluation Acknowledgement Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Acknowledgement Report</li>
        </ol>
      </nav>
    </div>

    <div class="card p-4 mb-4">
      <form method="GET" action="superadmin-acknowledgementreport.php">
        <div class="row align-items-end mb-4">
          <div class="col-md-2">
            <label for="department" class="form-label">Select Department</label>
            <select class="form-select" name="department" id="department" onchange="this.form.submit()">
              <option value="">-- All Departments --</option>
              <?php
              $dept_query = mysqli_query($conn, "SELECT DISTINCT department FROM faculty ORDER BY department ASC");
              while ($row = mysqli_fetch_assoc($dept_query)) {
                $selected = (isset($_GET['department']) && $_GET['department'] == $row['department']) ? "selected" : "";
                echo "<option value='{$row['department']}' $selected>{$row['department']}</option>";
              }
              ?>
            </select>
          </div>

          <div class="col-md-4">
            <label for="faculty_id" class="form-label">Select Faculty</label>
            <select class="form-select" name="faculty_id" id="faculty_id" required>
              <option value="" disabled selected>-- Choose Faculty --</option>
              <?php
              $faculty_query_sql = "SELECT idnumber, first_name, mid_name, last_name FROM faculty";
              $params = [];
              $types = '';
              if (!empty($_GET['department'])) {
                $faculty_query_sql .= " WHERE department = ?";
                $params[] = $_GET['department'];
                $types .= 's';
              }
              $faculty_query_sql .= " ORDER BY last_name ASC";
              $stmt = $conn->prepare($faculty_query_sql);
              if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
              }
              $stmt->execute();
              $faculty_result = $stmt->get_result();
              while ($row = $faculty_result->fetch_assoc()) {
                $full_name_option = trim("{$row['last_name']}, {$row['first_name']} {$row['mid_name']}");
                $selected = ($faculty_id === $row['idnumber']) ? "selected" : "";
                echo "<option value='{$row['idnumber']}' $selected>$full_name_option</option>";
              }
              $stmt->close();
              ?>
            </select>
          </div>

          <div class="col-md-2">
            <label for="semester" class="form-label">Semester</label>
            <select class="form-select" name="semester" id="semester">
              <option value="" disabled selected>-- Select Semester --</option>
              <?php
              $sem_query = mysqli_query($conn, "SELECT DISTINCT semester FROM admin_evaluation ORDER BY semester ASC");
              while ($row = mysqli_fetch_assoc($sem_query)) {
                $selected = ($sem_filter === $row['semester']) ? "selected" : "";
                echo "<option value='{$row['semester']}' $selected>{$row['semester']}</option>";
              }
              ?>
            </select>
          </div>

          <div class="col-md-2">
            <label for="academic_year" class="form-label">Academic Year</label>
            <select class="form-select" name="academic_year" id="academic_year">
              <option value="" disabled selected>-- Select Academic Year --</option>
              <?php
              $ay_query = mysqli_query($conn, "SELECT DISTINCT academic_year FROM admin_evaluation ORDER BY academic_year DESC");
              while ($row = mysqli_fetch_assoc($ay_query)) {
                $selected = ($ay_filter === $row['academic_year']) ? "selected" : "";
                echo "<option value='{$row['academic_year']}' $selected>{$row['academic_year']}</option>";
              }
              ?>
            </select>
          </div>

          <div class="col-md-auto">
            <button type="submit" class="btn btn-success">Generate</button>
          </div>
        </div>
      </form>

      <?php if (!empty($faculty_id)) { ?>
        <div id="printSection">
          <h5 class="text-center"><strong>FACULTY EVALUATION ACKNOWLEDGEMENT FORM</strong></h5>

          <h6><strong>FACULTY MEMBER INFORMATION</strong></h6>
          <table class="table table-bordered w-100">
            <tr>
              <th>Name of Faculty</th>
              <td><?= htmlspecialchars($full_name) ?></td>
            </tr>
            <tr>
              <th>Department/College</th>
              <td><?= htmlspecialchars($dept) ?></td>
            </tr>
            <tr>
              <th>Current Faculty Rank</th>
              <td><?= htmlspecialchars($rank) ?></td>
            </tr>
            <tr>
              <th>Semester/Term & Academic Year</th>
              <td><?= htmlspecialchars($sem) ?> / <?= htmlspecialchars($sy) ?></td>
            </tr>
          </table>

          <h6><strong>FACULTY EVALUATION SUMMARY</strong></h6>
          <table class="table table-bordered text-center w-100">
            <thead>
              <tr>
                <th>Student Evaluation of Teachers (SET)</th>
                <th>Supervisor's Evaluation of Faculty (SEF)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong><?= $set_avg ?></strong></td>
                <td><strong><?= $sef_avg ?></strong></td>
              </tr>
            </tbody>
          </table>

          <p>
            I acknowledge that I have received and reviewed the faculty evaluation conducted for the period mentioned above.
            I understand that my signature below does not necessarily indicate agreement with the evaluation but confirms that I have been given the opportunity to discuss it with my supervisor.
          </p>

          <h6><strong>SUPERVISOR</strong></h6>
          <table class="table table-bordered w-100">
            <tr>
              <th>Signature</th>
              <td class="signature-box"></td>
              <th>Name</th>
              <td class="signature-box"><?= htmlspecialchars($evaluator_name) ?></td>
              <th>Date Signed</th>
              <td class="signature-box"><?= date('F j, Y') ?></td>
            </tr>
          </table>

          <h6><strong>FACULTY</strong></h6>
          <table class="table table-bordered w-100 table-responsive">
            <tr>
              <th>Signature</th>
              <td class="signature-box"></td>
              <th>Name</th>
              <td class="signature-box"><?= htmlspecialchars($full_name) ?></td>
              <th>Date Signed</th>
              <td class="signature-box"><?= date('F j, Y') ?></td>
            </tr>
          </table>
        </div>

        <a href="superadmin-acknowledgementreport-print.php?faculty_id=<?= urlencode($faculty_id) ?>&semester=<?= urlencode($sem_filter) ?>&academic_year=<?= urlencode($ay_filter) ?>" target="_blank" class="offset-4 col-md-3 btn btn-secondary print-btn">
          Print Acknowledgement
        </a>
      <?php } ?>
    </div>
  </main>

  <?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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
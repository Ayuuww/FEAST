<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Logged-in superadmin info for "Prepared By"
$superadmin_id = $_SESSION['idnumber'];
$prepared_by_name = "N/A";
$prep_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM superadmin WHERE idnumber = ?");
$prep_stmt->bind_param("s", $superadmin_id);
$prep_stmt->execute();
$prep_stmt->bind_result($prep_fname, $prep_mname, $prep_lname);
if ($prep_stmt->fetch()) {

  $middle_initial = '';
  if (!empty($prep_mname)) {
    $middle_initial = ' ' . substr($prep_mname, 0, 1) . '.'; // Add space, initial, and period
  }

  $prepared_by_name = strtoupper("$prep_fname $middle_initial $prep_lname");
}
$prep_stmt->close();

// Get filter values from URL
$selected_dept = $_GET['department'] ?? '';
$selected_faculty_id = $_GET['faculty_id'] ?? '';
$selected_semester = $_GET['semester'] ?? '';
$selected_academic_year = $_GET['academic_year'] ?? '';

// --- Fetch data for dropdowns ---
$departments_result = $conn->query("SELECT DISTINCT department FROM faculty WHERE department IS NOT NULL AND department != '' ORDER BY department ASC");

$faculty_sql = "SELECT idnumber, first_name, mid_name, last_name FROM faculty";
if (!empty($selected_dept)) {
  $faculty_sql .= " WHERE department = ?";
}
$faculty_sql .= " ORDER BY last_name ASC";
$faculty_list_stmt = $conn->prepare($faculty_sql);
if (!empty($selected_dept)) {
  $faculty_list_stmt->bind_param("s", $selected_dept);
}
$faculty_list_stmt->execute();
$faculty_list_result = $faculty_list_stmt->get_result();

$semesters_query = $conn->query("SELECT DISTINCT semester FROM evaluation WHERE semester IS NOT NULL AND semester != '' UNION SELECT DISTINCT semester FROM admin_evaluation WHERE semester IS NOT NULL AND semester != '' ORDER BY semester ASC");
$academic_years_query = $conn->query("SELECT DISTINCT academic_year FROM evaluation WHERE academic_year IS NOT NULL AND academic_year != '' UNION SELECT DISTINCT academic_year FROM admin_evaluation WHERE academic_year IS NOT NULL AND academic_year != '' ORDER BY academic_year DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <style>
    .table th,
    .table td {
      text-align: left;
      vertical-align: middle;
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
      <form method="GET" action="">
        <div class="row align-items-end mb-4">
          <div class="col-md-3">
            <label for="department" class="form-label">Select Department</label>
            <select class="form-select" name="department" id="department" onchange="this.form.submit()">
              <option value="">-- All Departments --</option>
              <?php while ($row = $departments_result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['department']) ?>" <?= ($selected_dept == $row['department']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($row['department']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label for="faculty_id" class="form-label">Select Faculty</label>
            <select class="form-select" name="faculty_id" id="faculty_id" required>
              <option value="" disabled <?= empty($selected_faculty_id) ? 'selected' : ''; ?>>-- Choose Faculty --</option>
              <?php while ($row = $faculty_list_result->fetch_assoc()):
                $full_name = htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['mid_name']);
                $selected = ($selected_faculty_id == $row['idnumber']) ? "selected" : "";
                echo "<option value='{$row['idnumber']}' $selected>$full_name</option>";
              endwhile; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label for="semester" class="form-label">Semester</label>
            <select class="form-select" name="semester" id="semester">
              <option value="">-- All Semesters --</option>
              <?php while ($sem_row = $semesters_query->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($sem_row['semester']) ?>" <?= ($selected_semester == $sem_row['semester']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($sem_row['semester']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label for="academic_year" class="form-label">Academic Year</label>
            <select class="form-select" name="academic_year" id="academic_year">
              <option value="">-- All Academic Years --</option>
              <?php while ($ay_row = $academic_years_query->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($ay_row['academic_year']) ?>" <?= ($selected_academic_year == $ay_row['academic_year']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($ay_row['academic_year']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Generate</button>
          </div>
        </div>
      </form>

      <?php
      if (!empty($selected_faculty_id)) {
        $faculty_id = $selected_faculty_id;

        // --- Build WHERE clauses and parameters ---
        $params_types = "s";
        $params_values = [$faculty_id];
        $eval_where_clauses = ["faculty_id = ?"];
        $admin_eval_where_clauses = ["evaluatee_id = ?"];

        if (!empty($selected_semester)) {
          $eval_where_clauses[] = "semester = ?";
          $admin_eval_where_clauses[] = "semester = ?";
          $params_types .= "s";
          $params_values[] = $selected_semester;
        }
        if (!empty($selected_academic_year)) {
          $eval_where_clauses[] = "academic_year = ?";
          $admin_eval_where_clauses[] = "academic_year = ?";
          $params_types .= "s";
          $params_values[] = $selected_academic_year;
        }
        $eval_where_sql = implode(' AND ', $eval_where_clauses);
        $admin_eval_where_sql = implode(' AND ', $admin_eval_where_clauses);

        // --- Fetch all data needed for the report ---
        $stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
        $stmt->bind_param("s", $faculty_id);
        $stmt->execute();
        $stmt->bind_result($fname, $mname, $lname, $dept, $rank);
        $stmt->fetch();
        $stmt->close();
        $full_name = strtoupper(trim("$fname $mname $lname"));
        $dept_display = strtoupper($dept);
        $rank_display = ucwords($rank);

        $sem = $selected_semester ?: "All Semesters";
        $sy = $selected_academic_year ?: "All Academic Years";

        // SET Rating
        $set_avg = '0.00';
        $stmt_set_avg = $conn->prepare("SELECT AVG(computed_rating) as avg FROM evaluation WHERE {$eval_where_sql}");
        $stmt_set_avg->bind_param($params_types, ...$params_values);
        $stmt_set_avg->execute();
        $stmt_set_avg->bind_result($avg_res);
        if ($stmt_set_avg->fetch() && $avg_res !== null) {
          $set_avg = number_format($avg_res, 2);
        }
        $stmt_set_avg->close();

        // SEF Rating
        $sef_avg = '0.00';
        $stmt_sef_avg = $conn->prepare("SELECT AVG(computed_rating) as avg FROM admin_evaluation WHERE {$admin_eval_where_sql}");
        $stmt_sef_avg->bind_param($params_types, ...$params_values);
        $stmt_sef_avg->execute();
        $stmt_sef_avg->bind_result($avg_res);
        if ($stmt_sef_avg->fetch() && $avg_res !== null) {
          $sef_avg = number_format($avg_res, 2);
        }
        $stmt_sef_avg->close();

        // ✅ --- START OF FIX ---

        // Supervisor Name (FIXED QUERY)
        $evaluator_name = 'N/A';
        // ✅ Corrected Supervisor Query (linked via admin_departments)
        $evaluator_name = 'N/A';
        $stmt_supervisor = $conn->prepare("SELECT a.first_name, a.mid_name, a.last_name
                                                  FROM admin a
                                                  INNER JOIN admin_departments ad ON a.idnumber = ad.admin_idnumber
                                                  WHERE ad.department_name = ?
                                                    AND (a.position LIKE 'Dean%' OR a.position LIKE 'Chair%' OR a.position LIKE 'Program Chair%' OR a.position LIKE 'Director%')
                                                  ORDER BY
                                                    CASE
                                                      WHEN a.position LIKE 'Dean%' THEN 1
                                                      WHEN a.position LIKE 'Chair%' THEN 2
                                                      WHEN a.position LIKE 'Program Chair%' THEN 3
                                                      WHEN a.position LIKE 'Director%' THEN 4
                                                      ELSE 5
                                                    END
                                                  LIMIT 1");
        $stmt_supervisor->bind_param("s", $dept); // use faculty's department
        $stmt_supervisor->execute();
        $stmt_supervisor->bind_result($sfn, $smn, $sln);
        if ($stmt_supervisor->fetch()) {
          $evaluator_name = strtoupper(trim("$sfn $smn $sln"));
        }
        $stmt_supervisor->close();

        // ✅ --- END OF FIX ---
      ?>

        <div id="printSection" class="mt-4">
          <h4 class="text-center mb-4"><strong>FACULTY EVALUATION ACKNOWLEDGEMENT FORM</strong></h4>
          <h5 class="mt-4"><strong>FACULTY MEMBER INFORMATION</strong></h5>
          <table class="table table-bordered">
            <tr>
              <th style="width: 30%;">Name of Faculty</th>
              <td style="width: 2%; text-align: center;">:</td>
              <td style="font-weight: bold;"><?= htmlspecialchars($full_name) ?></td>
            </tr>
            <tr>
              <th>Department/College</th>
              <td style="text-align: center;">:</td>
              <td style="font-weight: bold;"><?= htmlspecialchars($dept_display) ?></td>
            </tr>
            <tr>
              <th>Current Faculty Rank</th>
              <td style="text-align: center;">:</td>
              <td style="font-weight: bold;"><?= htmlspecialchars($rank_display) ?></td>
            </tr>
            <tr>
              <th>Semester/Term & Academic Year</th>
              <td style="text-align: center;">:</td>
              <td style="font-weight: bold;"><?= htmlspecialchars($sem) ?> / <?= htmlspecialchars($sy) ?></td>
            </tr>
          </table>

          <h5 class="mt-5"><strong>FACULTY EVALUATION SUMMARY</strong></h5>
          <table class="table table-bordered text-center">
            <thead>
              <tr>
                <th colspan="2">Overall Rating</th>
              </tr>
              <tr>
                <th style="width: 50%;">Student Evaluation of Teachers (SET)</th>
                <th style="width: 50%;">Supervisor's Evaluation of Faculty (SAF)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="font-size: 1.1rem; font-weight: bold;"><?= htmlspecialchars($set_avg) ?></td>
                <td style="font-size: 1.1rem; font-weight: bold;"><?= htmlspecialchars($sef_avg) ?></td>
              </tr>
            </tbody>
          </table>

          <p class="mt-4" style="text-align: justify;">I acknowledge that I have received and reviewed the faculty evaluation conducted for the period mentioned above. I understand that my signature below does not necessarily indicate agreement with the evaluation but confirms that I have been given the opportunity to discuss it with my supervisor.</p>

          <table class="table table-bordered mt-5">
            <thead>
              <tr>
                <th colspan="3" class="text-center bg-dark text-white">SUPERVISOR</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width: 20%;">Signature</th>
                <td style="width: 2%; text-align: center;">:</td>
                <td style="height: 50px;"></td>
              </tr>
              <tr>
                <th>Name</th>
                <td style="text-align: center;">:</td>
                <td><?= htmlspecialchars($evaluator_name) ?></td>
              </tr>
              <tr>
                <th>Date Signed</th>
                <td style="text-align: center;">:</td>
                <td></td>
              </tr>
            </tbody>
          </table>

          <table class="table table-bordered mt-4">
            <thead>
              <tr>
                <th colspan="3" class="text-center bg-dark text-white">FACULTY</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width: 20%;">Signature</th>
                <td style="width: 2%; text-align: center;">:</td>
                <td style="height: 50px;"></td>
              </tr>
              <tr>
                <th>Name</th>
                <td style="text-align: center;">:</td>
                <td><?= htmlspecialchars($full_name) ?></td>
              </tr>
              <tr>
                <th>Date Signed</th>
                <td style="text-align: center;">:</td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="text-end mb-3">
          <?php
          $print_url = "superadmin-acknowledgementreport-print.php?faculty_id=" . urlencode($faculty_id);
          if (!empty($selected_semester)) $print_url .= "&semester=" . urlencode($selected_semester);
          if (!empty($selected_academic_year)) $print_url .= "&academic_year=" . urlencode($selected_academic_year);
          ?>
          <a href="<?= $print_url ?>" target="_blank" class="btn btn-secondary">
            <i class="bi bi-printer"></i> Print Report
          </a>
        </div>

      <?php } ?>
    </div>
  </main>

  <?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
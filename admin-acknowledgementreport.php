<?php
session_start();
include 'conn/conn.php'; // This connects to your database

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get the logged-in admin's department using a prepared statement
$admin_dept = '';
$stmt_admin_dept = $conn->prepare("SELECT department FROM admin WHERE idnumber = ?");
if ($stmt_admin_dept) {
  $stmt_admin_dept->bind_param("s", $admin_id);
  $stmt_admin_dept->execute();
  $stmt_admin_dept->bind_result($admin_dept);
  $stmt_admin_dept->fetch();
  $stmt_admin_dept->close();
}

// Get unique semesters and academic years for filters
$semesters_query = mysqli_query($conn, "SELECT DISTINCT semester FROM evaluation ORDER BY semester ASC");
$academic_years_query = mysqli_query($conn, "SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");

$selected_faculty_id = $_GET['faculty_id'] ?? '';
$selected_semester = $_GET['semester'] ?? '';
$selected_academic_year = $_GET['academic_year'] ?? '';

?>


<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

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

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Faculty Evaluation Acknowledgement Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Acknowledgement Report</li>
        </ol>
      </nav>
    </div>

    </div>
    <div class="card p-4 mb-4">
      <form method="GET" action="admin-acknowledgementreport.php">
        <div class="row align-items-end mb-4">
          <div class="col-md-4">
            <label for="faculty_id" class="form-label">Select Faculty</label>
            <select class="form-select" name="faculty_id" id="faculty_id" required>
              <option value="" disabled selected>-- Choose Faculty --</option>
              <?php
              // Now filter faculty by the same department using a prepared statement
              $stmt_faculty = $conn->prepare("SELECT idnumber, first_name, mid_name, last_name FROM faculty WHERE department = ? ORDER BY last_name ASC");
              if ($stmt_faculty) {
                $stmt_faculty->bind_param("s", $admin_dept);
                $stmt_faculty->execute();
                $faculty_result = $stmt_faculty->get_result();
                while ($row = $faculty_result->fetch_assoc()) {
                  $full_name = htmlspecialchars($row['last_name']) . ', ' . htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['mid_name']);
                  echo "<option value='" . htmlspecialchars($row['idnumber']) . "' " .
                    ($selected_faculty_id == $row['idnumber'] ? "selected" : "") .
                    ">$full_name</option>";
                }
                $stmt_faculty->close();
              }
              ?>
            </select>
          </div>
          <div class="col-md-3">
            <label for="semester" class="form-label">Semester</label>
            <select class="form-select" name="semester" id="semester">
              <option value="" disabled selected>-- Select Semesters --</option>
              <?php
              // Reset pointer for semesters_query if needed
              mysqli_data_seek($semesters_query, 0);
              while ($sem_row = mysqli_fetch_assoc($semesters_query)) {
                echo "<option value='{$sem_row['semester']}' " .
                  ($selected_semester == $sem_row['semester'] ? "selected" : "") .
                  ">" . htmlspecialchars($sem_row['semester']) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-3">
            <label for="academic_year" class="form-label">Academic Year</label>
            <select class="form-select" name="academic_year" id="academic_year">
              <option value="" disabled selected>-- Select Academic Years --</option>
              <?php
              // Reset pointer for academic_years_query if needed
              mysqli_data_seek($academic_years_query, 0);
              while ($ay_row = mysqli_fetch_assoc($academic_years_query)) {
                echo "<option value='{$ay_row['academic_year']}' " .
                  ($selected_academic_year == $ay_row['academic_year'] ? "selected" : "") .
                  ">" . htmlspecialchars($ay_row['academic_year']) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-success mt-3 mt-md-0 w-100">Generate Report</button>
          </div>
        </div>
      </form>

      <?php
      if (isset($_GET['faculty_id'])) {
        $faculty_id = $_GET['faculty_id'];

        // Build common WHERE clauses and parameters for evaluation queries
        $params_types = "s";
        $params_values = [$faculty_id];

        $admin_eval_where_clauses = ["evaluatee_id = ?"];
        $eval_where_clauses = ["faculty_id = ?"];

        if (!empty($selected_semester)) {
          $admin_eval_where_clauses[] = "semester = ?";
          $eval_where_clauses[] = "semester = ?";
          $params_types .= "s";
          $params_values[] = $selected_semester;
        }
        if (!empty($selected_academic_year)) {
          $admin_eval_where_clauses[] = "academic_year = ?";
          $eval_where_clauses[] = "academic_year = ?";
          $params_types .= "s";
          $params_values[] = $selected_academic_year;
        }

        $admin_eval_where_sql = implode(' AND ', $admin_eval_where_clauses);
        $eval_where_sql = implode(' AND ', $eval_where_clauses);


        // Get faculty info using prepared statement
        $fname = $mname = $lname = $dept = $rank = '';
        $stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
        if ($stmt) {
          $stmt->bind_param("s", $faculty_id);
          $stmt->execute();
          $stmt->bind_result($fname, $mname, $lname, $dept, $rank);
          $stmt->fetch();
          $stmt->close();
        }
        $full_name = strtoupper("$fname $mname $lname");
        $dept_display = strtoupper($dept);
        $rank_display = ucwords($rank);

        // Get semester/year from the latest admin evaluation based on filters
        $sem = "N/A";
        $sy = "N/A";
        $stmt_sem_sy = $conn->prepare("SELECT semester, academic_year FROM admin_evaluation WHERE " . $admin_eval_where_sql . " ORDER BY evaluation_date DESC LIMIT 1");
        if ($stmt_sem_sy) {
          $stmt_sem_sy->bind_param($params_types, ...$params_values);
          $stmt_sem_sy->execute();
          $stmt_sem_sy->bind_result($sem_res, $sy_res);
          if ($stmt_sem_sy->fetch()) {
            $sem = $sem_res;
            $sy = $sy_res;
          }
          $stmt_sem_sy->close();
        }

        // Fallback for semester/year if admin_evaluation doesn't yield a result for the selected filters
        if ($sem === "N/A" && $sy === "N/A") {
          $stmt_sem_sy_fallback = $conn->prepare("SELECT semester, academic_year FROM evaluation WHERE " . $eval_where_sql . " ORDER BY created_at DESC LIMIT 1");
          if ($stmt_sem_sy_fallback) {
            $stmt_sem_sy_fallback->bind_param($params_types, ...$params_values);
            $stmt_sem_sy_fallback->execute();
            $stmt_sem_sy_fallback->bind_result($sem_res, $sy_res);
            if ($stmt_sem_sy_fallback->fetch()) {
              $sem = $sem_res;
              $sy = $sy_res;
            }
            $stmt_sem_sy_fallback->close();
          }
        }

        // SET Rating using prepared statement
        $set_avg = '0.00';
        $stmt_set_avg = $conn->prepare("SELECT AVG(computed_rating) as avg FROM evaluation WHERE " . $eval_where_sql);
        if ($stmt_set_avg) {
          $stmt_set_avg->bind_param($params_types, ...$params_values);
          $stmt_set_avg->execute();
          $stmt_set_avg->bind_result($avg_res);
          if ($stmt_set_avg->fetch()) {
            $set_avg = number_format($avg_res, 2);
          }
          $stmt_set_avg->close();
        }

        // SEF Rating using prepared statement
        $sef_avg = '0.00';
        $stmt_sef_avg = $conn->prepare("SELECT AVG(computed_rating) as avg FROM admin_evaluation WHERE " . $admin_eval_where_sql);
        if ($stmt_sef_avg) {
          $stmt_sef_avg->bind_param($params_types, ...$params_values);
          $stmt_sef_avg->execute();
          $stmt_sef_avg->bind_result($avg_res);
          if ($stmt_sef_avg->fetch()) {
            $sef_avg = number_format($avg_res, 2);
          }
          $stmt_sef_avg->close();
        }


        // Get the supervisor (Dean / Chair / Program Chair) of the same department
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
      ?>

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
              <td><?= htmlspecialchars($dept_display) ?></td>
            </tr>
            <tr>
              <th>Current Faculty Rank</th>
              <td><?= htmlspecialchars($rank_display) ?></td>
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
                <td><strong><?= htmlspecialchars($set_avg) ?></strong></td>
                <td><strong><?= htmlspecialchars($sef_avg) ?></strong></td>
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
              <td class="signature-box"></td>
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
              <td class="signature-box"></td>
            </tr>
          </table>
        </div>

        <div class="text-end mb-3">
          <?php
          // Pass selected semester and academic year to printing page
          $print_url = "admin-acknowledgementreport-print.php?faculty_id=" . urlencode($faculty_id);
          if (!empty($selected_semester)) {
            $print_url .= "&semester=" . urlencode($selected_semester);
          }
          if (!empty($selected_academic_year)) {
            $print_url .= "&academic_year=" . urlencode($selected_academic_year);
          }
          ?>
          <a href="<?= $print_url ?>" target="_blank" class="btn btn-secondary">
            <i class="bi bi-printer"></i> Print Report
          </a>
        </div>

      <?php } ?>
    </div>
  </main>

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
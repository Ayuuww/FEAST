<?php
session_start();
include 'conn/conn.php';
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get the department of the admin
$dept_query = mysqli_prepare($conn, "SELECT department FROM admin WHERE idnumber = ?");
mysqli_stmt_bind_param($dept_query, "s", $admin_id);
mysqli_stmt_execute($dept_query);
mysqli_stmt_bind_result($dept_query, $admin_department);
mysqli_stmt_fetch($dept_query);
mysqli_stmt_close($dept_query);

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
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Reports </title>

  <?php include 'header.php' ?>

  <style>
    .table td,
    .table th {
      text-align: left !important;
      vertical-align: top;
    }

    .signature-cell {
      height: 60px;
      min-width: 250px;
    }

    .wide-cell {
      min-width: 70px;
    }
  </style>

</head>

<body>

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <!-- Evaluate Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-evaluate.php">
              <i class="bi bi-circle"></i><span>Form</span>
            </a>
          </li>
          <li>
            <a href="admin-evaluatedfaculty.php">
              <i class="bi bi-circle"></i><span>Evaluated Faculty</span>
            </a>
          </li>
        </ul>
      </li><!-- End Evaluate Nav -->

      <!-- Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#subject" data-bs-toggle="collapse" href="#">
          <i class="ri-book-line"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="subject" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-subjectlist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="admin-subjectadding.php">
              <i class="bi bi-circle"></i><span>Add Subject</span>
            </a>
          </li>
        </ul>
      </li><!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-studentsubject.php">
          <i class="ri-book-fill"></i>
          <span>Assign Subject</span>
        </a>
      </li><!-- End Student Subject Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-evaluatedsubject.php">
          <i class="bi bi-book-fill"></i>
          <span>Subject Evaluated</span>
        </a>
      </li><!-- End Profile Nav -->

      <!-- Reports Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="reports" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-individualreport.php" class="active">
              <i class="bi bi-circle"></i><span>Invidiual Report</span>
            </a>
          </li>
          <li>
            <a href="admin-acknowledgementreport.php">
              <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
            </a>
          </li>
          <li>
            <a href="admin-overallreport-set.php">
              <i class="bi bi-circle"></i><span>Overall Report SET</span>
            </a>
          </li>
          <li>
            <a href="admin-overallreport-sef.php">
              <i class="bi bi-circle"></i><span>Overall Report SEF</span>
            </a>
          </li>
          <li>
            <a href="admin-overallreport.php">
              <i class="bi bi-circle"></i><span>Overall Report (SET & SEF)</span>
            </a>
          </li>
          <li>
            <a href="admin-pastrecords.php">
              <i class="bi bi-circle"></i><span>Past Record</span>
            </a>
          </li>
        </ul>
      </li><!-- End Reports Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-user-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End Sign out Nav -->

    </ul>

  </aside><!-- End Sidebar-->


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Individual Faculty Evaluation Reports</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Individual Reports</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    </div>
    <div class="card p-4 mb-4">
      <form method="GET" action="admin-individualreport.php">
        <div class="row align-items-end mb-4">
          <div class="col-md-4">
            <label for="faculty_id" class="form-label">Select Faculty</label>
            <select class="form-select" name="faculty_id" id="faculty_id" required>
              <option value="" disabled <?php echo empty($selected_faculty_id) ? 'selected' : ''; ?>>-- Choose Faculty --</option>
              <?php
              $faculty_query = mysqli_query($conn, "SELECT idnumber, first_name, mid_name, last_name
                                                FROM faculty
                                                WHERE department = '" . mysqli_real_escape_string($conn, $admin_department) . "'
                                                ORDER BY last_name ASC");
              while ($row = mysqli_fetch_assoc($faculty_query)) {
                $full_name = $row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['mid_name'];
                echo "<option value='{$row['idnumber']}' " .
                  ($selected_faculty_id == $row['idnumber'] ? "selected" : "") .
                  ">$full_name</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-3">
            <label for="semester" class="form-label">Select Semester</label>
            <select class="form-select" name="semester" id="semester">
              <option value="">-- All Semesters --</option>
              <?php
              while ($sem_row = mysqli_fetch_assoc($semesters_query)) {
                echo "<option value='{$sem_row['semester']}' " .
                  ($selected_semester == $sem_row['semester'] ? "selected" : "") .
                  ">" . htmlspecialchars($sem_row['semester']) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-3">
            <label for="academic_year" class="form-label">Select Academic Year</label>
            <select class="form-select" name="academic_year" id="academic_year">
              <option value="">-- All Academic Years --</option>
              <?php
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

        // Faculty basic info
        $stmt = $conn->prepare("SELECT last_name, first_name, mid_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
        $stmt->bind_param("s", $faculty_id);
        $stmt->execute();
        $stmt->bind_result($lname, $fname, $mname, $department, $faculty_rank);
        $stmt->fetch();
        $stmt->close();

        $faculty_name = "$fname $mname $lname";

        // Parameters for filtered queries
        $params_types = "s";
        $params_values = [$faculty_id];

        // Dynamic WHERE clauses for both evaluation and admin_evaluation tables
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


        // Get latest semester/year evaluated by supervisor based on filters
        $semester = "N/A";
        $academic_year = "N/A";

        // Try admin_evaluation first
        // FIX: Select only the columns you intend to bind
        $admin_eval_stmt = $conn->prepare("SELECT semester, academic_year FROM admin_evaluation WHERE " . $admin_eval_where_sql . " ORDER BY evaluation_date DESC LIMIT 1");
        if ($admin_eval_stmt) {
          $admin_eval_stmt->bind_param($params_types, ...$params_values);
          $admin_eval_stmt->execute();
          // Bind only the two columns you selected: semester and academic_year
          $admin_eval_stmt->bind_result($sem_res, $ay_res);
          if ($admin_eval_stmt->fetch()) {
            $semester = $sem_res;
            $academic_year = $ay_res;
          }
          $admin_eval_stmt->close();
        }

        // Fallback: Try from student evaluation if supervisor evaluation is missing or filtered out
        if ($semester == "N/A" || $academic_year == "N/A") {
          // FIX: Select only the columns you intend to bind
          $eval_fallback_stmt = $conn->prepare("SELECT semester, academic_year FROM evaluation WHERE " . $eval_where_sql . " ORDER BY id DESC LIMIT 1");
          if ($eval_fallback_stmt) {
            $eval_fallback_stmt->bind_param($params_types, ...$params_values);
            $eval_fallback_stmt->execute();
            // Bind only the two columns you selected: semester and academic_year
            $eval_fallback_stmt->bind_result($semester_res, $academic_year_res);
            if ($eval_fallback_stmt->fetch()) {
              $semester = $semester_res;
              $academic_year = $academic_year_res;
            }
            $eval_fallback_stmt->close();
          }
        }

        // ===========================
        // B. Summary of Average SET Rating
        // ===========================
        $set_summary_query = "SELECT
                                        e.subject_code,
                                        TRIM(e.student_section) AS student_section,
                                        COUNT(*) AS num_students,
                                        ROUND(AVG(e.computed_rating), 2) AS avg_rating,
                                        ROUND(COUNT(*) * AVG(e.computed_rating), 2) AS weighted_value
                                    FROM evaluation e
                                    WHERE " . $eval_where_sql; // Use the eval_where_sql here

        $set_summary_query .= " GROUP BY e.subject_code, TRIM(e.student_section)";

        $stmt_set = $conn->prepare($set_summary_query);
        if ($stmt_set) {
          $stmt_set->bind_param($params_types, ...$params_values);
          $stmt_set->execute();
          $result = $stmt_set->get_result();
        } else {
          $result = false; // Handle error if prepare fails
        }


        $total_students = 0;
        $total_weighted_value = 0;
        $table_rows = '';

        if ($result) {
          while ($row = mysqli_fetch_assoc($result)) {
            $subject = htmlspecialchars($row['subject_code']);
            $section = htmlspecialchars($row['student_section']);
            $students = $row['num_students'];
            $avg = number_format($row['avg_rating'], 2);
            $weighted = number_format($row['weighted_value'], 2);

            $total_students += $students;
            $total_weighted_value += $row['weighted_value'];

            $table_rows .= "<tr>
                                            <td>$subject</td>
                                            <td>$section</td>
                                            <td>$students</td>
                                            <td>$avg</td>
                                            <td>$weighted</td>
                                        </tr>";
          }
          $stmt_set->close();
        }


        $overall_set = $total_students ? number_format($total_weighted_value / $total_students, 2) : '0.00';

        // ===========================
        // C. Supervisor Evaluation (SEF)
        // ===========================
        $sef_query = "SELECT AVG(computed_rating) as sef_rating FROM admin_evaluation WHERE " . $admin_eval_where_sql; // Use admin_eval_where_sql here
        $stmt_sef = $conn->prepare($sef_query);
        $sef_rating = 0;
        if ($stmt_sef) {
          $stmt_sef->bind_param($params_types, ...$params_values);
          $stmt_sef->execute();
          $sef_result = $stmt_sef->get_result();
          $sef_rating = mysqli_fetch_assoc($sef_result)['sef_rating'] ?? 0;
          $sef_rating = number_format($sef_rating, 2);
          $stmt_sef->close();
        }


        // ===========================
        // D. Qualitative Comments
        // ===========================
        $comments_query = "SELECT comment FROM evaluation WHERE " . $eval_where_sql . " AND comment IS NOT NULL AND comment <> '' LIMIT 5";
        $stmt_comments = $conn->prepare($comments_query);
        if ($stmt_comments) {
          $stmt_comments->bind_param($params_types, ...$params_values);
          $stmt_comments->execute();
          $comments_q = $stmt_comments->get_result();
        } else {
          $comments_q = false; // Handle error if prepare fails
        }


        // ===========================
        // HTML Output Starts Here
        // ===========================
      ?>

        <h3><strong>INDIVIDUAL FACULTY EVALUATION REPORT</strong></h3>

        <h5>A. Faculty Information</h5>
        <table class="table table-bordered">
          <tr>
            <th>Name of Faculty Evaluated:</th>
            <td><?= htmlspecialchars($faculty_name) ?></td>
          </tr>
          <tr>
            <th>Department/College:</th>
            <td><?= htmlspecialchars($department) ?></td>
          </tr>
          <tr>
            <th>Current Faculty Rank:</th>
            <td><?= htmlspecialchars($faculty_rank) ?></td>
          </tr>
          <tr>
            <th>Semester/Term & Academic Year:</th>
            <td><?= htmlspecialchars($semester) ?> / <?= htmlspecialchars($academic_year) ?></td>
          </tr>
        </table>

        <h5>B. Summary of Average SET Rating</h5>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Section</th>
              <th>No. of Students</th>
              <th>Avg. SET Rating</th>
              <th>Weighted Value</th>
            </tr>
          </thead>
          <tbody>
            <?= $table_rows ?>
            <tr>
              <th colspan='2'>TOTAL</th>
              <td><?= $total_students ?></td>
              <td></td>
              <td><?= number_format($total_weighted_value, 2) ?></td>
            </tr>
          </tbody>
        </table>

        <h5>C. SET and SEF Ratings</h5>
        <table class="table table-bordered">
          <tr>
            <th>OVERALL SET Rating</th>
            <td><?= $overall_set ?></td>
          </tr>
          <tr>
            <th>Supervisor (SEF) Rating</th>
            <td><?= $sef_rating ?></td>
          </tr>
        </table>

        <h5>D. Summary of Qualitative Comments and Suggestions</h5>
        <table class="table table-bordered">
          <tr>
            <th>#</th>
            <th>Comments</th>
          </tr>
          <?php
          $count = 1;
          if ($comments_q) {
            while ($row = mysqli_fetch_assoc($comments_q)) {
              echo "<tr><td>{$count}</td><td>" . htmlspecialchars($row['comment']) . "</td></tr>";
              $count++;
            }
            $stmt_comments->close();
          }
          if ($count == 1) echo "<tr><td colspan='2'>No comments available.</td></tr>";
          ?>
        </table>

        <h5>E. Development Plan (to be accomplished by Supervisor and Faculty)</h5>
        <table class="table table-bordered">
          <tr>
            <th>Areas for Improvement</th>
          </tr>
          <tr>
            <td style="height:60px;"></td>
          </tr>
          <tr>
            <th>Proposed Learning and Development Activities</th>
          </tr>
          <tr>
            <td style="height:60px;"></td>
          </tr>
          <tr>
            <th>Action Plan</th>
          </tr>
          <tr>
            <td style="height:60px;"></td>
          </tr>
        </table>

        <br>
        <table class="table table-bordered">
          <tr>
            <th class="wide-cell">Prepared by (Staff Signature)</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Name:</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Date:</th>
            <td class="signature-cell"><?= date('F j, Y') ?></td>
          </tr>
          <tr>
            <th class="wide-cell">Reviewed by (Authorized Official)</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Name:</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Date:</th>
            <td class="signature-cell"><?= date('F j, Y') ?></td>
          </tr>
        </table>

        <div class="text-end mb-3">
          <?php
          // Pass selected semester and academic year to printing page
          $print_url = "admin-individualreport-printing.php?faculty_id=" . $faculty_id;
          if (!empty($selected_semester)) {
            $print_url .= "&semester=" . urlencode($selected_semester);
          }
          if (!empty($selected_academic_year)) {
            $print_url .= "&academic_year=" . urlencode($selected_academic_year);
          }
          ?>
          <a href="<?= $print_url ?>" class="btn btn-secondary" target="_blank">
            <i class="bi bi-printer"></i> Print Report
          </a>
        </div>
      <?php } ?>
    </div>
  </main><!-- End #main -->

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
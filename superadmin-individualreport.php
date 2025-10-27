<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Logged-in superadmin info
$superadmin_id = $_SESSION['idnumber'];
$prepared_by_name = "N/A";
$prep_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM superadmin WHERE idnumber = ?");
$prep_stmt->bind_param("s", $superadmin_id);
$prep_stmt->execute();
$prep_stmt->bind_result($prep_fname, $prep_mname, $prep_lname);
if ($prep_stmt->fetch()) {
  $prepared_by_name = trim("$prep_fname $prep_mname $prep_lname");
}
$prep_stmt->close();

// --- Get Form Selections ---
$selected_dept = $_GET['department'] ?? '';
$selected_faculty_id = $_GET['faculty_id'] ?? '';
$selected_semester = $_GET['semester'] ?? '';
$selected_academic_year = $_GET['academic_year'] ?? '';

// --- Fetch Data for Dropdowns (BEFORE the form) ---
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
    .report-container {
      font-family: Arial, sans-serif;
      margin: auto;
      border: 1px solid #dee2e6;
      padding: 25px;
      background: #fff
    }

    .report-header {
      text-align: center;
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 20px
    }

    .section-title {
      font-weight: 700;
      font-size: 1.1rem;
      margin-top: 20px;
      margin-bottom: 10px
    }

    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px
    }

    .report-table th,
    .report-table td {
      border: 1px solid #000;
      padding: 8px;
      vertical-align: top
    }

    .report-table th {
      font-weight: 700;
      background-color: #f2f2f2
    }

    .info-table th {
      width: 30%;
      background-color: transparent
    }

    .info-table td {
      font-weight: 700
    }

    .summary-table th,
    .summary-table td {
      text-align: center
    }

    .summary-table .total-row th,
    .summary-table .total-row td {
      font-weight: 700
    }

    .comment-table th {
      text-align: center
    }

    .dev-plan-table th {
      background-color: #f2f2f2;
      text-align: left
    }

    .dev-plan-table td {
      height: 80px
    }

    .signature-table .label {
      width: 15%;
      font-weight: 400
    }

    .signature-table .line {
      border-bottom: 1px solid #000
    }

    @media print {
      body * {
        visibility: hidden
      }

      .report-container,
      .report-container * {
        visibility: visible
      }

      .report-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none
      }

      .no-print {
        display: none !important
      }
    }
  </style>
</head>

<body>
  <?php include 'superadmin-header.php' ?>
  <?php include 'superadmin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle no-print">
      <h1>Individual Faculty Evaluation Reports</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Individual Reports</li>
        </ol>
      </nav>
    </div>

    <div class="card p-4 mb-4 no-print">
      <form method="GET" action="">
        <div class="row align-items-end">
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
            <button type="submit" class="btn btn-success w-100">Generate Report</button>
          </div>
        </div>
      </form>
    </div>

    <?php if (!empty($selected_faculty_id)): ?>
      <?php
      $faculty_id = $selected_faculty_id; // Use consistent variable name

      // --- Faculty basic info ---
      $stmt = $conn->prepare("SELECT last_name, first_name, mid_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
      $stmt->bind_param("s", $faculty_id);
      $stmt->execute();
      $stmt->bind_result($lname, $fname, $mname, $department, $faculty_rank);
      $stmt->fetch();
      $stmt->close();
      $faculty_name = strtoupper("$lname, $fname $mname");

      // --- Reviewed by ---
      $reviewed_by_name = "N/A";
      $rev_stmt = $conn->prepare("SELECT a.first_name, a.mid_name, a.last_name FROM admin a INNER JOIN admin_departments ad ON a.idnumber = ad.admin_idnumber WHERE ad.department_name = ? AND (a.position LIKE '%Dean%' OR a.position LIKE '%Chair%' OR a.position LIKE 'Director%') ORDER BY CASE WHEN a.position LIKE '%Dean%' THEN 1 ELSE 2 END LIMIT 1");
      $rev_stmt->bind_param("s", $department);
      $rev_stmt->execute();
      $rev_stmt->bind_result($rev_fname, $rev_mname, $rev_lname);
      if ($rev_stmt->fetch()) {
        $reviewed_by_name = trim("$rev_fname $rev_mname $rev_lname");
      }
      $rev_stmt->close();

      // --- Build WHERE clauses for filters ---
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

      $term_display = ($selected_semester ?: "All Semesters") . " / " . ($selected_academic_year ?: "All Academic Years");

      // --- B. Summary of Average SET Rating ---
      $set_summary_query = "SELECT e.subject_code, TRIM(e.student_section) AS student_section, COUNT(*) AS num_students, ROUND(AVG(e.computed_rating), 2) AS avg_rating FROM evaluation e WHERE {$eval_where_sql} GROUP BY e.subject_code, TRIM(e.student_section) ORDER BY e.subject_code";
      $stmt_set = $conn->prepare($set_summary_query);
      $stmt_set->bind_param($params_types, ...$params_values);
      $stmt_set->execute();
      $result_set = $stmt_set->get_result();

      $total_students = 0;
      $total_weighted_value = 0;
      while ($row = $result_set->fetch_assoc()) {
        $total_students += $row['num_students'];
        $total_weighted_value += $row['num_students'] * $row['avg_rating'];
      }
      $overall_set = $total_students ? number_format($total_weighted_value / $total_students, 2) : '0.00';
      $result_set->data_seek(0);

      // --- C. SEF Rating ---
      $sef_query = "SELECT AVG(computed_rating) as sef_rating FROM admin_evaluation WHERE {$admin_eval_where_sql}";
      $stmt_sef = $conn->prepare($sef_query);
      $stmt_sef->bind_param($params_types, ...$params_values);
      $stmt_sef->execute();
      $sef_rating = number_format($stmt_sef->get_result()->fetch_assoc()['sef_rating'] ?? 0, 2);
      $stmt_sef->close();

      // --- D. Comments ---
      $comments_query = "SELECT comment FROM evaluation WHERE {$eval_where_sql} AND comment IS NOT NULL AND TRIM(comment) <> '' LIMIT 5";
      $stmt_comments = $conn->prepare($comments_query);
      $stmt_comments->bind_param($params_types, ...$params_values);
      $stmt_comments->execute();
      $comments_q = $stmt_comments->get_result();

      $sup_comments_query = "SELECT comments FROM admin_evaluation WHERE {$admin_eval_where_sql} AND comments IS NOT NULL AND TRIM(comments) <> '' LIMIT 5";
      $stmt_sup_comments = $conn->prepare($sup_comments_query);
      $stmt_sup_comments->bind_param($params_types, ...$params_values);
      $stmt_sup_comments->execute();
      $sup_comments_q = $stmt_sup_comments->get_result();
      ?>

      <div class="report-container mt-4">


        <div class="report-header">INDIVIDUAL FACULTY EVALUATION REPORT</div>

        <div class="section-title">A. Faculty Information</div>
        <table class="report-table info-table">
          <tr>
            <th>Name of Faculty Evaluated</th>
            <td>: <?= htmlspecialchars($faculty_name) ?></td>
          </tr>
          <tr>
            <th>Department/College</th>
            <td>: <?= htmlspecialchars($department) ?></td>
          </tr>
          <tr>
            <th>Current Faculty Rank</th>
            <td>: <?= htmlspecialchars($faculty_rank) ?></td>
          </tr>
          <tr>
            <th>Semester/Term & Academic Year</th>
            <td>: <?= htmlspecialchars($term_display) ?></td>
          </tr>
        </table>

        <div class="section-title">B. Summary of Average SET Rating</div>
        <table class="report-table summary-table">
          <thead>
            <tr>
              <th>(1)<br>Course Code</th>
              <th>(2)<br>Section</th>
              <th>(3)<br>No. of Students</th>
              <th>(4)<br>Ave. SET Rating</th>
              <th>(3 x 4)<br>Weighted Value</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result_set->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['subject_code']) ?></td>
                <td><?= htmlspecialchars($row['student_section']) ?></td>
                <td><?= $row['num_students'] ?></td>
                <td><?= number_format($row['avg_rating'], 2) ?></td>
                <td><?= number_format($row['num_students'] * $row['avg_rating'], 2) ?></td>
              </tr>
            <?php endwhile; ?>
            <tr class="total-row">
              <th colspan="2">TOTAL</th>
              <td><?= $total_students ?></td>
              <td></td>
              <td><?= number_format($total_weighted_value, 2) ?></td>
            </tr>
          </tbody>
        </table>

        <div class="section-title">C. SET and SEF Ratings</div>
        <table class="report-table summary-table" style="width: 80%; margin: auto;">
          <thead>
            <tr>
              <th>SET Rating</th>
              <th>*SEF Rating</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-size: 1.1em; font-weight: bold;"><?= $overall_set ?></td>
              <td style="font-size: 1.1em; font-weight: bold;"><?= $sef_rating ?></td>
            </tr>
          </tbody>
        </table>
        <p style="font-size: 0.8em; margin-top: 5px; text-align: center;"><i>*Note: rating given by the supervisor using the SEF instrument</i></p>

        <div class="section-title">D. Summary of Qualitative Comments and Suggestions</div>
        <table class="report-table comment-table">
          <thead>
            <tr>
              <th>Comments and Suggestions from the Students</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $comment_count = 0;
            while ($row = $comments_q->fetch_assoc()) {
              echo "<tr><td style='text-align: left;'><b>" . (++$comment_count) . ".</b> " . htmlspecialchars($row['comment']) . "</td></tr>";
            }
            if ($comment_count == 0) {
              echo "<tr><td style='text-align: center; font-style: italic;'>No student comments available.</td></tr>";
            }
            ?>
          </tbody>
        </table>
        <table class="report-table comment-table" style="margin-top: 20px;">
          <thead>
            <tr>
              <th>Comments and Suggestions from the Supervisor</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sup_comment_count = 0;
            while ($row = $sup_comments_q->fetch_assoc()) {
              echo "<tr><td style='text-align: left;'><b>" . (++$sup_comment_count) . ".</b> " . htmlspecialchars($row['comments']) . "</td></tr>";
            }
            if ($sup_comment_count == 0) {
              echo "<tr><td style='text-align: center; font-style: italic;'>No supervisor comments available.</td></tr>";
            }
            ?>
          </tbody>
        </table>

        <div class="section-title">E. Development Plan</div>
        <table class="report-table dev-plan-table">
          <tr>
            <th>Areas for Improvement</th>
          </tr>
          <tr>
            <td></td>
          </tr>
          <tr>
            <th>Proposed Learning and Development Activities</th>
          </tr>
          <tr>
            <td></td>
          </tr>
          <tr>
            <th>Action Plan</th>
          </tr>
          <tr>
            <td></td>
          </tr>
        </table>

        <table class="report-table" style="border: none; margin-top: 30px;">
          <tr>
            <td style="border: none; width: 50%; padding-right: 20px;">
              <table class="report-table">
                <tr>
                  <th colspan="2">Prepared by:</th>
                </tr>
                <tr>
                  <td class="label">Signature:</td>
                  <td class="line"></td>
                </tr>
                <tr>
                  <td class="label">Name:</td>
                  <td><?= htmlspecialchars($prepared_by_name) ?></td>
                </tr>
                <tr>
                  <td class="label">Date:</td>
                  <td><?= date('F j, Y') ?></td>
                </tr>
              </table>
            </td>
            <td style="border: none; width: 50%; padding-left: 20px;">
              <table class="report-table">
                <tr>
                  <th colspan="2">Reviewed by:</th>
                </tr>
                <tr>
                  <td class="label">Signature:</td>
                  <td class="line"></td>
                </tr>
                <tr>
                  <td class="label">Name:</td>
                  <td><?= htmlspecialchars($reviewed_by_name) ?></td>
                </tr>
                <tr>
                  <td class="label">Date:</td>
                  <td></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <div class="text-end mb-3 no-print">
          <?php
          $print_url = "superadmin-individualreport-print.php?faculty_id=" . urlencode($faculty_id);
          if (!empty($selected_semester)) $print_url .= "&semester=" . urlencode($selected_semester);
          if (!empty($selected_academic_year)) $print_url .= "&academic_year=" . urlencode($selected_academic_year);
          ?>
          <a href="<?= $print_url ?>" target="_blank" class="btn btn-secondary">
            <i class="bi bi-printer"></i> Print Report
          </a>
        </div>

      </div><?php endif; ?>
  </main>

  <?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
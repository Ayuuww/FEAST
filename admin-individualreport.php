<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// --- ✅ START FIX: Get all college/program pairs assigned to this admin ---
$admin_assignments = [];
$stmt_admin_dept = $conn->prepare("SELECT college_name, program_name FROM admin_college WHERE admin_idnumber = ?");
if ($stmt_admin_dept) {
  $stmt_admin_dept->bind_param("s", $admin_id);
  $stmt_admin_dept->execute();
  $result = $stmt_admin_dept->get_result();
  while ($row = $result->fetch_assoc()) {
    $admin_assignments[] = $row; // Store as pairs, e.g., ['college_name' => 'CAS', 'program_name' => 'BSCS']
  }
  $stmt_admin_dept->close();
}
// --- END FIX ---

// Handle case where admin has no assigned college
if (empty($admin_assignments)) {
  // Provide a more user-friendly error
  $_SESSION['msg'] = "You are not assigned to any college or program. Please contact the Superadmin.";
  $_SESSION['msg_type'] = "error";
  header("Location: admin-dashboard.php");
  exit();
}

// Get unique semesters and academic years for filters
$semesters_query = mysqli_query($conn, "
    SELECT DISTINCT semester FROM evaluation WHERE semester IS NOT NULL AND semester != ''
    UNION
    SELECT DISTINCT semester FROM admin_evaluation WHERE semester IS NOT NULL AND semester != ''
    ORDER BY semester ASC
");

$academic_years_query = mysqli_query($conn, "
    SELECT DISTINCT academic_year FROM evaluation WHERE academic_year IS NOT NULL AND academic_year != ''
    UNION
    SELECT DISTINCT academic_year FROM admin_evaluation WHERE academic_year IS NOT NULL AND academic_year != ''
    ORDER BY academic_year DESC
");

$selected_faculty_id = $_GET['faculty_id'] ?? '';
$selected_semester = $_GET['semester'] ?? '';
$selected_academic_year = $_GET['academic_year'] ?? '';

// --- ✅ NEW: Handle Saving Development Plan ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_dev_plan'])) {
  $p_faculty_id = $_POST['p_faculty_id'];
  $p_semester   = $_POST['p_semester'];
  $p_acad_year  = $_POST['p_academic_year'];
  $areas        = $_POST['areas_improvement'];
  $activities   = $_POST['proposed_activities'];
  $action       = $_POST['action_plan'];

  // Insert or Update (Upsert)
  $stmt_save = $conn->prepare("
        INSERT INTO faculty_dev_plan (faculty_id, semester, academic_year, areas_improvement, proposed_activities, action_plan)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        areas_improvement = VALUES(areas_improvement),
        proposed_activities = VALUES(proposed_activities),
        action_plan = VALUES(action_plan)
    ");
  $stmt_save->bind_param("ssssss", $p_faculty_id, $p_semester, $p_acad_year, $areas, $activities, $action);

  if ($stmt_save->execute()) {
    // Refresh to show saved data
    $redirect_url = "?faculty_id=$p_faculty_id&semester=$p_semester&academic_year=$p_acad_year";
    echo "<script>alert('Development Plan Saved Successfully!'); window.location.href='$redirect_url';</script>";
    exit();
  } else {
    echo "<script>alert('Error saving plan.');</script>";
  }
}

// --- ✅ NEW: Fetch Existing Development Plan Data ---
$dev_areas = "";
$dev_activities = "";
$dev_action = "";

if (!empty($selected_faculty_id)) {
  // Default to 'All' if filters are empty to prevent saving to null keys, 
  // or handle strictly. Here we use the selected values.
  $q_sem = $selected_semester ?: 'All';
  $q_ay  = $selected_academic_year ?: 'All';

  $stmt_get_plan = $conn->prepare("SELECT areas_improvement, proposed_activities, action_plan FROM faculty_dev_plan WHERE faculty_id = ? AND semester = ? AND academic_year = ?");
  $stmt_get_plan->bind_param("sss", $selected_faculty_id, $q_sem, $q_ay);
  $stmt_get_plan->execute();
  $stmt_get_plan->bind_result($dev_areas, $dev_activities, $dev_action);
  $stmt_get_plan->fetch();
  $stmt_get_plan->close();
}
// --- Fetch Data for Dropdowns (BEFORE the form) ---

// --- ✅ START FIX: Build complex query to get faculty ---
// This query finds faculty whose home dept/prog matches the admin's assignments
$faculty_query_parts = [];
$params = [];
$types = "";
foreach ($admin_assignments as $assignment) {
  $faculty_query_parts[] = "(college = ? AND program = ?)";
  $params[] = $assignment['college_name'];
  $params[] = $assignment['program_name'];
  $types .= "ss";
}
$faculty_where_sql = implode(' OR ', $faculty_query_parts);

$faculty_query = "SELECT idnumber, first_name, mid_name, last_name FROM faculty 
                  WHERE ($faculty_where_sql) 
                  ORDER BY last_name ASC";

$faculty_list_stmt = $conn->prepare($faculty_query);
$faculty_list_stmt->bind_param($types, ...$params);
$faculty_list_stmt->execute();
$faculty_list_result = $faculty_list_stmt->get_result();
// --- END FIX ---


// --- Default values for display ---
$reviewer_name = "N/A";
$reviewer_position = "N/A";
$prepared_by_name = "N/A";
$prepared_by_position = "N/A";

// ✅ Get Reviewer Info (Dean/Chairperson)
// We need just the college names for this part
$admin_college_only = array_unique(array_column($admin_assignments, 'college_name'));
$placeholders = implode("','", array_map([$conn, 'real_escape_string'], $admin_college_only));
$reviewer_query_str = "
    SELECT first_name, mid_name, last_name, position 
    FROM admin 
    WHERE idnumber IN (
        SELECT admin_idnumber FROM admin_college WHERE college_name IN ('$placeholders')
    ) AND (position LIKE 'Dean%' OR position LIKE 'Chair%' OR position LIKE 'Program Chair%' OR position LIKE 'Director%')
    ORDER BY CASE 
        WHEN position LIKE 'Dean%' THEN 1
        WHEN position LIKE 'Chair%' THEN 2
        WHEN position LIKE 'Program Chair%' THEN 3
        WHEN position LIKE 'Director%' THEN 4
        ELSE 5
    END
    LIMIT 1";

$reviewer_result = $conn->query($reviewer_query_str);
if ($reviewer_result && $rev_row = $reviewer_result->fetch_assoc()) {
  $middle_initial_rev = !empty($rev_row['mid_name']) ? ' ' . substr($rev_row['mid_name'], 0, 1) . '.' : '';
  $reviewer_name = strtoupper(trim("{$rev_row['first_name']}{$middle_initial_rev} {$rev_row['last_name']}"));
  $reviewer_position = $rev_row['position'];
}

// ✅ Get Logged-in Admin Info (for "Prepared by")
$admin_info_query = $conn->prepare("SELECT first_name, mid_name, last_name, position FROM admin WHERE idnumber = ?");
$admin_info_query->bind_param("s", $admin_id);
$admin_info_query->execute();
$admin_info_query->bind_result($a_fname, $a_mname, $a_lname, $a_position);
if ($admin_info_query->fetch()) {
  $middle_initial = !empty($a_mname) ? ' ' . substr($a_mname, 0, 1) . '.' : '';
  $prepared_by_name = strtoupper(trim("$a_fname $middle_initial $a_lname"));
  $prepared_by_position = $a_position;
}
$admin_info_query->close();
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
      background: #fff;
    }

    .report-header {
      text-align: center;
      font-weight: bold;
      font-size: 1.2rem;
      margin-bottom: 20px;
    }

    .section-title {
      font-weight: bold;
      font-size: 1.1rem;
      margin-top: 20px;
      margin-bottom: 10px;
    }

    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .report-table th,
    .report-table td {
      border: 1px solid black;
      padding: 8px;
      vertical-align: top;
    }

    .report-table th {
      font-weight: bold;
      background-color: #f2f2f2;
    }

    .info-table th {
      width: 30%;
      background-color: transparent;
    }

    .info-table td {
      font-weight: bold;
    }

    .summary-table th,
    .summary-table td {
      text-align: center;
    }

    .summary-table .total-row th,
    .summary-table .total-row td {
      font-weight: bold;
    }

    .comment-table th {
      text-align: center;
    }

    .dev-plan-table th {
      background-color: #f2f2f2;
      text-align: left;
    }

    .dev-plan-table td {
      height: 80px;
    }

    .signature-table td {
      padding-top: 15px;
      padding-bottom: 15px;
    }

    .signature-table .label {
      width: 15%;
      font-weight: normal;
    }

    .signature-table .line {
      border-bottom: 1px solid black;
    }

    /* Hide filter card when printing */
    @media print {
      body * {
        visibility: hidden;
      }

      .report-container,
      .report-container * {
        visibility: visible;
      }

      .report-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none;
      }

      .no-print {
        display: none !important;
      }
    }

    /* Add this to your existing CSS */
    .editable-textarea {
      width: 100%;
      min-height: 80px;
      border: 1px solid #ccc;
      /* Visible border on screen */
      padding: 10px;
      font-family: inherit;
      font-size: 1rem;
      resize: vertical;
      background-color: #fff;
    }

    /* Print specific styles for textareas */
    @media print {
      .editable-textarea {
        border: none !important;
        /* Remove border when printing */
        resize: none;
        overflow: visible;
        height: auto !important;
        /* Expand to fit content */
        padding: 0;
      }

      /* Hide the Save Button when printing */
      .btn-save-plan {
        display: none !important;
      }

      /* Ensure the table cell doesn't clip content */
      .dev-plan-table td {
        height: auto !important;
      }
    }
  </style>
</head>

<body>

  <?php include 'admin-header.php' ?>
  <?php include 'admin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle no-print">
      <h1>Individual Faculty Evaluation Reports</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Individual Reports</li>
        </ol>
      </nav>
    </div>
    <div class="card p-4 mb-4 no-print">
      <form method="GET" action="">
        <div class="row align-items-end">
          <div class="col-md-4">
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
          <div class="col-md-3">
            <label for="semester" class="form-label">Semester</label>
            <select class="form-select" name="semester" id="semester">
              <option value="">-- All Semesters --</option>
              <?php while ($sem_row = mysqli_fetch_assoc($semesters_query)): ?>
                <option value="<?= $sem_row['semester'] ?>" <?= ($selected_semester == $sem_row['semester']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($sem_row['semester']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label for="academic_year" class="form-label">Academic Year</label>
            <select class="form-select" name="academic_year" id="academic_year">
              <option value="">-- All Academic Years --</option>
              <?php while ($ay_row = mysqli_fetch_assoc($academic_years_query)): ?>
                <option value="<?= $ay_row['academic_year'] ?>" <?= ($selected_academic_year == $ay_row['academic_year']) ? 'selected' : '' ?>>
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
      // =======================================================
      // All data fetching logic for the selected faculty
      // =======================================================

      // --- Faculty basic info ---
      $stmt = $conn->prepare("SELECT last_name, first_name, mid_name, college, faculty_rank FROM faculty WHERE idnumber = ?");
      $stmt->bind_param("s", $selected_faculty_id);
      $stmt->execute();
      $stmt->bind_result($lname, $fname, $mname, $college, $faculty_rank);
      $stmt->fetch();
      $stmt->close();
      $faculty_name = strtoupper("$lname, $fname $mname");

      // --- Build WHERE clauses for filters ---
      $params_types = "s";
      $params_values = [$selected_faculty_id];
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

      // --- Get Term Info (Semester / Year) ---
      $term_display = ($selected_semester ?: "All Semesters") . " / " . ($selected_academic_year ?: "All Academic Years");

      // --- B. Summary of Average SET Rating ---
      $set_summary_query = "
                SELECT e.subject_code, TRIM(e.student_section) AS student_section,
                       COUNT(*) AS num_students, ROUND(AVG(e.computed_rating), 2) AS avg_rating
                FROM evaluation e WHERE {$eval_where_sql}
                GROUP BY e.subject_code, TRIM(e.student_section) ORDER BY e.subject_code";

      $stmt_set = $conn->prepare($set_summary_query);
      $stmt_set->bind_param($params_types, ...$params_values);
      $stmt_set->execute();
      $result_set = $stmt_set->get_result();

      // --- C. SET and SEF Ratings ---
      $total_students = 0;
      $total_weighted_value = 0;
      while ($row = $result_set->fetch_assoc()) {
        $total_students += $row['num_students'];
        $total_weighted_value += $row['num_students'] * $row['avg_rating'];
      }
      $overall_set = $total_students ? number_format($total_weighted_value / $total_students, 2) : '0.00';
      mysqli_data_seek($result_set, 0); // Reset pointer

      $sef_query = "SELECT AVG(computed_rating) as sef_rating FROM admin_evaluation WHERE {$admin_eval_where_sql}";
      $stmt_sef = $conn->prepare($sef_query);
      $stmt_sef->bind_param($params_types, ...$params_values);
      $stmt_sef->execute();
      $sef_result = $stmt_sef->get_result()->fetch_assoc();
      $sef_rating = number_format($sef_result['sef_rating'] ?? 0, 2);

      // --- D. Student Comments grouped by subject_code ---
      $comments_query = " SELECT subject_code, comment 
                          FROM evaluation 
                          WHERE {$eval_where_sql}
                            AND comment IS NOT NULL 
                            AND TRIM(comment) <> ''
                          ORDER BY subject_code ASC, created_at ASC";
      $stmt_comments = $conn->prepare($comments_query);
      $stmt_comments->bind_param($params_types, ...$params_values);
      $stmt_comments->execute();
      $comments_q = $stmt_comments->get_result();

      // Group comments by subject_code
      $grouped_comments = [];
      while ($row = $comments_q->fetch_assoc()) {
        $subj = $row['subject_code'];
        if (!isset($grouped_comments[$subj])) {
          $grouped_comments[$subj] = [];
        }
        $grouped_comments[$subj][] = $row['comment'];
      }

      // --- Supervisor Comments ---
      $sup_comments_query = "SELECT comments FROM admin_evaluation WHERE {$admin_eval_where_sql} AND comments IS NOT NULL AND TRIM(comments) <> '' LIMIT 5";
      $stmt_sup_comments = $conn->prepare($sup_comments_query);
      $stmt_sup_comments->bind_param($params_types, ...$params_values);
      $stmt_sup_comments->execute();
      $sup_comments_q = $stmt_sup_comments->get_result();
      ?>

      <div class="report-container">

        <div class="report-header">INDIVIDUAL FACULTY EVALUATION REPORT</div>

        <div class="section-title">A. Faculty Information</div>
        <table class="report-table info-table">
          <tr>
            <th>Name of Faculty Evaluated</th>
            <td>: <?= htmlspecialchars($faculty_name) ?></td>
          </tr>
          <tr>
            <th>college/College</th>
            <td>: <?= htmlspecialchars($college) ?></td>
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
        <p style="font-size: 0.9em; font-style: italic;">
          <b>Computation:</b><br>
          <b>Step 1:</b> Get the average SET rating for each class.<br>
          <b>Step 2:</b> Multiply the number of students in each class with its average SET rating to get the Weighted Value per class.<br>
          <b>Step 3:</b> Get the total number of students and the total weighted value.
        </p>
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
        <p style="font-size: 0.9em; font-style: italic;">
          <b>Computation:</b> Calculate the Overall SET Rating by dividing the total weighted value by the total number of students.
        </p>
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

            <?php if (empty($grouped_comments)): ?>
              <tr>
                <td style="text-align: center; font-style: italic;">
                  No student comments available.
                </td>
              </tr>
            <?php else: ?>

              <?php foreach ($grouped_comments as $subject_code => $comments): ?>
                <tr>
                  <td style="background: #f8f9fa; font-weight: bold; font-size: 1rem;">Subject Code:
                    <?= htmlspecialchars($subject_code) ?>
                  </td>
                </tr>

                <?php
                $count = 1;
                foreach ($comments as $comment):
                ?>
                  <tr>
                    <td style="text-align: left;">
                      <b><?= $count++ ?>.</b> <?= htmlspecialchars($comment) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>

              <?php endforeach; ?>

            <?php endif; ?>

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

        <div class="section-title">E. Development Plan (to be jointly accomplished by the Supervisor and Faculty)</div>

        <form method="POST" action="">
          <input type="hidden" name="p_faculty_id" value="<?= htmlspecialchars($selected_faculty_id) ?>">
          <input type="hidden" name="p_semester" value="<?= htmlspecialchars($selected_semester ?: 'All') ?>">
          <input type="hidden" name="p_academic_year" value="<?= htmlspecialchars($selected_academic_year ?: 'All') ?>">
          <input type="hidden" name="save_dev_plan" value="1">

          <table class="report-table dev-plan-table">
            <tr>
              <th>Areas for Improvement</th>
            </tr>
            <tr>
              <td style="padding: 0;">
                <textarea
                  name="areas_improvement"
                  class="editable-textarea"
                  placeholder="Enter areas for improvement here..."><?= htmlspecialchars($dev_areas) ?></textarea>
              </td>
            </tr>
            <tr>
              <th>Proposed Learning and Development Activities</th>
            </tr>
            <tr>
              <td style="padding: 0;">
                <textarea
                  name="proposed_activities"
                  class="editable-textarea"
                  placeholder="Enter proposed activities here..."><?= htmlspecialchars($dev_activities) ?></textarea>
              </td>
            </tr>
            <tr>
              <th>Action Plan</th>
            </tr>
            <tr>
              <td style="padding: 0;">
                <textarea
                  name="action_plan"
                  class="editable-textarea"
                  placeholder="Enter action plan here..."><?= htmlspecialchars($dev_action) ?></textarea>
              </td>
            </tr>
          </table>

          <div class="text-end mt-2 mb-4 no-print">
            <button type="submit" class="btn btn-primary btn-save-plan">
              <i class="bi bi-save"></i> Save Development Plan
            </button>
          </div>
        </form>

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
                  <td><?= htmlspecialchars($reviewer_name) ?></td>
                </tr>
                <tr>
                  <td class="label">Date:</td>
                  <td><?= date('F j, Y') ?></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <div class="text-end mb-3 no-print">
          <?php
          // 1. Build the URL for the FPDF printing script
          $print_url = "admin-individualreport-printing.php?faculty_id=" . urlencode($selected_faculty_id);

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

      </div><?php endif; ?>
  </main>

  <?php include 'footer.php' ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
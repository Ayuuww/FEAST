<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

if (!isset($_GET['faculty_id'])) {
  die("No faculty selected.");
}

$faculty_id = $_GET['faculty_id'];
$superadmin_id = $_SESSION['idnumber'];
$filter_semester = $_GET['semester'] ?? '';
$filter_academic_year = $_GET['academic_year'] ?? '';

// =======================================================
// 1. SECURE & FILTERED DATA FETCHING
// =======================================================

// --- Prepared By Info (Superadmin) ---
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

// --- Faculty Info ---
// --- Faculty basic info ---
$stmt = $conn->prepare("SELECT last_name, first_name, mid_name, department, program, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($lname, $fname, $mname, $department, $faculty_program, $faculty_rank); // Added $faculty_program
$stmt->fetch();
$stmt->close();
$faculty_name = strtoupper(trim("$fname $mname $lname"));
$dept_display = strtoupper($department);
$rank_display = ucwords($faculty_rank);
$term_display = ($filter_semester ?: "All Semesters") . " / " . ($filter_academic_year ?: "All Academic Years");

// --- Build WHERE clauses for filters ---
$params_types = "s";
$params_values = [$faculty_id];
$eval_where_clauses = ["faculty_id = ?"];
$admin_eval_where_clauses = ["evaluatee_id = ?"];

if (!empty($filter_semester)) {
  $eval_where_clauses[] = "semester = ?";
  $admin_eval_where_clauses[] = "semester = ?";
  $params_types .= "s";
  $params_values[] = $filter_semester;
}
if (!empty($filter_academic_year)) {
  $eval_where_clauses[] = "academic_year = ?";
  $admin_eval_where_clauses[] = "academic_year = ?";
  $params_types .= "s";
  $params_values[] = $filter_academic_year;
}
$eval_where_sql = implode(' AND ', $eval_where_clauses);
$admin_eval_where_sql = implode(' AND ', $admin_eval_where_clauses);

// --- SET Summary Data ---
$set_summary_sql = "
    SELECT e.subject_code, TRIM(e.student_section) AS student_section,
           COUNT(*) AS num_students, ROUND(AVG(e.computed_rating), 2) AS avg_rating
    FROM evaluation e WHERE {$eval_where_sql}
    GROUP BY e.subject_code, TRIM(e.student_section) ORDER BY e.subject_code";

$stmt_set = $conn->prepare($set_summary_sql);
$stmt_set->bind_param($params_types, ...$params_values);
$stmt_set->execute();
$result_set = $stmt_set->get_result();

$total_students = 0;
$total_weighted_value = 0;
$summary_data = [];
while ($row = $result_set->fetch_assoc()) {
  $total_students += $row['num_students'];
  $total_weighted_value += $row['num_students'] * $row['avg_rating'];
  $summary_data[] = $row;
}
$stmt_set->close();
$overall_set = $total_students ? number_format($total_weighted_value / $total_students, 2) : '0.00';

// --- SEF Rating ---
$sef_query = "SELECT AVG(computed_rating) as sef_rating FROM admin_evaluation WHERE {$admin_eval_where_sql}";
$stmt_sef = $conn->prepare($sef_query);
$stmt_sef->bind_param($params_types, ...$params_values);
$stmt_sef->execute();
$sef_result = $stmt_sef->get_result()->fetch_assoc();
$sef_rating = number_format($sef_result['sef_rating'] ?? 0, 2);
$stmt_sef->close();

// --- Student & Supervisor Comments ---
$comments_query = "
    SELECT subject_code, comment 
    FROM evaluation
    WHERE {$eval_where_sql}
      AND comment IS NOT NULL
      AND TRIM(comment) <> ''
    ORDER BY subject_code ASC, created_at ASC
";
$stmt_comments = $conn->prepare($comments_query);
$stmt_comments->bind_param($params_types, ...$params_values);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();
$grouped_student_comments = [];

while ($row = $result_comments->fetch_assoc()) {
  $subj = $row['subject_code'];
  if (!isset($grouped_student_comments[$subj])) {
    $grouped_student_comments[$subj] = [];
  }
  $grouped_student_comments[$subj][] = $row['comment'];
}
$stmt_comments->close();

$sup_comments_query = "SELECT comments FROM admin_evaluation WHERE {$admin_eval_where_sql} AND comments IS NOT NULL AND TRIM(comments) <> '' LIMIT 5";
$stmt_sup_comments = $conn->prepare($sup_comments_query);
$stmt_sup_comments->bind_param($params_types, ...$params_values);
$stmt_sup_comments->execute();
$supervisor_comments = $stmt_sup_comments->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_sup_comments->close();

// --- Reviewed By Name (FIXED QUERY) ---
$reviewed_by_name = "N/A";
// $dept and $faculty_program come from the faculty info query above
$rev_stmt = $conn->prepare("
SELECT a.first_name, a.mid_name, a.last_name
FROM admin a
INNER JOIN admin_departments ad ON a.idnumber = ad.admin_idnumber
WHERE ad.department_name = ?
AND (a.position LIKE '%Dean%' OR a.position LIKE '%Chair%' OR a.position LIKE '%Program Head%' OR a.position LIKE '%Director%')
ORDER BY
-- Priority 1: Admin matches BOTH department and program
CASE WHEN ad.program_name = ? THEN 1 ELSE 2 END ASC,
-- Priority 2: Deans/Directors first, then Chairs/Heads
CASE WHEN a.position LIKE '%Dean%' OR a.position LIKE '%Director%' THEN 1 ELSE 2 END ASC
LIMIT 1
");
// Bind both the department and the faculty's program
$rev_stmt->bind_param("ss", $department, $faculty_program);
$rev_stmt->execute();
$rev_stmt->bind_result($rev_fname, $rev_mname, $rev_lname);
if ($rev_stmt->fetch()) {

  $middle_initial = '';
  if (!empty($rev_mname)) {
    $middle_initial = ' ' . substr($rev_mname, 0, 1) . '.'; // Add space, initial, and period
  }

  $reviewed_by_name = strtoupper("$rev_fname $middle_initial $rev_lname");
}
$rev_stmt->close();

// =======================================================
// 2. GENERATE PDF DOCUMENT
// =======================================================

require 'superadmin-printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4', $conn); // <-- pass $conn here
$pdf->department = $department;
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'INDIVIDUAL FACULTY EVALUATION REPORT', 0, 1, 'C');
$pdf->Ln(5);

// --- Section A ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'A. Faculty Information', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 7, 'Name of Faculty Evaluated', 1, 0);
$pdf->Cell(5, 7, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $faculty_name, 1, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 7, 'Department/College', 1, 0);
$pdf->Cell(5, 7, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $dept_display, 1, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 7, 'Current Faculty Rank', 1, 0);
$pdf->Cell(5, 7, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $rank_display, 1, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 14, 'Semester/Term & Academic Year', 1, 0);
$pdf->Cell(5, 14, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 14, $term_display, 1, 1);

// --- Section B ---
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'B. Summary of Average SET Rating', 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 4, "Computation:\nStep 1: Get the average SET rating for each class.\nStep 2: Multiply the number of students in each class with its average SET rating to get the Weighted Value per class.\nStep 3: Get the total number of students and the total weighted value.", 0, 'L');
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(40, 12, "(1)\nCourse Code", 1, 0, 'C', true);
$pdf->Cell(30, 12, "(2)\nSection", 1, 0, 'C', true);
$pdf->Cell(30, 12, "(3)\nNo. of Students", 1, 0, 'C', true);
$pdf->Cell(40, 12, "(4)\nAve. SET Rating", 1, 0, 'C', true);
$pdf->Cell(40, 12, "(3 x 4)\nWeighted Value", 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 9);
foreach ($summary_data as $row) {
  $pdf->Cell(40, 6, $row['subject_code'], 1, 0, 'C');
  $pdf->Cell(30, 6, $row['student_section'], 1, 0, 'C');
  $pdf->Cell(30, 6, $row['num_students'], 1, 0, 'C');
  $pdf->Cell(40, 6, number_format($row['avg_rating'], 2), 1, 0, 'C');
  $pdf->Cell(40, 6, number_format($row['num_students'] * $row['avg_rating'], 2), 1, 1, 'C');
}
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(70, 7, 'TOTAL', 1, 0, 'C');
$pdf->Cell(30, 7, $total_students, 1, 0, 'C');
$pdf->Cell(40, 7, 'TOTAL', 1, 0, 'C');
$pdf->Cell(40, 7, number_format($total_weighted_value, 2), 1, 1, 'C');

// --- Section C ---
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'C. SET and SEF Ratings', 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 4, "Computation: Calculate the Overall SET Rating by dividing the total weighted value by the total number of students.", 0, 'L');
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 9);
$label_width = 60;
$rating_width = 60;
$cell_height = 7;
$startX = $pdf->GetX();
$startY = $pdf->GetY();
$pdf->Cell($label_width, $cell_height * 2, 'OVERALL RATING', 1, 0, 'C');
$pdf->SetFillColor(230, 230, 230);
$pdf->SetXY($startX + $label_width, $startY);
$pdf->Cell($rating_width, $cell_height, 'SET Rating', 1, 0, 'C', true);
$pdf->Cell($rating_width, $cell_height, '*SEF Rating', 1, 1, 'C', true);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX($startX + $label_width);
$pdf->Cell($rating_width, $cell_height, $overall_set, 1, 0, 'C');
$pdf->Cell($rating_width, $cell_height, $sef_rating, 1, 1, 'C');
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, '*Note: rating given by the supervisor using the SEF instrument', 0, 1, 'C');

// --- Section D ---
// --- Section D ---

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'D. Summary of Qualitative Comments and Suggestions', 0, 1);

// ================================
// STUDENT COMMENTS (GROUPED)
// ================================
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 7, 'Comments and Suggestions from the Students', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 9);

if (empty($grouped_student_comments)) {
  $pdf->Cell(0, 7, 'No student comments available.', 1, 1, 'C');
} else {
  foreach ($grouped_student_comments as $subj => $comments) {

    // Subject header
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(0, 6, "SUBJECT: " . $subj, 1, 1, 'L', true);

    // Comments list
    $pdf->SetFont('Arial', '', 9);
    $count = 1;

    foreach ($comments as $cmt) {
      $pdf->MultiCell(0, 5, "   {$count}. " . $cmt, 1, 'L');
      $count++;
    }

    $pdf->Ln(1);
  }
}
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 7, 'Comments and Suggestions from the Supervisor', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 9);
if (empty($supervisor_comments)) {
  $pdf->Cell(0, 7, 'No supervisor comments available.', 1, 1, 'C');
} else {
  foreach ($supervisor_comments as $i => $row) {
    $pdf->MultiCell(0, 5, ($i + 1) . '. ' . $row['comments'], 'LRB', 'L');
  }
}

// --- Section E ---
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'E. Development Plan (to be jointly accomplished by the Supervisor and Faculty)', 0, 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 7, 'Areas for Improvement', 1, 1, 'L', true);
$pdf->Cell(0, 25, '', 1, 1);
$pdf->Cell(0, 7, 'Proposed Learning and Development Activities', 1, 1, 'L', true);
$pdf->Cell(0, 25, '', 1, 1);
$pdf->Cell(0, 7, 'Action Plan', 1, 1, 'L', true);
$pdf->Cell(0, 25, '', 1, 1);

// --- Signatories ---
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$y_pos = $pdf->GetY();
$left_margin = 15;
$right_box_start = 110;
$box_width = 90;
$label_width = 25;
$value_width = $box_width - $label_width;

$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY($left_margin, $y_pos);
$pdf->Cell($box_width, 7, 'Prepared by:', 'LTR', 1, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->SetX($left_margin);
$pdf->Cell($label_width, 12, 'Signature:', 'L', 0);
$pdf->Cell($value_width, 12, '', 'R', 1);
$pdf->SetX($left_margin);
$pdf->Cell($label_width, 7, 'Name:', 'L', 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($value_width, 7, $prepared_by_name, 'R', 1);
$pdf->SetFont('Arial', '', 10);
$pdf->SetX($left_margin);
$pdf->Cell($label_width, 7, 'Date:', 'LB', 0);
$pdf->Cell($value_width, 7, date('F j, Y'), 'RB', 1);

$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY($right_box_start, $y_pos);
$pdf->Cell($box_width, 7, 'Reviewed by:', 'LTR', 1, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->SetX($right_box_start);
$pdf->Cell($label_width, 12, 'Signature:', 'L', 0);
$pdf->Cell($value_width, 12, '', 'R', 1);
$pdf->SetX($right_box_start);
$pdf->Cell($label_width, 7, 'Name:', 'L', 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($value_width, 7, $reviewed_by_name, 'R', 1);
$pdf->SetFont('Arial', '', 10);
$pdf->SetX($right_box_start);
$pdf->Cell($label_width, 7, 'Date:', 'LB', 0);
$pdf->Cell($value_width, 7, '', 'RB', 1);

$pdf->Output('I', 'Individual-Faculty-Report.pdf');

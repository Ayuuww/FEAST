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
$admin_id = $_SESSION['idnumber'];
$filter_semester = $_GET['semester'] ?? '';
$filter_academic_year = $_GET['academic_year'] ?? '';

// =======================================================
// ✅ 1. SECURE & FILTERED DATA FETCHING
// =======================================================

// --- Prepared By Info (Superadmin) ---
$prepared_by_name = "N/A";
$prep_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM superadmin WHERE idnumber = ?");
$prep_stmt->bind_param("s", $admin_id);
$prep_stmt->execute();
$prep_stmt->bind_result($prep_fname, $prep_mname, $prep_lname);
if ($prep_stmt->fetch()) {
    $prepared_by_name = trim("$prep_fname $prep_mname $prep_lname");
}
$prep_stmt->close();

// --- Faculty Info ---
$stmt = $conn->prepare("SELECT last_name, first_name, mid_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($lname, $fname, $mname, $dept, $faculty_rank);
$stmt->fetch();
$stmt->close();
$faculty_name = strtoupper(trim("$fname $mname $lname"));
$dept_display = strtoupper($dept);
$rank_display = ucwords($faculty_rank);

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

$semester_display = $filter_semester ?: "All Semesters";
$ay_display = $filter_academic_year ?: "All Academic Years";

// --- SET Summary Data ---
$set_summary_sql = "
    SELECT subject_code, TRIM(student_section) AS student_section, COUNT(*) AS num_students,
           AVG(computed_rating) AS avg_rating
    FROM evaluation WHERE {$eval_where_sql}
    GROUP BY subject_code, TRIM(student_section)";

$stmt_set_summary = $conn->prepare($set_summary_sql);
$stmt_set_summary->bind_param($params_types, ...$params_values);
$stmt_set_summary->execute();
$result_set_summary = $stmt_set_summary->get_result();

$total_students = 0;
$total_weighted_value = 0;
$summary = [];
while ($row = $result_set_summary->fetch_assoc()) {
    $weighted = $row['num_students'] * $row['avg_rating'];
    $total_students += $row['num_students'];
    $total_weighted_value += $weighted;
    $summary[] = [
        'subject_code' => $row['subject_code'], 'section' => $row['student_section'],
        'num_students' => $row['num_students'], 'avg_rating' => number_format($row['avg_rating'], 2),
        'weighted' => number_format($weighted, 2)
    ];
}
$stmt_set_summary->close();
$overall_set = $total_students ? number_format($total_weighted_value / $total_students, 2) : '0.00';

// --- SEF Rating ---
$sef_sql = "SELECT AVG(computed_rating) as sef_rating FROM admin_evaluation WHERE {$admin_eval_where_sql}";
$stmt_sef = $conn->prepare($sef_sql);
$stmt_sef->bind_param($params_types, ...$params_values);
$stmt_sef->execute();
$sef_result = $stmt_sef->get_result()->fetch_assoc();
$sef_rating = number_format($sef_result['sef_rating'] ?? 0, 2);
$stmt_sef->close();

// --- Comments ---
$comments_sql = "SELECT comment FROM evaluation WHERE {$eval_where_sql} AND comment IS NOT NULL AND comment <> '' LIMIT 5";
$stmt_comments = $conn->prepare($comments_sql);
$stmt_comments->bind_param($params_types, ...$params_values);
$stmt_comments->execute();
$comments_q = $stmt_comments->get_result();
$comments = [];
while ($row = $comments_q->fetch_assoc()) {
    $comments[] = $row['comment'];
}
$stmt_comments->close();

// --- Reviewed By Name (FIXED QUERY) ---
$reviewed_by_name = "N/A";
$rev_stmt = $conn->prepare("
    SELECT a.first_name, a.mid_name, a.last_name
    FROM admin a
    INNER JOIN admin_departments ad ON a.idnumber = ad.admin_idnumber
    WHERE ad.department_name = ?
      AND (a.position LIKE '%Dean%' OR a.position LIKE '%Chair%' OR a.position LIKE '%Program Head%')
    ORDER BY CASE WHEN a.position LIKE '%Dean%' THEN 1 ELSE 2 END
    LIMIT 1
");
$rev_stmt->bind_param("s", $dept);
$rev_stmt->execute();
$rev_stmt->bind_result($rev_fname, $rev_mname, $rev_lname);
if ($rev_stmt->fetch()) {
    $reviewed_by_name = trim("$rev_fname $rev_mname $rev_lname");
}
$rev_stmt->close();

// =======================================================
// 2. GENERATE PDF DOCUMENT
// =======================================================

require 'superadmin-printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->department = $dept;
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'INDIVIDUAL FACULTY EVALUATION REPORT', 0, 1, 'C');

// Section A: Faculty Info
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'A. Faculty Information', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(80, 8, 'Name of Faculty Evaluated:', 1);
$pdf->Cell(110, 8, $faculty_name, 1, 1);
$pdf->Cell(80, 8, 'Department/College:', 1);
$pdf->Cell(110, 8, $dept_display, 1, 1);
$pdf->Cell(80, 8, 'Current Faculty Rank:', 1);
$pdf->Cell(110, 8, $rank_display, 1, 1);
$pdf->Cell(80, 8, 'Semester / Academic Year:', 1);
$pdf->Cell(110, 8, "$semester_display / $ay_display", 1, 1);

// Section B: Summary
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'B. Summary of Average SET Rating', 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 8, 'Course Code', 1, 0, 'C');
$pdf->Cell(30, 8, 'Section', 1, 0, 'C');
$pdf->Cell(30, 8, 'No. of Students', 1, 0, 'C');
$pdf->Cell(40, 8, 'Avg. SET Rating', 1, 0, 'C');
$pdf->Cell(40, 8, 'Weighted Value', 1, 1, 'C');
$pdf->SetFont('Arial', '', 10);
foreach ($summary as $row) {
    $pdf->Cell(35, 8, $row['subject_code'], 1, 0, 'C');
    $pdf->Cell(30, 8, $row['section'], 1, 0, 'C');
    $pdf->Cell(30, 8, $row['num_students'], 1, 0, 'C');
    $pdf->Cell(40, 8, $row['avg_rating'], 1, 0, 'C');
    $pdf->Cell(40, 8, $row['weighted'], 1, 1, 'C');
}
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(65, 8, 'TOTAL', 1, 0, 'C');
$pdf->Cell(30, 8, $total_students, 1, 0, 'C');
$pdf->Cell(40, 8, '', 1, 0, 'C');
$pdf->Cell(40, 8, number_format($total_weighted_value, 2), 1, 1, 'C');

// Section C
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'C. SET and SEF Ratings', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(80, 8, 'OVERALL SET Rating', 1);
$pdf->Cell(110, 8, $overall_set, 1, 1);
$pdf->Cell(80, 8, 'Supervisor (SEF) Rating', 1);
$pdf->Cell(110, 8, $sef_rating, 1, 1);

// Section D
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'D. Summary of Qualitative Comments and Suggestions', 0, 1);
$pdf->SetFont('Arial', '', 10);
if (count($comments) > 0) {
    foreach ($comments as $index => $comment) {
        $pdf->MultiCell(0, 8, ($index + 1) . ". " . $comment, 1);
    }
} else {
    $pdf->Cell(0, 8, 'No comments available.', 1, 1);
}

// Section E
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'E. Development Plan', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, 'Areas for Improvement:', 1, 1);
$pdf->Cell(0, 20, '', 1, 1);
$pdf->Cell(0, 8, 'Proposed Learning and Development Activities:', 1, 1);
$pdf->Cell(0, 20, '', 1, 1);
$pdf->Cell(0, 8, 'Action Plan:', 1, 1);
$pdf->Cell(0, 20, '', 1, 1);

// Signatories
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);

$y = $pdf->GetY();
$pdf->MultiCell(50, 5, "Prepared by:\n(Staff Signature):", 0);
$pdf->SetXY(65, $y);
$pdf->Cell(40, 10, 'Name:', 0, 0);
$pdf->Cell(85, 10, $prepared_by_name, 0, 1);

$pdf->SetY($y + 10);
$pdf->Cell(65, 10, '', 0, 0); // Spacer
$pdf->Cell(40, 10, 'Date:', 0, 0);
$pdf->Cell(85, 10, date('F j, Y'), 0, 1);

$pdf->Ln(5);
$y = $pdf->GetY();
$pdf->MultiCell(50, 5, "Reviewed by:\n(Authorized Official):", 0);
$pdf->SetXY(65, $y);
$pdf->Cell(40, 10, 'Name:', 0, 0);
$pdf->Cell(85, 10, $reviewed_by_name, 0, 1);

$pdf->SetY($y + 10);
$pdf->Cell(65, 10, '', 0, 0); // Spacer
$pdf->Cell(40, 10, 'Date:', 0, 0);
$pdf->Cell(85, 10, '', 0, 1); // Blank Date

$pdf->Output('I', 'Individual-Faculty-Evaluation.pdf');
?>
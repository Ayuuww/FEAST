<?php
session_start();
require('fpdf/fpdf.php');
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized.");
}
if (!isset($_GET['faculty_id'])) {
    die("Missing faculty ID.");
}

$faculty_id = $_GET['faculty_id'];
$selected_semester = $_GET['semester'] ?? '';
$selected_academic_year = $_GET['academic_year'] ?? '';

// =======================================================
// 1. FETCH ALL DATA (Your existing logic is correct)
// =======================================================

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

// Get faculty info
$fname = $mname = $lname = $dept = $rank = '';
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, college, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($fname, $mname, $lname, $dept, $rank);
$stmt->fetch();
$stmt->close();
$middle_initial = '';
if (!empty($mname)) {
    $middle_initial = ' ' . substr($mname, 0, 1) . '.'; // Add space, initial, and period
}

$full_name = strtoupper(trim("$fname $middle_initial $lname"));

$dept_display = strtoupper($dept);
$rank_display = ucwords($rank);

// Get semester/year for display
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

// Supervisor Name
$evaluator_name = 'N/A';
$stmt_supervisor = $conn->prepare("
    SELECT a.first_name, a.mid_name, a.last_name FROM admin a
    INNER JOIN admin_college ad ON a.idnumber = ad.admin_idnumber
    WHERE ad.college_name = ? AND (a.position LIKE 'Dean%' OR a.position LIKE 'Chair%' OR a.position LIKE 'Program Chair%' OR a.position LIKE 'Director%')
    ORDER BY CASE WHEN a.position LIKE 'Dean%' THEN 1 ELSE 2 END LIMIT 1");
$stmt_supervisor->bind_param("s", $dept);
$stmt_supervisor->execute();
$stmt_supervisor->bind_result($sfn, $smn, $sln);
if ($stmt_supervisor->fetch()) {

    $middle_initial = '';
    if (!empty($smn)) {
        $middle_initial = ' ' . substr($smn, 0, 1) . '.'; // Add space, initial, and period
    }

    $evaluator_name = strtoupper(trim("$sfn $middle_initial $sln"));
}
$stmt_supervisor->close();


// =======================================================
// 2. GENERATE PDF DOCUMENT
// =======================================================

require 'printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

// --- Document Title ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'FACULTY EVALUATION ACKNOWLEDGEMENT FORM', 0, 1, 'C');
$pdf->Ln(8);

// --- Section: FACULTY MEMBER INFORMATION ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY MEMBER INFORMATION', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 7, 'Name of Faculty', 1, 0);
$pdf->Cell(5, 7, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $full_name, 1, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 7, 'College', 1, 0);
$pdf->Cell(5, 7, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $dept_display, 1, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 7, 'Current Faculty Rank', 1, 0);
$pdf->Cell(5, 7, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $rank_display, 1, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 7, 'Semester/Term & Academic Year', 1, 0);
$pdf->Cell(5, 7, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, "$sem / $sy", 1, 1);

// --- Section: FACULTY EVALUATION SUMMARY ---
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY EVALUATION SUMMARY', 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'Overall Rating', 1, 1, 'C');
$pdf->Cell(100, 7, 'Student Evaluation of Teachers (SET)', 1, 0, 'C');
$pdf->Cell(90, 7, "Supervisor's Evaluation of Faculty (SAF)", 1, 1, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(100, 8, $set_avg, 1, 0, 'C');
$pdf->Cell(90, 8, $sef_avg, 1, 1, 'C');

// --- Acknowledgement Paragraph ---
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, "I acknowledge that I have received and reviewed the faculty evaluation conducted for the period mentioned above. I understand that my signature below does not necessarily indicate agreement with the evaluation but confirms that I have been given the opportunity to discuss it with my supervisor.");

// --- Section: SUPERVISOR SIGNATURE ---
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(45, 45, 45); // Dark grey
$pdf->SetTextColor(255, 255, 255); // White text
$pdf->Cell(0, 8, 'SUPERVISOR', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0); // Reset to black
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 10, 'Signature', 'L', 0);
$pdf->Cell(5, 10, ':', 0, 0, 'C');
$pdf->Cell(0, 10, '', 'R', 1);

$pdf->Cell(35, 7, 'Name', 'L', 0);
$pdf->Cell(5, 7, ':', 0, 0, 'C');
$pdf->Cell(0, 7, $evaluator_name, 'R', 1);

$pdf->Cell(35, 7, 'Date Signed', 'LB', 0);
$pdf->Cell(5, 7, ':', 'B', 0, 'C');
$pdf->Cell(0, 7, '', 'RB', 1);


// --- Section: FACULTY SIGNATURE ---
// $pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(45, 45, 45);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 8, 'FACULTY', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 10, 'Signature', 'L', 0);
$pdf->Cell(5, 10, ':', 0, 0, 'C');
$pdf->Cell(0, 10, '', 'R', 1);

$pdf->Cell(35, 7, 'Name', 'L', 0);
$pdf->Cell(5, 7, ':', 0, 0, 'C');
$pdf->Cell(0, 7, $full_name, 'R', 1);

$pdf->Cell(35, 7, 'Date Signed', 'LB', 0);
$pdf->Cell(5, 7, ':', 'B', 0, 'C');
$pdf->Cell(0, 7, '', 'RB', 1);

// --- Final Output ---
$pdf->Output('I', 'Acknowledgement-Form.pdf');
exit;

<?php
session_start();
require('fpdf/fpdf.php');
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    die("Unauthorized.");
}
if (!isset($_GET['faculty_id'])) {
    die("Missing faculty ID.");
}

$faculty_id = $_GET['faculty_id'];
$selected_semester = $_GET['semester'] ?? '';
$selected_academic_year = $_GET['academic_year'] ?? '';

// =======================================================
// 1. FETCH DATA
// =======================================================

// Faculty Info
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($fname, $mname, $lname, $dept, $faculty_rank);
$stmt->fetch();
$stmt->close();

$full_name = strtoupper(trim("$fname $mname $lname"));
$dept_display = strtoupper($dept);
$rank_display = ucwords($faculty_rank);

// Semester/Academic Year
$sem = $selected_semester ?: 'All Semesters';
$sy = $selected_academic_year ?: 'All Academic Years';

// SET Average
$set_avg = '0.00';
$stmt_set = $conn->prepare("SELECT AVG(computed_rating) AS avg FROM evaluation WHERE faculty_id = ?");
$stmt_set->bind_param("s", $faculty_id);
$stmt_set->execute();
$stmt_set->bind_result($avg);
if ($stmt_set->fetch() && $avg !== null) $set_avg = number_format($avg, 2);
$stmt_set->close();

// SEF Average
$sef_avg = '0.00';
$stmt_sef = $conn->prepare("SELECT AVG(computed_rating) AS avg FROM admin_evaluation WHERE evaluatee_id = ?");
$stmt_sef->bind_param("s", $faculty_id);
$stmt_sef->execute();
$stmt_sef->bind_result($avg);
if ($stmt_sef->fetch() && $avg !== null) $sef_avg = number_format($avg, 2);
$stmt_sef->close();

// Supervisor Name (Dean/Chair/Program Chair)
$evaluator_name = 'N/A';
$stmt_supervisor = $conn->prepare("
    SELECT a.first_name, a.mid_name, a.last_name 
    FROM admin a
    INNER JOIN admin_departments ad ON a.idnumber = ad.admin_idnumber
    WHERE ad.department_name = ?
      AND (a.position LIKE 'Dean%' OR a.position LIKE 'Chair%' OR a.position LIKE 'Program Chair%' OR a.position LIKE 'Director%')
    ORDER BY 
      CASE 
        WHEN a.position LIKE 'Dean%' THEN 1 
        WHEN a.position LIKE 'Chair%' THEN 2 
        ELSE 3 
      END
    LIMIT 1
");
$stmt_supervisor->bind_param("s", $dept);
$stmt_supervisor->execute();
$stmt_supervisor->bind_result($sfn, $smn, $sln);
if ($stmt_supervisor->fetch()) {
    $evaluator_name = strtoupper(trim("$sfn $smn $sln"));
}
$stmt_supervisor->close();

// =======================================================
// 2. GENERATE PDF
// =======================================================

require 'superadmin-printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4', $conn); // <-- pass $conn here
$pdf->department = $dept;
$pdf->AddPage();

// --- Title ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'FACULTY EVALUATION ACKNOWLEDGEMENT FORM', 0, 1, 'C');
$pdf->Ln(8);

// --- Section: Faculty Info ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY MEMBER INFORMATION', 0, 1);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(65, 7, 'Name of Faculty', 1, 0);
$pdf->Cell(5, 7, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $full_name, 1, 1);

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
$pdf->Cell(65, 7, 'Semester/Term & Academic Year', 1, 0);
$pdf->Cell(5, 7, ':', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, "$sem / $sy", 1, 1);

// --- Section: Summary ---
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY EVALUATION SUMMARY', 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'Overall Rating', 1, 1, 'C');
$pdf->Cell(100, 7, 'Student Evaluation of Teachers (SET)', 1, 0, 'C');
$pdf->Cell(90, 7, "Supervisor's Evaluation of Faculty (SEF)", 1, 1, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(100, 8, $set_avg, 1, 0, 'C');
$pdf->Cell(90, 8, $sef_avg, 1, 1, 'C');

// --- Acknowledgement ---
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5,
    "I acknowledge that I have received and reviewed the faculty evaluation conducted for the period mentioned above. I understand that my signature below does not necessarily indicate agreement with the evaluation but confirms that I have been given the opportunity to discuss it with my supervisor."
);

// --- Section: Supervisor ---
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(45, 45, 45);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 8, 'SUPERVISOR', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 10, 'Signature', 'L', 0);
$pdf->Cell(5, 10, ':', 0, 0, 'C');
$pdf->Cell(0, 10, '', 'R', 1);

$pdf->Cell(35, 7, 'Name', 'L', 0);
$pdf->Cell(5, 7, ':', 0, 0, 'C');
$pdf->Cell(0, 7, $evaluator_name, 'R', 1);

$pdf->Cell(35, 7, 'Date Signed', 'LB', 0);
$pdf->Cell(5, 7, ':', 'B', 0, 'C');
$pdf->Cell(0, 7, '', 'RB', 1);

// --- Section: Faculty ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(45, 45, 45);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 8, 'FACULTY', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 10, 'Signature', 'L', 0);
$pdf->Cell(5, 10, ':', 0, 0, 'C');
$pdf->Cell(0, 10, '', 'R', 1);

$pdf->Cell(35, 7, 'Name', 'L', 0);
$pdf->Cell(5, 7, ':', 0, 0, 'C');
$pdf->Cell(0, 7, $full_name, 'R', 1);

$pdf->Cell(35, 7, 'Date Signed', 'LB', 0);
$pdf->Cell(5, 7, ':', 'B', 0, 'C');
$pdf->Cell(0, 7, '', 'RB', 1);

// --- Output ---
$pdf->Output('I', 'Acknowledgement-Form.pdf');
exit;
?>

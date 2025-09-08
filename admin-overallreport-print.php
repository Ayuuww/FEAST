<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    die('Access denied');
}

$admin_id = $_SESSION['idnumber'];

// Get department and admin name
$stmt = $conn->prepare("SELECT department, first_name, mid_name, last_name FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_department, $a_fname, $a_mname, $a_lname);
$stmt->fetch();
$stmt->close();

$prepared_by = "$a_fname $a_mname $a_lname";

// --- Filters ---
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$academic_filter = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';

$where_clause = "";
if (!empty($semester_filter)) {
  $where_clause .= " AND semester = '" . $conn->real_escape_string($semester_filter) . "' ";
}
if (!empty($academic_filter)) {
  $where_clause .= " AND academic_year = '" . $conn->real_escape_string($academic_filter) . "' ";
}

// Get all faculty
$query = $conn->prepare("SELECT idnumber, last_name, first_name, mid_name FROM faculty WHERE department = ? ORDER BY last_name ASC");
$query->bind_param("s", $admin_department);
$query->execute();
$faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
$query->close();

// Custom PDF class
require 'printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, "$admin_department Overall Faculty Evaluation Report", 0, 1, 'C');
$pdf->Ln(3);

// Show filter info
$pdf->SetFont('Arial', '', 11);

$semester_text = 'All Semesters';
if (!empty($semester_filter)) {
    $semester_text = $semester_filter;
} else {
    // If no specific filter → show "1st Semester / 2nd Semester"
    $semester_text = '1st Semester / 2nd Semester';
}

$pdf->Cell(0, 8, "Semester: " . $semester_text, 0, 1, 'L');
$pdf->Cell(0, 8, "Academic Year: " . (!empty($academic_filter) ? $academic_filter : 'All Academic Years'), 0, 1, 'L');
$pdf->Cell(0, 8, "Date: " . date('F j, Y'), 0, 1, 'L');
$pdf->Ln(5);

// Section Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, "$admin_department Combined Overall Evaluation (SET + SEF)", 0, 1, 'L', true);
$pdf->Ln(2);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$headers = ['Faculty Member Name', 'SET Avg (%)', 'SEF Avg (%)', 'Overall Avg (%)'];
$widths = [100, 30, 30, 30];
foreach ($headers as $i => $h) {
    $pdf->Cell($widths[$i], 8, $h, 1, 0, 'C');
}
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 10);
foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    $set = $conn->query("
        SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating 
        FROM evaluation 
        WHERE faculty_id = '$fid' $where_clause
    ")->fetch_assoc();
    $set_avg = $set['students'] ? number_format((float)$set['avg_rating'], 2) : '0.00';

    $sef = $conn->query("
        SELECT COUNT(*) AS admins, AVG(computed_rating) AS avg_rating 
        FROM admin_evaluation 
        WHERE evaluatee_id = '$fid' $where_clause
    ")->fetch_assoc();
    $sef_avg = $sef['admins'] ? number_format((float)$sef['avg_rating'], 2) : '0.00';

    $overall = ($set['students'] && $sef['admins'])
        ? number_format(((float)$set_avg + (float)$sef_avg) / 2, 2)
        : ($set['students'] ? $set_avg : ($sef['admins'] ? $sef_avg : '0.00'));

    $pdf->Cell(100, 8, $name, 1);
    $pdf->Cell(30, 8, $set_avg . ' %', 1, 0, 'C');
    $pdf->Cell(30, 8, $sef_avg . ' %', 1, 0, 'C');
    $pdf->Cell(30, 8, $overall . ' %', 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Ln(15);

// Prepared by & Date Signed
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 6, "Prepared by:", 0, 0, 'L');
$pdf->Cell(0, 6, "Date Signed: " . date("F d, Y"), 0, 1, 'L');
$pdf->Ln(10);
$pdf->Cell(140, 6, $prepared_by, 0, 0, 'L');
$pdf->Cell(0, 6, '', 0, 1, 'L');
$pdf->Cell(140, 0, '_________________________', 0, 0, 'L');

$pdf->Output('I', 'overall-evaluation-report.pdf');
?>

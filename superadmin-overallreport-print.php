<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    die('Access denied');
}

$admin_id = $_SESSION['idnumber'];

// Get department and admin name
// Get filters from request
$selected_department   = isset($_GET['department']) ? $_GET['department'] : "";
$selected_semester     = isset($_GET['semester']) ? $_GET['semester'] : "";
$selected_academic_year= isset($_GET['academic_year']) ? $_GET['academic_year'] : "";

// Get admin name for Prepared by
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name 
                        FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$stmt->bind_result($a_fname, $a_mname, $a_lname);
$stmt->fetch();
$stmt->close();

$prepared_by = "$a_fname $a_mname $a_lname";

// Get all faculty in the selected department
$query = $conn->prepare("SELECT idnumber, last_name, first_name, mid_name 
                         FROM faculty 
                         WHERE department = ? 
                         ORDER BY last_name ASC");
$query->bind_param("s", $selected_department);
$query->execute();
$faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
$query->close();


// Get filters from request (GET or POST)
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : "";
$selected_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : "";


// Custom PDF class
require 'printing-headerfooter.php';

// Set department for header
$_SESSION['department'] = $selected_department;

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$title = $title = "$selected_department Overall Faculty Evaluation Report";

if (!empty($selected_semester)) {
    $title .= " | Semester: $selected_semester";
}
if (!empty($selected_academic_year)) {
    $title .= " | AY: $selected_academic_year";
}
$pdf->Cell(0, 10, $title, 0, 1, 'C');
$pdf->Ln(3);

// Section Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, "Combined Overall Evaluation (SET + SEF)", 0, 1, 'L', true);
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

    // Build WHERE filters
    $where_set = "faculty_id = '$fid'";
    $where_sef = "evaluatee_id = '$fid'";
    if (!empty($selected_semester)) {
        $where_set .= " AND semester = '".$conn->real_escape_string($selected_semester)."'";
        $where_sef .= " AND semester = '".$conn->real_escape_string($selected_semester)."'";
    }
    if (!empty($selected_academic_year)) {
        $where_set .= " AND academic_year = '".$conn->real_escape_string($selected_academic_year)."'";
        $where_sef .= " AND academic_year = '".$conn->real_escape_string($selected_academic_year)."'";
    }

    // SET (Student Evaluation of Teachers)
    $set = $conn->query("SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating 
                         FROM evaluation WHERE $where_set")->fetch_assoc();
    $set_avg = $set['students'] ? number_format((float)$set['avg_rating'], 2) : '0.00';

    // SEF (Supervisor Evaluation of Faculty)
    $sef = $conn->query("SELECT COUNT(*) AS admins, AVG(computed_rating) AS avg_rating 
                         FROM admin_evaluation WHERE $where_sef")->fetch_assoc();
    $sef_avg = $sef['admins'] ? number_format((float)$sef['avg_rating'], 2) : '0.00';

    // Overall
    $overall = ($set['students'] && $sef['admins'])
        ? number_format(((float)$set_avg + (float)$sef_avg) / 2, 2)
        : ($set['students'] ? $set_avg : ($sef['admins'] ? $sef_avg : '0.00'));

    $pdf->Cell(100, 8, $name, 1);
    $pdf->Cell(30, 8, $set_avg.' %', 1, 0, 'C');
    $pdf->Cell(30, 8, $sef_avg.' %', 1, 0, 'C');
    $pdf->Cell(30, 8, $overall.' %', 1, 0, 'C');
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

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

// Get all faculty
$query = $conn->prepare("SELECT idnumber, last_name, first_name, mid_name FROM faculty WHERE department = ? ORDER BY last_name ASC");
$query->bind_param("s", $admin_department);
$query->execute();
$faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
$query->close();

// Initialize PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, "Overall Faculty Evaluation Report $admin_department", 0, 1, 'C');
$pdf->Ln(3);

// Section Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, 'Combined Overall Evaluation (SET + SEF)', 0, 1, 'L', true);
$pdf->Ln(2);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$headers = ['Faculty Name', 'SET Avg (%)', 'SEF Avg (%)', 'Overall Avg (%)'];
$widths = [120, 50, 50, 50];
foreach ($headers as $i => $h) {
    $pdf->Cell($widths[$i], 8, $h, 1, 0, 'C');
}
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 10);
foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    $set = $conn->query("SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating FROM evaluation WHERE faculty_id = '$fid'")->fetch_assoc();
    $set_avg = $set['students'] ? number_format((float)$set['avg_rating'], 2) : '0.00';

    $sef = $conn->query("SELECT COUNT(*) AS admins, AVG(computed_rating) AS avg_rating FROM admin_evaluation WHERE evaluatee_id = '$fid'")->fetch_assoc();
    $sef_avg = $sef['admins'] ? number_format((float)$sef['avg_rating'], 2) : '0.00';

    $overall = ($set['students'] && $sef['admins'])
        ? number_format(((float)$set_avg + (float)$sef_avg) / 2, 2)
        : ($set['students'] ? $set_avg : ($sef['admins'] ? $sef_avg : '0.00'));

    $pdf->Cell(120, 8, $name, 1);
    $pdf->Cell(50, 8, $set_avg . ' %', 1, 0, 'C');
    $pdf->Cell(50, 8, $sef_avg . ' %', 1, 0, 'C');
    $pdf->Cell(50, 8, $overall . ' %', 1, 0, 'C');
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

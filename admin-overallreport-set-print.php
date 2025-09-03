<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, position FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$stmt->bind_result($fname, $mname, $lname, $admin_department, $position);
$stmt->fetch();
$stmt->close();

$admin_name = $lname . ', ' . $fname . ' ' . $mname;

// Custom PDF class
require 'printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, $admin_department . ' ' . 'OVERALL SET REPORT', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Supervisor: ' . $admin_name, 0, 1);
$pdf->Cell(0, 8, 'Department: ' . $admin_department, 0, 1);
$pdf->Cell(0, 8, 'Date Generated: ' . date('F j, Y'), 0, 1);
$pdf->Ln(5);

// Table Headers
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'Faculty Name', 1);
$pdf->Cell(50, 10, 'Student Evaluations', 1);
$pdf->Cell(50, 10, 'Average SET Rating', 1);
$pdf->Ln();

// Fetch and display data
$query = $conn->prepare("
  SELECT idnumber, last_name, first_name, mid_name
  FROM faculty
  WHERE department = ?
  ORDER BY last_name ASC
");
$query->bind_param("s", $admin_department);
$query->execute();
$faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
$query->close();

$pdf->SetFont('Arial', 'B', 10);
foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";
  $r = $conn->query("
    SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating
    FROM evaluation
    WHERE faculty_id = '$fid'
  ")->fetch_assoc();
  $count = (int)$r['students'];
  $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

  $pdf->Cell(80, 8, $name, 1);
  $pdf->Cell(50, 8, $count, 1, 0, 'C');
  $pdf->Cell(50, 8, "$avg%", 1, 0, 'C');
  $pdf->Ln();
}

// Signature Block
$pdf->Ln(12);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Prepared by:', 0, 1);
$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, $admin_name, 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, $position, 0, 1);

$pdf->Output('I', 'Overall-SET-Report.pdf');
?>

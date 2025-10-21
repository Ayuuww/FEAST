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

// Get faculty info
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($fname, $mname, $lname, $dept, $faculty_rank);
$stmt->fetch();
$stmt->close();

$faculty_name = strtoupper("$fname $mname $lname");
$dept_display = strtoupper($dept);
$rank_display = ucwords($faculty_rank);

// Get semester/year
$sem = "N/A";
$sy = "N/A";
$q = mysqli_query($conn, "SELECT semester, academic_year FROM admin_evaluation WHERE evaluatee_id = '$faculty_id' ORDER BY evaluation_date DESC LIMIT 1");
if ($q && mysqli_num_rows($q) > 0) {
    $row = mysqli_fetch_assoc($q);
    $sem = $row['semester'];
    $sy = $row['academic_year'];
}

// SET Rating
$set_avg = "0.00";
$set_q = mysqli_query($conn, "SELECT AVG(computed_rating) as avg FROM evaluation WHERE faculty_id = '$faculty_id'");
if ($set_q && ($row = mysqli_fetch_assoc($set_q))) {
    $set_avg = number_format($row['avg'], 2);
}

// SEF Rating
$sef_avg = "0.00";
$sef_q = mysqli_query($conn, "SELECT AVG(computed_rating) as avg FROM admin_evaluation WHERE evaluatee_id = '$faculty_id'");
if ($sef_q && ($row = mysqli_fetch_assoc($sef_q))) {
    $sef_avg = number_format($row['avg'], 2);
}

// Supervisor Name
$evaluator_name = '';
$eval_result = mysqli_query($conn, "SELECT evaluator_id FROM admin_evaluation WHERE evaluatee_id = '$faculty_id' ORDER BY evaluation_date DESC LIMIT 1");
if ($eval_result && mysqli_num_rows($eval_result) > 0) {
    $admin_row = mysqli_fetch_assoc($eval_result);
    $admin_id = $admin_row['evaluator_id'];

    $admin_info = mysqli_query($conn, "SELECT first_name, mid_name, last_name FROM admin WHERE idnumber = '$admin_id'");
    if ($admin_info && mysqli_num_rows($admin_info) > 0) {
        $admin = mysqli_fetch_assoc($admin_info);
        $evaluator_name = strtoupper($admin['first_name'] . ' ' . $admin['mid_name'] . ' ' . $admin['last_name']);
    }
}

// Custom PDF class (with header/footer if you want to use one)
require 'superadmin-printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->department = $dept;
$pdf->AddPage();

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 2, "$dept FACULTY EVALUATION ACKNOWLEDGEMENT FORM", 0, 1, 'C');
$pdf->Ln(6);

// Faculty Info
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY MEMBER INFORMATION', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Name of Faculty:', 1);
$pdf->Cell(130, 8, $faculty_name, 1, 1);

$pdf->Cell(60, 8, 'Department/College:', 1);
$pdf->Cell(130, 8, $dept_display, 1, 1);

$pdf->Cell(60, 8, 'Current Faculty Rank:', 1);
$pdf->Cell(130, 8, $rank_display, 1, 1);

$pdf->Cell(60, 8, 'Semester/Term & Academic Year:', 1);
$pdf->Cell(130, 8, "$sem / $sy", 1, 1);

// Evaluation Summary
$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY EVALUATION SUMMARY', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 8, 'Student Evaluation of Teachers (SET)', 1, 0, 'C');
$pdf->Cell(95, 8, 'Supervisor\'s Evaluation of Faculty (SEF)', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, $set_avg, 1, 0, 'C');
$pdf->Cell(95, 8, $sef_avg, 1, 1, 'C');

// Acknowledgement paragraph
$pdf->Ln(6);
$pdf->MultiCell(0, 6, "I acknowledge that I have received and reviewed the faculty evaluation conducted for the period mentioned above. I understand that my signature below does not necessarily indicate agreement with the evaluation but confirms that I have been given the opportunity to discuss it with my supervisor.");

// Supervisor Section
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'SUPERVISOR', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'Signature:', 1);
$pdf->Cell(50, 8, '', 1);
$pdf->Cell(15, 8, 'Name:', 1);
$pdf->Cell(65, 8, $evaluator_name, 1);
$pdf->Cell(10, 8, 'Date:', 1);
$pdf->Cell(30, 8, '', 1, 1); // blank date field

// Faculty Section
$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'Signature:', 1);
$pdf->Cell(50, 8, '', 1);
$pdf->Cell(15, 8, 'Name:', 1);
$pdf->Cell(65, 8, $faculty_name, 1);
$pdf->Cell(10, 8, 'Date:', 1);
$pdf->Cell(30, 8, '', 1, 1); // blank date field

$pdf->Output();
exit;
?>

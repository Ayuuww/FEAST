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

// Get faculty info
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($fname, $mname, $lname, $dept, $rank);
$stmt->fetch();
$stmt->close();
$full_name = strtoupper("$fname $mname $lname");
$dept = strtoupper($dept);
$rank = ucwords($rank);

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
$set_q = mysqli_query($conn, "SELECT COUNT(*) as count, AVG(computed_rating) as avg FROM evaluation WHERE faculty_id = '$faculty_id'");
$set_avg = ($row = mysqli_fetch_assoc($set_q)) ? number_format($row['avg'], 2) : '0.00';

// SEF Rating
$sef_q = mysqli_query($conn, "SELECT AVG(computed_rating) as avg FROM admin_evaluation WHERE evaluatee_id = '$faculty_id'");
$sef_avg = ($row = mysqli_fetch_assoc($sef_q)) ? number_format($row['avg'], 2) : '0.00';

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

// Start FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 10, 'FACULTY EVALUATION ACKNOWLEDGEMENT FORM', 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY MEMBER INFORMATION', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, 'Name of Faculty:', 1);
$pdf->Cell(130, 8, $full_name, 1, 1);

$pdf->Cell(60, 8, 'Department/College:', 1);
$pdf->Cell(130, 8, $dept, 1, 1);

$pdf->Cell(60, 8, 'Current Faculty Rank:', 1);
$pdf->Cell(130, 8, $rank, 1, 1);

$pdf->Cell(60, 8, 'Semester/Term & Academic Year:', 1);
$pdf->Cell(130, 8, "$sem / $sy", 1, 1);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY EVALUATION SUMMARY', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 8, 'Student Evaluation of Teachers (SET)', 1, 0, 'C');
$pdf->Cell(95, 8, 'Supervisor\'s Evaluation of Faculty (SEF)', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, $set_avg, 1, 0, 'C');
$pdf->Cell(95, 8, $sef_avg, 1, 1, 'C');

$pdf->Ln(5);
$pdf->MultiCell(0, 6, "I acknowledge that I have received and reviewed the faculty evaluation conducted for the period mentioned above. I understand that my signature below does not necessarily indicate agreement with the evaluation but confirms that I have been given the opportunity to discuss it with my supervisor.");

$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'SUPERVISOR', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 8, 'Signature:', 1);
$pdf->Cell(50, 8, '', 1);
$pdf->Cell(15, 8, 'Name:', 1);
$pdf->Cell(65, 8, $evaluator_name, 1);
$pdf->Cell(10, 8, 'Date:', 1);
$pdf->Cell(30, 8, date('F j, Y'), 1, 1); 

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 8, 'Signature:', 1);
$pdf->Cell(50, 8, '', 1);
$pdf->Cell(15, 8, 'Name:', 1);
$pdf->Cell(65, 8, $full_name, 1);
$pdf->Cell(10, 8, 'Date:', 1);
$pdf->Cell(30, 8, date('F j, Y'), 1, 1);



$pdf->Output();
exit;
?>

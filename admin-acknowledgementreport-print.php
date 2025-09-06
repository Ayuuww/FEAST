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


// Get faculty info using prepared statement
$fname = $mname = $lname = $dept = $rank = '';
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
if ($stmt) {
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $stmt->bind_result($fname, $mname, $lname, $dept, $rank);
    $stmt->fetch();
    $stmt->close();
}
$full_name = strtoupper("$fname $mname $lname");
$dept_display = strtoupper($dept);
$rank_display = ucwords($rank);

// Get semester/year from the latest admin evaluation based on filters
$sem = "N/A";
$sy = "N/A";
$stmt_sem_sy = $conn->prepare("SELECT semester, academic_year FROM admin_evaluation WHERE " . $admin_eval_where_sql . " ORDER BY evaluation_date DESC LIMIT 1");
if ($stmt_sem_sy) {
    $stmt_sem_sy->bind_param($params_types, ...$params_values);
    $stmt_sem_sy->execute();
    $stmt_sem_sy->bind_result($sem_res, $sy_res);
    if ($stmt_sem_sy->fetch()) {
        $sem = $sem_res;
        $sy = $sy_res;
    }
    $stmt_sem_sy->close();
}

// Fallback for semester/year if admin_evaluation doesn't yield a result for the selected filters
if ($sem === "N/A" && $sy === "N/A") {
    $stmt_sem_sy_fallback = $conn->prepare("SELECT semester, academic_year FROM evaluation WHERE " . $eval_where_sql . " ORDER BY created_at DESC LIMIT 1");
    if ($stmt_sem_sy_fallback) {
        $stmt_sem_sy_fallback->bind_param($params_types, ...$params_values);
        $stmt_sem_sy_fallback->execute();
        $stmt_sem_sy_fallback->bind_result($sem_res, $sy_res);
        if ($stmt_sem_sy_fallback->fetch()) {
            $sem = $sem_res;
            $sy = $sy_res;
        }
        $stmt_sem_sy_fallback->close();
    }
}


// SET Rating using prepared statement
$set_avg = '0.00';
$stmt_set_avg = $conn->prepare("SELECT AVG(computed_rating) as avg FROM evaluation WHERE " . $eval_where_sql);
if ($stmt_set_avg) {
    $stmt_set_avg->bind_param($params_types, ...$params_values);
    $stmt_set_avg->execute();
    $stmt_set_avg->bind_result($avg_res);
    if ($stmt_set_avg->fetch()) {
        $set_avg = number_format($avg_res, 2);
    }
    $stmt_set_avg->close();
}

// SEF Rating using prepared statement
$sef_avg = '0.00';
$stmt_sef_avg = $conn->prepare("SELECT AVG(computed_rating) as avg FROM admin_evaluation WHERE " . $admin_eval_where_sql);
if ($stmt_sef_avg) {
    $stmt_sef_avg->bind_param($params_types, ...$params_values);
    $stmt_sef_avg->execute();
    $stmt_sef_avg->bind_result($avg_res);
    if ($stmt_sef_avg->fetch()) {
        $sef_avg = number_format($avg_res, 2);
    }
    $stmt_sef_avg->close();
}

// Supervisor Name (Dean / Program Chair / Chair) of the same department
$evaluator_name = 'N/A';
$stmt_supervisor = $conn->prepare("
    SELECT first_name, mid_name, last_name, position
    FROM admin
    WHERE department = ?
      AND (position LIKE 'Dean%' OR position LIKE 'Chair%' OR position LIKE 'Program Chair%')
    ORDER BY 
      CASE 
        WHEN position LIKE 'Dean%' THEN 1
        WHEN position LIKE 'Program Chair%' THEN 2
        WHEN position LIKE 'Chair%' THEN 3
        ELSE 4
      END
    LIMIT 1
");
if ($stmt_supervisor) {
    $stmt_supervisor->bind_param("s", $dept);
    $stmt_supervisor->execute();
    $stmt_supervisor->bind_result($sfn, $smn, $sln, $spos);
    if ($stmt_supervisor->fetch()) {
        $evaluator_name = strtoupper(trim("$sfn $smn $sln"));
    }
    $stmt_supervisor->close();
}


// Custom PDF class
require 'printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();


$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 2, $dept_display . " " . 'FACULTY EVALUATION ACKNOWLEDGEMENT FORM', 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, -1, "$sem / $sy", 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY MEMBER INFORMATION', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Name of Faculty:', 1);
$pdf->Cell(130, 8, $full_name, 1, 1);

$pdf->Cell(60, 8, 'Department/College:', 1);
$pdf->Cell(130, 8, $dept_display, 1, 1);

$pdf->Cell(60, 8, 'Current Faculty Rank:', 1);
$pdf->Cell(130, 8, $rank_display, 1, 1);

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

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'Signature:', 0);
$pdf->Cell(50, 8, '', 0);
$pdf->Cell(15, 8, 'Name:', 0);
$pdf->Cell(65, 8, $evaluator_name, 0); // Already has name + position

$pdf->Cell(10, 8, 'Date:', 0);
$pdf->Cell(30, 8, date('F j, Y'), 0, 1);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'FACULTY', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'Signature:', 0);
$pdf->Cell(50, 8, '', 0);
$pdf->Cell(15, 8, 'Name:', 0);
$pdf->Cell(65, 8, $full_name, 0);
$pdf->Cell(10, 8, 'Date:', 0);
$pdf->Cell(30, 8, date('F j, Y'), 0, 1);

$pdf->Output();
exit;
?>
<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    die('Access denied');
}

$superadmin_id = $_SESSION['idnumber'];

// 🔹 Get superadmin details (Prepared by)
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position 
                        FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $superadmin_id);
$stmt->execute();
$stmt->bind_result($s_fname, $s_mname, $s_lname, $s_position);
$stmt->fetch();
$stmt->close();

$prepared_by = trim("$s_fname $s_mname $s_lname");

// 🔹 Get filters from request
$selected_department = $_GET['department'] ?? '';
$selected_semester = $_GET['semester'] ?? '';
$selected_academic_year = $_GET['academic_year'] ?? '';

// 🔹 Get all faculty in the selected department
$faculties = [];
if (!empty($selected_department)) {
    $query = $conn->prepare("
        SELECT idnumber, last_name, first_name, mid_name
        FROM faculty
        WHERE department = ?
        ORDER BY last_name ASC
    ");
    $query->bind_param("s", $selected_department);
    $query->execute();
    $faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
    $query->close();
}

// 🔹 Custom PDF class (with header/footer)
require 'printing-headerfooter.php';
$_SESSION['department'] = $selected_department;

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$title = strtoupper($selected_department) . " OVERALL FACULTY EVALUATION REPORT";
$pdf->Cell(0, 10, $title, 0, 1, 'C');
$pdf->Ln(3);

// --- Filters Information ---
$pdf->SetFont('Arial', '', 11);
$semester_text = !empty($selected_semester) ? $selected_semester : 'All Semesters';
$academic_text = !empty($selected_academic_year) ? $selected_academic_year : 'All Academic Years';

$pdf->Cell(0, 8, "Semester: $semester_text", 0, 1, 'L');
$pdf->Cell(0, 8, "Academic Year: $academic_text", 0, 1, 'L');
$pdf->Cell(0, 8, "Date Generated: " . date('F j, Y'), 0, 1, 'L');
$pdf->Ln(5);

// --- Table Header ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(110, 8, 'Faculty Member Name', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'SET AVG', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'SEF AVG', 1, 1, 'C', true);

// --- Table Content ---
$pdf->SetFont('Arial', '', 10);
foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    // 🔹 Student Evaluation (SET)
    $sql_set = "SELECT COUNT(*) AS total, AVG(computed_rating) AS avg 
                FROM evaluation WHERE faculty_id = ?";
    $types = "s";
    $params = [$fid];

    if (!empty($selected_semester)) {
        $sql_set .= " AND semester = ?";
        $params[] = $selected_semester;
        $types .= "s";
    }
    if (!empty($selected_academic_year)) {
        $sql_set .= " AND academic_year = ?";
        $params[] = $selected_academic_year;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql_set);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $set_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $set_avg = $set_data['total'] ? number_format((float)$set_data['avg'], 2) : '0.00';

    // 🔹 Supervisor Evaluation (SEF)
    $sql_sef = "SELECT COUNT(*) AS total, AVG(computed_rating) AS avg 
                FROM admin_evaluation WHERE evaluatee_id = ?";
    $types = "s";
    $params = [$fid];

    if (!empty($selected_semester)) {
        $sql_sef .= " AND semester = ?";
        $params[] = $selected_semester;
        $types .= "s";
    }
    if (!empty($selected_academic_year)) {
        $sql_sef .= " AND academic_year = ?";
        $params[] = $selected_academic_year;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql_sef);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $sef_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $sef_avg = $sef_data['total'] ? number_format((float)$sef_data['avg'], 2) : '0.00';

    // 🔹 Output table row
    $pdf->Cell(110, 8, $name, 1);
    $pdf->Cell(40, 8, $set_avg, 1, 0, 'C');
    $pdf->Cell(40, 8, $sef_avg, 1, 1, 'C');
}

$pdf->Ln(15);

// --- Prepared By Section ---
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 6, "Prepared by:", 0, 0, 'L');
$pdf->Cell(0, 6, "Date Signed:", 0, 1, 'L');
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 6, $prepared_by, 0, 0, 'L');
$pdf->Cell(0, 6, '', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 0, '_________________________', 0, 0, 'L');
$pdf->Cell(0, 0, '_________________________', 0, 1, 'L');
$pdf->Cell(140, 6, $s_position, 0, 0, 'L');
$pdf->Cell(0, 6, '', 0, 1, 'L');

$pdf->Output('I', 'Overall-Evaluation-Report.pdf');
?>

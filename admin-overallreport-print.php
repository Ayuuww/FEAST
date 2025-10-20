<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    die('Access denied');
}

$admin_id = $_SESSION['idnumber'];

// 🔹 FIX 1: Get admin name (removed 'department')
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position 
                        FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$stmt->bind_result($a_fname, $a_mname, $a_lname, $a_position); // Added position
$stmt->fetch();
$stmt->close();

$prepared_by = "$a_fname $a_mname $a_lname";

// 🔹 FIX 2: Get all assigned departments from the correct table
$stmt = $conn->prepare("SELECT department_name FROM admin_departments WHERE admin_idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$departments = []; // This will be an array
while ($row = $result->fetch_assoc()) {
    $departments[] = $row['department_name'];
}
$stmt->close();

// This string is for the PDF title
$admin_department_display = !empty($departments) ? implode(', ', $departments) : 'No Department Assigned';

// --- Filters ---
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$academic_filter = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';

// 🔹 FIX 3: Get faculty using the $departments array and an IN clause
$faculties = [];
if (!empty($departments)) {
    // Create placeholders like (?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($departments), '?'));
    // Create types string like "sss"
    $types = str_repeat('s', count($departments));

    $sql = "
        SELECT idnumber, last_name, first_name, mid_name
        FROM faculty
        WHERE department IN ($placeholders)
        ORDER BY last_name ASC
    ";

    $query = $conn->prepare($sql);
    $query->bind_param($types, ...$departments); // Bind all departments
    $query->execute();
    $faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
    $query->close();
}

// Custom PDF class
require 'printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
// 🔹 FIX 4: Use the new display variable for the title
$pdf->Cell(0, 10, strtoupper($admin_department_display) . " OVERALL FACULTY EVALUATION REPORT", 0, 1, 'C');
$pdf->Ln(3);

// Show filter info
$pdf->SetFont('Arial', '', 11);

// ... (Your semester/year text logic is fine) ...
$semester_text = 'All Semesters';
if (!empty($semester_filter)) {
    $semester_text = $semester_filter;
} else {
    // You can also query this if you want it to be dynamic
    $semester_text = '1st Semester / 2nd Semester'; 
}

$pdf->Cell(0, 8, "Semester: " . $semester_text, 0, 1, 'L');
$pdf->Cell(0, 8, "Academic Year: " . (!empty($academic_filter) ? $academic_filter : 'All Academic Years'), 0, 1, 'L');
$pdf->Cell(0, 8, "Date: " . date('F j, Y'), 0, 1, 'L');
$pdf->Ln(5);

// Section Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, strtoupper($admin_department_display) . " OVERALL EVALUATION (SET & SEF)", 0, 1, 'C', true);
$pdf->Ln(2);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$headers = ['Faculty Member Name', 'SET AVG', 'SEF AVG'];
// 🔹 FIX: Adjusted widths to match 3 columns and fill the page (110 + 40 + 40 = 190)
$widths = [110, 40, 40]; 
foreach ($headers as $i => $h) {
$pdf->Cell($widths[$i], 8, $h, 1, 0, 'C');
}
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 10);
foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    // 🔹 FIX 5: Use Prepared Statements for SET query
    $sql = "SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating 
            FROM evaluation WHERE faculty_id = ?";
    $types = "s";
    $params = [$fid];

    if (!empty($semester_filter)) {
      $sql .= " AND semester = ?";
      $params[] = $semester_filter;
      $types .= "s";
    }
    if (!empty($academic_filter)) {
      $sql .= " AND academic_year = ?";
      $params[] = $academic_filter;
      $types .= "s";
    }

    $stmtEval = $conn->prepare($sql);
    $stmtEval->bind_param($types, ...$params);
    $stmtEval->execute();
    $set = $stmtEval->get_result()->fetch_assoc();
    $stmtEval->close();
    
    $set_avg = $set['students'] ? number_format((float)$set['avg_rating'], 2) : '0.00';

    // 🔹 FIX 6: Use Prepared Statements for SEF query
    $sql = "SELECT COUNT(*) AS admins, AVG(computed_rating) AS avg_rating 
            FROM admin_evaluation WHERE evaluatee_id = ?";
    $types = "s";
    $params = [$fid];

    if (!empty($semester_filter)) {
      $sql .= " AND semester = ?";
      $params[] = $semester_filter;
      $types .= "s";
    }
    if (!empty($academic_filter)) {
      $sql .= " AND academic_year = ?";
      $params[] = $academic_filter;
      $types .= "s";
    }

    $stmtEval = $conn->prepare($sql);
    $stmtEval->bind_param($types, ...$params);
    $stmtEval->execute();
    $sef = $stmtEval->get_result()->fetch_assoc();
    $stmtEval->close();

    $sef_avg = $sef['admins'] ? number_format((float)$sef['avg_rating'], 2) : '0.00';

    // ... (Your overall average logic is correct) ...
    $overall = ($set['students'] && $sef['admins'])
        ? number_format(((float)$set_avg + (float)$sef_avg) / 2, 2)
        : ($set['students'] ? $set_avg : ($sef['admins'] ? $sef_avg : '0.00'));

    // ... FIX: Matched cell widths to the new $widths array ...
    $pdf->Cell(110, 8, $name, 1);
    $pdf->Cell(40, 8, $set_avg, 1, 0, 'C');
    $pdf->Cell(40, 8, $sef_avg, 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Ln(15);

// Prepared by & Date Signed
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 6, "Prepared by:", 0, 0, 'L');
$pdf->Cell(0, 6, "Date Signed: ", 0, 1, 'L'); // Removed date, let them sign
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10); // Make name bold
$pdf->Cell(140, 6, $prepared_by, 0, 0, 'L');
$pdf->Cell(0, 6, '', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10); // Revert to normal
$pdf->Cell(140, 0, '_________________________', 0, 0, 'L');
$pdf->Cell(0, 0, '_________________________', 0, 1, 'L');
$pdf->Cell(140, 6, $a_position, 0, 0, 'L'); // Add position
$pdf->Cell(0, 6, '', 0, 1, 'L');


$pdf->Output('I', 'Overall-Evaluation-Report.pdf');
?>
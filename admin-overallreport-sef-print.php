<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// 🔹 FIX 1: Get admin info (removed 'department')
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position 
                        FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
// Removed $admin_department from binding
$stmt->bind_result($fname, $mname, $lname, $position);
$stmt->fetch();
$stmt->close();

$admin_name = $lname . ', ' . $fname . ' ' . $mname;

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


// 🔹 Get filters
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$year_filter     = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';

// Custom PDF class
require 'printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
// 🔹 FIX 3: Use the new display variable for the title
$pdf->Cell(0, 10, strtoupper($admin_department_display) . ' OVERALL SEF REPORT', 0, 1, 'C');

// ... (Your filter display logic is fine, but I'll update it to be more robust like the SET report) ...

$pdf->SetFont('Arial', '', 11);

// Handle semester display
if (!empty($semester_filter)) {
    $semester_display = $semester_filter;
} else {
    // Fetch all distinct semesters
    $semester_display = [];
    $res = $conn->query("SELECT DISTINCT semester FROM admin_evaluation ORDER BY semester ASC");
    while ($row = $res->fetch_assoc()) {
        if (!empty($row['semester'])) $semester_display[] = $row['semester'];
    }
    $res->close();
    $semester_display = !empty($semester_display) ? implode(" / ", $semester_display) : "All Semesters";
}

// Handle academic year display
if (!empty($year_filter)) {
    $year_display = $year_filter;
} else {
    // Fetch all distinct academic years
    $year_display = [];
    $res = $conn->query("SELECT DISTINCT academic_year FROM admin_evaluation ORDER BY academic_year DESC");
    while ($row = $res->fetch_assoc()) {
        if (!empty($row['academic_year'])) $year_display[] = $row['academic_year'];
    }
    $res->close();
    $year_display = !empty($year_display) ? implode(" / ", $year_display) : "All Academic Years";
}

$pdf->Cell(0, 8, "Semester: $semester_display", 0, 1);
$pdf->Cell(0, 8, "Academic Year: $year_display", 0, 1);
$pdf->Cell(0, 8, "Date: " . date('F j, Y'), 0, 1);
$pdf->Ln(5);


// Section Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(150, 10, strtoupper($admin_department_display) . " OVERALL EVALUATION SET", 0, 1, 'C', true);
$pdf->Ln(2);

// Table Headers
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(100, 10, 'Faculty Name', 1);
$pdf->Cell(50, 10, 'Average SEF Rating', 1,0,'C');
$pdf->Ln();

// 🔹 FIX 4: Fetch faculty using the $departments array and an IN clause
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


$pdf->SetFont('Arial', '', 10);
foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    // Query with filters
    $sql = "SELECT COUNT(*) AS evals, AVG(computed_rating) AS avg_rating
            FROM admin_evaluation WHERE evaluatee_id = ?";
    $params = [$fid];
    $types = "s";

    if (!empty($semester_filter)) {
        $sql .= " AND semester = ?";
        $params[] = $semester_filter;
        $types .= "s";
    }
    if (!empty($year_filter)) {
        $sql .= " AND academic_year = ?";
        $params[] = $year_filter;
        $types .= "s";
    }

    $stmtEval = $conn->prepare($sql);
    $stmtEval->bind_param($types, ...$params);
    $stmtEval->execute();
    $r = $stmtEval->get_result()->fetch_assoc();
    $stmtEval->close();

    $count = (int)$r['evals'];
    $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

    $pdf->Cell(100, 8, $name, 1);
    $pdf->Cell(50, 8, "$avg", 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Ln(10);

// Signature Section
$pdf->Ln(12);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Prepared by:', 0, 1);
$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, $admin_name, 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, $position, 0, 1);

$pdf->Output('I', 'Overall-SEF-Report.pdf');
?>
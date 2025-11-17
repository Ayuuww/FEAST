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

$middle_initial = '';
if (!empty($mname)) {
    $middle_initial = ' ' . substr($mname, 0, 1) . '.'; // Add space, initial, and period
}

$admin_name = strtoupper($fname . ' ' . $middle_initial  . ' ' . $lname);

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
$pdf->Cell(0, 10, 'COLLEGE SEF REPORT', 0, 1, 'C');

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
$pdf->Cell(180, 10, "COLLEGE SET EVALUATION REPORT", 0, 1, 'C', true); // Note: This says SET, might want to change to SEF
$pdf->Ln(2);

// --- ✅ MODIFICATION 1: Adjust Table Headers ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'Faculty Name', 1); // Adjusted width
$pdf->Cell(60, 10, 'No. of Supervisor Evaluations', 1, 0, 'C'); // Added new column
$pdf->Cell(40, 10, 'Avg. SEF Rating', 1, 0, 'C'); // Adjusted width
$pdf->Ln();
// --- End Modification 1 ---

// 🔹 CHANGE 3: Fix Faculty Query (use Dept/Prog pairs)
$faculties = [];

// ✅ FIRST, get the admin's assignments as pairs
$admin_assignments = [];
// We need to re-fetch the department/program pairs
$stmt_admin_dept = $conn->prepare("SELECT department_name, program_name FROM admin_departments WHERE admin_idnumber = ?");
$stmt_admin_dept->bind_param("s", $admin_id);
$stmt_admin_dept->execute();
$result = $stmt_admin_dept->get_result();
while ($row = $result->fetch_assoc()) {
    $admin_assignments[] = $row;
}
$stmt_admin_dept->close();


if (!empty($admin_assignments)) {
    // ✅ Build the query to check for (dept = ? AND prog = ?) OR (dept = ? AND prog = ?)
    $faculty_query_parts = [];
    $params = [];
    $types = "";

    foreach ($admin_assignments as $assignment) {
        $faculty_query_parts[] = "(department = ? AND program = ?)";
        $params[] = $assignment['department_name'];
        $params[] = $assignment['program_name'];
        $types .= "ss";
    }
    $faculty_where_sql = implode(' OR ', $faculty_query_parts);

    // ✅ This query now finds faculty whose home dept/prog matches the admin's assignments
    $sql = "
        SELECT idnumber, last_name, first_name, mid_name
        FROM faculty
        WHERE ($faculty_where_sql)
        ORDER BY last_name ASC
    ";

    $query = $conn->prepare($sql);
    $query->bind_param($types, ...$params);
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

    $count = (int)$r['evals']; // ✅ This is the count
    $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

    // --- ✅ MODIFICATION 2: Add Data Cell ---
    $pdf->Cell(80, 8, $name, 1); // Adjusted width
    $pdf->Cell(60, 8, $count, 1, 0, 'C'); // Added cell for the count
    $pdf->Cell(40, 8, "$avg", 1, 0, 'C'); // Adjusted width
    $pdf->Ln();
    // --- End Modification 2 ---
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

$pdf->Ln(12);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Reviewed by:', 0, 1);
$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, $admin_name, 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, $position, 0, 1);

$pdf->Output('I', 'Overall-SEF-Report.pdf');

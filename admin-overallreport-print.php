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

$middle_initial = '';
if (!empty($a_mname)) {
  $middle_initial = ' ' . substr($a_mname, 0, 1) . '.'; // Add space, initial, and period
}

$prepared_by = strtoupper("$a_fname $middle_initial $a_lname");

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

// Custom PDF class
require 'printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
// 🔹 FIX 4: Use the new display variable for the title
$pdf->Cell(0, 10, "COLLEGE SET & SEF EVALUATION REPORT", 0, 1, 'C');
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
$pdf->Cell(0, 10, strtoupper($admin_department_display) . " SET & SEF REPORT", 0, 1, 'C', true);
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

$total_set_avg = 0;
$total_sef_avg = 0;
$faculty_with_set = 0;
$faculty_with_sef = 0;

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

  // ✅ GET RAW FLOATS FIRST
  $set_avg_raw = $set['students'] ? (float)$set['avg_rating'] : 0.00;
  $sef_avg_raw = $sef['admins'] ? (float)$sef['avg_rating'] : 0.00;

  // ✅ UPDATE TOTALS WITH RAW FLOATS
  if ($set['students'] > 0) {
    $total_set_avg += $set_avg_raw;
    $faculty_with_set++;
  }
  if ($sef['admins'] > 0) {
    $total_sef_avg += $sef_avg_raw;
    $faculty_with_sef++;
  }

  // Now format for display
  $set_avg_display = number_format($set_avg_raw, 2);
  $sef_avg_display = number_format($sef_avg_raw, 2);

  // ... (Your overall average logic is fine) ...
  $overall = ($set['students'] && $sef['admins'])
    ? number_format(($set_avg_raw + $sef_avg_raw) / 2, 2) // Use raw values here too
    : ($set['students'] ? $set_avg_display : ($sef['admins'] ? $sef_avg_display : '0.00'));

  // ... FIX: Matched cell widths to the new $widths array ...
  $pdf->Cell(110, 8, $name, 1);
  $pdf->Cell(40, 8, $set_avg_display, 1, 0, 'C'); // Use display string
  $pdf->Cell(40, 8, $sef_avg_display, 1, 0, 'C'); // Use display string
  $pdf->Ln();
}

$final_set_average = ($faculty_with_set > 0) ? ($total_set_avg / $faculty_with_set) : 0;
$final_sef_average = ($faculty_with_sef > 0) ? ($total_sef_avg / $faculty_with_sef) : 0;

// --- Draw the Total Row ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(240, 240, 240); // Light gray background
$pdf->Cell($widths[0], 8, 'Department Average:', 1, 0, 'R', true); // 110 width
$pdf->Cell($widths[1], 8, number_format($final_set_average, 2), 1, 0, 'C', true); // 40 width
$pdf->Cell($widths[2], 8, number_format($final_sef_average, 2), 1, 1, 'C', true); // 40 width, with Ln()

$pdf->Ln(15);

// Prepared by & Date Signed
// --- ✅ START: New Signature Block ---

// Prepared by
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 6, "Prepared by:", 0, 1, 'L'); // Use 0, 1 for a new line
$pdf->Ln(10); // Space for signature

$pdf->SetFont('Arial', 'B', 10); // Make name bold
$pdf->Cell(80, 6, $prepared_by, 0, 1, 'L'); // Use 80 width

$pdf->SetFont('Arial', '', 10); // Revert to normal
$pdf->Cell(80, 6, $a_position, 0, 1, 'L'); // Add position

// Reviewed by
$pdf->Ln(12); // Add space between blocks
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 6, "Reviewed by:", 0, 1, 'L');
$pdf->Ln(10); // Space for signature

$pdf->SetFont('Arial', 'B', 10); // Make name bold
$pdf->Cell(80, 6, $prepared_by, 0, 1, 'L'); // Use the same admin name

$pdf->SetFont('Arial', '', 10); // Revert to normal
$pdf->Cell(80, 6, $a_position, 0, 1, 'L'); // Use the same admin position

// --- ✅ END: New Signature Block ---

$pdf->Output('I', 'Overall-Evaluation-Report.pdf');


$pdf->Output('I', 'Overall-Evaluation-Report.pdf');

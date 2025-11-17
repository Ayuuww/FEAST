<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// ─────────────────────────────────────────────
// 1️⃣ Get filter selections
// ─────────────────────────────────────────────
$selected_department = $_GET['department'] ?? "";
$selected_semester = $_GET['semester'] ?? "";
$selected_academic_year = $_GET['academic_year'] ?? "";

if (empty($selected_department)) {
  die("No department selected. Please go back and select a department.");
}

// Store department in session (used by header/footer)
$_SESSION['department'] = $selected_department;

// ─────────────────────────────────────────────
// 2️⃣ Fetch program chair / supervisor(s)
// ─────────────────────────────────────────────
$supervisors = [];
$selected_program = $_GET['program'] ?? "";

// Build SQL with both filters
if (!empty($selected_program)) {
  // Fetch admins assigned to BOTH department and program
  $stmt = $conn->prepare("
    SELECT a.first_name, a.mid_name, a.last_name, a.position 
    FROM admin a
    INNER JOIN admin_departments ad ON a.idnumber = ad.admin_idnumber
    WHERE ad.department_name = ? AND ad.program_name = ?
  ");
  $stmt->bind_param("ss", $selected_department, $selected_program);
} else {
  // Fetch all admins of the department (no specific program)
  $stmt = $conn->prepare("
    SELECT a.first_name, a.mid_name, a.last_name, a.position 
    FROM admin a
    INNER JOIN admin_departments ad ON a.idnumber = ad.admin_idnumber
    WHERE ad.department_name = ?
  ");
  $stmt->bind_param("s", $selected_department);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

  $middle_initial = '';
  if (!empty($row['mid_name'])) {
    $middle_initial = ' ' . substr($row['mid_name'], 0, 1) . '.'; // Add space, initial, and period
  }
  
  $fullname = strtoupper(trim("{$row['first_name']} $middle_initial {$row['last_name']}"));
  $supervisors[] = [
    'name' => $fullname,
    'position' => strtoupper($row['position'])
  ];
}
$stmt->close();

// ─────────────────────────────────────────────
// 3️⃣ PDF setup
// ─────────────────────────────────────────────
require 'superadmin-printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4', $conn);
$pdf->department = $selected_department;
$pdf->AddPage();

// ─────────────────────────────────────────────
// 4️⃣ Title Section
// ─────────────────────────────────────────────
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, ' COLLEGE SET EVALUATION REPORT', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'Department/College: ' . (!empty($selected_department) ? $selected_department : 'All Programs'), 0, 1); // ✅ ADD THIS
$pdf->Cell(0, 8, 'Semester: ' . ($selected_semester ?: '1st / 2nd Semester'), 0, 1);
$pdf->Cell(0, 8, 'Academic Year: ' . ($selected_academic_year ?: 'All Academic Years'), 0, 1);
$pdf->Cell(0, 8, 'Date: ' . date('F j, Y'), 0, 1);
$pdf->Ln(5);

// Section Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(180, 10, "COLLEGE SET REPORT", 0, 1, 'C', true);
$pdf->Ln(2);

// ─────────────────────────────────────────────
// 5️⃣ Table Headers
// ─────────────────────────────────────────────
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'Faculty Name', 1);
$pdf->Cell(50, 10, 'Student Evaluations', 1);
$pdf->Cell(50, 10, 'Average SET Rating', 1);
$pdf->Ln();

// ─────────────────────────────────────────────
// 6️⃣ Faculty List per Department (AND PROGRAM)
// ─────────────────────────────────────────────
$faculty_sql = "
SELECT idnumber, last_name, first_name, mid_name
FROM faculty
WHERE department = ?
";
$params = [$selected_department];
$types = "s";

// Add program filter if it exists
if (!empty($selected_program)) {
  $faculty_sql .= " AND program = ?";
  $params[] = $selected_program;
  $types .= "s";
}

$faculty_sql .= " ORDER BY last_name ASC";

$query = $conn->prepare($faculty_sql);
$query->bind_param($types, ...$params);
$query->execute();
$faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
$query->close();

$pdf->SetFont('Arial', '', 10);
foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

  // Build WHERE dynamically
  $conditions = ["faculty_id = '$fid'"];
  if (!empty($selected_semester)) $conditions[] = "semester = '" . $conn->real_escape_string($selected_semester) . "'";
  if (!empty($selected_academic_year)) $conditions[] = "academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
  $where = implode(" AND ", $conditions);

  $r = $conn->query("SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating FROM evaluation WHERE $where")->fetch_assoc();
  $count = (int)$r['students'];
  $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

  $pdf->Cell(80, 8, $name, 1);
  $pdf->Cell(50, 8, $count, 1, 0, 'C');
  $pdf->Cell(50, 8, $avg, 1, 0, 'C');
  $pdf->Ln();
}

// ─────────────────────────────────────────────
// ✅ 6.5️⃣ College Average (Overall Department Average)
// ─────────────────────────────────────────────
$college_where = ["f.department = '" . $conn->real_escape_string($selected_department) . "'"];
if (!empty($selected_program)) $college_where[] = "f.program = '" . $conn->real_escape_string($selected_program) . "'";
if (!empty($selected_academic_year)) $college_where[] = "academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
$college_where_sql = implode(" AND ", $college_where);

$college_avg_query = "
  SELECT COUNT(*) AS total_students, AVG(computed_rating) AS college_avg
  FROM evaluation e
  INNER JOIN faculty f ON e.faculty_id = f.idnumber
  WHERE $college_where_sql
";
$college_result = $conn->query($college_avg_query)->fetch_assoc();
$total_students = (int)$college_result['total_students'];
$college_avg = $total_students ? number_format((float)$college_result['college_avg'], 2) : '0.00';

// Add college average row
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(130, 10, 'College Average', 1, 0, 'R', true);
$pdf->Cell(50, 10, number_format($college_avg, 2), 1, 1, 'C', true);
$pdf->Ln(12);

// ─────────────────────────────────────────────
// 7️⃣ Prepared by section (Superadmin info)
// ─────────────────────────────────────────────
$pdf->SetFont('Arial', '', 11);
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $_SESSION['idnumber']);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$stmt->close();

$middle_initial = '';
if (!empty($res['mid_name'])) {
  $middle_initial = ' ' . substr($res['mid_name'], 0, 1) . '.'; // Add space, initial, and period
}

$prepared_by = $res ? "{$res['first_name']} $middle_initial {$res['last_name']}" : 'Unknown';
$position = $res['position'] ?? 'Superadmin';

// Prepared by layout
$pdf->Ln(10);
$pdf->Cell(120, 6, "Prepared by:", 0, 0, 'L');
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(140, 6, strtoupper($prepared_by), 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, strtoupper($position), 0, 1, 'L');


// ─────────────────────────────────────────────
// 8️⃣ Approved by section (if you want Program Chair shown)
// ─────────────────────────────────────────────
if (!empty($supervisors)) {
  $pdf->Ln(20);
  $pdf->SetFont('Arial', '', 11);
  $pdf->Cell(0, 6, "Reviewed By:", 0, 1, 'L');
  $pdf->SetFont('Arial', '', 11);

  foreach ($supervisors as $sup) {
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(140, 6, $sup['name'], 0, 1, 'L');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(140, 6, strtoupper($sup['position']), 0, 1, 'L');
  }
}

$pdf->Output('I', 'College-SET-Report.pdf');

<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Get selected department from URL
$selected_department = isset($_GET['department']) ? $_GET['department'] : "";
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : "";
$selected_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : "";

// If no department is selected, stop
if (empty($selected_department)) {
  die("No department selected. Please go back and select a department.");
}

// 🔑 Set department into session so header/footer can access it
$_SESSION['department'] = $selected_department;

// Fetch supervisor(s) from admin table for this department
$supervisors = [];
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position 
                        FROM admin 
                        WHERE department = ?");
$stmt->bind_param("s", $selected_department);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $fullname = strtoupper($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['mid_name']);
  $supervisors[] = [
    'name' => $fullname,
    'position' => strtoupper($row['position'])
  ];
}
$stmt->close();

// Custom PDF class
require 'printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

// Title (use department code directly)
$pdf->SetFont('Arial', 'B', 14);
$title = $selected_department . ' COLLEGE SET REPORT';
if (!empty($selected_semester)) {
  $title .= " | Semester: " . $selected_semester;
}
if (!empty($selected_academic_year)) {
  $title .= " | AY: " . $selected_academic_year;
}

$pdf->Cell(0, 10, $title, 0, 1, 'C');


$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, 'Date Generated: ' . date('F j, Y'), 0, 1);
$pdf->Ln(5);

// Table Headers
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'Faculty Name', 1);
$pdf->Cell(50, 10, 'Student Evaluations', 1);
$pdf->Cell(50, 10, 'Average SET Rating', 1);
$pdf->Ln();

// Fetch and display faculty data for this department
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

$pdf->SetFont('Arial', '', 10);
foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";
  
  $where = "faculty_id = '$fid'";
if (!empty($selected_semester)) {
  $where .= " AND semester = '" . $conn->real_escape_string($selected_semester) . "'";
}
if (!empty($selected_academic_year)) {
  $where .= " AND academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
}

$r = $conn->query("
    SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating
    FROM evaluation
    WHERE $where
")->fetch_assoc();

  
  $count = (int)$r['students'];
  $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

  $pdf->Cell(80, 8, $name, 1);
  $pdf->Cell(50, 8, $count, 1, 0, 'C');
  $pdf->Cell(50, 8, "$avg%", 1, 0, 'C');
  $pdf->Ln();
}

// Signature / Supervisor Section
$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Supervisor(s):', 0, 1);

$pdf->SetFont('Arial', '', 11);
if (!empty($supervisors)) {
  foreach ($supervisors as $sup) {
    $pdf->Ln(10);
    $pdf->Cell(0, 6, $sup['name'], 0, 1);
    $pdf->Cell(0, 6, $sup['position'], 0, 1);
  }
} else {
  $pdf->Cell(0, 6, 'No supervisor (admin) found for this department.', 0, 1);
}

$pdf->Output('I', 'College-SET-Report.pdf');
?>

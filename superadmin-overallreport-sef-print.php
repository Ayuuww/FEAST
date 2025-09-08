<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Get selected filters from URL
$selected_department = isset($_GET['department']) ? $_GET['department'] : "";
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : "";
$selected_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : "";

// If no department is selected, stop
if (empty($selected_department)) {
  die("No department selected. Please go back and select a department.");
}

// 🔑 Store department in session so header/footer can access it
$_SESSION['department'] = $selected_department;

// Fetch supervisor(s) from admin table for this department
$supervisors = [];
$stmt = $conn->prepare("
  SELECT first_name, mid_name, last_name, position 
  FROM admin 
  WHERE department = ?
");
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

// Handle Semester display
if ($selected_semester) {
    $sem_display = "Semester: $selected_semester";
} else {
    $sem_display = "Semester: 1st / 2nd Semester";
}

// Handle Academic Year display
if ($selected_academic_year) {
    $ay_display = "Academic Year: $selected_academic_year";
} else {
    $ay_display = "Academic Year: All Academic Years";
}
$pdf->Cell(0, 10, $title, 0, 1, 'C');


$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, $sem_display, 0, 1);
$pdf->Cell(0, 8, $ay_display, 0, 1);
$pdf->Cell(0, 8, 'Date Generated: ' . date('F j, Y'), 0, 1);
$pdf->Ln(5);

// Table Headers
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'Faculty Name', 1);
$pdf->Cell(50, 10, 'Supervisor Evaluations', 1, 0, 'C');
$pdf->Cell(50, 10, 'Average SEF Rating', 1, 0, 'C');
$pdf->Ln();

// Fetch faculty for this department
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

// Display results
$pdf->SetFont('Arial', '', 10);
foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

  // Build filter conditions for SEF
  $where = "evaluatee_id = '" . $conn->real_escape_string($fid) . "'";
  if (!empty($selected_semester)) {
    $where .= " AND semester = '" . $conn->real_escape_string($selected_semester) . "'";
  }
  if (!empty($selected_academic_year)) {
    $where .= " AND academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
  }

  // Query SEF results
  $r = $conn->query("
    SELECT COUNT(*) AS evaluations, AVG(computed_rating) AS avg_rating
    FROM admin_evaluation
    WHERE $where
  ")->fetch_assoc();

  $count = (int)$r['evaluations'];
  $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

  $pdf->Cell(80, 8, $name, 1);
  $pdf->Cell(50, 8, $count, 1, 0, 'C');
  $pdf->Cell(50, 8, "$avg%", 1, 0, 'C');
  $pdf->Ln();
}

// Get logged-in superadmin info
$prepared_by = "";
if (isset($_SESSION['idnumber'])) {
  $stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position 
                          FROM superadmin 
                          WHERE idnumber = ?");
  $stmt->bind_param("s", $_SESSION['idnumber']);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if ($res) {
    $fullname = trim($res['last_name'] . ', ' . $res['first_name'] . ' ' . $res['mid_name']);
    $position = trim($res['position']);
    $prepared_by = $fullname;
  }
}

// Prepared by & Date Signed
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(120, 6, "Prepared by:", 0, 0, 'L');
$pdf->Cell(0, 6, "Date Signed: " . date("F d, Y"), 0, 1, 'L');
$pdf->Ln(10);
$pdf->Cell(140, 6, $prepared_by, 0, 1, 'L');
$pdf->Cell(0, 6, $position, 0, 1, 'L');
$pdf->Cell(140, 0, '_________________________', 0, 0, 'L');

$pdf->Output('I', 'Overall-SEF-Report.pdf');
?>

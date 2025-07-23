<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    die('Access denied');
}

$admin_id = $_SESSION['idnumber'];
$academic_year = $_GET['academic_year'] ?? '';
$subject_code  = $_GET['subject_code'] ?? '';

// Fetch admin info
$stmt = $conn->prepare("SELECT department, first_name, mid_name, last_name FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$stmt->bind_result($department, $fname, $mname, $lname);
$stmt->fetch();
$stmt->close();

$full_name = "$fname $mname $lname";

// Build evaluation query based on filters
$params = [$admin_id];
$types = "s";
$sql = "SELECT subject_code, subject_title, student_section, academic_year, semester, created_at,
               COUNT(*) AS student_count,
               AVG(total_score) AS avg_total_score,
               AVG(computed_rating) AS avg_computed_rating,
               GROUP_CONCAT(comment SEPARATOR ' | ') AS comments
        FROM evaluation
        WHERE faculty_id = ?";

if ($academic_year) {
    $sql .= " AND academic_year = ?";
    $params[] = $academic_year;
    $types .= "s";
}
if ($subject_code) {
    $sql .= " AND subject_code = ?";
    $params[] = $subject_code;
    $types .= "s";
}

$sql .= " GROUP BY subject_code, student_section, semester, academic_year
          ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$results = $stmt->get_result();
$stmt->close();

// PDF generation
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, "Self Evaluation Report", 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, "Name of Faculty: $full_name", 0, 1, 'C');
$pdf->Cell(0, 7, "Department/College: $department", 0, 1, 'C');

$filterInfo = '';
if ($academic_year) $filterInfo .= "A.Y.: $academic_year  ";
if ($subject_code) $filterInfo .= "Subject: $subject_code";
if ($filterInfo) {
    $pdf->Cell(0, 10, "Filter: $filterInfo", 0, 1, 'C');
}
$pdf->Ln(2);

// Table headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$headers = ['Date', 'Subject Code', 'Subject Title', 'Section', 'A.Y.', 'Semester', 'Avg Score', 'Rating (%)', 'No. Students', 'Comments'];
$widths = [25, 30, 50, 20, 25, 20, 22, 22, 25, 55];

foreach ($headers as $i => $header) {
    $pdf->Cell($widths[$i], 10, $header, 1, 0, 'C', true);
}
$pdf->Ln();

// Table body
$pdf->SetFont('Arial', '', 9);
while ($row = $results->fetch_assoc()) {
    $pdf->Cell($widths[0], 8, date("M j, Y", strtotime($row['created_at'])), 1);
    $pdf->Cell($widths[1], 8, $row['subject_code'], 1);
    $pdf->Cell($widths[2], 8, $row['subject_title'], 1);
    $pdf->Cell($widths[3], 8, $row['student_section'], 1);
    $pdf->Cell($widths[4], 8, $row['academic_year'], 1);
    $pdf->Cell($widths[5], 8, $row['semester'], 1);
    $pdf->Cell($widths[6], 8, number_format($row['avg_total_score'], 2), 1, 0, 'C');
    $pdf->Cell($widths[7], 8, number_format($row['avg_computed_rating'], 2) . '%', 1, 0, 'C');
    $pdf->Cell($widths[8], 8, $row['student_count'], 1, 0, 'C');
    
    // Truncate comment if too long
    $comments = strlen($row['comments']) > 80 ? substr($row['comments'], 0, 77) . '...' : $row['comments'];
    $pdf->Cell($widths[9], 8, $comments, 1);
    $pdf->Ln();
}

// Footer
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Prepared by:', 0, 1, 'L');
$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(90, 6, $full_name, 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 6, 'Administrative Staff', 0, 1, 'L');
$pdf->Cell(90, 6, 'Date Signed: ' . date('F d, Y'), 0, 1, 'L');

$pdf->Output('I', 'admin-self-pastrecords.pdf');
?>

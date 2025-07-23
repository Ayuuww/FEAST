<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

if (!isset($_GET['faculty_id'])) {
    die("No faculty selected.");
}

$faculty_id = $_GET['faculty_id'];

// Faculty Info
$stmt = $conn->prepare("SELECT last_name, first_name, mid_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($lname, $fname, $mname, $department, $faculty_rank);
$stmt->fetch();
$stmt->close();
$faculty_name = "$fname $mname $lname";

// Semester & Year
$semester = $academic_year = "N/A";
$eval_q = mysqli_query($conn, "SELECT semester, academic_year FROM admin_evaluation WHERE evaluatee_id = '$faculty_id' ORDER BY evaluation_date DESC LIMIT 1");
if ($eval_q && mysqli_num_rows($eval_q) > 0) {
    $row = mysqli_fetch_assoc($eval_q);
    $semester = $row['semester'];
    $academic_year = $row['academic_year'];
} else {
    $eval_fallback = mysqli_query($conn, "SELECT semester, academic_year FROM evaluation WHERE faculty_id = '$faculty_id' ORDER BY id DESC LIMIT 1");
    if ($eval_fallback && mysqli_num_rows($eval_fallback) > 0) {
        $row = mysqli_fetch_assoc($eval_fallback);
        $semester = $row['semester'];
        $academic_year = $row['academic_year'];
    }
}

// SET Summary
$result = mysqli_query($conn, "
    SELECT subject_code, student_section, COUNT(*) as num_students,
           AVG(computed_rating) as avg_rating
    FROM evaluation
    WHERE faculty_id = '$faculty_id'
    GROUP BY subject_code, student_section
");

$total_students = 0;
$total_weighted_value = 0;
$summary = [];

while ($row = mysqli_fetch_assoc($result)) {
    $weighted = $row['num_students'] * $row['avg_rating'];
    $total_students += $row['num_students'];
    $total_weighted_value += $weighted;
    $summary[] = [
        'subject_code' => $row['subject_code'],
        'section' => $row['student_section'],
        'num_students' => $row['num_students'],
        'avg_rating' => number_format($row['avg_rating'], 2),
        'weighted' => number_format($weighted, 2)
    ];
}

$overall_set = $total_students ? number_format($total_weighted_value / $total_students, 2) : '0.00';

// SEF
$sef_result = mysqli_query($conn, "SELECT AVG(computed_rating) as sef_rating FROM admin_evaluation WHERE evaluatee_id = '$faculty_id'");
$sef_rating = number_format(mysqli_fetch_assoc($sef_result)['sef_rating'] ?? 0, 2);

// Comments
$comments_q = mysqli_query($conn, "SELECT comment FROM evaluation WHERE faculty_id = '$faculty_id' AND comment IS NOT NULL AND comment <> '' LIMIT 5");
$comments = [];
while ($row = mysqli_fetch_assoc($comments_q)) {
    $comments[] = $row['comment'];
}

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'INDIVIDUAL FACULTY EVALUATION REPORT', 0, 1, 'C');

// Section A: Faculty Info
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'A. Faculty Information', 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(80, 8, 'Name of Faculty Evaluated:', 1);
$pdf->Cell(110, 8, $faculty_name, 1, 1);

$pdf->Cell(80, 8, 'Department/College:', 1);
$pdf->Cell(110, 8, $department, 1, 1);

$pdf->Cell(80, 8, 'Current Faculty Rank:', 1);
$pdf->Cell(110, 8, $faculty_rank, 1, 1);

$pdf->Cell(80, 8, 'Semester / Academic Year:', 1);
$pdf->Cell(110, 8, "$semester / $academic_year", 1, 1);

// Section B: Summary
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'B. Summary of Average SET Rating', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 8, 'Course Code', 1);
$pdf->Cell(30, 8, 'Section', 1);
$pdf->Cell(30, 8, 'No. of Students', 1);
$pdf->Cell(40, 8, 'Avg. SET Rating', 1);
$pdf->Cell(40, 8, 'Weighted Value', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
foreach ($summary as $row) {
    $pdf->Cell(35, 8, $row['subject_code'], 1);
    $pdf->Cell(30, 8, $row['section'], 1);
    $pdf->Cell(30, 8, $row['num_students'], 1);
    $pdf->Cell(40, 8, $row['avg_rating'], 1);
    $pdf->Cell(40, 8, $row['weighted'], 1);
    $pdf->Ln();
}
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(65, 8, 'TOTAL', 1);
$pdf->Cell(30, 8, $total_students, 1);
$pdf->Cell(40, 8, '', 1);
$pdf->Cell(40, 8, number_format($total_weighted_value, 2), 1);
$pdf->Ln();

// Section C
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'C. SET and SEF Ratings', 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(80, 8, 'OVERALL SET Rating', 1);
$pdf->Cell(110, 8, $overall_set, 1, 1);

$pdf->Cell(80, 8, 'Supervisor (SEF) Rating', 1);
$pdf->Cell(110, 8, $sef_rating, 1, 1);

// Section D
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'D. Summary of Qualitative Comments and Suggestions', 0, 1);

$pdf->SetFont('Arial', '', 10);
if (count($comments) > 0) {
    foreach ($comments as $index => $comment) {
        $pdf->MultiCell(0, 8, ($index + 1) . ". " . $comment, 1);
    }
} else {
    $pdf->Cell(0, 8, 'No comments available.', 1);
}

// Section E
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'E. Development Plan', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, 'Areas for Improvement:', 1, 1);
$pdf->Cell(0, 20, '', 1, 1);

$pdf->Cell(0, 8, 'Proposed Learning and Development Activities:', 1, 1);
$pdf->Cell(0, 20, '', 1, 1);

$pdf->Cell(0, 8, 'Action Plan:', 1, 1);
$pdf->Cell(0, 20, '', 1, 1);

// Footer
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(60, 8, 'Prepared by (Staff Signature):', 1);
$pdf->Cell(60, 8, '', 1);
$pdf->Cell(30, 8, 'Name:', 1);
$pdf->Cell(40, 8, '', 1, 1);

$pdf->Cell(60, 8, 'Reviewed by (Authorized Official):', 1);
$pdf->Cell(60, 8, '', 1);
$pdf->Cell(30, 8, 'Name:', 1);
$pdf->Cell(40, 8, '', 1);

$pdf->Output('I', 'Individual-Faculty-Evaluation.pdf');
?>

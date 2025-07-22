<?php
session_start();
require('fpdf/fpdf.php');
include 'conn/conn.php';

// Check student login
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
  header("Location: pages-login.php");
  exit();
}

$student_id = $_SESSION['idnumber'];
$faculty_id = $_GET['faculty_id'] ?? '';
$subject_code = $_GET['subject_code'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';
$department = $_GET['department'] ?? '';

if (!$faculty_id || !$subject_code || !$academic_year) {
  echo "Missing parameters.";
  exit();
}

// Fetch evaluation data
$stmt = $conn->prepare("SELECT * FROM student_evaluation_submissions WHERE student_id = ? AND faculty_id = ? AND subject_code = ? AND academic_year = ?");
$stmt->bind_param("ssss", $student_id, $faculty_id, $subject_code, $academic_year);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "No record found.";
  exit();
}

$data = $result->fetch_assoc();
$answers = json_decode($data['answers'], true);

// Helper: Get full name
function getName($conn, $table, $id)
{
  $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM $table WHERE idnumber = ?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res->num_rows > 0) {
    $r = $res->fetch_assoc();
    return $r['first_name'] . ' ' . $r['mid_name'] . ' ' . $r['last_name'];
  }
  return 'Unknown';
}

$faculty_name = getName($conn, 'faculty', $faculty_id);
$student_name = getName($conn, 'student', $student_id);

// Get subject title
$subject_title = '';
$sub_stmt = $conn->prepare("SELECT title FROM subject WHERE code = ?");
$sub_stmt->bind_param("s", $subject_code);
$sub_stmt->execute();
$sub_stmt->bind_result($subject_title);
$sub_stmt->fetch();
$sub_stmt->close();
$data['subject_title'] = $subject_title;

// Benchmark Questions
$questions = [
  "Comes to class on time regularly.",
  "Explains learning outcomes, expectations, grading system, and various requirements of the subject/course.",
  "Maximizes the allocated time/learning hours effectively.",
  "Facilitates students to think critically and creatively by providing appropriate learning activities.",
  "Guides students to learn on their own, reflect on new ideas and experiences, and make decisions in accomplishing given tasks.",
  "Communicates constructive feedback to students for their academic growth.",
  "Demonstrates extensive and broad knowledge of the subject/course.",
  "Simplifies complex ideas in the lesson for ease of understanding.",
  "Relates the subject matter to contemporary issues and developments in the discipline and/or daily life activities.",
  "Promotes active learning and student engagement by using appropriate teaching and learning resources including ICT Tools and platforms.",
  "Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes",
  "Recognizes and values the unique diversity and individuality difference among students.",
  "Assist students with their learning challenges during consultation hours.",
  "Provide immediate feedback on student outputs and performance.",
  "Provides transparent and clear criteria in rating student's performance."
];

// Create PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Faculty Evaluation Summary', 0, 1, 'C');
$pdf->Ln(3);

// Info Section
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Name of Faculty: " . $faculty_name, 0, 1);
$pdf->Cell(0, 6, "Department/College: " . $data['department'], 0, 1);
$pdf->Cell(0, 6, "Course Code/Title: " . $data['subject_code'] . " - " . $data['subject_title'], 0, 1);
$pdf->Cell(0, 6, "Rating Period (Academic Year): " . $data['academic_year'], 0, 1);
$pdf->Ln(3);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(150, 8, "Benchmark Statement", 1, 0, 'L', true);
$pdf->Cell(30, 8, "Rating (1-5)", 1, 1, 'C', true);

// Table Body
$pdf->SetFont('Arial', '', 10);
$lineHeight = 6;

foreach ($questions as $i => $q) {
  $question = ($i + 1) . ". " . $q;
  $rating = $answers["q$i"] ?? '-';

  // Handle multi-line cell alignment
  $lines = ceil($pdf->GetStringWidth($question) / 145);
  $rowHeight = max($lineHeight, $lines * $lineHeight);
  $x = $pdf->GetX();
  $y = $pdf->GetY();

  $pdf->MultiCell(150, $lineHeight, $question, 1, 'L');
  $pdf->SetXY($x + 150, $y);
  $pdf->Cell(30, $rowHeight, $rating, 1, 1, 'C');
}

// Scores
$pdf->Ln(3);
$pdf->Cell(0, 6, "Total Score: " . ($data['total_score'] ?? '-') . " / 75", 0, 1);
$pdf->Cell(0, 6, "Computed Rating: " . number_format($data['computed_rating'] ?? 0, 2) . "%", 0, 1);
$pdf->Ln(2);

// Comment
if (!empty($data['comment'])) {
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(0, 6, "Additional Comment:", 0, 1);
  $pdf->SetFont('Arial', '', 9);
  $pdf->MultiCell(0, 5, $data['comment'], 1);
  $pdf->Ln(2);
}

// Signature
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Signature of Evaluator: ____________________________", 0, 1);
$pdf->Cell(0, 6, "Name of Evaluator/ID Number: " . $student_name . " / " . $student_id, 0, 1);
$pdf->Cell(0, 6, "Date of Evaluation: " . date('F j, Y'), 0, 1);

// Output
$pdf->Output('I', 'Student_Evaluation_Reprint.pdf');
?>
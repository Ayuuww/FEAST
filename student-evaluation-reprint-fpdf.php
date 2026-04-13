<?php
session_start();
require('fpdf/fpdf.php');
include 'conn/conn.php';

// ✅ Check student login
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
  header("Location: pages-login.php");
  exit();
}

$student_id = $_SESSION['idnumber'];
$faculty_id = $_GET['faculty_id'] ?? '';
$subject_code = $_GET['subject_code'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';
$college = $_GET['college'] ?? '';

if (!$faculty_id || !$subject_code || !$academic_year) {
  echo "Missing parameters.";
  exit();
}

// ✅ Fetch the most recent evaluation record
$stmt = $conn->prepare("
  SELECT * FROM student_evaluation_submissions 
  WHERE student_id = ? AND faculty_id = ? AND subject_code = ? AND academic_year = ?
  ORDER BY id DESC LIMIT 1
");
$stmt->bind_param("ssss", $student_id, $faculty_id, $subject_code, $academic_year);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "No record found.";
  exit();
}

$data = $result->fetch_assoc();
$answers = json_decode($data['answers'], true);

// ✅ Helper: Get full name
function getName($conn, $table, $id)
{
  $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM $table WHERE idnumber = ?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res->num_rows > 0) {
    $r = $res->fetch_assoc();
    return trim($r['first_name'] . ' ' . $r['mid_name'] . ' ' . $r['last_name']);
  }
  return 'Unknown';
}

$faculty_name = getName($conn, 'faculty', $faculty_id);
$student_name = getName($conn, 'student', $student_id);

// ✅ Hide student info if anonymous
$is_anonymous = strtolower($data['is_anonymous'] ?? 'no');

// ✅ Get subject title
$subject_title = '';
$sub_stmt = $conn->prepare("SELECT title FROM subject WHERE code = ?");
$sub_stmt->bind_param("s", $subject_code);
$sub_stmt->execute();
$sub_stmt->bind_result($subject_title);
$sub_stmt->fetch();
$sub_stmt->close();
$data['subject_title'] = $subject_title;


// ==========================================
// DYNAMIC QUESTION FETCHING & LEGACY SUPPORT
// ==========================================
$categories = [];
$total_answered_questions = 0;

// ✅ Check for saved metadata snapshot (defaults to 5 if it's an old record)
$saved_max_scale = $answers['metadata']['max_scale'] ?? 5;

// Check if this is an old record using the static q0, q1 format
$is_legacy_record = isset($answers['q0']);

if ($is_legacy_record) {
  // BACKWARD COMPATIBILITY
  $max_rating_value = 5; // Legacy records were hardcoded to 5

  $legacy_questions = [
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
    "Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes.",
    "Recognizes and values the unique diversity and individuality difference among students.",
    "Assist students with their learning challenges during consultation hours.",
    "Provide immediate feedback on student outputs and performance.",
    "Provides transparent and clear criteria in rating student's performance."
  ];

  $categories[0] = ['category_name' => 'Evaluation (Legacy Record)', 'questions' => []];

  foreach ($legacy_questions as $index => $q_text) {
    $categories[0]['questions'][] = ['id' => 'legacy_' . $index, 'question_text' => $q_text];
    $answers["q_legacy_" . $index] = $answers["q" . $index];
    $total_answered_questions++;
  }
} else {
  // MODERN DYNAMIC FETCH
  $max_rating_value = $saved_max_scale; // ✅ FIX: Use the snapshotted scale value!

  $cat_res = $conn->query("SELECT * FROM evaluation_categories ORDER BY order_by ASC");
  if ($cat_res) {
    while ($cat = $cat_res->fetch_assoc()) {
      $cat_id = $cat['id'];
      $q_stmt = $conn->prepare("SELECT id, question_text FROM evaluation_questions WHERE category_id = ? ORDER BY order_by ASC");
      $q_stmt->bind_param("i", $cat_id);
      $q_stmt->execute();
      $q_result = $q_stmt->get_result();

      $questions = [];
      while ($q = $q_result->fetch_assoc()) {
        if (isset($answers["q_" . $q['id']])) {
          $questions[] = $q;
          $total_answered_questions++;
        }
      }
      $q_stmt->close();

      if (!empty($questions)) {
        $cat['questions'] = $questions;
        $categories[] = $cat;
      }
    }
  }
}

// ✅ Prioritize snapshot max score, fallback to calculation if legacy
$max_possible_score = $answers['metadata']['max_score'] ?? ($total_answered_questions * $max_rating_value);


// ✅ Custom PDF class
require 'printing-headerfooter.php';

// ✅ Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Faculty Evaluation Summary', 0, 1, 'C');
$pdf->Ln(3);

// Info Section
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, "Name of Faculty being Evaluated: " . $faculty_name, 0, 1);
$pdf->Cell(0, 6, "College: " . $data['college'], 0, 1);
$pdf->Cell(0, 6, "Course Code/Title: " . $data['subject_code'] . " - " . $data['subject_title'], 0, 1);
$pdf->Cell(0, 6, "Rating Period (Academic Year): " . $data['academic_year'], 0, 1);
$pdf->Ln(4);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(150, 8, "Benchmark Statement", 1, 0, 'L', true);
// ✅ Dynamic Header
$pdf->Cell(30, 8, "Rating (1-{$max_rating_value})", 1, 1, 'C', true);

// Table Body
$lineHeight = 6;
$q_number = 1;
$pageBreakThreshold = 270;

foreach ($categories as $category) {
  $pdf->SetFont('Arial', 'B', 9);
  $pdf->SetFillColor(245, 245, 245);
  $pdf->Cell(180, 8, "   " . mb_strtoupper($category['category_name']), 1, 1, 'L', true);

  $pdf->SetFont('Arial', '', 10);

  foreach ($category['questions'] as $q) {
    $question_text = $q_number . ". " . $q['question_text'];
    $q_id = $q['id'];
    $rating = $answers["q_$q_id"] ?? '-';

    $stringWidth = $pdf->GetStringWidth($question_text);
    $estimatedLines = ceil($stringWidth / 145);
    $estimatedHeight = $estimatedLines * $lineHeight;

    if ($pdf->GetY() + $estimatedHeight > $pageBreakThreshold) {
      $pdf->AddPage();
    }

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->MultiCell(150, $lineHeight, $question_text, 1, 'L');
    $questionHeight = $pdf->GetY() - $y;

    $pdf->SetXY($x + 150, $y);
    $pdf->Cell(30, $questionHeight, $rating, 1, 1, 'C');

    $pdf->SetY($y + $questionHeight);
    $q_number++;
  }
}

// Scores
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, "Total Score: " . ($data['total_score'] ?? '-') . " / " . $max_possible_score, 0, 1);
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

// ✅ Signature Section
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Signature of Evaluator: ____________________________", 0, 1);

if ($is_anonymous === 'yes') {
  $pdf->Cell(0, 6, "Name of Evaluator/ID Number: [ Anonymous Submission ]", 0, 1);
} else {
  $pdf->Cell(0, 6, "Name of Evaluator/ID Number: " . $student_name . " / " . $student_id, 0, 1);
}

$pdf->Cell(0, 6, "Date of Evaluation: " . date('F j, Y', strtotime($data['created_at'] ?? 'now')), 0, 1);

// ✅ Output
$pdf->Output('I', 'Student_Evaluation_Reprint.pdf');
exit;

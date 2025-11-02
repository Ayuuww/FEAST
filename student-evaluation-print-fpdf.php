<?php
session_start();
require('fpdf/fpdf.php');
include 'conn/conn.php';

// Check student login
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
    header("Location: pages-login.php");
    exit();
}

// ✅ Use session data from submit-evaluation.php
if (!isset($_SESSION['print_data'])) {
    echo "<script>alert('No evaluation data found to print.'); window.close();</script>";
    exit();
}

$data = $_SESSION['print_data'];
$student_id = $data['student_id'];
$faculty_id = $data['faculty_id'];
$subject_code = $data['subject_code'];
$academic_year = $data['academic_year'];
$department = $data['department'];
$is_anonymous = $data['is_anonymous'] ?? 'no';
$answers = $data['answers'] ?? [];

// Helper: Get full name
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
    "Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes.",
    "Recognizes and values the unique diversity and individuality difference among students.",
    "Assist students with their learning challenges during consultation hours.",
    "Provide immediate feedback on student outputs and performance.",
    "Provides transparent and clear criteria in rating student's performance."
];

// Custom PDF class
require 'printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Faculty Evaluation Summary', 0, 1, 'C');
$pdf->Ln(3);

// Info Section
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, "Name of Faculty being Evaluated: " . $faculty_name, 0, 1);
$pdf->Cell(0, 6, "Department/College: " . $department, 0, 1);
$pdf->Cell(0, 6, "Course Code/Title: " . $subject_code . " - " . $subject_title, 0, 1);
$pdf->Cell(0, 6, "Rating Period (Academic Year): " . $academic_year, 0, 1);
$pdf->Ln(4);

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

    // Save current X and Y position
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Draw question (MultiCell for wrapping)
    $pdf->MultiCell(150, $lineHeight, $question, 1, 'L');

    // Calculate the height used by the question text
    $questionHeight = $pdf->GetY() - $y;

    // Draw the rating box beside it
    $pdf->SetXY($x + 150, $y);
    $pdf->Cell(30, $questionHeight, $rating, 1, 1, 'C');
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

// ✅ Anonymous handling
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Signature of Evaluator: ____________________________", 0, 1);

if (strtolower($is_anonymous) === 'yes') {
    $pdf->Cell(0, 6, "Name of Evaluator/ID Number: ", 0, 1);
} else {
    $pdf->Cell(0, 6, "Name of Evaluator/ID Number: " . $student_name . " / " . $student_id, 0, 1);
}

$pdf->Cell(0, 6, "Date of Evaluation: " . date('F j, Y'), 0, 1);

// Output
$pdf->Output('I', 'Student_Evaluation_Reprint.pdf');

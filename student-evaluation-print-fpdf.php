<?php
session_start();
require('fpdf/fpdf.php'); // Make sure the path is correct
include 'conn/conn.php';

if (!isset($_SESSION['print_data'])) {
    header("Location: student-evaluate.php");
    exit();
}

$data = $_SESSION['print_data'];
unset($_SESSION['print_data']); // Prevent reprint on refresh

$student_name = 'Unknown Student';
$student_id = $data['student_id'] ?? '';

if ($student_id) {
    $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM student WHERE idnumber = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $s = $result->fetch_assoc();
        $student_name = $s['first_name'] . ' ' . $s['mid_name'] . ' ' . $s['last_name'];
    }
}

$faculty_name = 'Unknown Faculty';
$faculty_id = $data['faculty_id'] ?? '';

if ($faculty_id) {
    $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $f = $result->fetch_assoc();
        $faculty_name = $f['first_name'] . ' ' . $f['mid_name'] . ' ' . $f['last_name'];
    }
}

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

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Faculty Evaluation Summary', 0, 1, 'C');
$pdf->Ln(3);

// Faculty Info
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Name of Faculty being Evaluated: " . $faculty_name, 0, 1);
$pdf->Cell(0, 6, "Department/College: " . $data['department'], 0, 1);
$pdf->Cell(0, 6, "Subject Code/Title: " . $data['subject_code'] . " / " . $data['subject_title'], 0, 1);
$pdf->Cell(0, 6, "Academic Year: " . $data['academic_year'], 0, 1);
$pdf->Ln(4);

// Table Headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(150, 8, "Benchmark Statement", 1, 0, 'L', true);
$pdf->Cell(30, 8, "Rating (1-5)", 1, 1, 'C', true);

// Table Data
$pdf->SetFont('Arial', '', 10);
$lineHeight = 6; // standard line height

foreach ($questions as $i => $q) {
    $question = ($i + 1) . ". " . $q;
    $rating = $data['answers']["q$i"] ?? '-';

    // Get number of lines the question will take
    $lines = $pdf->GetStringWidth($question) / 145;
    $numLines = ceil($lines);
    $rowHeight = max($lineHeight, $numLines * $lineHeight);

    // Save current X and Y
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Draw the question cell
    $pdf->MultiCell(150, $lineHeight, $question, 1, 'L');

    // Move back to the right of the first cell
    $pdf->SetXY($x + 150, $y);
    $pdf->Cell(30, $rowHeight, $rating, 1, 1, 'C');
}

$pdf->Ln(3);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Total Score: " . ($data['total_score'] ?? '-') . " / 75", 0, 1);
$pdf->Cell(0, 6, "Computed Rating: " . number_format($data['computed_rating'] ?? 0, 2) . "%", 0, 1);
$pdf->Ln(2);

// Comment Section
if (!empty($data['comment'])) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, "Additional Comment:", 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $data['comment'], 1);
    $pdf->Ln(2);
}

// Signature and Evaluator Info
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Signature of Evaluator: ____________________________", 0, 1);
$pdf->Cell(0, 6, "Name of Evaluator/ID Number: " . $student_name . " / " . $student_id, 0, 1);
$pdf->Cell(0, 6, "Date of Evaluation: " . date('F j, Y'), 0, 1);

// Output the PDF
$pdf->Output('I', 'Evaluation_Summary.pdf');
?>

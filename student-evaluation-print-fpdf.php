<?php
session_start();
require('fpdf/fpdf.php');
include 'conn/conn.php';

// Check student login
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
    header("Location: pages-login.php");
    exit();
}

// Use session data from submit-evaluation.php
if (!isset($_SESSION['print_data'])) {
    echo "<script>alert('No evaluation data found to print.'); window.close();</script>";
    exit();
}

$data = $_SESSION['print_data'];
$student_id = $data['student_id'];
$faculty_id = $data['faculty_id'];
$subject_code = $data['subject_code'];
$academic_year = $data['academic_year'];
$college = $data['college'];
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


// ==========================================
// DYNAMIC QUESTION FETCHING & SNAPSHOT SYNC
// ==========================================
$categories = [];
$total_active_questions = 0;

$cat_res = $conn->query("SELECT * FROM evaluation_categories ORDER BY order_by ASC");
if ($cat_res) {
    while ($cat = $cat_res->fetch_assoc()) {
        $cat_id = $cat['id'];

        $q_stmt = $conn->prepare("SELECT id, question_text FROM evaluation_questions WHERE category_id = ? AND status = 'active' ORDER BY order_by ASC");
        $q_stmt->bind_param("i", $cat_id);
        $q_stmt->execute();
        $q_result = $q_stmt->get_result();

        $questions = [];
        while ($q = $q_result->fetch_assoc()) {
            $questions[] = $q;
            $total_active_questions++;
        }
        $q_stmt->close();

        if (!empty($questions)) {
            $cat['questions'] = $questions;
            $categories[] = $cat;
        }
    }
}

// ✅ Use Snapshot Metadata from submission to ensure mathematical consistency
$max_rating_value = $answers['metadata']['max_scale'] ?? 5;
$max_possible_score = $answers['metadata']['max_score'] ?? ($total_active_questions * 5);


// Custom PDF class (assuming you have this configured in printing-headerfooter.php)
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
$pdf->Cell(0, 6, "College: " . $college, 0, 1);
$pdf->Cell(0, 6, "Course Code/Title: " . $subject_code . " - " . $subject_title, 0, 1);
$pdf->Cell(0, 6, "Rating Period (Academic Year): " . $academic_year, 0, 1);
$pdf->Ln(4);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(150, 8, "Benchmark Statement", 1, 0, 'L', true);
// ✅ Dynamic Header using snapshot data
$pdf->Cell(30, 8, "Rating (1-{$max_rating_value})", 1, 1, 'C', true);

// Table Body
$lineHeight = 6;
$q_number = 1;
$pageBreakThreshold = 270;

foreach ($categories as $category) {
    // Print Category Header Row
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(245, 245, 245); // Lighter gray for categories
    $pdf->Cell(180, 8, "   " . mb_strtoupper($category['category_name']), 1, 1, 'L', true);

    $pdf->SetFont('Arial', '', 10);

    foreach ($category['questions'] as $q) {
        $question_text = $q_number . ". " . $q['question_text'];
        $q_id = $q['id'];

        // Grab the answer matching this dynamic question ID
        $rating = $answers["q_$q_id"] ?? '-';

        // --- Page Break Protection ---
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
// ✅ Uses snapshotted max possible score
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

// Anonymous handling
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Signature of Evaluator: ____________________________", 0, 1);

if (strtolower($is_anonymous) === 'yes') {
    $pdf->Cell(0, 6, "Name of Evaluator/ID Number: [ Anonymous Submission ]", 0, 1);
} else {
    $pdf->Cell(0, 6, "Name of Evaluator/ID Number: " . $student_name . " / " . $student_id, 0, 1);
}

$pdf->Cell(0, 6, "Date of Evaluation: " . date('F j, Y'), 0, 1);

// Output PDF
$pdf->Output('I', 'Student_Evaluation_Print.pdf');

<?php
session_start();
require('fpdf/fpdf.php');
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    header("Location: pages-login.php");
    exit();
}

// Get parameters from URL
$evaluator_id   = $_SESSION['idnumber'];
$evaluatee_id   = $_GET['evaluatee_id'] ?? '';
$academic_year  = $_GET['academic_year'] ?? '';
$semester       = $_GET['semester'] ?? '';

if (!$evaluatee_id || !$academic_year || !$semester) {
    die("Missing parameters.");
}

// Fetch evaluation record (Checking both standard answers column or legacy form_data)
$stmt = $conn->prepare("SELECT * FROM admin_evaluation_submissions WHERE evaluatee_id = ? AND academic_year = ? AND semester = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("sss", $evaluatee_id, $academic_year, $semester);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Evaluation not found.");
}

$data = $result->fetch_assoc();
// Support both old JSON column names (form_data) and new (answers)
$raw_json = $data['answers'] ?? $data['form_data'] ?? '{}';
$answers = json_decode($raw_json, true);

// Helpers
function getFacultyDetails($conn, $id)
{
    $stmt = $conn->prepare("SELECT first_name, mid_name, last_name, college, faculty_rank FROM faculty WHERE idnumber = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?? null;
}

function getAdminDetails($conn, $id)
{
    $stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position FROM admin WHERE idnumber = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?? null;
}

$evaluatorData = getAdminDetails($conn, $data['evaluator_id']);
$evaluateeData = getFacultyDetails($conn, $data['evaluatee_id']);

$evaluatorName = $evaluatorData ? trim($evaluatorData['first_name'] . ' ' . $evaluatorData['mid_name'] . ' ' . $evaluatorData['last_name']) : 'Unknown';
$evaluateeName = $evaluateeData ? trim($evaluateeData['first_name'] . ' ' . $evaluateeData['mid_name'] . ' ' . $evaluateeData['last_name']) : 'Unknown';

$evaluateeDept = $evaluateeData['college'] ?? 'N/A';
$evaluateeRank = $evaluateeData['faculty_rank'] ?? 'N/A';
$evaluatorPosition = $evaluatorData['position'] ?? 'Not Set';

// Helper function to safely calculate MultiCell height 
function getEstimatedHeight($pdf, $width, $text, $lineHeight)
{
    $lines = explode("\n", $text);
    $totalLines = 0;
    foreach ($lines as $line) {
        $stringWidth = $pdf->GetStringWidth($line);
        $lineCount = ceil($stringWidth / ($width - 4)); // Subtract 4 for cell padding
        $totalLines += ($lineCount == 0) ? 1 : $lineCount;
    }
    return $totalLines * $lineHeight;
}

// ✅ Fetch highest rating dynamically from database
$scale_res = $conn->query("SELECT MAX(scale_value) as max_val FROM evaluation_rating_scales");
$max_rating_value = $scale_res->fetch_assoc()['max_val'] ?? 5;


// ==========================================
// DYNAMIC FETCHING & LEGACY SUPPORT
// ==========================================
$is_legacy_record = isset($answers['q0']);
$categories = [];
$total_answered_questions = 0;

if ($is_legacy_record) {
    // Legacy support setup
    $max_possible_score = 75;
    $legacy_questions = [
        "Comes to class on time regularly.",
        "Submits updated syllabus, grade sheets, and other required reports on time.",
        "Maximizes the allocated time/learning hours effectively.",
        "Provide appropriate learning activities that facilitate critical thinking and creativity of students.",
        "Guides students to learn on their own, reflect on new ideas and experiences, and make decisions in accomplishing given tasks.",
        "Communicates constructive feedback to students for their academic growth.",
        "Demonstrates extensive and broad knowledge of the subject/course.",
        "Simplifies complex ideas in the lesson for ease of understanding.",
        "Integrates contemporary issues and developments in the discipline and/or daily life activities in the syllabus.",
        "Promotes active learning and student engagement by using appropriate teaching and learning resources including ICT Tools and platforms.",
        "Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes",
        "Recognizes and values the unique diversity and individual differences among students.",
        "Assist students with their learning challenges during consultation hours.",
        "Provide immediate feedback on student outputs and performance.",
        "Provides transparent and clear criteria in rating student's performance."
    ];
    $verifications = [
        " Daily time record, Faculty schedule, Informal interview with students",
        " Submission logs, Receipts or Acknowledgment emails",
        " Syllabus, Learning plan, LMS logs, Classroom observations",
        " Course syllabus, LMS logs, Informal interviews",
        " Work samples, Consultation logs, Classroom observations",
        " Graded work, Consultation logs, LMS logs",
        " Syllabus, Learning plan, Instructional Materials",
        " Lecture notes, Presentations, Observations",
        " Syllabus, Webinars, Daily life examples",
        " Multimedia, LMS logs, Classroom observations",
        " Assessment tools, Rubrics, Samples",
        " IMs, Classroom observation, Student diversity notes",
        " Advisory logs, Consult hours, LMS logs",
        " Rubrics, Feedback, Informal interviews",
        " Syllabus, Student outputs, Observations"
    ];
} else {
    // Dynamic Fetch for Modern Records
    $cat_res = $conn->query("SELECT * FROM admin_evaluation_categories ORDER BY order_by ASC");
    if ($cat_res) {
        while ($cat = $cat_res->fetch_assoc()) {
            $cat_id = $cat['id'];
            $q_stmt = $conn->prepare("SELECT id, question_text FROM admin_evaluation_questions WHERE category_id = ? ORDER BY order_by ASC");
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
    // Calculate max possible score using dynamic rating value
    $max_possible_score = $total_answered_questions * $max_rating_value;
}


// Custom PDF class
require 'printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, -5, 'Supervisor-to-Faculty Evaluation Summary', 0, 1, 'C');
$pdf->Ln(10);

// Faculty info
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, "Evaluatee: " . $evaluateeName, 0, 1);
$pdf->Cell(0, 6, "Academic Rank: " . $evaluateeRank, 0, 1);
$pdf->Cell(0, 6, "College: " . $evaluateeDept, 0, 1);
$pdf->Cell(0, 6, "Rating Period (Academic Year): " . $data['academic_year'], 0, 1);
$pdf->Ln(4);


if ($is_legacy_record) {
    // --- RENDER LEGACY 3-COLUMN TABLE ---
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(80, 8, "Benchmark Statement", 1, 0, 'C', true);
    $pdf->Cell(80, 8, "Suggested Means of Verification", 1, 0, 'C', true);
    $pdf->Cell(30, 8, "Rating", 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 9);
    $lineHeight = 5;
    $pageBreakThreshold = 270;

    foreach ($legacy_questions as $i => $q) {
        $question = ($i + 1) . ". " . $q;
        $verify = trim($verifications[$i]);
        $rating = $answers["q$i"] ?? '-';

        $w1 = 80;
        $w2 = 80;
        $w3 = 30;

        $h1 = getEstimatedHeight($pdf, $w1, $question, $lineHeight);
        $h2 = getEstimatedHeight($pdf, $w2, $verify, $lineHeight);
        $maxHeight = max($h1, $h2) + 2;

        if ($pdf->GetY() + $maxHeight > $pageBreakThreshold) {
            $pdf->AddPage();
        }

        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $pdf->Rect($x, $y, $w1, $maxHeight);
        $pdf->Rect($x + $w1, $y, $w2, $maxHeight);
        $pdf->Rect($x + $w1 + $w2, $y, $w3, $maxHeight);

        $pdf->SetXY($x, $y + 1);
        $pdf->MultiCell($w1, $lineHeight, $question, 0, 'L');
        $pdf->SetXY($x + $w1, $y + 1);
        $pdf->MultiCell($w2, $lineHeight, $verify, 0, 'L');

        // Center the rating vertically and horizontally
        $pdf->SetXY($x + $w1 + $w2, $y + 1);
        $pdf->MultiCell($w3, $lineHeight, $rating, 0, 'C');

        $pdf->SetY($y + $maxHeight);
    }
} else {
    // --- RENDER MODERN DYNAMIC TABLE ---
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(80, 8, "Benchmark Statement", 1, 0, 'C', true);
    $pdf->Cell(80, 8, "Checked Verifications", 1, 0, 'C', true);
    $pdf->Cell(30, 8, "Rating (1-{$max_rating_value})", 1, 1, 'C', true);

    $lineHeight = 5;
    $q_number = 1;
    $pageBreakThreshold = 270;
    $w1 = 80;
    $w2 = 80;
    $w3 = 30;

    foreach ($categories as $category) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(190, 8, "   " . mb_strtoupper($category['category_name']), 1, 1, 'L', true);

        $pdf->SetFont('Arial', '', 9);
        foreach ($category['questions'] as $q) {
            $question_text = $q_number . ". " . $q['question_text'];
            $q_id = $q['id'];
            $rating = $answers["q_$q_id"] ?? '-';

            // Extract checked verifications for this question
            $v_array = $answers["v_$q_id"] ?? [];
            if (empty($v_array)) {
                $verify_text = "None selected.";
            } else {
                $verify_text = chr(149) . " " . implode("\n" . chr(149) . " ", $v_array);
            }

            // Calculate heights
            $h1 = getEstimatedHeight($pdf, $w1, $question_text, $lineHeight);
            $h2 = getEstimatedHeight($pdf, $w2, $verify_text, $lineHeight);
            $maxHeight = max($h1, $h2) + 2;

            if ($pdf->GetY() + $maxHeight > $pageBreakThreshold) {
                $pdf->AddPage();
            }

            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // Draw boundaries
            $pdf->Rect($x, $y, $w1, $maxHeight);
            $pdf->Rect($x + $w1, $y, $w2, $maxHeight);
            $pdf->Rect($x + $w1 + $w2, $y, $w3, $maxHeight);

            // Print text
            $pdf->SetXY($x, $y + 1);
            $pdf->MultiCell($w1, $lineHeight, $question_text, 0, 'L');

            $pdf->SetXY($x + $w1, $y + 1);
            $pdf->MultiCell($w2, $lineHeight, $verify_text, 0, 'L');

            $pdf->SetXY($x + $w1 + $w2, $y + 1);
            $pdf->MultiCell($w3, $lineHeight, $rating, 0, 'C');

            $pdf->SetY($y + $maxHeight);
            $q_number++;
        }
    }
}

// Total and rating
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, "Total Score: " . ($data['total_score'] ?? '-') . " / " . $max_possible_score, 0, 1);
// Handle column name variation (computed_rating vs rating_percent)
$computed_rating = $data['computed_rating'] ?? $data['rating_percent'] ?? 0;
$pdf->Cell(0, 6, "Computed Rating: " . number_format($computed_rating, 2) . "%", 0, 1);
$pdf->Ln(2);

// Comment
if (!empty($data['comment']) && !empty(trim($data['comment']))) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, "Additional Comment:", 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $data['comment'], 1);
    $pdf->Ln(2);
}

// Signature
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Signature of Supervisor: ____________________________", 0, 1);
$pdf->Cell(0, 6, "Name of Supervisor: " . $evaluatorName, 0, 1);
$pdf->Cell(0, 6, "Position of Supervisor: " . $evaluatorPosition, 0, 1);
$pdf->Cell(0, 6, "Date of Evaluation: " . date('F j, Y', strtotime($data['created_at'] ?? $data['submission_date'] ?? 'now')), 0, 1);

// Output
$pdf->Output('I', 'Supervisor_Evaluation_' . $evaluatee_id . '.pdf');
exit;

<?php
session_start();
require('fpdf/fpdf.php');
include 'conn/conn.php';

if (!isset($_SESSION['admin_print_data'])) {
    header("Location: admin-evaluate.php");
    exit();
}

$data = $_SESSION['admin_print_data'];
unset($_SESSION['admin_print_data']); // Prevent reprint on refresh

$answers = $data['answers'] ?? [];

function getFacultyName($conn, $id)
{
    $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $r = $res->fetch_assoc();
        return trim($r['first_name'] . ' ' . $r['mid_name'] . ' ' . $r['last_name']);
    }
    return 'Unknown';
}

$evaluatorName  = getFacultyName($conn, $data['evaluator_id']);
$evaluateeName  = getFacultyName($conn, $data['evaluatee_id']);

// ==========================================
// DYNAMIC FETCHING & SNAPSHOT SYNC
// ==========================================
$categories = [];
$total_active_questions = 0;

$cat_res = $conn->query("SELECT * FROM admin_evaluation_categories ORDER BY order_by ASC");
if ($cat_res) {
    while ($cat = $cat_res->fetch_assoc()) {
        $cat_id = $cat['id'];

        $q_stmt = $conn->prepare("SELECT id, question_text FROM admin_evaluation_questions WHERE category_id = ? AND status = 'active' ORDER BY order_by ASC");
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

// Helper function to safely calculate MultiCell height 
function getEstimatedHeight($pdf, $width, $text, $lineHeight)
{
    $lines = explode("\n", $text);
    $totalLines = 0;
    foreach ($lines as $line) {
        $stringWidth = $pdf->GetStringWidth($line);
        $lineCount = ceil($stringWidth / ($width - 4));
        $totalLines += ($lineCount == 0) ? 1 : $lineCount;
    }
    return $totalLines * $lineHeight;
}

// Custom PDF class
require 'printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, -5, 'Supervisor-to-Faculty Evaluation Summary', 0, 1, 'C');
$pdf->Ln(10);

// Faculty Info
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, "Evaluatee: " . $evaluateeName, 0, 1);
$pdf->Cell(0, 6, "Academic Rank: " . ($data['faculty_rank'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 6, "College: " . ($data['college'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 6, "Rating Period (Academic Year): " . $data['academic_year'], 0, 1);
$pdf->Ln(4);

// Table Headers
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(80, 8, "Benchmark Statement", 1, 0, 'C', true);
$pdf->Cell(80, 8, "Checked Verifications", 1, 0, 'C', true);
// ✅ Dynamic Header using snapshot data
$pdf->Cell(30, 8, "Rating (1-{$max_rating_value})", 1, 1, 'C', true);

// Table Body Settings
$lineHeight = 5;
$q_number = 1;
$pageBreakThreshold = 270;
$w1 = 80;
$w2 = 80;
$w3 = 30;

foreach ($categories as $category) {
    // Print Category Header Row
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Cell(190, 8, "   " . mb_strtoupper($category['category_name']), 1, 1, 'L', true);

    $pdf->SetFont('Arial', '', 9);

    foreach ($category['questions'] as $q) {
        $question_text = $q_number . ". " . $q['question_text'];
        $q_id = $q['id'];

        $rating = $answers["q_$q_id"] ?? '-';

        $v_array = $answers["v_$q_id"] ?? [];
        if (empty($v_array)) {
            $verify_text = "None selected.";
        } else {
            $verify_text = chr(149) . " " . implode("\n" . chr(149) . " ", $v_array);
        }

        $h1 = getEstimatedHeight($pdf, $w1, $question_text, $lineHeight);
        $h2 = getEstimatedHeight($pdf, $w2, $verify_text, $lineHeight);
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
        $pdf->MultiCell($w1, $lineHeight, $question_text, 0, 'L');

        $pdf->SetXY($x + $w1, $y + 1);
        $pdf->MultiCell($w2, $lineHeight, $verify_text, 0, 'L');

        $pdf->SetXY($x + $w1 + $w2, $y + 1);
        $pdf->MultiCell($w3, $lineHeight, $rating, 0, 'C');

        $pdf->SetY($y + $maxHeight);
        $q_number++;
    }
}

// Score Section
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 10);
// ✅ Uses snapshotted max possible score
$pdf->Cell(0, 6, "Total Score: " . ($data['total_score'] ?? '-') . " / " . $max_possible_score, 0, 1);
$pdf->Cell(0, 6, "Computed Rating: " . number_format($data['computed_rating'] ?? 0, 2) . "%", 0, 1);
$pdf->Ln(2);

// Comment Section
if (!empty($data['comment']) && !empty(trim($data['comment']))) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, "Additional Comment:", 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $data['comment'], 1);
    $pdf->Ln(2);
}

// Signature Block
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Signature of Supervisor: ____________________________", 0, 1);
$pdf->Cell(0, 6, "Name of Supervisor: " . $evaluatorName, 0, 1);
$pdf->Cell(0, 6, "Position of Supervisor: " . ($data['evaluator_position'] ?? 'Not Set'), 0, 1);
$pdf->Cell(0, 6, "Date of Evaluation: " . date('F j, Y'), 0, 1);

// Output PDF
$pdf->Output('I', 'Supervisor_Evaluation.pdf');
exit;

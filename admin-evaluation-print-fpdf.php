<?php
session_start();
require('fpdf/fpdf.php');
include 'conn/conn.php';

if (!isset($_SESSION['admin_print_data'])) {
    header("Location: admin-evaluate.php");
    exit();
}
class PDF_Extended extends FPDF
{
    function GetMultiCellHeight($w, $h, $txt)
    {
        $cw = &$this->CurrentFont['cw']; // now accessible
        if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;

        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) $i++;
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $h * $nl;
    }
}


$data = $_SESSION['admin_print_data'];
unset($_SESSION['admin_print_data']); // Prevent reprint on refresh

function getFacultyName($conn, $id)
{
    $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $r = $res->fetch_assoc();
        return $r['first_name'] . ' ' . $r['mid_name'] . ' ' . $r['last_name'];
    }
    return 'Unknown';
}


$evaluatorName  = getFacultyName($conn, $data['evaluator_id']);
$evaluateeName  = getFacultyName($conn, $data['evaluatee_id']);

$questions = [
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

// Initialize FPDF
$pdf = new PDF_Extended('P', 'mm', 'A4');

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Supervisor-to-Faculty Evaluation Summary', 0, 1, 'C');
$pdf->Ln(4);

// Faculty Info
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Evaluatee: " . $evaluateeName, 0, 1);
$pdf->Cell(0, 6, "Academic Rank: " . $data['faculty_rank'], 0, 1);
$pdf->Cell(0, 6, "Department/College: " . $data['department'], 0, 1);
$pdf->Cell(0, 6, "Rating Period (Academic Year): " . $data['academic_year'], 0, 1);
$pdf->Ln(4);

// Table Headers
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(80, 8, "Benchmark Statement", 1, 0, 'C', true);
$pdf->Cell(80, 8, "Suggested Means of Verification", 1, 0, 'C', true);
$pdf->Cell(30, 8, "Rating", 1, 1, 'C', true);

// Table Rows
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(245, 245, 245);
$lineHeight = 5;

function getMultiCellHeight($pdf, $w, $h, $txt)
{
    $cw = &$pdf->CurrentFont['cw'];
    if ($w == 0) $w = $pdf->w - $pdf->rMargin - $pdf->x;
    $wmax = ($w - 2 * $pdf->cMargin) * 1000 / $pdf->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if ($nb > 0 and $s[$nb - 1] == "\n") $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;

    while ($i < $nb) {
        $c = $s[$i];
        if ($c == "\n") {
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
            continue;
        }
        if ($c == ' ') $sep = $i;
        $l += $cw[$c];
        if ($l > $wmax) {
            if ($sep == -1) {
                if ($i == $j) $i++;
            } else {
                $i = $sep + 1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
        } else {
            $i++;
        }
    }
    return $h * $nl;
}

foreach ($questions as $i => $q) {
    $question = ($i + 1) . ". " . $q;
    $verify = $verifications[$i];
    $rating = $data['answers']["q$i"] ?? '-';

    // Column widths
    $w1 = 80;
    $w2 = 80;
    $w3 = 30;
    $lineHeight = 5;

    // Save starting position
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Measure max height
    $h1 = $pdf->GetMultiCellHeight($w1, $lineHeight, $question);
    $h2 = $pdf->GetMultiCellHeight($w2, $lineHeight, $verify);
    $maxHeight = max($h1, $h2);

    // Draw background cells
    $pdf->Rect($x, $y, $w1, $maxHeight);
    $pdf->Rect($x + $w1, $y, $w2, $maxHeight);
    $pdf->Rect($x + $w1 + $w2, $y, $w3, $maxHeight);

    // Render text inside cells
    $pdf->SetXY($x, $y);
    $pdf->MultiCell($w1, $lineHeight, $question, 0, 'L');

    $pdf->SetXY($x + $w1, $y);
    $pdf->MultiCell($w2, $lineHeight, $verify, 0, 'L');

    $pdf->SetXY($x + $w1 + $w2, $y);
    $pdf->MultiCell($w3, $maxHeight, $rating, 0, 'C');

    // Move to next row
    $pdf->SetY($y + $maxHeight);
}

// Score
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 10);
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

// Signature Block
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Signature of Supervisor: ____________________________", 0, 1);
$pdf->Cell(0, 6, "Name of Supervisor: " . $evaluatorName, 0, 1);
$pdf->Cell(0, 6, "Position of Supervisor: " . ($data['evaluator_position'] ?? 'Not Set'), 0, 1);
$pdf->Cell(0, 6, "Date of Evaluation: " . date('F j, Y'), 0, 1);

// Output PDF
$pdf->Output('I', 'Supervisor_Evaluation.pdf');

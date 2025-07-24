<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    header("Location: pages-login.php");
    exit();
}

if (!isset($_GET['faculty_id'])) {
    die("No faculty selected.");
}

$faculty_id = $_GET['faculty_id'];
$filter_semester = $_GET['semester'] ?? '';
$filter_academic_year = $_GET['academic_year'] ?? '';

// Faculty Info
$stmt = $conn->prepare("SELECT last_name, first_name, mid_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($lname, $fname, $mname, $department, $faculty_rank);
$stmt->fetch();
$stmt->close();
$faculty_name = "$fname $mname $lname";

// Prepare base query parts for filtering
$where_clauses = ["faculty_id = ?"];
$params_types = "s";
$params_values = [$faculty_id];

if (!empty($filter_semester)) {
    $where_clauses[] = "semester = ?";
    $params_types .= "s";
    $params_values[] = $filter_semester;
}
if (!empty($filter_academic_year)) {
    $where_clauses[] = "academic_year = ?";
    $params_types .= "s";
    $params_values[] = $filter_academic_year;
}
$where_sql = implode(' AND ', $where_clauses);


// Semester & Year for display (should reflect filtered data, or N/A if no data for filters)
$semester_display = $filter_semester ?: "All Semesters";
$academic_year_display = $filter_academic_year ?: "All Academic Years";

// Get latest semester/year evaluated by supervisor based on filters for display
$eval_q_sql = "SELECT semester, academic_year FROM admin_evaluation WHERE evaluatee_id = ?";
$admin_eval_params_types = "s";
$admin_eval_params_values = [$faculty_id];

if (!empty($filter_semester)) {
    $eval_q_sql .= " AND semester = ?";
    $admin_eval_params_types .= "s";
    $admin_eval_params_values[] = $filter_semester;
}
if (!empty($filter_academic_year)) {
    $eval_q_sql .= " AND academic_year = ?";
    $admin_eval_params_types .= "s";
    $admin_eval_params_values[] = $filter_academic_year;
}
$eval_q_sql .= " ORDER BY evaluation_date DESC LIMIT 1";

$stmt_eval_q = $conn->prepare($eval_q_sql);
if ($stmt_eval_q) {
    $stmt_eval_q->bind_param($admin_eval_params_types, ...$admin_eval_params_values);
    $stmt_eval_q->execute();
    $stmt_eval_q->bind_result($sem_res, $ay_res);
    if ($stmt_eval_q->fetch()) {
        $semester_display = $sem_res;
        $academic_year_display = $ay_res;
    }
    $stmt_eval_q->close();
}


// SET Summary
$set_summary_sql = "SELECT 
                        subject_code, TRIM(student_section) AS student_section, COUNT(*) AS num_students,
                        AVG(computed_rating) AS avg_rating
                    FROM evaluation
                    WHERE $where_sql
                    GROUP BY subject_code, TRIM(student_section)";

$stmt_set_summary = $conn->prepare($set_summary_sql);
$stmt_set_summary->bind_param($params_types, ...$params_values);
$stmt_set_summary->execute();
$result_set_summary = $stmt_set_summary->get_result();

$total_students = 0;
$total_weighted_value = 0;
$summary = [];

while ($row = mysqli_fetch_assoc($result_set_summary)) {
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
$stmt_set_summary->close();

$overall_set = $total_students ? number_format($total_weighted_value / $total_students, 2) : '0.00';

// SEF
$sef_sql = "SELECT AVG(computed_rating) as sef_rating FROM admin_evaluation WHERE evaluatee_id = ?";
$sef_params_types = "s";
$sef_params_values = [$faculty_id];

if (!empty($filter_semester)) {
    $sef_sql .= " AND semester = ?";
    $sef_params_types .= "s";
    $sef_params_values[] = $filter_semester;
}
if (!empty($filter_academic_year)) {
    $sef_sql .= " AND academic_year = ?";
    $sef_params_types .= "s";
    $sef_params_values[] = $filter_academic_year;
}

$stmt_sef = $conn->prepare($sef_sql);
$stmt_sef->bind_param($sef_params_types, ...$sef_params_values);
$stmt_sef->execute();
$sef_result = $stmt_sef->get_result();
$sef_rating = number_format(mysqli_fetch_assoc($sef_result)['sef_rating'] ?? 0, 2);
$stmt_sef->close();


// Comments
$comments_sql = "SELECT comment FROM evaluation WHERE $where_sql AND comment IS NOT NULL AND comment <> '' LIMIT 5";
$stmt_comments = $conn->prepare($comments_sql);
$stmt_comments->bind_param($params_types, ...$params_values);
$stmt_comments->execute();
$comments_q = $stmt_comments->get_result();
$comments = [];
while ($row = mysqli_fetch_assoc($comments_q)) {
    $comments[] = $row['comment'];
}
$stmt_comments->close();


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
$pdf->Cell(110, 8, "$semester_display / $academic_year_display", 1, 1); // Display filtered values

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

$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);

// Current position
$x = $pdf->GetX();
$y = $pdf->GetY();

// Row 1: Prepared by
$pdf->SetXY($x, $y);
$pdf->MultiCell(35, 8, "Prepared by\n(Staff Signature):", 1); // 2 lines, height 8 each

$pdf->SetXY($x + 35, $y);
$pdf->Cell(45, 16, '', 1, 0); // Signature

$pdf->Cell(20, 16, 'Name:', 1, 0);
$pdf->Cell(45, 16, $evaluator_name ?? '', 1, 0);

$pdf->Cell(15, 16, 'Date:', 1, 0);
$pdf->Cell(0, 16, date('F j, Y'), 1, 1); // Auto date

// Row 2: Reviewed by
$y2 = $pdf->GetY(); // Move to next line
$pdf->SetXY($x, $y2);
$pdf->MultiCell(35, 8, "Reviewed by\n(Authorized Official):", 1);

$pdf->SetXY($x + 35, $y2);
$pdf->Cell(45, 16, '', 1, 0); // Signature

$pdf->Cell(20, 16, 'Name:', 1, 0);
$pdf->Cell(45, 16, '', 1, 0); // Leave blank or insert official name

$pdf->Cell(15, 16, 'Date:', 1, 0);
$pdf->Cell(0, 16, date('F j, Y'), 1, 1); // Auto date

$pdf->Output('I', 'Individual-Faculty-Evaluation.pdf');
?>
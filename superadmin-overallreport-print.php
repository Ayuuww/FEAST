<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    die('Access denied');
}

$superadmin_id = $_SESSION['idnumber'];

// 🔹 1. Get superadmin details (Prepared by)
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $superadmin_id);
$stmt->execute();
$stmt->bind_result($s_fname, $s_mname, $s_lname, $s_position);
$stmt->fetch();
$stmt->close();

$prep_middle_initial = !empty($s_mname) ? ' ' . substr($s_mname, 0, 1) . '.' : '';
$prepared_by = strtoupper(trim("$s_fname$prep_middle_initial $s_lname"));
$s_position = strtoupper($s_position);

// 🔹 2. Get filters from request
$selected_college = $_GET['college'] ?? '';
$selected_program = $_GET['program'] ?? '';
$selected_semester = $_GET['semester'] ?? '';
$selected_academic_year = $_GET['academic_year'] ?? '';

// 🔹 3. Get all faculty in the selected college/program
$faculties = [];
if (!empty($selected_college)) {
    $faculty_sql = "SELECT idnumber, last_name, first_name, mid_name FROM faculty WHERE college = ?";
    $params = [$selected_college];
    $types = "s";

    if (!empty($selected_program)) {
        $faculty_sql .= " AND program = ?";
        $params[] = $selected_program;
        $types .= "s";
    }
    $faculty_sql .= " ORDER BY last_name ASC";

    $query = $conn->prepare($faculty_sql);
    $query->bind_param($types, ...$params);
    $query->execute();
    $faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
    $query->close();
}

// 🔹 4. OPTIMIZATION: Pre-fetch ALL SET Averages at once (Fixes N+1 Query Problem)
$set_lookup = [];
$set_sql = "SELECT faculty_id, COUNT(*) AS total, AVG(computed_rating) AS avg FROM evaluation WHERE 1=1";
$set_params = [];
$set_types = "";
if (!empty($selected_semester)) {
    $set_sql .= " AND semester = ?";
    $set_params[] = $selected_semester;
    $set_types .= "s";
}
if (!empty($selected_academic_year)) {
    $set_sql .= " AND academic_year = ?";
    $set_params[] = $selected_academic_year;
    $set_types .= "s";
}
$set_sql .= " GROUP BY faculty_id";

$set_stmt = $conn->prepare($set_sql);
if (!empty($set_types)) $set_stmt->bind_param($set_types, ...$set_params);
$set_stmt->execute();
$set_res = $set_stmt->get_result();
while ($row = $set_res->fetch_assoc()) {
    $set_lookup[$row['faculty_id']] = ['total' => $row['total'], 'avg' => $row['avg']];
}
$set_stmt->close();

// 🔹 5. OPTIMIZATION: Pre-fetch ALL SEF Averages at once
$sef_lookup = [];
$sef_sql = "SELECT evaluatee_id, COUNT(*) AS total, AVG(computed_rating) AS avg FROM admin_evaluation WHERE 1=1";
$sef_params = [];
$sef_types = "";
if (!empty($selected_semester)) {
    $sef_sql .= " AND semester = ?";
    $sef_params[] = $selected_semester;
    $sef_types .= "s";
}
if (!empty($selected_academic_year)) {
    $sef_sql .= " AND academic_year = ?";
    $sef_params[] = $selected_academic_year;
    $sef_types .= "s";
}
$sef_sql .= " GROUP BY evaluatee_id";

$sef_stmt = $conn->prepare($sef_sql);
if (!empty($sef_types)) $sef_stmt->bind_param($sef_types, ...$sef_params);
$sef_stmt->execute();
$sef_res = $sef_stmt->get_result();
while ($row = $sef_res->fetch_assoc()) {
    $sef_lookup[$row['evaluatee_id']] = ['total' => $row['total'], 'avg' => $row['avg']];
}
$sef_stmt->close();

// 🔹 6. Get Highest Ranking Admin (Dean > Chair > Program Chair) for Signature
$reviewer_name = "N/A";
$reviewer_position = "N/A";

$sql_reviewer = "
    SELECT a.first_name, a.mid_name, a.last_name, a.position
    FROM admin a
    INNER JOIN admin_college ac ON a.idnumber = ac.admin_idnumber
    WHERE ac.college_name = ?
    ORDER BY 
        CASE 
            WHEN a.position LIKE 'Dean%' THEN 1
            WHEN a.position LIKE 'Chair%' THEN 2
            WHEN a.position LIKE 'Program Chair%' THEN 3
            WHEN a.position LIKE 'Director%' THEN 4
            ELSE 5 
        END ASC
    LIMIT 1
";
$stmt_rev = $conn->prepare($sql_reviewer);
$stmt_rev->bind_param("s", $selected_college);
$stmt_rev->execute();
$res_rev = $stmt_rev->get_result();
if ($row_rev = $res_rev->fetch_assoc()) {
    $mid_init = !empty($row_rev['mid_name']) ? ' ' . substr($row_rev['mid_name'], 0, 1) . '.' : '';
    $reviewer_name = strtoupper(trim("{$row_rev['first_name']}{$mid_init} {$row_rev['last_name']}"));
    $reviewer_position = strtoupper($row_rev['position']);
}
$stmt_rev->close();

// ==========================================================
// 🔹 PDF GENERATION START
// ==========================================================
require 'superadmin-printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4', $conn);
$pdf->college = $selected_college;
$pdf->AddPage();

// --- Filters Information ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, "COLLEGE SET & SEF EVALUATION REPORT", 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$program_text = !empty($selected_program) ? $selected_program : 'All Programs';
$semester_text = !empty($selected_semester) ? $selected_semester : 'All Semesters';
$academic_text = !empty($selected_academic_year) ? $selected_academic_year : 'All Academic Years';

$pdf->Cell(0, 6, "Program: " . $program_text, 0, 1, 'L');
$pdf->Cell(0, 6, "Semester: " . $semester_text, 0, 1, 'L');
$pdf->Cell(0, 6, "Academic Year: " . $academic_text, 0, 1, 'L');
$pdf->Cell(0, 6, "Date Printed: " . date('F j, Y'), 0, 1, 'L');
$pdf->Ln(4);

// --- Table Header ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(50, 50, 50);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(100, 8, 'Faculty Member Name', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'SET Average', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'SEF Average', 1, 1, 'C', true);

// --- Table Content ---
$pdf->SetTextColor(0, 0, 0); // Reset text color to black
$pdf->SetFont('Arial', '', 10);

$total_set = 0;
$count_set = 0;
$total_sef = 0;
$count_sef = 0;
$fill = false; // For zebra striping

foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    // Pull from pre-fetched arrays
    $set_avg_raw = isset($set_lookup[$fid]) ? (float)$set_lookup[$fid]['avg'] : 0;
    $sef_avg_raw = isset($sef_lookup[$fid]) ? (float)$sef_lookup[$fid]['avg'] : 0;

    $set_avg = $set_avg_raw > 0 ? number_format($set_avg_raw, 2) . "%" : 'No Data';
    $sef_avg = $sef_avg_raw > 0 ? number_format($sef_avg_raw, 2) . "%" : 'No Data';

    // Zebra Striping Colors
    if ($fill) {
        $pdf->SetFillColor(240, 240, 240); // Light Gray
    } else {
        $pdf->SetFillColor(255, 255, 255); // White
    }

    $pdf->Cell(100, 8, "  " . $name, 1, 0, 'L', $fill);
    $pdf->Cell(45, 8, $set_avg, 1, 0, 'C', $fill);
    $pdf->Cell(45, 8, $sef_avg, 1, 1, 'C', $fill);

    $fill = !$fill; // Toggle for next row

    if ($set_avg_raw > 0) {
        $total_set += $set_avg_raw;
        $count_set++;
    }
    if ($sef_avg_raw > 0) {
        $total_sef += $sef_avg_raw;
        $count_sef++;
    }
}

// --- Compute Final Averages ---
$final_set_average = $count_set > 0 ? number_format($total_set / $count_set, 2) . "%" : '0.00%';
$final_sef_average = $count_sef > 0 ? number_format($total_sef / $count_sef, 2) . "%" : '0.00%';

// Add row at bottom of table
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(220, 220, 220); // Darker gray for the footer row
$pdf->Cell(100, 8, 'OVERALL COLLEGE AVERAGE ', 1, 0, 'R', true);
$pdf->Cell(45, 8, $final_set_average, 1, 0, 'C', true);
$pdf->Cell(45, 8, $final_sef_average, 1, 1, 'C', true);

$pdf->Ln(20);

// --- Side-by-Side Signatures ---
// Save current Y position
$y_pos = $pdf->GetY();

// Left Side (Prepared By)
$pdf->SetXY(10, $y_pos);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, "Prepared by:", 0, 1, 'L');
$pdf->Ln(8); // Space for signature
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 6, $prepared_by, 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, $s_position, 0, 1, 'L');

// Right Side (Reviewed By)
$pdf->SetXY(105, $y_pos);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, "Reviewed by:", 0, 1, 'L');
$pdf->SetXY(105, $pdf->GetY() + 8); // Align with signature space
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 6, $reviewer_name, 0, 1, 'L');
$pdf->SetXY(105, $pdf->GetY());
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, $reviewer_position, 0, 1, 'L');

$pdf->Output('I', 'Overall-Evaluation-Report.pdf');

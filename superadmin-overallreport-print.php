<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    die('Access denied');
}

$superadmin_id = $_SESSION['idnumber'];

// 🔹 Get superadmin details (Prepared by)
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position 
                        FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $superadmin_id);
$stmt->execute();
$stmt->bind_result($s_fname, $s_mname, $s_lname, $s_position);
$stmt->fetch();
$stmt->close();

// Fix for middle initial
$prep_middle_initial = '';
if (!empty($s_mname)) {
    $prep_middle_initial = ' ' . substr($s_mname, 0, 1) . '.';
}
$prepared_by = strtoupper(trim("$s_fname$prep_middle_initial $s_lname"));
$s_position = strtoupper($s_position);

// 🔹 Get filters from request
$selected_college = $_GET['college'] ?? '';
$selected_program = $_GET['program'] ?? '';
$selected_semester = $_GET['semester'] ?? '';
$selected_academic_year = $_GET['academic_year'] ?? '';

// 🔹 Get all faculty in the selected college
// 🔹 Get all faculty in the selected college (and program, if selected)
$faculties = [];
if (!empty($selected_college)) {
    $faculty_sql = "SELECT idnumber, last_name, first_name, mid_name
          FROM faculty
          WHERE college = ?";
    $params = [$selected_college];
    $types = "s";

    // Add program filter if it exists
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
    // 🔹 Get all faculty...
    $query->close();
}

// ✅ START: NEW BLOCK TO FETCH SUPERVISORS
$reviewers = [];
$sql = "SELECT a.first_name, a.mid_name, a.last_name, a.position
    FROM admin a
    INNER JOIN admin_college ad ON a.idnumber = ad.admin_idnumber
    WHERE ad.college_name = ?";
$params_rev = [$selected_college];
$types_rev = "s";

// Add program filter if it was selected
if (!empty($selected_program)) {
    $sql .= " AND ad.program_name = ?";
    $params_rev[] = $selected_program;
    $types_rev .= "s";
}

// Order by position (e.g., Dean, then Chair)
$sql .= " ORDER BY CASE 
      WHEN a.position LIKE 'Dean%' THEN 1
      WHEN a.position LIKE 'Chair%' THEN 2
      WHEN a.position LIKE 'Program Chair%' THEN 3
      ELSE 4 
     END";

$stmt_rev = $conn->prepare($sql);
$stmt_rev->bind_param($types_rev, ...$params_rev);
$stmt_rev->execute();
$result_rev = $stmt_rev->get_result();

while ($row = $result_rev->fetch_assoc()) {
    // Format the name with middle initial
    $middle_initial = '';
    if (!empty($row['mid_name'])) {
        $middle_initial = ' ' . substr($row['mid_name'], 0, 1) . '.';
    }
    $full_name = strtoupper(trim("{$row['first_name']}$middle_initial {$row['last_name']}"));
    $reviewers[] = [
        'name' => $full_name,
        'position' => strtoupper($row['position'])
    ];
}
$stmt_rev->close();
// ✅ END: NEW BLOCK

// 🔹 Custom PDF class (with header/footer)
require 'superadmin-printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4', $conn);
$pdf->college = $selected_college;
$pdf->AddPage();

// $pdf->SetFont('Arial', 'B', 14);
// $title = "COLLEGE SET & SEF EVALUATION REPORT – $program_text";
// $pdf->Cell(0, 10, $title, 0, 1, 'C');
// $pdf->Ln(3);

// --- Filters Information ---
$pdf->SetFont('Arial', 'B', 11);
$program_text = !empty($selected_program) ? $selected_program : 'All Programs';
$title = "COLLEGE SET & SEF EVALUATION REPORT";
$pdf->Cell(0, 10, $title, 0, 1, 'C');
$semester_text = !empty($selected_semester) ? $selected_semester : 'All Semesters';
$academic_text = !empty($selected_academic_year) ? $selected_academic_year : 'All Academic Years';

$pdf->Cell(0, 8, "Program: $program_text", 0, 1, 'L'); // ✅ ADD THIS
$pdf->Cell(0, 8, "Semester: $semester_text", 0, 1, 'L');
$pdf->Cell(0, 8, "Academic Year: $academic_text", 0, 1, 'L');
$pdf->Cell(0, 8, "Date: " . date('F j, Y'), 0, 1, 'L');
$pdf->Ln(5);

// --- Table Header ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(110, 8, 'Faculty Member Name', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'SET AVG', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'SEF AVG', 1, 1, 'C', true);

// --- Table Content ---
$pdf->SetFont('Arial', '', 10);
$total_set = 0;
$total_sef = 0;
$count_set = 0;
$count_sef = 0;

foreach ($faculties as $fac) {
    $fid = $fac['idnumber'];
    $fid = $fac['idnumber'];
    $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

    // 🔹 Student Evaluation (SET)
    $sql_set = "SELECT COUNT(*) AS total, AVG(computed_rating) AS avg 
                FROM evaluation WHERE faculty_id = ?";
    $types = "s";
    $params = [$fid];

    if (!empty($selected_semester)) {
        $sql_set .= " AND semester = ?";
        $params[] = $selected_semester;
        $types .= "s";
    }
    if (!empty($selected_academic_year)) {
        $sql_set .= " AND academic_year = ?";
        $params[] = $selected_academic_year;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql_set);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $set_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $set_avg = $set_data['total'] ? number_format((float)$set_data['avg'], 2) : '0.00';

    // 🔹 Supervisor Evaluation (SEF)
    $sql_sef = "SELECT COUNT(*) AS total, AVG(computed_rating) AS avg 
                FROM admin_evaluation WHERE evaluatee_id = ?";
    $types = "s";
    $params = [$fid];

    if (!empty($selected_semester)) {
        $sql_sef .= " AND semester = ?";
        $params[] = $selected_semester;
        $types .= "s";
    }
    if (!empty($selected_academic_year)) {
        $sql_sef .= " AND academic_year = ?";
        $params[] = $selected_academic_year;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql_sef);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $sef_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $sef_avg = $sef_data['total'] ? number_format((float)$sef_data['avg'], 2) : '0.00';

    // 🔹 Output table row
    $set_avg_raw = $set_data['total'] ? (float)$set_data['avg'] : 0;
    $sef_avg_raw = $sef_data['total'] ? (float)$sef_data['avg'] : 0;

    $set_avg = number_format($set_avg_raw, 2);
    $sef_avg = number_format($sef_avg_raw, 2);

    $pdf->Cell(110, 8, $name, 1);
    $pdf->Cell(40, 8, $set_avg, 1, 0, 'C');
    $pdf->Cell(40, 8, $sef_avg, 1, 1, 'C');

    if ($set_avg_raw > 0) {
        $total_set += $set_avg_raw;
        $count_set++;
    }
    if ($sef_avg_raw > 0) {
        $total_sef += $sef_avg_raw;
        $count_sef++;
    }
}

// Compute averages
$final_set_average = $count_set > 0 ? number_format($total_set / $count_set, 2) : '0.00';
$final_sef_average = $count_sef > 0 ? number_format($total_sef / $count_sef, 2) : '0.00';

// Add row at bottom of table
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(110, 8, 'College Average', 1, 0, 'R', true);
$pdf->Cell(40, 8, $final_set_average, 1, 0, 'C', true);
$pdf->Cell(40, 8, $final_sef_average, 1, 1, 'C', true);

$pdf->Ln(15);

// --- Prepared By Section ---
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 6, "Prepared by:", 0, 0, 'L');
$pdf->Cell(0, 6, "Date Signed: " . date("F d, Y"), 0, 1, 'L');
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 6, $prepared_by, 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 6, $s_position, 0, 1, 'L');


// ─────────────────────────────────────────────
// Reviewed By section (1 admin only, Dean prioritized)
// ─────────────────────────────────────────────
$reviewer_name = "N/A";
$reviewer_position = "N/A";

// 1️⃣ Try to get Dean first
$sql_dean = "
    SELECT a.first_name, a.mid_name, a.last_name, a.position
    FROM admin a
    INNER JOIN admin_college ac ON a.idnumber = ac.admin_idnumber
    WHERE ac.college_name = ?
      AND a.position LIKE 'Dean%'
    LIMIT 1
";
$stmt = $conn->prepare($sql_dean);
$stmt->bind_param("s", $selected_college);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $middle_initial = !empty($row['mid_name']) ? ' ' . substr($row['mid_name'], 0, 1) . '.' : '';
    $reviewer_name = strtoupper(trim("{$row['first_name']}{$middle_initial} {$row['last_name']}"));
    $reviewer_position = $row['position'];
} else {
    // 2️⃣ Fallback → Chair / Program Chair / Director
    $sql_fallback = "
        SELECT a.first_name, a.mid_name, a.last_name, a.position
        FROM admin a
        INNER JOIN admin_college ac ON a.idnumber = ac.admin_idnumber
        WHERE ac.college_name = ?
          AND (a.position LIKE 'Chair%' OR a.position LIKE 'Program Chair%' OR a.position LIKE 'Director%')
        ORDER BY
            CASE
                WHEN a.position LIKE 'Chair%' THEN 1
                WHEN a.position LIKE 'Program Chair%' THEN 2
                WHEN a.position LIKE 'Director%' THEN 3
            END
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql_fallback);
    $stmt->bind_param("s", $selected_college);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row2 = $res->fetch_assoc()) {
        $middle_initial = !empty($row2['mid_name']) ? ' ' . substr($row2['mid_name'], 0, 1) . '.' : '';
        $reviewer_name = strtoupper(trim("{$row2['first_name']}{$middle_initial} {$row2['last_name']}"));
        $reviewer_position = $row2['position'];
    }
}
$stmt->close();

// Display in PDF
$pdf->Ln(20);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, "Reviewed By:", 0, 1, 'L');
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(140, 6, $reviewer_name, 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(140, 6, strtoupper($reviewer_position), 0, 1, 'L');



$pdf->Output('I', 'Overall-Evaluation-Report.pdf');

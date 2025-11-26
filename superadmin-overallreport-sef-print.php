<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Get selected filters from URL
$selected_college = isset($_GET['college']) ? $_GET['college'] : "";
$selected_program = isset($_GET['program']) ? $_GET['program'] : "";
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : "";
$selected_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : "";

// If no college is selected, stop
if (empty($selected_college)) {
  die("No college selected. Please go back and select a college.");
}

// 🔑 Store college in session so header/footer can access it
$_SESSION['college'] = $selected_college;

// ✅ Fetch supervisors from admin + admin_college (new schema)
$supervisors = [];

$sql = "SELECT a.first_name, a.mid_name, a.last_name, a.position
        FROM admin a
        INNER JOIN admin_college ad ON a.idnumber = ad.admin_idnumber
        WHERE ad.college_name = ?";
$params = [$selected_college];
$types = "s";

// Add program filter if it was selected
if (!empty($selected_program)) {
  $sql .= " AND ad.program_name = ?";
  $params[] = $selected_program;
  $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

  $middle_initial = '';
  if (!empty($row['mid_name'])) {
    $middle_initial = ' ' . substr($row['mid_name'], 0, 1) . '.'; // Add space, initial, and period
  }

  $fullname = strtoupper(trim($row['first_name'] . ' ' . $middle_initial . ' ' . $row['last_name']));
  $supervisors[] = [
    'name' => $fullname,
    'position' => strtoupper(trim($row['position']))
  ];
}
$stmt->close();


// Custom PDF class
require 'superadmin-printing-headerfooter.php';
$pdf = new PDF_EXTENDED('P', 'mm', 'A4', $conn);
$pdf->college = $selected_college;
$pdf->AddPage();

// Title (same layout)
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, ' COLLEGE SEF REPORT', 0, 1, 'C');
$pdf->Ln(3);

// 🔹 Show selected filters clearly
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'college/College: ' . (!empty($selected_college) ? $selected_college : 'All college'), 0, 1);
$pdf->Cell(0, 8, 'Program: ' . (!empty($selected_program) ? $selected_program : 'All Programs'), 0, 1); // ✅ **MODIFICATION 1: Show program**
$pdf->Cell(0, 8, 'Semester: ' . (!empty($selected_semester) ? $selected_semester : 'All Semesters'), 0, 1);
$pdf->Cell(0, 8, 'Academic Year: ' . (!empty($selected_academic_year) ? $selected_academic_year : 'All Academic Years'), 0, 1);
$pdf->Cell(0, 8, 'Date: ' . date('F j, Y'), 0, 1);
$pdf->Ln(5);

// Section Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(180, 10, "COLLEGE SEF EVALUATION REPORT", 0, 1, 'C', true);
$pdf->Ln(2);

// --- ✅ MODIFICATION 2: Adjust Table Headers ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'Faculty Name', 1); // Adjusted width
$pdf->Cell(60, 10, 'No. of Supervisor Evaluations', 1, 0, 'C'); // Added new column
$pdf->Cell(40, 10, 'Avg. SEF Rating', 1, 0, 'C'); // Adjusted width
$pdf->Ln();
// --- End Modification 2 ---

// Fetch faculty for this college (and program, if specified)
$faculty_sql = "SELECT idnumber, last_name, first_name, mid_name
                FROM faculty
                WHERE college = ?";
$params = [$selected_college];
$types = "s";

// Add program filter if it was selected
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

// Display results
$pdf->SetFont('Arial', '', 10);
foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

  // Build filter conditions for SEF
  $where = "evaluatee_id = '" . $conn->real_escape_string($fid) . "'";
  if (!empty($selected_semester)) {
    $where .= " AND semester = '" . $conn->real_escape_string($selected_semester) . "'";
  }
  if (!empty($selected_academic_year)) {
    $where .= " AND academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
  }

  // Query SEF results
  $r = $conn->query("
    SELECT COUNT(*) AS evaluations, AVG(computed_rating) AS avg_rating
    FROM admin_evaluation
    WHERE $where
  ")->fetch_assoc();

  $count = (int)$r['evaluations']; // This is the count you wanted
  $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';

  // --- ✅ MODIFICATION 3: Add Data Cell ---
  $pdf->Cell(80, 8, $name, 1); // Adjusted width
  $pdf->Cell(60, 8, $count, 1, 0, 'C'); // Added cell for the count
  $pdf->Cell(40, 8, "$avg", 1, 0, 'C'); // Adjusted width
  $pdf->Ln();
  // --- End Modification 3 ---
}

// ─────────────────────────────────────────────
// ✅ 6.5️⃣ College Average (Overall college Average)
// ─────────────────────────────────────────────
$college_where = ["f.college = '" . $conn->real_escape_string($selected_college) . "'"];
if (!empty($selected_program)) $college_where[] = "f.program = '" . $conn->real_escape_string($selected_program) . "'";
if (!empty($selected_academic_year)) $college_where[] = "academic_year = '" . $conn->real_escape_string($selected_academic_year) . "'";
$college_where_sql = implode(" AND ", $college_where);

$college_avg_query = "
  SELECT COUNT(*) AS total_students, AVG(computed_rating) AS college_avg
  FROM evaluation e
  INNER JOIN faculty f ON e.faculty_id = f.idnumber
  WHERE $college_where_sql
";
$college_result = $conn->query($college_avg_query)->fetch_assoc();
$total_students = (int)$college_result['total_students'];
$college_avg = $total_students ? number_format((float)$college_result['college_avg'], 2) : '0.00';

// Add college average row
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(140, 10, 'College Average', 1, 0, 'R', true);
$pdf->Cell(40, 10, number_format($college_avg, 2), 1, 1, 'C', true);
$pdf->Ln(12);

// Get logged-in superadmin info
$prepared_by = "";
$position = "";
if (isset($_SESSION['idnumber'])) {
  $stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position 
                           FROM superadmin 
                           WHERE idnumber = ?");
  $stmt->bind_param("s", $_SESSION['idnumber']);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if ($res) {

    $middle_initial = '';
    if (!empty($res['mid_name'])) {
      $middle_initial = ' ' . substr($res['mid_name'], 0, 1) . '.'; // Add space, initial, and period
    }

    $fullname = strtoupper($res['first_name'] . ' ' . $middle_initial  . ' ' . $res['last_name']);
    $position = trim($res['position']);
    $prepared_by = $fullname;
  }
}

// Footer Section: Prepared By & Reviewed By
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(120, 6, "Prepared by:", 0, 0, 'L');
$pdf->Ln(10);

// Prepared By details
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(120, 6, $prepared_by, 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(120, 6, strtoupper($position), 0, 1, 'L');
$pdf->Ln(8);

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


$pdf->Output('I', 'Overall-SEF-Report.pdf');

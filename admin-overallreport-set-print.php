<?php
require('fpdf/fpdf.php');
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// 🔹 CHANGE 1: Fix Admin Info Query (Remove 'college')
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, position 
                        FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
// Bind to new variables (removed $admin_college)
$stmt->bind_result($fname, $mname, $lname, $position);
$stmt->fetch();
$stmt->close();

// ✅ START FIX: Format middle name as initial
$middle_initial = '';
if (!empty($mname)) {
  $middle_initial = ' ' . substr($mname, 0, 1) . '.'; // Add space, initial, and period
}
$admin_name = $fname . $middle_initial . ' ' . $lname; // e.g., "Sample A. Yes"
// ✅ END FIX

// 🔹 CHANGE 2: Add correct logic to fetch college
// Fetch all college assigned to this admin
$stmt = $conn->prepare("SELECT college_name FROM admin_college WHERE admin_idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$college = [];
while ($row = $result->fetch_assoc()) {
  $college[] = $row['college_name'];
}
$stmt->close();

// For the PDF title
$admin_college_display = !empty($college) ? implode(', ', $college) : 'No college Assigned';


// 🔹 Get filters
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$year_filter     = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';

// Custom PDF class
require 'printing-headerfooter.php';

// Start PDF
$pdf = new PDF_EXTENDED('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
// Use the new display variable
$pdf->Cell(0, 10, 'COLLEGE SET EVALUATION REPORT', 0, 1, 'C');

// ... (rest of your semester/year filter logic is fine) ...

$pdf->SetFont('Arial', '', 11);

// Handle semester display
if (!empty($semester_filter)) {
  $semester_display = $semester_filter;
} else {
  // Fetch all distinct semesters
  $semester_display = [];
  $res = $conn->query("SELECT DISTINCT semester FROM evaluation ORDER BY semester ASC");
  while ($row = $res->fetch_assoc()) {
    if (!empty($row['semester'])) {
      $semester_display[] = $row['semester'];
    }
  }
  $res->close();
  $semester_display = implode(" / ", $semester_display); // e.g. "1st Semester / 2nd Semester"
}

// Handle academic year display
if (!empty($year_filter)) {
  $year_display = $year_filter;
} else {
  // Fetch all distinct academic years
  $year_display = [];
  $res = $conn->query("SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
  while ($row = $res->fetch_assoc()) {
    if (!empty($row['academic_year'])) {
      $year_display[] = $row['academic_year'];
    }
  }
  $res->close();
  $year_display = implode(" / ", $year_display); // e.g. "2023-2024 / 2022-2023"
}

$pdf->Cell(0, 8, "Semester: $semester_display", 0, 1);
$pdf->Cell(0, 8, "Academic Year: $year_display", 0, 1);
$pdf->Cell(0, 8, 'Date: ' . date('F j, Y'), 0, 1);
$pdf->Ln(5);

// Section Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(180, 10, strtoupper($admin_college_display) . " SET REPORT", 0, 1, 'C', true);
$pdf->Ln(2);

// Table Headers
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'Faculty Name', 1);
$pdf->Cell(50, 10, 'Student Evaluations', 1);
$pdf->Cell(50, 10, 'Average SET Rating', 1);
$pdf->Ln();

// 🔹 CHANGE 3: Fix Faculty Query (use Dept/Prog pairs)
$faculties = [];

// ✅ FIRST, get the admin's assignments as pairs
$admin_assignments = [];
$stmt_admin_dept = $conn->prepare("SELECT college_name, program_name FROM admin_college WHERE admin_idnumber = ?");
$stmt_admin_dept->bind_param("s", $admin_id);
$stmt_admin_dept->execute();
$result = $stmt_admin_dept->get_result();
while ($row = $result->fetch_assoc()) {
  $admin_assignments[] = $row;
}
$stmt_admin_dept->close();



// ---------------------------------------------
// GET REVIEWER (Dean → fallback Chair)
// ---------------------------------------------

$admin_colleges_only = array_unique(array_column($admin_assignments, 'college_name'));

$reviewer_name = "N/A";
$reviewer_position = "N/A";

if (!empty($admin_colleges_only)) {

  $placeholders = implode(',', array_fill(0, count($admin_colleges_only), '?'));
  $types_rev = str_repeat("s", count($admin_colleges_only));

  // 1️⃣ Try to get DEAN
  $sql_dean = "
        SELECT a.first_name, a.mid_name, a.last_name, a.position
        FROM admin a
        INNER JOIN admin_college ac ON a.idnumber = ac.admin_idnumber
        WHERE ac.college_name IN ($placeholders)
        AND a.position LIKE 'Dean%'
        LIMIT 1
    ";

  $stmt_rev = $conn->prepare($sql_dean);
  $stmt_rev->bind_param($types_rev, ...$admin_colleges_only);
  $stmt_rev->execute();
  $res_rev = $stmt_rev->get_result();

  if ($row = $res_rev->fetch_assoc()) {
    $middle_initial_rev = !empty($row['mid_name']) ? ' ' . substr($row['mid_name'], 0, 1) . '.' : '';
    $reviewer_name = strtoupper(trim("{$row['first_name']}{$middle_initial_rev} {$row['last_name']}"));
    $reviewer_position = $row['position'];
  } else {
    // 2️⃣ Fallback → Chair / Program Chair / Director
    $sql_chair = "
            SELECT a.first_name, a.mid_name, a.last_name, a.position
            FROM admin a
            INNER JOIN admin_college ac ON a.idnumber = ac.admin_idnumber
            WHERE ac.college_name IN ($placeholders)
            AND (
                a.position LIKE 'Chair%'
                OR a.position LIKE 'Program Chair%'
                OR a.position LIKE 'Director%'
            )
            ORDER BY
                CASE
                    WHEN a.position LIKE 'Chair%' THEN 1
                    WHEN a.position LIKE 'Program Chair%' THEN 2
                    WHEN a.position LIKE 'Director%' THEN 3
                END
            LIMIT 1
        ";

    $stmt_rev2 = $conn->prepare($sql_chair);
    $stmt_rev2->bind_param($types_rev, ...$admin_colleges_only);
    $stmt_rev2->execute();
    $res_rev2 = $stmt_rev2->get_result();

    if ($row2 = $res_rev2->fetch_assoc()) {
      $middle_initial_rev = !empty($row2['mid_name']) ? ' ' . substr($row2['mid_name'], 0, 1) . '.' : '';
      $reviewer_name = strtoupper(trim("{$row2['first_name']}{$middle_initial_rev} {$row2['last_name']}"));
      $reviewer_position = $row2['position'];
    }
  }
}

if (!empty($admin_assignments)) {
  // ✅ Build the query to check for (dept = ? AND prog = ?) OR (dept = ? AND prog = ?)
  $faculty_query_parts = [];
  $params = [];
  $types = "";

  foreach ($admin_assignments as $assignment) {
    $faculty_query_parts[] = "(college = ? AND program = ?)";
    $params[] = $assignment['college_name'];
    $params[] = $assignment['program_name'];
    $types .= "ss";
  }
  $faculty_where_sql = implode(' OR ', $faculty_query_parts);

  // ✅ This query now finds faculty whose home dept/prog matches the admin's assignments
  $sql = "
        SELECT idnumber, last_name, first_name, mid_name
        FROM faculty
        WHERE ($faculty_where_sql)
        ORDER BY last_name ASC
    ";

  $query = $conn->prepare($sql);
  $query->bind_param($types, ...$params);
  $query->execute();
  $faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
  $query->close();
}

$pdf->SetFont('Arial', '', 10);

$total_avg_rating = 0;
$faculty_count_with_evals = 0;
$faculty_row_count = 0;
foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

  // 🔹 Build evaluation query with filters
  $sql = "
    SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating
    FROM evaluation
    WHERE faculty_id = ?
  ";
  $params = [$fid];
  $types = "s";

  if (!empty($semester_filter)) {
    $sql .= " AND semester = ?";
    $params[] = $semester_filter;
    $types .= "s";
  }
  if (!empty($year_filter)) {
    $sql .= " AND academic_year = ?";
    $params[] = $year_filter;
    $types .= "s";
  }

  $stmtEval = $conn->prepare($sql);
  $stmtEval->bind_param($types, ...$params);
  $stmtEval->execute();
  $r = $stmtEval->get_result()->fetch_assoc();
  $stmtEval->close();

  $count = (int)$r['students'];
  $avg_raw = $count ? (float)$r['avg_rating'] : 0.00; // Get the raw float for math
  $avg_display = number_format($avg_raw, 2); // Get the formatted string for display

  // ✅ ADD THIS
  if ($count > 0) {
    $total_avg_rating += $avg_raw;
    $faculty_count_with_evals++;
  }

  $pdf->Cell(80, 8, $name, 1);
  $pdf->Cell(50, 8, $count, 1, 0, 'C');
  $pdf->Cell(50, 8, $avg_display, 1, 0, 'C');
  $pdf->Ln();
} // <-- ✅ END OF THE FOREACH LOOP

// ✅ --- PASTE THE CODE BLOCK HERE ---
$college_average = 0;
if ($faculty_count_with_evals > 0) {
  $college_average = $total_avg_rating / $faculty_count_with_evals;
}

// --- Draw the Total Row (now outside the loop) ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240); // Light gray background
$pdf->Cell(130, 10, 'College Average:', 1, 0, 'R', true);
$pdf->Cell(50, 10, number_format($college_average, 2), 1, 1, 'C', true);
// --- END OF FIX ---

// --- Prepared by Block ---
$pdf->Ln(12);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Prepared by:', 0, 1);
$pdf->Ln(12); // Space for signature

// Printed Name (ALL CAPS)
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 6, strtoupper($admin_name), 0, 1);

// Position
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(80, 6, $position, 0, 1);


// --- Reviewed by Block ---
$pdf->Ln(12);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Reviewed by:', 0, 1);
$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, $reviewer_name, 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, $reviewer_position, 0, 1);

// --- END: New Signature Block ---


$pdf->Output('I', 'Overall-SET-Report.pdf');

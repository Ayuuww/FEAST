<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
    die('Access denied');
}

$faculty_id = $_SESSION['idnumber'];
$academic_year = $_GET['academic_year'] ?? '';
$semester      = $_GET['semester'] ?? ''; // NEW: Get the semester filter
$subject_code  = $_GET['subject_code'] ?? '';

// Fetch faculty info
$stmt = $conn->prepare("SELECT department, first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
if ($stmt) {
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $stmt->bind_result($department, $fname, $mname, $lname);
    $stmt->fetch();
    $stmt->close();
} else {
    // Handle error if prepare fails
    error_log("Failed to prepare faculty info statement: " . $conn->error);
    $department = $fname = $mname = $lname = 'N/A'; // Default values
}

$full_name = "$fname $mname $lname";

// Build evaluation query for PDF content
$params = [$faculty_id];
$types = "s";
$sql = "SELECT subject_code, subject_title, student_section, academic_year, semester, created_at,
               COUNT(*) AS student_count,
               AVG(total_score) AS avg_total_score,
               AVG(computed_rating) AS avg_computed_rating,
               GROUP_CONCAT(comment SEPARATOR ' | ') AS comments
        FROM evaluation
        WHERE faculty_id = ?";

if ($academic_year) {
    $sql .= " AND academic_year = ?";
    $params[] = $academic_year;
    $types .= "s";
}
// NEW: Add semester filter to the main query
if ($semester) {
    $sql .= " AND semester = ?";
    $params[] = $semester;
    $types .= "s";
}
if ($subject_code) {
    $sql .= " AND subject_code = ?";
    $params[] = $subject_code;
    $types .= "s";
}

$sql .= " GROUP BY subject_code, student_section, semester, academic_year
          ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $results = $stmt->get_result();
    $stmt->close();
} else {
    // Handle error if prepare fails
    error_log("Failed to prepare evaluation data statement: " . $conn->error);
    $results = false; // Indicate an error
}


// Start PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, "Student Evaluation Report", 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, "Name of Faculty: $full_name", 0, 1, 'C');
$pdf->Cell(0, 7, "Department/College: $department", 0, 1, 'C');

// Filter info for the PDF header
$filterInfo = [];
if ($academic_year) {
    $filterInfo[] = "A.Y.: " . $academic_year;
}
if ($semester) { // NEW: Include semester in filter info
    $filterInfo[] = "Semester: " . $semester;
}
if ($subject_code) {
    $filterInfo[] = "Subject: " . $subject_code;
}

if (!empty($filterInfo)) {
    $pdf->Cell(0, 10, "Filters: " . implode(' | ', $filterInfo), 0, 1, 'C');
}
$pdf->Ln(2);

// Table headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$headers = ['Date', 'Subject Code', 'Subject Title', 'Section', 'A.Y.', 'Semester', 'Avg Score', 'Rating (%)', 'No. Students', 'Comments'];
$widths = [25, 30, 50, 20, 25, 20, 22, 22, 25, 55]; // Adjusted width for Comments to fit A4 landscape

foreach ($headers as $i => $header) {
    $pdf->Cell($widths[$i], 10, $header, 1, 0, 'C', true);
}
$pdf->Ln();

// Table data
$pdf->SetFont('Arial', '', 9);
if ($results && $results->num_rows > 0) {
    while ($row = $results->fetch_assoc()) {
        $pdf->Cell($widths[0], 8, date("M j, Y", strtotime($row['created_at'])), 1);
        $pdf->Cell($widths[1], 8, $row['subject_code'], 1);
        $pdf->Cell($widths[2], 8, $row['subject_title'], 1);
        $pdf->Cell($widths[3], 8, $row['student_section'], 1);
        $pdf->Cell($widths[4], 8, $row['academic_year'], 1);
        $pdf->Cell($widths[5], 8, $row['semester'], 1);
        $pdf->Cell($widths[6], 8, number_format($row['avg_total_score'], 2), 1, 0, 'C');
        $pdf->Cell($widths[7], 8, number_format($row['avg_computed_rating'], 2) . '%', 1, 0, 'C');
        $pdf->Cell($widths[8], 8, $row['student_count'], 1, 0, 'C');

        // Handle comments that are too long for the cell
        $comments = $row['comments'];
        $comment_cell_width = $widths[9];
        $pdf->SetX($pdf->GetX()); // Set X to the current position before printing MultiCell

        if ($pdf->GetStringWidth($comments) > $comment_cell_width - 2) { // -2 for padding
            $pdf->MultiCell($comment_cell_width, 4, $comments, 1, 'L'); // Use MultiCell for wrapping
            $current_y = $pdf->GetY();
            $last_cell_height = $pdf->GetY() - $current_y;
            // Move back to the beginning of the row for the next cells if MultiCell created new lines
            $pdf->SetY($current_y - 4); // Reset Y to the top of the multicell
            $pdf->SetX($pdf->GetX() - $comment_cell_width - array_sum(array_slice($widths, 0, 9))); // Adjust X back to previous cell
        } else {
            $pdf->Cell($comment_cell_width, 8, $comments, 1, 0, 'L');
        }
        $pdf->Ln(); // Move to the next line after the row
    }
} else {
    // Display a message if no records are found under the given filters
    $pdf->Cell(array_sum($widths), 10, "No evaluation records found for the selected filters.", 1, 1, 'C');
}


// Footer
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Prepared by:', 0, 1, 'L');
$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(90, 6, $full_name, 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 6, 'Faculty Member', 0, 1, 'L');
$pdf->Cell(90, 6, 'Date Signed: ' . date('F d, Y'), 0, 1, 'L');

$pdf->Output('I', 'faculty-pastrecords.pdf');
?>
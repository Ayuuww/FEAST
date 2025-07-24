<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    die('Access denied');
}

$admin_id = $_SESSION['idnumber'];
$academic_year = $_GET['academic_year'] ?? '';
$semester      = $_GET['semester'] ?? ''; // NEW: Get the semester filter
$subject_code  = $_GET['subject_code'] ?? '';

// Fetch admin info (assuming admin table structure has department, first_name, etc.)
$stmt = $conn->prepare("SELECT department, first_name, mid_name, last_name FROM admin WHERE idnumber = ?");
if ($stmt) {
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $stmt->bind_result($department, $fname, $mname, $lname);
    $stmt->fetch();
    $stmt->close();
} else {
    // Handle error if prepare fails
    error_log("Failed to prepare admin info statement in admin-pastrecords-print.php: " . $conn->error);
    $department = $fname = $mname = $lname = 'N/A'; // Default values
}

$full_name = "$fname $mname $lname";

// Build evaluation query based on filters
$params = [$admin_id];
$types = "s";
$sql = "SELECT subject_code, subject_title, student_section, academic_year, semester, created_at,
               COUNT(*) AS student_count,
               AVG(total_score) AS avg_total_score,
               AVG(computed_rating) AS avg_computed_rating,
               GROUP_CONCAT(comment SEPARATOR ' | ') AS comments
        FROM evaluation
        WHERE faculty_id = ?"; // Important: filter by faculty_id, which is $admin_id in this context

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
if ($stmt) { // Check if prepare was successful
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $results = $stmt->get_result();
    $stmt->close();
} else {
    // Handle error if prepare fails
    error_log("Failed to prepare evaluation data statement in admin-pastrecords-print.php: " . $conn->error);
    $results = false; // Indicate an error
}


// PDF generation
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, "Self Evaluation Report", 0, 1, 'C');

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
$widths = [25, 30, 50, 20, 25, 20, 22, 22, 25, 55]; // Ensure widths accommodate content, total 284mm for A4 landscape

foreach ($headers as $i => $header) {
    $pdf->Cell($widths[$i], 10, $header, 1, 0, 'C', true);
}
$pdf->Ln();

// Table body
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

        // Handling comments with MultiCell for wrapping
        $comments = $row['comments'];
        $comment_cell_width = $widths[9];

        // Store current Y position before MultiCell
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Output MultiCell for comments (height 4 per line, Border 1, Align Left)
        $pdf->MultiCell($comment_cell_width, 4, $comments, 1, 'L');

        // Calculate the height taken by the MultiCell
        $multi_cell_height = $pdf->GetY() - $y;

        // Move the Y position back to the original row's height, if MultiCell didn't use full height.
        // Or advance Y by the MultiCell height for the next row.
        $pdf->SetY($y); // Move cursor back to the starting Y of the current row
        $pdf->SetX($x + $comment_cell_width); // Move cursor right to the end of the MultiCell

        // Ensure all cells in the row have the same height. If MultiCell wrapped,
        // we need to set the height for the rest of the cells for the next row.
        // We'll manually advance the Y pointer for the entire row here.
        $pdf->SetY($y + max(8, $multi_cell_height)); // Use max(8, actual height) to ensure minimum row height

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
$pdf->Cell(90, 6, 'Administrative Staff', 0, 1, 'L');
$pdf->Cell(90, 6, 'Date Signed: ' . date('F d, Y'), 0, 1, 'L');

$pdf->Output('I', 'admin-self-pastrecords.pdf');
?>
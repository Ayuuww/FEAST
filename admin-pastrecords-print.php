<?php
require('fpdf/fpdf.php');
include 'conn/conn.php';
include 'printing-headerfooter.php';
session_start();

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    die('Access denied');
}

$admin_id     = $_SESSION['idnumber'];
$academic_year = $_GET['academic_year'] ?? '';
$semester      = $_GET['semester'] ?? '';
$subject_code  = $_GET['subject_code'] ?? '';

// Fetch admin info
$stmt = $conn->prepare("SELECT department, faculty_rank, first_name, mid_name, last_name, position 
                        FROM admin 
                        WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$stmt->bind_result($department, $faculty_rank, $fname, $mname, $lname, $position);
$stmt->fetch();
$stmt->close();

$admin_name = trim("$fname $mname $lname");

// Save department for header/footer
$_SESSION['department'] = $department;

// --- Build evaluation query ---
$params = [$admin_id];
$types  = "s";
$sql = "SELECT subject_code, subject_title, student_section, academic_year, semester, created_at,
               COUNT(*) AS student_count,
               AVG(total_score) AS avg_total_score,
               AVG(computed_rating) AS avg_computed_rating
        FROM evaluation
        WHERE faculty_id = ?";

if ($academic_year) { $sql .= " AND academic_year = ?"; $params[] = $academic_year; $types .= "s"; }
if ($semester)      { $sql .= " AND semester = ?";      $params[] = $semester;      $types .= "s"; }
if ($subject_code)  { $sql .= " AND subject_code = ?";  $params[] = $subject_code;  $types .= "s"; }

$sql .= " GROUP BY subject_code, student_section, semester, academic_year
          ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// --- PDF ---
$pdf = new PDF_EXTENDED('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// Admin + filter info
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,6,"Name: $admin_name",0,1,'L');
$pdf->Cell(0,6,"Department: $department",0,1,'L');

if ($semester) {
    $pdf->Cell(0,6,"Semester: $semester",0,1,'L');
} else {
    $pdf->Cell(0,6,"Semester: All Semesters",0,1,'L');
}

if ($academic_year) {
    $pdf->Cell(0,6,"Academic Year: $academic_year",0,1,'L');
} else {
    $pdf->Cell(0,6,"Academic Year: All Academic Years",0,1,'L');
}

$pdf->Cell(0,6,"Date Printed: ".date("F j, Y"),0,1,'L');
$pdf->Ln(3);

// Table header
$headers = ['Date','Subject Code','Subject Title','Section','A.Y.','Semester','Avg Score','Rating (%)','Students'];
$widths  = [25, 28, 55, 25, 28, 28, 22, 25, 28];
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(230,230,230);
foreach ($headers as $i=>$h) {
    $pdf->Cell($widths[$i],8,$h,1,0,'C',true);
}
$pdf->Ln();

// Table data
$pdf->SetFont('Arial','',9);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell($widths[0],7,date("M j, Y",strtotime($row['created_at'])),1);
        $pdf->Cell($widths[1],7,$row['subject_code'],1);
        $pdf->Cell($widths[2],7,$row['subject_title'],1);
        $pdf->Cell($widths[3],7,$row['student_section'],1);
        $pdf->Cell($widths[4],7,$row['academic_year'],1);
        $pdf->Cell($widths[5],7,$row['semester'],1);
        $pdf->Cell($widths[6],7,number_format($row['avg_total_score'],2),1,0,'C');
        $pdf->Cell($widths[7],7,number_format($row['avg_computed_rating'],2).'%',1,0,'C');
        $pdf->Cell($widths[8],7,$row['student_count'],1,0,'C');
        $pdf->Ln();
    }
} else {
    $pdf->Cell(array_sum($widths),8,"No evaluation records found.",1,1,'C');
}

// --- Summary of Comments ---
// $pdf->AddPage();
$pdf->Ln(4);
$pdf->SetFont('Arial','B',12);

if ($subject_code) {
    $pdf->Cell(0,8,"Summary of Comments for Subject: $subject_code",0,1,'L');
} else {
    $pdf->Cell(0,8,"Summary of Comments",0,1,'L');
}
$pdf->Ln(3);    

$comment_sql = "SELECT subject_code, comment 
                FROM evaluation 
                WHERE faculty_id = ? 
                  AND comment IS NOT NULL 
                  AND TRIM(comment) <> ''";
$comment_params = [$admin_id];
$comment_types  = "s";

if ($academic_year) { $comment_sql .= " AND academic_year = ?"; $comment_params[] = $academic_year; $comment_types .= "s"; }
if ($semester)     { $comment_sql .= " AND semester = ?";      $comment_params[] = $semester;      $comment_types .= "s"; }
if ($subject_code) { $comment_sql .= " AND subject_code = ?";  $comment_params[] = $subject_code;  $comment_types .= "s"; }

$stmt2 = $conn->prepare($comment_sql);
$stmt2->bind_param($comment_types, ...$comment_params);
$stmt2->execute();
$comments = $stmt2->get_result();
$stmt2->close();

$positive = [];
$others   = [];

if ($comments->num_rows > 0) {
    while ($c = $comments->fetch_assoc()) {
        $comment = trim($c['comment']);
        if ($comment !== '') {
            if (preg_match('/\b(excellent|nice|great|good|very good|amazing|outstanding)\b/i', $comment)) {
                $positive[] = $c['subject_code'].": ".$comment;
            } else {
                $others[] = $c['subject_code'].": ".$comment;
            }
        }
    }
    $all_comments = array_merge($positive, $others);
    $all_comments = array_slice($all_comments, 0, 5);

    $pdf->SetFont('Arial','',10);
    $i = 1;
    foreach ($all_comments as $comment) {
        $pdf->MultiCell(0,6,"$i. ".$comment,0,'L');
        $i++;
    }
} else {
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,6,"No comments available.",0,1,'L');
}

// --- Prepared by ---
$pdf->Ln(10);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,6,"Prepared by:",0,1,'L');
$pdf->Ln(8);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(90,6,$admin_name,0,1,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(90,6,$faculty_rank,0,1,'L');
$pdf->Cell(90,6,"Date Signed: ".date("F d, Y"),0,1,'L');

$pdf->Output('I','admin-pastrecords.pdf');
?>

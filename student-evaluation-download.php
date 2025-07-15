<?php
$student_id = $_GET['student_id'] ?? '';
$faculty_id = $_GET['faculty_id'] ?? '';
$subject_code = $_GET['subject_code'] ?? '';

$filename = "evaluation_{$student_id}_{$faculty_id}_{$subject_code}.pdf";
$filePath = __DIR__ . "/pdfs/" . $filename;

if (file_exists($filePath)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    readfile($filePath);
    exit;
} else {
    echo "PDF not found.";
}
?>

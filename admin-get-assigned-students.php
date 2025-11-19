<?php
session_start();
header('Content-Type: application/json');

include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Optional: require admin role
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  echo json_encode([]);
  exit;
}

// read subject codes from POST
$subject_codes = $_POST['subject_codes'] ?? [];
if (!is_array($subject_codes) || count($subject_codes) === 0) {
  echo json_encode([]);
  exit;
}

// get current academic year / semester (same logic as page)
$periodRes = $conn->query("SELECT academic_year, semester FROM evaluation_settings ORDER BY updated_at DESC LIMIT 1");
$period = $periodRes->fetch_assoc();
$ay = $period['academic_year'] ?? null;
$sem = $period['semester'] ?? null;

// if no active period, return empty (or you may prefer to return error)
if (!$ay || !$sem) {
  echo json_encode([]);
  exit;
}

// escape and build quoted list
$escaped = array_map(function ($v) use ($conn) {
  return "'" . $conn->real_escape_string(trim($v)) . "'";
}, $subject_codes);
$in_list = implode(',', $escaped);

// query student_subject for matches
$sql = "SELECT DISTINCT student_id FROM student_subject
        WHERE subject_code IN ($in_list)
          AND academic_year = '" . $conn->real_escape_string($ay) . "'
          AND semester = '" . $conn->real_escape_string($sem) . "'";

$res = $conn->query($sql);
$assigned = [];
while ($row = $res->fetch_assoc()) {
  $assigned[] = $row['student_id'];
}

// return JSON array of student ids
echo json_encode($assigned);
exit;

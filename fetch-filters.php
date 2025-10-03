<?php
include 'conn/conn.php';

// Get distinct academic years
$yearsRes = $conn->query("SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
$years = [];
while ($row = $yearsRes->fetch_assoc()) {
  if (!empty($row['academic_year'])) {
    $years[] = $row['academic_year'];
  }
}

// Get distinct semesters
$semRes = $conn->query("SELECT DISTINCT semester FROM evaluation ORDER BY semester ASC");
$semesters = [];
while ($row = $semRes->fetch_assoc()) {
  if (!empty($row['semester'])) {
    $semesters[] = $row['semester'];
  }
}

header('Content-Type: application/json');
echo json_encode([
  "years" => $years,
  "semesters" => $semesters
]);

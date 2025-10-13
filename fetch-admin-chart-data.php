<?php
header('Content-Type: application/json; charset=utf-8');
include 'conn/conn.php';

$year     = isset($_GET['year']) && $_GET['year'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['year']) : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['semester']) : null;
$dept     = isset($_GET['dept']) && $_GET['dept'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['dept']) : null;

$sql = "SELECT f.idnumber AS faculty_id,
               CONCAT(f.first_name, ' ', f.last_name) AS faculty_name,
               s.code AS subject_code,
               s.title AS subject_name,
               f.department
        FROM faculty f
        JOIN subject s ON s.faculty_id = f.idnumber";

$conds = [];
if ($dept) {
  $conds[] = "f.department = '{$dept}'";
}
if (!empty($conds)) {
  $sql .= " WHERE " . implode(" AND ", $conds);
}
$sql .= " ORDER BY f.last_name, f.first_name, s.title";

$res = mysqli_query($conn, $sql);
if (!$res) {
  echo json_encode(['error' => mysqli_error($conn)]);
  exit;
}

$labels = [];
$subjects = [];
$completed = [];
$pending = [];
$ratios = []; // <-- NEW

while ($row = mysqli_fetch_assoc($res)) {
  $faculty_id   = $row['faculty_id'];
  $subject_code = $row['subject_code'];

  // Expected students
  $exp_sql = "SELECT COUNT(DISTINCT ss.student_id) AS cnt
                FROM student_subject ss
                WHERE ss.subject_code = '" . mysqli_real_escape_string($conn, $subject_code) . "'
                  AND ss.faculty_id = '" . mysqli_real_escape_string($conn, $faculty_id) . "'";
  if ($year) {
    $exp_sql .= " AND ss.academic_year = '{$year}'";
  }
  if ($semester) {
    $exp_sql .= " AND ss.semester = '{$semester}'";
  }
  $exp_res = mysqli_query($conn, $exp_sql);
  $expected = (int) (mysqli_fetch_assoc($exp_res)['cnt'] ?? 0);

  // Completed evals
  $comp_sql = "SELECT COUNT(DISTINCT e.student_id) AS cnt
                 FROM evaluation e
                 WHERE e.subject_code = '" . mysqli_real_escape_string($conn, $subject_code) . "'
                   AND e.faculty_id = '" . mysqli_real_escape_string($conn, $faculty_id) . "'";
  if ($year) {
    $comp_sql .= " AND e.academic_year = '{$year}'";
  }
  if ($semester) {
    $comp_sql .= " AND e.semester = '{$semester}'";
  }
  $comp_res = mysqli_query($conn, $comp_sql);
  $completed_cnt = (int) (mysqli_fetch_assoc($comp_res)['cnt'] ?? 0);

  // Compute percentage
  $progress = $expected > 0 ? round(($completed_cnt / $expected) * 100, 2) : 0;

  $labels[]    = $row['faculty_name'] . " — " . $row['subject_name'];
  $subjects[]  = $row['subject_name'];
  $completed[] = $progress;
  $pending[]   = round(100 - $progress, 2);
  $ratios[]    = "{$completed_cnt}/{$expected}"; // <-- NEW: ratio for tooltip
}

echo json_encode([
  "labels"   => $labels,
  "subjects" => $subjects,
  "ratios"   => $ratios, // <-- NEW
  "datasets" => [
    [
      "label" => "Completed",
      "data"  => $completed,
      "backgroundColor" => "rgba(75, 192, 192, 0.85)"
    ],
    [
      "label" => "Pending",
      "data"  => $pending,
      "backgroundColor" => "rgba(255, 99, 132, 0.7)"
    ]
  ]
]);

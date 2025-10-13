<?php
header('Content-Type: application/json; charset=utf-8');
include 'conn/conn.php';

$year     = isset($_GET['year']) && $_GET['year'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['year']) : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['semester']) : null;
$dept     = isset($_GET['dept']) && $_GET['dept'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['dept']) : null;

// ✅ Always filter faculty by department (ground truth)
$faculty_sql = "SELECT idnumber, CONCAT(first_name, ' ', last_name) AS faculty_name, department 
                FROM faculty 
                WHERE status='active'";
if ($dept) {
  $faculty_sql .= " AND department = '{$dept}'";
}
$faculty_res = mysqli_query($conn, $faculty_sql);

$labels    = [];
$completed = [];
$pending   = [];

while ($fac = mysqli_fetch_assoc($faculty_res)) {
  $faculty_id   = $fac['idnumber'];
  $faculty_name = $fac['faculty_name'];

  // Expected = 1 supervisor evaluation per faculty
  $expected = 1;

  // ✅ Count evaluations ONLY by faculty_id, year, sem (not evaluation.department)
  $eval_sql = "SELECT COUNT(*) AS cnt
             FROM admin_evaluation
             WHERE evaluator_position IN ('Dean', 'Chair Person', 'Program Chair')
               AND evaluatee_id = '{$faculty_id}'";

  if ($year) {
    $eval_sql .= " AND academic_year = '{$year}'";
  }
  if ($semester) {
    $eval_sql .= " AND semester = '{$semester}'";
  }

  $eval_res = mysqli_query($conn, $eval_sql);
  $completed_cnt = (int) (mysqli_fetch_assoc($eval_res)['cnt'] ?? 0);

  // Calculate %
  $progress = $expected > 0 ? round(($completed_cnt / $expected) * 100, 2) : 0;

  $labels[]    = $faculty_name;
  $completed[] = $progress;
  $pending[]   = round(100 - $progress, 2);
}

echo json_encode([
  "labels"   => $labels,
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

<?php
session_start();
include 'conn/conn.php';

// Apply filters
$year = isset($_GET['year']) && $_GET['year'] !== 'All' ? $_GET['year'] : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? $_GET['semester'] : null;

$labels = [];
$completed = [];
$notCompleted = [];
$doneCounts = [];
$totalCounts = [];

// Get all faculty in admin's department
// Get all faculty+subjects in admin’s department
$facultyQuery = $conn->query("
    SELECT f.idnumber AS faculty_id,
           CONCAT(f.first_name, ' ', f.last_name) AS faculty_name,
           s.code AS subject_code,
           s.title AS subject_name
    FROM faculty f
    JOIN subject s ON s.faculty_id = f.idnumber
    WHERE f.department = '{$_SESSION['department']}'
    ORDER BY f.last_name, f.first_name, s.title
");

while ($row = $facultyQuery->fetch_assoc()) {
  $faculty_id   = $row['faculty_id'];
  $subject_code = $row['subject_code'];

  // Expected students (faculty+subject)
  $exp_sql = "
        SELECT COUNT(DISTINCT ss.student_id) AS total
        FROM student_subject ss
        WHERE ss.subject_code = '{$subject_code}'
          AND ss.faculty_id = '{$faculty_id}'
    ";
  if ($year)     $exp_sql .= " AND ss.academic_year = '{$year}'";
  if ($semester) $exp_sql .= " AND ss.semester = '{$semester}'";

  $expected = (int)($conn->query($exp_sql)->fetch_assoc()['total'] ?? 0);

  // Completed evaluations (faculty+subject)
  $done_sql = "
        SELECT COUNT(DISTINCT e.student_id) AS done
        FROM evaluation e
        WHERE e.subject_code = '{$subject_code}'
          AND e.faculty_id = '{$faculty_id}'
    ";
  if ($year)     $done_sql .= " AND e.academic_year = '{$year}'";
  if ($semester) $done_sql .= " AND e.semester = '{$semester}'";

  $done = (int)($conn->query($done_sql)->fetch_assoc()['done'] ?? 0);

  $completedPercent    = $expected > 0 ? round(($done / $expected) * 100, 2) : 0;
  $notCompletedPercent = 100 - $completedPercent;

  $labels[]      = $row['faculty_name'] . " — " . $row['subject_name'];
  $completed[]   = $completedPercent;
  $notCompleted[] = $notCompletedPercent;
  $doneCounts[]  = $done;
  $totalCounts[] = $expected;
}


// Build chart data
$data = [
  "labels" => $labels,
  "datasets" => [
    [
      "label" => "Completed (%)",
      "data" => $completed,
      "backgroundColor" => "rgba(75, 192, 192, 0.7)",
      "borderColor" => "rgba(75, 192, 192, 1)",
      "borderWidth" => 1
    ],
    [
      "label" => "Not Completed (%)",
      "data" => $notCompleted,
      "backgroundColor" => "rgba(255, 99, 132, 0.7)",
      "borderColor" => "rgba(255, 99, 132, 1)",
      "borderWidth" => 1
    ]
  ],
  "meta" => [
    "done" => $doneCounts,
    "total" => $totalCounts
  ]
];

header('Content-Type: application/json');
echo json_encode($data);

<?php
include 'conn/conn.php';

$selectedYear = $_GET['year'] ?? 'All';

// Base query
$query = "
  SELECT department, semester, AVG(computed_rating) AS avg_rating
  FROM admin_evaluation
";

// Filter by year if not "All"
if ($selectedYear !== 'All') {
  $query .= " WHERE academic_year = '" . mysqli_real_escape_string($conn, $selectedYear) . "'";
}

$query .= " GROUP BY department, semester ORDER BY department ASC";

// Run query
$result = mysqli_query($conn, $query);

$departments = [];
$semesters = ['1st Semester', '2nd Semester', 'Summer'];
$dataBySemester = [];

// Collect all department names and average ratings
while ($row = mysqli_fetch_assoc($result)) {
  $dept = $row['department'] ?? 'Unassigned';
  $sem = $row['semester'];
  $avg = round($row['avg_rating'], 2);

  $departments[$dept] = true;
  $dataBySemester[$sem][$dept] = $avg;
}

// Sort departments alphabetically
$departmentLabels = array_keys($departments);
sort($departmentLabels);

// Color scheme for semesters
$colors = [
  '1st Semester' => '#4CAF50',
  '2nd Semester' => '#007A33',
  'Summer' => '#005825ff'
];

$datasets = [];

foreach ($semesters as $semester) {
  $data = [];
  foreach ($departmentLabels as $dept) {
    $data[] = $dataBySemester[$semester][$dept] ?? 0;
  }
  $datasets[] = [
    'label' => $semester,
    'data' => $data,
    'backgroundColor' => $colors[$semester]
  ];
}

// Output chart data
echo json_encode([
  'labels' => $departmentLabels,
  'datasets' => $datasets
]);
?>
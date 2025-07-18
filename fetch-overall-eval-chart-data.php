<?php
include 'conn/conn.php';

$selectedYear = $_GET['year'] ?? 'All';

// Step 1: Fetch student evaluations
$student_query = "
  SELECT f.department, e.semester, AVG(e.computed_rating) AS avg_rating
  FROM evaluation e
  JOIN faculty f ON f.idnumber = e.faculty_id
";

if ($selectedYear !== 'All') {
  $student_query .= " WHERE e.academic_year = '" . mysqli_real_escape_string($conn, $selectedYear) . "'";
}

$student_query .= " GROUP BY f.department, e.semester";
$student_result = mysqli_query($conn, $student_query);

// Step 2: Fetch admin evaluations
$admin_query = "
  SELECT f.department, a.semester, AVG(a.computed_rating) AS avg_rating
  FROM admin_evaluation a
  JOIN faculty f ON f.idnumber = a.evaluatee_id
";

if ($selectedYear !== 'All') {
  $admin_query .= " WHERE a.academic_year = '" . mysqli_real_escape_string($conn, $selectedYear) . "'";
}

$admin_query .= " GROUP BY f.department, a.semester";
$admin_result = mysqli_query($conn, $admin_query);

// Step 3: Merge student + admin scores
$departments = [];
$rawData = [];

// Student data
while ($row = mysqli_fetch_assoc($student_result)) {
  $dept = $row['department'];
  $sem = $row['semester'];
  $departments[$dept] = true;
  $rawData[$sem][$dept]['student'] = round($row['avg_rating'], 2);
}

// Admin data
while ($row = mysqli_fetch_assoc($admin_result)) {
  $dept = $row['department'];
  $sem = $row['semester'];
  $departments[$dept] = true;
  $rawData[$sem][$dept]['admin'] = round($row['avg_rating'], 2);
}

// Step 4: Build datasets
$departmentLabels = array_keys($departments);
$colors = ['#4CAF50', '#007A33', '#FF9800', '#9C27B0', '#03A9F4', '#F44336'];
$datasets = [];

$index = 0;
foreach ($rawData as $semester => $semData) {
  $data = [];
  foreach ($departmentLabels as $dept) {
    $student = $semData[$dept]['student'] ?? null;
    $admin = $semData[$dept]['admin'] ?? null;

    if ($student !== null && $admin !== null) {
      $avg = round(($student + $admin) / 2, 2);
    } elseif ($student !== null) {
      $avg = $student;
    } elseif ($admin !== null) {
      $avg = $admin;
    } else {
      $avg = 0;
    }

    $data[] = $avg;
  }

  $datasets[] = [
    'label' => $semester,
    'data' => $data,
    'backgroundColor' => $colors[$index++ % count($colors)]
  ];
}

// Final output
$chartData = [
  'labels' => $departmentLabels,
  'datasets' => $datasets
];

echo json_encode($chartData);
?>

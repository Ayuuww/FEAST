<?php
include 'conn/conn.php';

$selectedYear = $_GET['year'] ?? 'All';

$chart_query = "
  SELECT f.college, e.semester, AVG(e.computed_rating) AS avg_rating
  FROM evaluation e
  JOIN faculty f ON f.idnumber = e.faculty_id
";

if ($selectedYear !== 'All') {
  $chart_query .= " WHERE e.academic_year = '" . mysqli_real_escape_string($conn, $selectedYear) . "'";
}

$chart_query .= " GROUP BY f.college, e.semester";

$chart_result = mysqli_query($conn, $chart_query);

$college = [];
$rawData = [];

while ($row = mysqli_fetch_assoc($chart_result)) {
  $dept = $row['college'];
  $sem = $row['semester'];
  $college[$dept] = true;
  $rawData[$sem][$dept] = round($row['avg_rating'], 2);
}

$collegeLabels = array_keys($college);
$colors = ['#4CAF50', '#007A33', '#005825ff', '#537bc4', '#acc236', '#166a8f'];

$datasets = [];
$index = 0;
foreach ($rawData as $semester => $values) {
  $data = [];
  foreach ($collegeLabels as $dept) {
    $data[] = $values[$dept] ?? 0;
  }
  $datasets[] = [
    'label' => $semester,
    'data' => $data,
    'backgroundColor' => $colors[$index++ % count($colors)]
  ];
}

$chartData = [
  'labels' => $collegeLabels,
  'datasets' => $datasets
];

echo json_encode($chartData);
?>
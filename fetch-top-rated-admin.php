<?php
include 'conn/conn.php';

$year = $_GET['year'] ?? 'All';
$semester = $_GET['semester'] ?? 'All';

$where = [];
if ($year !== 'All') {
  $where[] = "academic_year = '" . mysqli_real_escape_string($conn, $year) . "'";
}
if ($semester !== 'All') {
  $where[] = "semester = '" . mysqli_real_escape_string($conn, $semester) . "'";
}

$whereClause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "
  SELECT 
    evaluatee_id,
    CONCAT(f.first_name, ' ', LEFT(f.mid_name, 1), '. ', f.last_name) AS name,
    f.department,
    AVG(e.computed_rating) AS avg_rating
  FROM admin_evaluation e
  JOIN faculty f ON f.idnumber = e.evaluatee_id
  $whereClause
  GROUP BY evaluatee_id
  ORDER BY avg_rating DESC
  LIMIT 10
";

$result = mysqli_query($conn, $query);

$names = [];
$ratings = [];

while ($row = mysqli_fetch_assoc($result)) {
  $names[] = $row['name'];
  $ratings[] = [
    'value' => round((float)$row['avg_rating'], 2),
    'name' => $row['name'],
    'department' => $row['department']
  ];
}

echo json_encode([
  'names' => $names,
  'ratings' => $ratings
]);

?>
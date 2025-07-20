<?php
include 'conn/conn.php';

$year = $_GET['year'] ?? 'All';
$semester = $_GET['semester'] ?? 'All';

$where = [];
if ($year !== 'All') $where[] = "e.academic_year = '" . mysqli_real_escape_string($conn, $year) . "'";
if ($semester !== 'All') $where[] = "e.semester = '" . mysqli_real_escape_string($conn, $semester) . "'";

$where_clause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "
  SELECT 
    f.idnumber, 
    CONCAT(f.first_name, ' ', LEFT(f.mid_name, 1), '. ', f.last_name) AS name,
    f.department,
    AVG(e.computed_rating) AS avg_rating
  FROM evaluation e
  JOIN faculty f ON f.idnumber = e.faculty_id
  $where_clause
  GROUP BY f.idnumber
  ORDER BY avg_rating DESC
  LIMIT 10
";

$result = mysqli_query($conn, $query);

$names = [];
$ratings = [];

while ($row = mysqli_fetch_assoc($result)) {
  $names[] = $row['name'];
  $ratings[] = [
    'value' => round($row['avg_rating'], 2),
    'name' => $row['name'],
    'department' => $row['department']
  ];
}

echo json_encode([
  'names' => array_reverse($names),
  'ratings' => array_reverse($ratings)
]);

?>
<?php
include 'conn/conn.php';
session_start();

$admin_id = $_SESSION['idnumber'] ?? '';

// Get the department of the logged-in admin
$dept_stmt = $conn->prepare("SELECT department FROM admin WHERE idnumber = ?");
$dept_stmt->bind_param("s", $admin_id);
$dept_stmt->execute();
$dept_result = $dept_stmt->get_result();
$department = $dept_result->fetch_assoc()['department'] ?? '';

$year = $_GET['year'] ?? 'All';
$semester = $_GET['semester'] ?? 'All';

$query = "
  SELECT f.idnumber, f.first_name, f.mid_name, f.last_name, AVG(e.computed_rating) as avg_rating
  FROM evaluation e
  JOIN faculty f ON e.faculty_id = f.idnumber
  WHERE f.department = ?
";

$params = [$department];
$types = 's';

if ($year !== 'All') {
  $query .= " AND e.academic_year = ?";
  $params[] = $year;
  $types .= 's';
}
if ($semester !== 'All') {
  $query .= " AND e.semester = ?";
  $params[] = $semester;
  $types .= 's';
}

$query .= " GROUP BY f.idnumber ORDER BY avg_rating DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
  $full_name =  $row['last_name'] . ', '  . $row['first_name'] ;
  $data['names'][] = $full_name;
  $data['ratings'][] = [
    'value' => round($row['avg_rating'], 2),
    'name' => $full_name
  ];
}
echo json_encode($data);

?>
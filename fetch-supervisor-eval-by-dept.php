<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['names' => [], 'ratings' => []]);
    exit();
}

$admin_id = $_SESSION['idnumber'];

// Get admin's department
$stmt = $conn->prepare("SELECT department FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$dept_result = $stmt->get_result()->fetch_assoc();
$department = $dept_result['department'] ?? '';

$year = $_GET['year'] ?? 'All';
$semester = $_GET['semester'] ?? 'All';

$sql = "
  SELECT f.idnumber, f.first_name, f.last_name, AVG(a.computed_rating) as avg_rating
  FROM admin_evaluation a
  JOIN faculty f ON f.idnumber = a.evaluatee_id
  WHERE f.department = ?
";

$params = [$department];
$types = "s";

if ($year !== 'All') {
    $sql .= " AND a.academic_year = ?";
    $types .= "s";
    $params[] = $year;
}
if ($semester !== 'All') {
    $sql .= " AND a.semester = ?";
    $types .= "s";
    $params[] = $semester;
}

$sql .= " GROUP BY f.idnumber, f.first_name, f.last_name ORDER BY avg_rating DESC LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$names = [];
$ratings = [];

while ($row = $result->fetch_assoc()) {
    $names[] = $row['last_name'] . ', ' . $row['first_name'] ;
    $ratings[] = [
        'value' => round($row['avg_rating'], 2),
        'name' => $row['first_name'] . ' ' . $row['last_name']
    ];
}

echo json_encode(['names' => $names, 'ratings' => $ratings]);

?>
<?php
session_start();
include 'conn/conn.php';

header('Content-Type: application/json');

// Ensure only admins can access this
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  echo json_encode(['names' => [], 'ratings' => []]);
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get the department of the current admin
$stmt = $conn->prepare("SELECT department FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$admin_dept = $stmt->get_result()->fetch_assoc()['department'] ?? '';

$year = $_GET['year'] ?? 'All';
$semester = $_GET['semester'] ?? 'All';

// Combined query from student and admin evaluations
$sql = "
    SELECT 
        f.idnumber,
        f.first_name,
        f.last_name,
        AVG(all_data.rating) AS avg_rating
    FROM (
        SELECT 
            faculty_id AS fac_id, 
            computed_rating AS rating, 
            academic_year, 
            semester
        FROM evaluation
        WHERE faculty_id IS NOT NULL

        UNION ALL

        SELECT 
            evaluatee_id AS fac_id, 
            computed_rating AS rating, 
            academic_year, 
            semester
        FROM admin_evaluation
    ) AS all_data
    JOIN faculty f ON f.idnumber = all_data.fac_id
    WHERE f.department = ?
";

$types = "s";
$params = [$admin_dept];

if ($year !== 'All') {
  $sql .= " AND all_data.academic_year = ?";
  $types .= "s";
  $params[] = $year;
}

if ($semester !== 'All') {
  $sql .= " AND all_data.semester = ?";
  $types .= "s";
  $params[] = $semester;
}

$sql .= " GROUP BY f.idnumber ORDER BY avg_rating DESC LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$names = [];
$ratings = [];

while ($row = $result->fetch_assoc()) {
  $name = $row['last_name'] . ', ' . $row['first_name'];
  $avg = round((float)$row['avg_rating'], 2);
  $names[] = $name;
  $ratings[] = [
    'value' => $avg,
    'name' => $name
  ];
}

echo json_encode(['names' => $names, 'ratings' => $ratings]);

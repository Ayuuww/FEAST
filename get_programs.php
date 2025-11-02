<?php
include 'conn/conn.php';
header('Content-Type: application/json');

$department = $_GET['department'] ?? '';
$programs = [];

if ($department) {
  $stmt = $conn->prepare("SELECT DISTINCT program_name FROM adds WHERE department_name = ? AND program_name IS NOT NULL AND program_name != '' ORDER BY program_name ASC");
  $stmt->bind_param("s", $department);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $programs[] = $row['program_name'];
  }
  $stmt->close();
}

echo json_encode($programs);

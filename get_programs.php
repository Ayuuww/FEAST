<?php
include 'conn/conn.php';
header('Content-Type: application/json');

$college = $_GET['college'] ?? '';
$programs = [];

if ($college) {
  $stmt = $conn->prepare("SELECT DISTINCT program_name FROM adds WHERE college_name = ? AND program_name IS NOT NULL AND program_name != '' ORDER BY program_name ASC");
  $stmt->bind_param("s", $college);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $programs[] = $row['program_name'];
  }
  $stmt->close();
}

echo json_encode($programs);

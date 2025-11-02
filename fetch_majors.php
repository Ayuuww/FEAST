<?php
include 'conn/conn.php';
$program_id = $_POST['program_id'] ?? 0;

$stmt = $conn->prepare("SELECT id, major_name FROM majors WHERE program_id = ? ORDER BY major_name ASC");
$stmt->bind_param("i", $program_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<option disabled selected>Select Major (optional)</option>';
while ($row = $result->fetch_assoc()) {
  echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['major_name']) . '</option>';
}
$stmt->close();

<?php
include 'conn/conn.php';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$filter = $_GET['filter'] ?? 'all';

$where = '';
if ($filter === 'today') {
  $where = "WHERE DATE(timestamp) = CURDATE()";
} elseif ($filter === 'month') {
  $where = "WHERE MONTH(timestamp) = MONTH(CURDATE()) AND YEAR(timestamp) = YEAR(CURDATE())";
} elseif ($filter === 'year') {
  $where = "WHERE YEAR(timestamp) = YEAR(CURDATE())";
}

$query = "SELECT * FROM activity_logs $where ORDER BY timestamp DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$logs = [];
while ($row = mysqli_fetch_assoc($result)) {
  $logs[] = [
    'timestamp' => $row['timestamp'],
    'role' => $row['role'],
    'activity' => $row['activity']
  ];
}

header('Content-Type: application/json');
echo json_encode($logs);

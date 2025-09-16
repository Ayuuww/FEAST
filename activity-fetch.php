<?php
include 'conn/conn.php';

$limit  = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$filter = $_GET['filter'] ?? 'all';

$where = '';
if ($filter === 'today') {
    $where = "WHERE DATE(`timestamp`) = CURDATE()";
} elseif ($filter === 'month') {
    $where = "WHERE MONTH(`timestamp`) = MONTH(CURDATE()) AND YEAR(`timestamp`) = YEAR(CURDATE())";
} elseif ($filter === 'year') {
    $where = "WHERE YEAR(`timestamp`) = YEAR(CURDATE())";
}

$query = "SELECT `timestamp`, role, user_id, activity FROM activity_logs $where 
          ORDER BY `timestamp` DESC 
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . mysqli_error($conn)]));
}

$logs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $logs[] = $row;
}

header('Content-Type: application/json');
echo json_encode($logs);

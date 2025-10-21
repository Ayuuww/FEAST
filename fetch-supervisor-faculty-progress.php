<?php
session_start();
include 'conn/conn.php';

// Check for superadmin role
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get filters
$year = $_GET['year'] ?? 'All';
$semester = $_GET['semester'] ?? 'All';
$dept = $_GET['dept'] ?? 'All';

$params = [];
$types = '';
$conditions = [];

// Base query
$sql = "
    SELECT f.department, COUNT(DISTINCT f.idnumber) AS total_faculty,
           COUNT(DISTINCT ae.evaluatee_id) AS completed_evaluations
    FROM faculty f
    LEFT JOIN admin_evaluation ae ON f.idnumber = ae.evaluatee_id
";

// Apply filters
if ($year !== 'All') {
    $conditions[] = "ae.academic_year = ?";
    $params[] = $year;
    $types .= 's';
}
if ($semester !== 'All') {
    $conditions[] = "ae.semester = ?";
    $params[] = $semester;
    $types .= 's';
}
if ($dept !== 'All') {
    $sql .= " WHERE f.department = ?";
    $params[] = $dept;
    $types .= 's';
}

if (!empty($conditions)) {
    $sql .= ($dept !== 'All' ? ' AND ' : ' WHERE ') . implode(' AND ', $conditions);
}

$sql .= " GROUP BY f.department ORDER BY f.department ASC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo json_encode(['error' => 'Query preparation failed.']);
    exit;
}

$labels = [];
$completedData = [];
$pendingData = [];
$counts = [];

while ($row = $result->fetch_assoc()) {
    $total = (int)$row['total_faculty'];
    $completed = (int)$row['completed_evaluations'];
    $pending = $total - $completed;

    $completedPercent = ($total > 0) ? round(($completed / $total) * 100, 2) : 0;
    $pendingPercent = ($total > 0) ? round(($pending / $total) * 100, 2) : 0;

    // 👇 Add department name with count text
    $labels[] = $row['department'] . " ({$completed}/{$total})";

    $completedData[] = $completedPercent;
    $pendingData[] = $pendingPercent;
}

$data = [
    "labels" => $labels,
    "datasets" => [
        [
            "label" => "Completed",
            "data" => $completedData,
            "backgroundColor" => "#4bc0c0ff"
        ],
        [
            "label" => "Pending",
            "data" => $pendingData,
            "backgroundColor" => "#ff6384ff"
        ]
    ]
];

header('Content-Type: application/json');
echo json_encode($data);
?>

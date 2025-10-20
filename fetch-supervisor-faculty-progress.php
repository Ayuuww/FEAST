<?php
session_start();
include 'conn/conn.php';

// Check for superadmin role
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get filters from the URL
$year = $_GET['year'] ?? 'All';
$semester = $_GET['semester'] ?? 'All';
$dept = $_GET['dept'] ?? 'All';

// --- Build Query with Prepared Statements ---
$params = [];
$types = '';
$conditions = [];

// Base query: All faculty who are NOT admins (since admins evaluate them)
$sql = "
    SELECT f.department, COUNT(DISTINCT f.idnumber) as total_faculty,
           COUNT(DISTINCT ae.evaluatee_id) as completed_evaluations
    FROM faculty f
    LEFT JOIN admin_evaluation ae ON f.idnumber = ae.evaluatee_id
";

// Apply filters securely
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
    // This condition applies to the faculty table
    $sql .= " WHERE f.department = ?";
    $params[] = $dept;
    $types .= 's';
}

if (!empty($conditions)) {
    // If a department filter is active, use AND, otherwise use WHERE
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
    // Handle SQL error
    echo json_encode(['error' => 'Query preparation failed.']);
    exit;
}

$labels = [];
$completedData = [];
$pendingData = [];

while ($row = $result->fetch_assoc()) {
    $total = (int)$row['total_faculty'];
    $completed = (int)$row['completed_evaluations'];
    $pending = $total - $completed;
    
    // Calculate percentages
    $completedPercent = ($total > 0) ? round(($completed / $total) * 100, 2) : 0;
    $pendingPercent = ($total > 0) ? round(($pending / $total) * 100, 2) : 0;

    $labels[] = $row['department'];
    $completedData[] = $completedPercent;
    $pendingData[] = $pendingPercent;
}

// --- Return chart data as JSON ---
$data = [
    "labels" => $labels,
    "datasets" => [
        [
            "label" => "Completed",
            "data" => $completedData,
            "backgroundColor" => "#4CAF50", // Green
        ],
        [
            "label" => "Pending",
            "data" => $pendingData,
            "backgroundColor" => "#F44336", // Red
        ]
    ]
];

header('Content-Type: application/json');
echo json_encode($data);
?>
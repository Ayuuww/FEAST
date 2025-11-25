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
    SELECT f.college, COUNT(DISTINCT f.idnumber) AS total_faculty,
           COUNT(DISTINCT ae.evaluatee_id) AS completed_evaluations
    FROM faculty f
    LEFT JOIN admin_evaluation ae ON f.idnumber = ae.evaluatee_id
";

// Apply filters
if ($year !== 'All') {
    // IMPORTANT: Add an alias to the joined table condition
    $conditions[] = "ae.academic_year = ?";
    $params[] = $year;
    $types .= 's';
}
if ($semester !== 'All') {
    // IMPORTANT: Add an alias to the joined table condition
    $conditions[] = "ae.semester = ?";
    $params[] = $semester;
    $types .= 's';
}

// Build the JOIN...ON conditions dynamically (to handle 'All' filters)
$join_conditions = "f.idnumber = ae.evaluatee_id";
if (!empty($conditions)) {
    $join_conditions .= " AND " . implode(' AND ', $conditions);
}
// Re-build the LEFT JOIN
$sql = str_replace(
    "LEFT JOIN admin_evaluation ae ON f.idnumber = ae.evaluatee_id",
    "LEFT JOIN admin_evaluation ae ON {$join_conditions}",
    $sql
);


// The college filter goes in the WHERE clause
if ($dept !== 'All') {
    $sql .= " WHERE f.college = ?";
    $params[] = $dept;
    $types .= 's';
}


$sql .= " GROUP BY f.college ORDER BY f.college ASC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo json_encode(['error' => 'Query preparation failed.', 'sql' => $sql]);
    exit;
}

$labels = [];
$completedData = [];
$pendingData = [];
$ratios = []; // <-- NEW: We'll store counts here

while ($row = $result->fetch_assoc()) {
    $total = (int)$row['total_faculty'];
    $completed = (int)$row['completed_evaluations'];
    $pending = $total - $completed;

    $completedPercent = ($total > 0) ? round(($completed / $total) * 100, 2) : 0;
    $pendingPercent = 100 - $completedPercent; // Simpler

    // --- MODIFICATION ---
    $labels[] = $row['college']; // Just the college name
    $ratios[] = "{$completed}/{$total}"; // Store the ratio string separately

    $completedData[] = $completedPercent;
    $pendingData[] = $pendingPercent;
}

$data = [
    "labels" => $labels,
    "ratios" => $ratios, // <-- NEW: Send the ratios
    "datasets" => [
        [
            "label" => "Completed",
            "data" => $completedData,
            "backgroundColor" => "rgba(75, 192, 192, 0.2)",  // <-- MODIFIED
            "borderColor" => "rgb(75, 192, 192)",        // <-- MODIFIED
            "borderWidth" => 1
        ],
        [
            "label" => "Pending",
            "data" => $pendingData,
            "backgroundColor" => "rgba(255, 99, 132, 0.2)",   // <-- MODIFIED
            "borderColor" => "rgb(255, 99, 132)",         // <-- MODIFIED
            "borderWidth" => 1
        ]
    ]
];

header('Content-Type: application/json');
echo json_encode($data);

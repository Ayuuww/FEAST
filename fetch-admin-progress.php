<?php
session_start();
include 'conn/conn.php';

// Check if admin is logged in
if (!isset($_SESSION['idnumber'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}
$admin_id = $_SESSION['idnumber'];

// ✅ 1. Get all departments assigned to the logged-in admin
$stmt_depts = $conn->prepare("SELECT department_name FROM admin_departments WHERE admin_idnumber = ?");
$stmt_depts->bind_param("s", $admin_id);
$stmt_depts->execute();
$result_depts = $stmt_depts->get_result();

$departments = [];
while ($row = $result_depts->fetch_assoc()) {
    $departments[] = $row['department_name'];
}
$stmt_depts->close();

// If admin has no departments, return empty data to prevent errors
if (empty($departments)) {
    echo json_encode(["labels" => [], "datasets" => [], "meta" => ["done" => [], "total" => []]]);
    exit();
}

// --- Filters ---
$year = isset($_GET['year']) && $_GET['year'] !== 'All' ? $_GET['year'] : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? $_GET['semester'] : null;

// --- Build query pieces with prepared statements ---
$params = [];
$types = '';

// Subquery (for expected and completed counts)
$subquery_where_sql = '';
$subquery_conditions = [];
if ($year) {
    $subquery_conditions[] = "academic_year = ?";
    $params[] = $year;
    $types .= 's';
}
if ($semester) {
    $subquery_conditions[] = "semester = ?";
    $params[] = $semester;
    $types .= 's';
}
if (!empty($subquery_conditions)) {
    $subquery_where_sql = "WHERE " . implode(" AND ", $subquery_conditions);
}

// Main query (for department filtering)
$placeholders = implode(',', array_fill(0, count($departments), '?'));
$main_where_sql = "WHERE f.department IN ($placeholders)";
foreach ($departments as $dept) {
    $params[] = $dept; // Add departments to the end of the params list
}
$types .= str_repeat('s', count($departments));

// The subquery parameters need to be duplicated for the second subquery
$final_params = array_merge(array_slice($params, 0, strlen($types) - count($departments)), array_slice($params, 0, strlen($types) - count($departments)), array_slice($params, strlen($types) - count($departments)));
$final_types = str_repeat(substr($types, 0, strlen($types) - count($departments)), 2) . substr($types, strlen($types) - count($departments));


// ✅ 2. THE NEW, SINGLE, SECURE QUERY
$sql = "
    SELECT
        f.idnumber AS faculty_id,
        CONCAT(f.first_name, ' ', f.last_name) AS faculty_name,
        s.title AS subject_name,
        COALESCE(exp.expected_count, 0) AS expected_count,
        COALESCE(comp.completed_count, 0) AS completed_count
    FROM
        faculty f
    JOIN
        subject s ON f.idnumber = s.faculty_id
    LEFT JOIN
        (
            SELECT faculty_id, subject_code, COUNT(DISTINCT student_id) as expected_count
            FROM student_subject
            {$subquery_where_sql}
            GROUP BY faculty_id, subject_code
        ) AS exp ON f.idnumber = exp.faculty_id AND s.code = exp.subject_code
    LEFT JOIN
        (
            SELECT faculty_id, subject_code, COUNT(DISTINCT student_id) as completed_count
            FROM evaluation
            {$subquery_where_sql}
            GROUP BY faculty_id, subject_code
        ) AS comp ON f.idnumber = comp.faculty_id AND s.code = comp.subject_code
    
    {$main_where_sql}
    
    ORDER BY
        f.last_name, f.first_name, s.title
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    // Log error and send a generic error message
    error_log("SQL Prepare Error: " . $conn->error);
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'A database error occurred.']);
    exit;
}

$stmt->bind_param($final_types, ...$final_params);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$completed = [];
$notCompleted = [];
$doneCounts = [];
$totalCounts = [];

while ($row = $result->fetch_assoc()) {
    $expected = (int) $row['expected_count'];
    $done = (int) $row['completed_count'];

    $completedPercent = ($expected > 0) ? round(($done / $expected) * 100, 2) : 0;
    $notCompletedPercent = 100 - $completedPercent;

    $labels[] = $row['faculty_name'] . " — " . $row['subject_name'];
    $completed[] = $completedPercent;
    $notCompleted[] = $notCompletedPercent;
    $doneCounts[] = $done;
    $totalCounts[] = $expected;
}
$stmt->close();

// --- Return chart data ---
$data = [
    "labels" => $labels,
    "datasets" => [
        [
            "label" => "Completed (%)",
            "data" => $completed,
            "backgroundColor" => "rgba(75, 192, 192, 0.7)",
            "borderColor" => "rgba(75, 192, 192, 1)",
            "borderWidth" => 1
        ],
        [
            "label" => "Not Completed (%)",
            "data" => $notCompleted,
            "backgroundColor" => "rgba(255, 99, 132, 0.7)",
            "borderColor" => "rgba(255, 99, 132, 1)",
            "borderWidth" => 1
        ]
    ],
    "meta" => [
        "done" => $doneCounts,
        "total" => $totalCounts
    ]
];

header('Content-Type: application/json');
echo json_encode($data);
?>
<?php
session_start();
include 'conn/conn.php';

// --- Filters ---
$year = isset($_GET['year']) && $_GET['year'] !== 'All' ? $conn->real_escape_string($_GET['year']) : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? $conn->real_escape_string($_GET['semester']) : null;
$department = isset($_SESSION['department']) ? $conn->real_escape_string($_SESSION['department']) : null;

// --- Build WHERE clauses for subqueries ---
$subquery_conditions = [];
if ($year) {
    $subquery_conditions[] = "academic_year = '{$year}'";
}
if ($semester) {
    $subquery_conditions[] = "semester = '{$semester}'";
}
$subquery_where_clause = !empty($subquery_conditions) ? "WHERE " . implode(" AND ", $subquery_conditions) : "";

// --- Build main query conditions ---
$main_conditions = [];
if ($department) {
    $main_conditions[] = "f.department = '{$department}'";
}
$main_where_clause = !empty($main_conditions) ? "WHERE " . implode(" AND ", $main_conditions) : "";


// --- THE NEW, SINGLE-QUERY ---
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
            -- Subquery to get expected students, WITH FILTERS
            SELECT faculty_id, subject_code, COUNT(DISTINCT student_id) as expected_count
            FROM student_subject
            {$subquery_where_clause}
            GROUP BY faculty_id, subject_code
        ) AS exp ON f.idnumber = exp.faculty_id AND s.code = exp.subject_code
    LEFT JOIN
        (
            -- Subquery to get completed evaluations, WITH FILTERS
            SELECT faculty_id, subject_code, COUNT(DISTINCT student_id) as completed_count
            FROM evaluation
            {$subquery_where_clause}
            GROUP BY faculty_id, subject_code
        ) AS comp ON f.idnumber = comp.faculty_id AND s.code = comp.subject_code
    
    {$main_where_clause}
    
    ORDER BY
        f.last_name, f.first_name, s.title
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    echo json_encode(['error' => mysqli_error($conn), 'sql' => $sql]);
    exit;
}

$labels = [];
$completed = [];
$notCompleted = [];
$doneCounts = [];
$totalCounts = [];

while ($row = mysqli_fetch_assoc($result)) {
    $expected = (int) $row['expected_count'];
    $done = (int) $row['completed_count'];

    // This logic is now consistent
    $completedPercent = ($expected > 0) ? round(($done / $expected) * 100, 2) : 0;
    $notCompletedPercent = 100 - $completedPercent;

    $labels[] = $row['faculty_name'] . " — " . $row['subject_name'];
    $completed[] = $completedPercent;
    $notCompleted[] = $notCompletedPercent;
    $doneCounts[] = $done;
    $totalCounts[] = $expected;
}

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

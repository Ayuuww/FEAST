<?php
header('Content-Type: application/json; charset=utf-8');
include 'conn/conn.php';

// Sanitize user inputs
$year     = isset($_GET['year']) && $_GET['year'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['year']) : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['semester']) : null; // <-- ADDED BACK
$dept     = isset($_GET['dept']) && $_GET['dept'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['dept']) : null;

// --- Build the WHERE clause for the subqueries ---
$subquery_conditions = [];
if ($year) {
    $subquery_conditions[] = "academic_year = '{$year}'";
}
if ($semester) { // <-- ADDED BACK
    $subquery_conditions[] = "semester = '{$semester}'";
}
$subquery_where_clause = !empty($subquery_conditions) ? "WHERE " . implode(" AND ", $subquery_conditions) : "";
// --- End of new part ---

// THE FINAL, CORRECTED SQL QUERY
$sql = "
    SELECT
        f.idnumber AS faculty_id,
        CONCAT(f.first_name, ' ', f.last_name) AS faculty_name,
        s.title AS subject_name,
        exp.expected_count,
        COALESCE(comp.completed_count, 0) AS completed_count
    FROM
        (
            -- Subquery to get expected students, WITH FILTERS APPLIED
            SELECT faculty_id, subject_code, COUNT(DISTINCT student_id) as expected_count
            FROM student_subject
            {$subquery_where_clause}
            GROUP BY faculty_id, subject_code
        ) AS exp
    LEFT JOIN
        (
            -- Subquery to get completed evaluations, WITH THE SAME FILTERS APPLIED
            SELECT faculty_id, subject_code, COUNT(DISTINCT student_id) as completed_count
            FROM evaluation
            {$subquery_where_clause}
            GROUP BY faculty_id, subject_code
        ) AS comp ON exp.faculty_id = comp.faculty_id
                    AND exp.subject_code = comp.subject_code
    JOIN
        faculty f ON exp.faculty_id = f.idnumber
    JOIN
        subject s ON exp.subject_code = s.code
";

// The college filter is applied on the main query
$main_conditions = [];
if ($dept) {
    $main_conditions[] = "f.college = '{$dept}'";
}
if (!empty($main_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $main_conditions);
}

$sql .= " ORDER BY f.last_name, f.first_name, s.title";

$result = mysqli_query($conn, $sql);
if (!$result) {
    echo json_encode(['error' => mysqli_error($conn), 'sql' => $sql]);
    exit;
}

$labels = [];
$completed_percent = [];
$pending_percent = [];
$ratios = [];

while ($row = mysqli_fetch_assoc($result)) {
    $expected = (int) $row['expected_count'];
    $completed = (int) $row['completed_count'];

    $progress = ($expected > 0) ? round(($completed / $expected) * 100, 2) : 0;

    $labels[] = $row['faculty_name'] . " — " . $row['subject_name'];
    $completed_percent[] = $progress;
    $pending_percent[] = 100 - $progress;
    $ratios[] = "{$completed}/{$expected}";
}

// Prepare the JSON response for Chart.js
echo json_encode([
    "labels" => $labels,
    "ratios" => $ratios,
    "datasets" => [
        [
            "label" => "Completed",
            "data" => $completed_percent,
            "backgroundColor" => "rgba(75, 192, 192, 0.2)",  // <-- MODIFIED
            "borderColor" => "rgb(75, 192, 192)",        // <-- MODIFIED
            "borderWidth" => 1
        ],
        [
            "label" => "Pending",
            "data" => $pending_percent,
            "backgroundColor" => "rgba(255, 99, 132, 0.2)",   // <-- MODIFIED
            "borderColor" => "rgb(255, 99, 132)",         // <-- MODIFIED
            "borderWidth" => 1
        ]
    ]
]);

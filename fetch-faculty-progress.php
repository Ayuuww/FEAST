<?php
session_start();
include 'conn/conn.php';

// Ensure user is logged in
if (!isset($_SESSION['idnumber'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$faculty_id = $conn->real_escape_string($_SESSION['idnumber']); // faculty login

// --- START: Filter Logic (handles "All") ---
$year = isset($_GET['year']) && $_GET['year'] !== 'All' ? $conn->real_escape_string($_GET['year']) : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? $conn->real_escape_string($_GET['semester']) : null;

// Build the WHERE clause for the subqueries
$conditions = [];
$conditions[] = "faculty_id = '{$faculty_id}'"; // Always filter by the logged-in user

if ($year) {
    $conditions[] = "academic_year = '{$year}'";
}
if ($semester) {
    $conditions[] = "semester = '{$semester}'";
}

$subquery_where_clause = "WHERE " . implode(" AND ", $conditions);
// --- END: Filter Logic ---


$labels = [];
$completed = [];
$pending = [];
$doneCounts = [];
$totalCounts = [];

// --- THE NEW, SINGLE-QUERY (Fixes N+1 problem) ---
// This query gets all subjects, joins their total student count,
// and joins their completed evaluation count, all based on the filters.
$sql = "
    SELECT
        s.title,
        COALESCE(exp.total, 0) AS total_students,
        COALESCE(comp.done, 0) AS done_students
    FROM
        subject s
    LEFT JOIN
        (
            -- Subquery to get total expected students, with FILTERS
            SELECT subject_code, COUNT(DISTINCT student_id) AS total
            FROM student_subject
            {$subquery_where_clause}
            GROUP BY subject_code
        ) AS exp ON s.code = exp.subject_code
    LEFT JOIN
        (
            -- Subquery to get total completed evaluations, with FILTERS
            SELECT subject_code, COUNT(DISTINCT student_id) AS done
            FROM evaluation
            {$subquery_where_clause}
            GROUP BY subject_code
        ) AS comp ON s.code = comp.subject_code
    
    -- This JOIN ensures we only show subjects that match the filters
    -- (i.e., subjects taught by this faculty IN THE SELECTED TERM)
    JOIN (
        SELECT DISTINCT subject_code
        FROM student_subject
        {$subquery_where_clause}
    ) AS term_subjects ON s.code = term_subjects.subject_code
    
    WHERE
        s.faculty_id = '{$faculty_id}' -- Main WHERE for the subject table
    ORDER BY
        s.title ASC
";

$result = $conn->query($sql);

if (!$result) {
    // Handle SQL error
    header('Content-Type: application/json');
    echo json_encode(['error' => $conn->error, 'sql' => $sql]);
    exit;
}

// Loop over the single result set
while ($row = $result->fetch_assoc()) {
    $total = (int) $row['total_students'];
    $done = (int) $row['done_students'];

    // Only add if there are students enrolled in that term
    if ($total > 0) {
        $progress = round(($done / $total) * 100, 2);
        $notProgress = 100 - $progress;

        $labels[] = $row['title'];
        $completed[] = $progress;
        $pending[] = $notProgress;
        $doneCounts[] = $done;
        $totalCounts[] = $total;
    }
}


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
            "label" => "Pending (%)",
            "data" => $pending,
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
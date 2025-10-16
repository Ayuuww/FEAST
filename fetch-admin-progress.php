<?php
session_start();
include 'conn/conn.php';

// Debugging (optional - comment out in production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// ✅ Get current evaluation setting (fallback-safe)
$setting_sql = "SELECT semester, academic_year FROM evaluation_settings ORDER BY updated_at DESC LIMIT 1";
$setting_result = $conn->query($setting_sql);
if ($setting_result && $setting_result->num_rows > 0) {
    $setting = $setting_result->fetch_assoc();
    $current_semester = $setting['semester'];
    $current_acad_year = $setting['academic_year'];
} else {
    $current_semester = null;
    $current_acad_year = null;
}

// ✅ Filters (default = All)
$year = isset($_GET['year']) && $_GET['year'] !== 'All' ? $_GET['year'] : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? $_GET['semester'] : null;
$department = isset($_SESSION['department']) ? $_SESSION['department'] : null;

$labels = [];
$completed = [];
$notCompleted = [];
$doneCounts = [];
$totalCounts = [];

// ✅ Build faculty + subject list
$sql = "
    SELECT f.idnumber AS faculty_id,
           CONCAT(f.first_name, ' ', f.last_name) AS faculty_name,
           s.code AS subject_code,
           s.title AS subject_name
    FROM faculty f
    JOIN subject s ON s.faculty_id = f.idnumber
";

if ($department) {
    $sql .= " WHERE f.department = ?";
}

$sql .= " ORDER BY f.last_name, f.first_name, s.title";

$stmt = $conn->prepare($sql);
if ($department) {
    $stmt->bind_param("s", $department);
}
$stmt->execute();
$result = $stmt->get_result();

// ✅ Loop over faculty + subjects
while ($row = $result->fetch_assoc()) {
    $faculty_id = $row['faculty_id'];
    $subject_code = $row['subject_code'];

    // ✅ Expected students per subject
    $exp_sql = "
        SELECT COUNT(DISTINCT ss.student_id) AS total
        FROM student_subject ss
        WHERE ss.subject_code = ?
          AND ss.faculty_id = ?
    ";

    $params = [$subject_code, $faculty_id];
    $types = "ss";

    if ($year) {
        $exp_sql .= " AND ss.academic_year = ?";
        $params[] = $year;
        $types .= "s";
    }
    if ($semester) {
        $exp_sql .= " AND ss.semester = ?";
        $params[] = $semester;
        $types .= "s";
    }

    $exp_stmt = $conn->prepare($exp_sql);
    $exp_stmt->bind_param($types, ...$params);
    $exp_stmt->execute();
    $expected = (int)($exp_stmt->get_result()->fetch_assoc()['total'] ?? 0);
    $exp_stmt->close();

    // ✅ Completed evaluations
    $done_sql = "
        SELECT COUNT(DISTINCT e.student_id) AS done
        FROM evaluation e
        WHERE e.subject_code = ?
          AND e.faculty_id = ?
    ";

    $params = [$subject_code, $faculty_id];
    $types = "ss";

    if ($year) {
        $done_sql .= " AND e.academic_year = ?";
        $params[] = $year;
        $types .= "s";
    }
    if ($semester) {
        $done_sql .= " AND e.semester = ?";
        $params[] = $semester;
        $types .= "s";
    }

    $done_stmt = $conn->prepare($done_sql);
    $done_stmt->bind_param($types, ...$params);
    $done_stmt->execute();
    $done = (int)($done_stmt->get_result()->fetch_assoc()['done'] ?? 0);
    $done_stmt->close();

    // ✅ Compute percentages
    $completedPercent = $expected > 0 ? round(($done / $expected) * 100, 2) : 0;
    $notCompletedPercent = 100 - $completedPercent;

    $labels[] = $row['faculty_name'] . " — " . $row['subject_name'];
    $completed[] = $completedPercent;
    $notCompleted[] = $notCompletedPercent;
    $doneCounts[] = $done;
    $totalCounts[] = $expected;
}

// ✅ Return chart data (safe output)
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

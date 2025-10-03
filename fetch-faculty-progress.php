<?php
session_start();
include 'conn/conn.php';

$faculty_id = $_SESSION['idnumber']; // assuming faculty login stores their ID

$labels = [];
$completed = [];
$pending = [];
$doneCounts = [];
$totalCounts = [];

// Get all subjects handled by this faculty
$subjectQuery = $conn->query("
    SELECT s.code, s.title
    FROM subject s
    WHERE s.faculty_id = '$faculty_id'
    ORDER BY s.title ASC
");

while ($subject = $subjectQuery->fetch_assoc()) {
    $subject_code = $subject['code'];

    // Total students assigned to this subject
    $totalQuery = $conn->query("
      SELECT COUNT(DISTINCT ss.student_id) AS total
      FROM student_subject ss
      WHERE ss.faculty_id = '$faculty_id'
        AND ss.subject_code = '$subject_code'
  ");
    $total = $totalQuery->fetch_assoc()['total'] ?? 0;

    // Students who have completed evaluation for this subject
    $doneQuery = $conn->query("
      SELECT COUNT(DISTINCT e.student_id) AS done
      FROM evaluation e
      WHERE e.faculty_id = '$faculty_id'
        AND e.subject_code = '$subject_code'
  ");
    $done = $doneQuery->fetch_assoc()['done'] ?? 0;

    // Progress
    $progress = $total > 0 ? round(($done / $total) * 100, 2) : 0;
    $notProgress = 100 - $progress;

    $labels[] = $subject['title'];
    $completed[] = $progress;
    $pending[] = $notProgress;
    $doneCounts[] = $done;
    $totalCounts[] = $total;
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

<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Get college filter (if any)
$college = isset($_GET['college']) ? $_GET['college'] : 'All';

// Base query for active faculty
if ($college === 'All') {
  $faculty_query = "SELECT COUNT(*) AS total_faculty FROM faculty WHERE role = 'faculty' AND status = 'active'";
} else {
  $faculty_query = "
    SELECT COUNT(DISTINCT f.idnumber) AS total_faculty
    FROM faculty f
    INNER JOIN student_subject ss ON ss.faculty_id = f.idnumber
    WHERE f.role = 'faculty' AND f.status = 'active' 
    AND ss.college = '" . mysqli_real_escape_string($conn, $college) . "'
  ";
}
$faulty_result = mysqli_query($conn, $faculty_query);
$totalfaculty = mysqli_fetch_assoc($faulty_result)['total_faculty'] ?? 0;

// Completed evaluations (student → faculty)
if ($college === 'All') {
  $completedQuery = "SELECT COUNT(DISTINCT faculty_id) AS completed FROM evaluation";
} else {
  $completedQuery = "
    SELECT COUNT(DISTINCT e.faculty_id) AS completed
    FROM evaluation e
    INNER JOIN student_subject ss ON ss.faculty_id = e.faculty_id
    WHERE ss.college = '" . mysqli_real_escape_string($conn, $college) . "'
  ";
}
$completedResult = mysqli_query($conn, $completedQuery);
$completed = mysqli_fetch_assoc($completedResult)['completed'] ?? 0;

// Pending evaluations
$pending = max(0, $totalfaculty - $completed);

// Return data as JSON
$data = [
  "labels" => ["Completed", "Pending"],
  "datasets" => [[
    "label" => "Faculty Evaluations",
    "data" => [$completed, $pending],
    "backgroundColor" => ["#28a745", "#ffc107"]
  ]]
];

header('Content-Type: application/json');
echo json_encode($data);
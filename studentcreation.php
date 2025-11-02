<?php
session_start();
include 'conn/conn.php';

// Sanitize inputs
$idnumber   = mysqli_real_escape_string($conn, $_POST['idnumber']);
$first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
$mid_name   = mysqli_real_escape_string($conn, $_POST['mid_name']);
$last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
$password   = trim($_POST['password']); // don’t escape before hashing
$department = mysqli_real_escape_string($conn, $_POST['department']);
$program    = mysqli_real_escape_string($conn, $_POST['program']); // ✅ ADDED
$section    = mysqli_real_escape_string($conn, $_POST['section']);

// ✅ Hash password before saving
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Check if student with same ID already exists
$check = $conn->prepare("SELECT COUNT(*) FROM student WHERE idnumber = ?");
$check->bind_param("s", $idnumber);
$check->execute();
$check->bind_result($exists);
$check->fetch();
$check->close();

if ($exists > 0) {
    $_SESSION['msg'] = "ID number already exists. Please enter a different one.";
    $_SESSION['msg_type'] = "warning";
    header("Location: superadmin-studentcreation.php");
    exit();
}

// Insert new student
// ✅ MODIFIED: Added `program` column and one `?`
$stmt = $conn->prepare("INSERT INTO student (idnumber, first_name, mid_name, last_name, password, department, program, section) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

// ✅ MODIFIED: Added `$program` and changed type string to "ssssssss"
$stmt->bind_param("ssssssss", $idnumber, $first_name, $mid_name, $last_name, $hashed, $department, $program, $section);

if ($stmt->execute()) {
    $_SESSION['msg'] = "Student account has been created successfully.";
    $_SESSION['msg_type'] = "success";
} else {
    $_SESSION['msg'] = "Failed to create student account.";
    $_SESSION['msg_type'] = "danger";
}
$stmt->close();

header("Location: superadmin-studentcreation.php");
exit();

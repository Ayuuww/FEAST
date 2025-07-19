<?php
session_start();
include 'conn/conn.php';

// Sanitize inputs
$idnumber   = mysqli_real_escape_string($conn, $_POST['idnumber']);
$first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
$mid_name   = mysqli_real_escape_string($conn, $_POST['mid_name']);
$last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
$password   = mysqli_real_escape_string($conn, $_POST['password']);
$department = mysqli_real_escape_string($conn, $_POST['department']);
$section    = mysqli_real_escape_string($conn, $_POST['section']);

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
$stmt = $conn->prepare("INSERT INTO student (idnumber, first_name, mid_name, last_name, password, department, section) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $idnumber, $first_name, $mid_name, $last_name, $password, $department, $section);

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

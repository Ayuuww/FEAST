<?php
session_start();
include 'conn/conn.php';

// Sanitize inputs
$idnumber   = mysqli_real_escape_string($conn, $_POST['idnumber']);
$first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
$mid_name   = mysqli_real_escape_string($conn, $_POST['mid_name']);
$last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
$password   = trim($_POST['password']);
$rank       = mysqli_real_escape_string($conn, $_POST['faculty_rank']);
$department = mysqli_real_escape_string($conn, $_POST['department']);
$program    = mysqli_real_escape_string($conn, $_POST['program']);

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check duplicate
$check = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE idnumber = ?");
$check->bind_param("s", $idnumber);
$check->execute();
$check->bind_result($exists);
$check->fetch();
$check->close();

if ($exists > 0) {
    $_SESSION['msg'] = "ID number already exists. Please enter a different one.";
    $_SESSION['msg_type'] = "warning";
    header("Location: register-facultycreation.php");
    exit();
}

// Insert
$stmt = $conn->prepare("
  INSERT INTO faculty 
  (idnumber, first_name, mid_name, last_name, password, faculty_rank, department, program)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssssss", $idnumber, $first_name, $mid_name, $last_name, $hashed_password, $rank, $department, $program);

if ($stmt->execute()) {
    $_SESSION['msg'] = "Faculty account has been created successfully.";
    $_SESSION['msg_type'] = "success";
} else {
    $_SESSION['msg'] = "Failed to create faculty account.";
    $_SESSION['msg_type'] = "danger";
}

$stmt->close();
header("Location: register-facultycreation.php");
exit();

<?php
session_start();
include 'conn/conn.php'; // Connection to the database

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

// Get admin's department
$admin_id = $_SESSION['idnumber'];
$dept_query = mysqli_query($conn, "SELECT department FROM admin WHERE idnumber = '$admin_id' LIMIT 1");
$admin_dept = '';

if ($dept_query && mysqli_num_rows($dept_query) > 0) {
  $admin_data = mysqli_fetch_assoc($dept_query);
  $admin_dept = $admin_data['department'];
}

if (isset($_POST['addsubject'])) {
  $subject_code   = $_POST['code'];
  $subject_title  = $_POST['title'];
  $faculty_id     = $_POST['faculty_id'] ?? null;
  $admin_id       = $_POST['admin_id'] ?? null;

  // Validate: Only one should be filled
  if ($faculty_id && $admin_id) {
    $_SESSION['msg'] = 'Select either Faculty or Admin, not both.';
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-subjectadding.php");
    exit;
  }

  if ($faculty_id) {
    $stmt = $conn->prepare("INSERT INTO subject (code, title, faculty_id, department) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $subject_code, $subject_title, $faculty_id, $admin_dept);
  } elseif ($admin_id) {
    $stmt = $conn->prepare("INSERT INTO subject (code, title, admin_id, department) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $subject_code, $subject_title, $admin_id, $admin_dept);
  } else {
    $_SESSION['msg'] = 'Please select a faculty or admin.';
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-subjectadding.php");
    exit;
  }

  if ($stmt->execute()) {
    $_SESSION['msg'] = 'Subject added successfully!';
    $_SESSION['msg_type'] = 'success';
  } else {
    $_SESSION['msg'] = 'Error adding subject!';
    $_SESSION['msg_type'] = 'danger';
  }

  header("Location: admin-subjectadding.php");
  exit;
}

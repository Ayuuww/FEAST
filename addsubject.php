<?php
session_start();
include 'conn/conn.php'; // Connection to the database

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

// Get admin's department
$admin_id = $_SESSION['idnumber'];
$admin_dept = '';
$dept_query = $conn->prepare("SELECT department_name FROM admin_departments WHERE admin_idnumber = ? LIMIT 1");
$dept_query->bind_param("s", $admin_id);
$dept_query->execute();
$result = $dept_query->get_result();
if ($row = $result->fetch_assoc()) {
  $admin_dept = $row['department_name'];
}
$dept_query->close();

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

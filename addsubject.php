<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

// ✅ 1. Get the ID of the admin who is adding the subject
$assigning_admin_id = $_SESSION['idnumber'];

// Check if the form was submitted
if (isset($_POST['addsubject'])) {

  // Get all form data
  $subject_code   = $_POST['code'] ?? null;
  $subject_title  = $_POST['title'] ?? null;
  $faculty_id     = $_POST['faculty_id'] ?? null;
  $college     = $_POST['college'] ?? null;
  $program        = $_POST['program'] ?? null;

  // --- Validation ---
  if (empty($subject_code) || empty($subject_title)) {
    $_SESSION['msg'] = 'Subject Code and Title are required.';
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-subjectadding.php");
    exit;
  }
  if (empty($faculty_id)) {
    $_SESSION['msg'] = 'Please select a faculty to assign the subject to.';
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-subjectadding.php");
    exit;
  }
  if (empty($college) || empty($program)) {
    $_SESSION['msg'] = 'Please select the College and Program for this subject.';
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-subjectadding.php");
    exit;
  }
  // --- End Validation ---


  // ✅ 2. Prepare the new INSERT statement with 'admin_id'
  $stmt = $conn->prepare("
        INSERT INTO subject (code, title, faculty_id, college, program, admin_id) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

  // ✅ 3. Bind all 6 parameters (changed "sssss" to "ssssss")
  $stmt->bind_param(
    "ssssss",
    $subject_code,
    $subject_title,
    $faculty_id,
    $college,
    $program,
    $assigning_admin_id  // Add the admin's ID here
  );

  if ($stmt->execute()) {
    $_SESSION['msg'] = 'Subject added successfully!';
    $_SESSION['msg_type'] = 'success';
  } else {
    // Provide more detailed error for debugging
    $_SESSION['msg'] = 'Error adding subject: ' . $stmt->error;
    $_SESSION['msg_type'] = 'danger';
  }

  header("Location: admin-subjectadding.php");
  exit;
}

<?php 
session_start();
include 'conn/conn.php';// Connection to the database

if (isset($_POST['addsubject'])) {
    $subject_code   = $_POST['code'];
    $subject_title  = $_POST['title'];
    $faculty_id = $_POST['faculty_id'] ?? null;
    $admin_id = $_POST['admin_id'] ?? null;

    // Validate: Only one of them should be filled
    if ($faculty_id && $admin_id) {
        $_SESSION['msg'] = 'Select either Faculty or Admin, not both.';
        header("Location: superadmin-subjectadding.php");
        exit;
    }

    // Determine which column to insert
    if ($faculty_id) {
        $stmt = $conn->prepare("INSERT INTO subject (code, title, faculty_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $subject_code, $subject_title, $faculty_id);
    } elseif ($admin_id) {
        $stmt = $conn->prepare("INSERT INTO subject (code, title, admin_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $subject_code, $subject_title, $admin_id);
    } else {
        $_SESSION['msg'] = 'Please select a faculty or admin.';
        header("Location: superadmin-subjectadding.php");
        exit;
    }

    // Execute
    if ($stmt->execute()) {
        $_SESSION['msg'] = 'Subject added successfully!';
    } else {
        $_SESSION['msg'] = 'Error Adding Subject!';
    }
    header("Location: superadmin-subjectadding.php");
    exit;

}

?>

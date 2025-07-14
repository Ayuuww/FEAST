<?php
session_start();
include 'conn/conn.php';

$_SESSION['msg'] = "Subject assigned successfully!";
$_SESSION['msg_type'] = "success"; // or 'danger', 'warning', 'info'


if (isset($_POST['assign'])) {
    $student_id   = $_POST['student_id'];
    $subject_code = $_POST['subject_code'];
    $faculty_id   = $_POST['faculty_id'] ?? null;
    $admin_id     = $_POST['admin_id'] ?? null;

    // Check: only one must be set
    if (!$faculty_id && !$admin_id) {
        $_SESSION['msg'] = "Instructor ID not set. Please re-select the subject.";
        header("Location: superadmin-studentsubject.php");
        exit;
    }

    // Check for duplicate
    $dup = mysqli_query($conn, "SELECT * FROM student_subject 
                                WHERE student_id = '$student_id' 
                                AND subject_code = '$subject_code'");
    if (mysqli_num_rows($dup) > 0) {
        $_SESSION['msg'] = "Subject already assigned to this student.";
    } else {
        // Dynamic query based on which ID is used
        if ($faculty_id) {
            $insert = "INSERT INTO student_subject (student_id, subject_code, faculty_id) 
                    VALUES ('$student_id', '$subject_code', '$faculty_id')";
        } else {
            $insert = "INSERT INTO student_subject (student_id, subject_code, admin_id) 
                    VALUES ('$student_id', '$subject_code', '$admin_id')";
        }

        if (mysqli_query($conn, $insert)) {
            $_SESSION['msg'] = "Subject successfully assigned.";
        } else {
            $_SESSION['msg'] = "Error: " . mysqli_error($conn);
        }
    }
}

header("Location: superadmin-studentsubject.php");
exit;

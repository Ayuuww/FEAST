<?php

session_start();
include 'conn/conn.php';// Connection to the database


if (isset($_POST['assign'])) {
    $student_id     = $_POST['student_id'];
    $subject_code   = $_POST['subject_code'];

    // Count current subjects
    $check = "SELECT COUNT(*) as count FROM student_subject WHERE student_id = '$student_id'";
    $check_result   = mysqli_query($conn, $check);
    $data = mysqli_fetch_assoc($check_result);
    $current_count  = $data['count'];

    $max_subjects = 9; // Maximum subjects allowed

    if ($current_count  >= $max_subjects) {
        $_SESSION['msg'] = "Student already has maximum subjects.";
    } else {
        // Check for duplicate assignment
        $dup = mysqli_query($conn, "SELECT * FROM student_subject WHERE student_id = '$student_id' AND subject_code = '$subject_code'");
        if (mysqli_num_rows($dup) > 0) {
            $_SESSION['msg'] = "Subject already assigned to this student.";
        } else {
            // Assign the subject
            $insert = "INSERT INTO student_subject (student_id, subject_code) VALUES ('$student_id', '$subject_code')";
            if (mysqli_query($conn, $insert)) {
                $_SESSION['msg'] = "Subject successfully assigned.";
            } else {
                $_SESSION['msg'] = "Error: " . mysqli_error($conn);
            }
        }
    }

    header("Location: superadmin-studentsubject.php");
    exit;
}
?>
<?php
session_start();
include 'conn/conn.php';

if (isset($_POST['assign'])) {
    $student_id   = $_POST['student_id'];
    $subject_code = $_POST['subject_code'];
    $faculty_id   = $_POST['faculty_id']; // Coming from hidden input (set by JS)

    // Optional: validate if subject_code/faculty_id are present
    if (empty($faculty_id)) {
        $_SESSION['msg'] = "Faculty ID not set. Please re-select the subject.";
        header("Location: superadmin-studentsubject.php");
        exit;
    }

    // Check for duplicate assignment
    $dup = mysqli_query($conn, "SELECT * FROM student_subject 
                                WHERE student_id = '$student_id' 
                                  AND subject_code = '$subject_code'");
    if (mysqli_num_rows($dup) > 0) {
        $_SESSION['msg'] = "Subject already assigned to this student.";
    } else {
        // Insert with correct faculty_id
        $insert = "INSERT INTO student_subject (student_id, subject_code, faculty_id) 
                   VALUES ('$student_id', '$subject_code', '$faculty_id')";

        if (mysqli_query($conn, $insert)) {
            $_SESSION['msg'] = "Subject successfully assigned.";
        } else {
            $_SESSION['msg'] = "Error: " . mysqli_error($conn);
        }
    }
}

header("Location: superadmin-studentsubject.php");
exit;

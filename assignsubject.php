<?php
session_start();
include 'conn/conn.php';

$_SESSION['msg'] = "Subject assigned successfully!";
$_SESSION['msg_type'] = "success"; // or 'danger', 'warning', 'info'


if (isset($_POST['assign'])) {
    $student_ids   = $_POST['student_id'];   // now an array
    $subject_codes = $_POST['subject_code']; // now an array

    foreach ($student_ids as $student_id) {
        foreach ($subject_codes as $subject_code) {
            // Get faculty/admin ID based on subject
            $stmt = $conn->prepare("SELECT faculty_id, admin_id FROM subject WHERE code = ?");
            $stmt->bind_param("s", $subject_code);
            $stmt->execute();
            $result = $stmt->get_result();
            $subject_data = $result->fetch_assoc();
            $stmt->close();

            $faculty_id = $subject_data['faculty_id'] ?? null;
            $admin_id   = $subject_data['admin_id'] ?? null;

            if (!$faculty_id && !$admin_id) {
                $_SESSION['msg'] = "Instructor ID missing for subject $subject_code.";
                $_SESSION['msg_type'] = "danger";
                header("Location: admin-studentsubject.php");
                exit;
            }

            // Check for duplicate
            $check = $conn->prepare("SELECT * FROM student_subject WHERE student_id = ? AND subject_code = ?");
            $check->bind_param("ss", $student_id, $subject_code);
            $check->execute();
            $check_result = $check->get_result();

            if ($check_result->num_rows === 0) {
                if ($faculty_id) {
                    $insert = $conn->prepare("INSERT INTO student_subject (student_id, subject_code, faculty_id) VALUES (?, ?, ?)");
                    $insert->bind_param("sss", $student_id, $subject_code, $faculty_id);
                } else {
                    $insert = $conn->prepare("INSERT INTO student_subject (student_id, subject_code, admin_id) VALUES (?, ?, ?)");
                    $insert->bind_param("sss", $student_id, $subject_code, $admin_id);
                }

                if ($insert->execute()) {
                    $_SESSION['msg'] = "Subjects successfully assigned.";
                    $_SESSION['msg_type'] = "success";
                } else {
                    $_SESSION['msg'] = "DB Insert Error: " . $conn->error;
                    $_SESSION['msg_type'] = "danger";
                }

                $insert->close();
            } else {
                $_SESSION['msg'] = "Subject $subject_code already assigned to student $student_id.";
                $_SESSION['msg_type'] = "warning";
            }

            $check->close();
        }
    }

    $conn->close();
    header("Location: admin-studentsubject.php");
    exit;
}

header("Location: admin-studentsubject.php");
exit;

?>
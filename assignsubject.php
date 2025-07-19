<?php
session_start();
include 'conn/conn.php';

if (isset($_POST['assign'])) {
    $student_ids   = $_POST['student_id'];   // now an array
    $subject_codes = $_POST['subject_code']; // now an array

    $success = 0;
    $errors = [];

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
                $errors[] = "❌ Subject $subject_code has no assigned instructor.";
                continue;
            }

            // Check if already assigned
            $check = $conn->prepare("SELECT 1 FROM student_subject WHERE student_id = ? AND subject_code = ?");
            $check->bind_param("ss", $student_id, $subject_code);
            $check->execute();
            $check_result = $check->get_result();

            if ($check_result->num_rows > 0) {
                $errors[] = "⚠️ Student $student_id already assigned to subject $subject_code.";
                $check->close();
                continue;
            }
            $check->close();

            // Validate faculty_id if exists
            if ($faculty_id) {
                $faculty_check = $conn->prepare("SELECT 1 FROM faculty WHERE idnumber = ?");
                $faculty_check->bind_param("s", $faculty_id);
                $faculty_check->execute();
                $faculty_check->store_result();

                if ($faculty_check->num_rows === 0) {
                    $errors[] = "❌ Faculty ID $faculty_id (for subject $subject_code) not found.";
                    $faculty_check->close();
                    continue;
                }
                $faculty_check->close();

                $insert = $conn->prepare("INSERT INTO student_subject (student_id, subject_code, faculty_id) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $student_id, $subject_code, $faculty_id);
            } else {
                $insert = $conn->prepare("INSERT INTO student_subject (student_id, subject_code, admin_id) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $student_id, $subject_code, $admin_id);
            }

            if ($insert->execute()) {
                $success++;
            } else {
                $errors[] = "❌ Failed to assign $subject_code to $student_id.";
            }

            $insert->close();
        }
    }

    // Set session message
    if ($success > 0 && count($errors) > 0) {
        $_SESSION['msg_type'] = 'warning';
        $_SESSION['msg'] = "$success subject(s) assigned. Some were skipped.";
        $_SESSION['detailed_errors'] = $errors;
    } elseif ($success > 0) {
        $_SESSION['msg_type'] = 'success';
        $_SESSION['msg'] = "$success subject(s) successfully assigned.";
        $_SESSION['detailed_errors'] = [];
    } else {
        $_SESSION['msg_type'] = 'error';
        $_SESSION['msg'] = "No subjects were assigned.";
        $_SESSION['detailed_errors'] = $errors;
    }

    $conn->close();
    header("Location: admin-studentsubject.php");
    exit;
}

header("Location: admin-studentsubject.php");
exit;

?>
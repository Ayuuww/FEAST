<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    header("Location: pages-login.php");
    exit();
}

if (isset($_POST['idnumber'])) {

    $id = $_POST['idnumber'];

    // Check if subject exists
    $check_stmt = $conn->prepare("SELECT idnumber FROM subject WHERE idnumber = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows == 0) {
        $_SESSION['msg'] = "Subject does not exist!";
        $_SESSION['msg_type'] = "error";
        header("Location: admin-subjectlist.php");
        exit();
    }

    // Now delete
    $delete_stmt = $conn->prepare("DELETE FROM subject WHERE idnumber = ?");
    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        $_SESSION['msg'] = "Subject deleted successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Error deleting subject!";
        $_SESSION['msg_type'] = "error";
    }

    header("Location: admin-subjectlist.php");
    exit();
}

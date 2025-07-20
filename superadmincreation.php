<?php
session_start();
include 'conn/conn.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and trim inputs
    $id         = mysqli_real_escape_string($conn, trim($_POST['idnumber']));
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $mid_name   = mysqli_real_escape_string($conn, trim($_POST['mid_name']));
    $last_name  = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $password   = mysqli_real_escape_string($conn, trim($_POST['password']));

    // Check for duplicate ID
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM superadmin WHERE idnumber = ?");
    $check_stmt->bind_param("s", $id);
    $check_stmt->execute();
    $check_stmt->bind_result($exists);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($exists > 0) {
        $_SESSION['msg'] = 'Super Admin with this ID already exists!';
        $_SESSION['msg_type'] = 'warning';
        header("Location: superadmin-superadmincreation.php");
        exit();
    }

    // Hash the password before storing
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new superadmin
    $insert_stmt = $conn->prepare("INSERT INTO superadmin (idnumber, first_name, mid_name, last_name, password) VALUES (?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("sssss", $id, $first_name, $mid_name, $last_name, $hashed_password);

    if ($insert_stmt->execute()) {
        $_SESSION['msg'] = 'Super Admin account successfully created.';
        $_SESSION['msg_type'] = 'success';
    } else {
        $_SESSION['msg'] = 'Error creating Super Admin account: ' . $insert_stmt->error;
        $_SESSION['msg_type'] = 'danger';
    }
    $insert_stmt->close();

    header("Location: superadmin-superadmincreation.php");
    exit();
} else {
    $_SESSION['msg'] = 'Invalid access method.';
    $_SESSION['msg_type'] = 'danger';
    header("Location: superadmin-superadmincreation.php");
    exit();
}

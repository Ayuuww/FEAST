<?php
session_start();
include 'conn/conn.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize inputs
    $id           = mysqli_real_escape_string($conn, trim($_POST['idnumber']));
    $first_name   = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $mid_name     = mysqli_real_escape_string($conn, trim($_POST['mid_name']));
    $last_name    = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $password     = trim($_POST['password']); // plain text before hashing
    $position     = mysqli_real_escape_string($conn, $_POST['position']);
    $college      = isset($_POST['college']) ? mysqli_real_escape_string($conn, $_POST['college']) : null;
    $program      = isset($_POST['program']) ? mysqli_real_escape_string($conn, $_POST['program']) : null;
    $faculty_rank = isset($_POST['faculty_rank']) ? mysqli_real_escape_string($conn, $_POST['faculty_rank']) : null;

    // ✅ Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if superadmin already exists
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM superadmin WHERE idnumber = ?");
    $check_stmt->bind_param("s", $id);
    $check_stmt->execute();
    $check_stmt->bind_result($exists);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($exists > 0) {
        $_SESSION['msg'] = 'Super Admin with this ID already exists!';
        $_SESSION['msg_type'] = 'warning';
        header("Location: register-superadmincreation.php");
        exit();
    }

    // ✅ Verify college exists (if provided)
    if (!empty($college)) {
        $dep_check = $conn->prepare("SELECT COUNT(*) FROM adds WHERE college_name = ?");
        $dep_check->bind_param("s", $college);
        $dep_check->execute();
        $dep_check->bind_result($dep_exists);
        $dep_check->fetch();
        $dep_check->close();

        if ($dep_exists == 0) {
            $_SESSION['msg'] = "The selected college ($college) does not exist in the system.";
            $_SESSION['msg_type'] = 'error';
            header("Location: register-superadmincreation.php");
            exit();
        }
    }

    // ✅ Insert into superadmin
    $insert_stmt = $conn->prepare("
        INSERT INTO superadmin 
        (idnumber, first_name, mid_name, last_name, password, role, college, program, faculty_rank, position, status)
        VALUES (?, ?, ?, ?, ?, 'superadmin', ?, ?, ?, ?, 'active')
    ");
    $insert_stmt->bind_param(
        "sssssssss",
        $id,
        $first_name,
        $mid_name,
        $last_name,
        $hashed_password,
        $college,
        $program,
        $faculty_rank,
        $position
    );

    if ($insert_stmt->execute()) {

        // ✅ Automatically add to faculty table
        $insert_fac = $conn->prepare("
            INSERT INTO faculty 
            (idnumber, first_name, mid_name, last_name, password, college, program, faculty_rank, role, status)
            VALUES (?, ?, ?, ?, NULL, ?, ?, ?, 'faculty', 'active')
        ");
        $insert_fac->bind_param(
            "sssssss",
            $id,
            $first_name,
            $mid_name,
            $last_name,
            $college,
            $program,
            $faculty_rank
        );
        $insert_fac->execute();
        $insert_fac->close();

        $_SESSION['msg'] = 'Super Admin account created successfully! ✨';
        $_SESSION['msg_type'] = 'success';
    } else {
        $_SESSION['msg'] = 'Error creating Super Admin account: ' . $insert_stmt->error;
        $_SESSION['msg_type'] = 'error';
    }

    $insert_stmt->close();
    header("Location: register-superadmincreation.php");
    exit();
} else {
    $_SESSION['msg'] = 'Invalid access method.';
    $_SESSION['msg_type'] = 'error';
    header("Location: register-superadmincreation.php");
    exit();
}

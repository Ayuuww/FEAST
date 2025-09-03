<?php
session_start();
include 'conn/conn.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $id             = mysqli_real_escape_string($conn, trim($_POST['idnumber']));
    $first_name     = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $mid_name       = mysqli_real_escape_string($conn, trim($_POST['mid_name']));
    $last_name      = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $password       = trim($_POST['password']); // don’t escape before hashing
    $position       = mysqli_real_escape_string($conn, $_POST['position']);
    $department     = mysqli_real_escape_string($conn, $_POST['department']);
    $faculty        = mysqli_real_escape_string($conn, $_POST['faculty']);
    $faculty_rank   = ($faculty === 'Yes' && isset($_POST['faculty_rank'])) 
                        ? mysqli_real_escape_string($conn, $_POST['faculty_rank']) 
                        : null;

    // ✅ Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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

    // Insert into superadmin table (store hashed password!)
    $insert_stmt = $conn->prepare("
        INSERT INTO superadmin 
        (idnumber, first_name, mid_name, last_name, password, role, position, department, faculty, faculty_rank, status) 
        VALUES (?, ?, ?, ?, ?, 'superadmin', ?, ?, ?, ?, 'active')
    ");
    $insert_stmt->bind_param("sssssssss", 
        $id, $first_name, $mid_name, $last_name, 
        $hashed_password, $position, $department, $faculty, $faculty_rank
    );

    if ($insert_stmt->execute()) {
        // If faculty = Yes -> also insert into faculty table
        if ($faculty === "Yes" && !empty($faculty_rank)) {
            $insert_fac = $conn->prepare("
                INSERT INTO faculty 
                (idnumber, first_name, mid_name, last_name, department, faculty_rank, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'active')
            ");
            $insert_fac->bind_param("ssssss", 
                $id, $first_name, $mid_name, $last_name, $department, $faculty_rank
            );
            $insert_fac->execute();
            $insert_fac->close();
        }

        $_SESSION['msg'] = 'Super Admin account created successfully! ✨';
        $_SESSION['msg_type'] = 'success';
    } else {
        $_SESSION['msg'] = 'Error creating Super Admin account: ' . $insert_stmt->error;
        $_SESSION['msg_type'] = 'error';
    }

    $insert_stmt->close();
    header("Location: superadmin-superadmincreation.php");
    exit();
} else {
    $_SESSION['msg'] = 'Invalid access method.';
    $_SESSION['msg_type'] = 'error';
    header("Location: superadmin-superadmincreation.php");
    exit();
}

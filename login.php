<?php
session_start();
include "conn/conn.php";// connection to the database

$id         = $_POST['idnumber'];
$password   = $_POST['password'];

// Sanitize input (very important for security)
$id         = mysqli_real_escape_string($conn, $id);
$password   = mysqli_real_escape_string($conn, $password);

// ---- Check Superadmin ----
$query2     = "SELECT * FROM superadmin WHERE idnumber='$id' AND password='$password'";
$result2    = mysqli_query($conn, $query2);

if ($row = mysqli_fetch_assoc($result2)) {
    $_SESSION['idnumber']   = $row['idnumber'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name']  = $row['last_name'];
    $_SESSION['role']       = 'superadmin';
    header("Location: superadmin-dashboard.php");
    exit();
}

// ---- Check Admin ----
$query3  = "SELECT * FROM admin WHERE idnumber='$id' AND password='$password'";
$result3 = mysqli_query($conn, $query3);

if ($row = mysqli_fetch_assoc($result3)) {
    $_SESSION['idnumber']   = $row['idnumber'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name']  = $row['last_name'];
    $_SESSION['role']       = 'admin';
    header("Location: admin-dashboard.php");
    exit();
}

// ---- Check Faculty/Student ----
$query  = "SELECT * FROM register WHERE idnumber='$id' AND password='$password'";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['idnumber']   = $row['idnumber'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name'] = $row['last_name'];
    $_SESSION['role']       = $row['role'];
    $_SESSION['status']     = $row['status'];

    if ($row['status']      == 'pending') {
        $_SESSION['msg'] = 'Your account is still in pending. Please contact the admin.';
        header("Location: pages-login.php");
        exit();
        
    } elseif ($row['status'] == 'disapproved') {
        $_SESSION['msg'] = 'Your account has been rejected. Please contact the admin.';
        header("Location: pages-login.php");
        exit();
    }

    if ($row['role']        == 'faculty') {
        header("Location: faculty-dashboard.php");
    } elseif ($row['role']  == 'student') {
        header("Location: student-dashboard.php");
    }

    exit();
}

// ---- Invalid Login ----
$_SESSION['msg'] = 'Invalid ID or Password. Please try again.';
        header("Location: pages-login.php");
        exit();

?>

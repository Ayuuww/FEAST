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
    $_SESSION['department'] = $row['department'];
    $_SESSION['position']   = $row['position'];
    $_SESSION['email']      = $row['email'];
    $_SESSION['role']       = 'admin';
    header("Location: admin-dashboard.php");
    exit();
}

// ---- Check Faculty ----
$query1  = "SELECT * FROM faculty WHERE idnumber='$id' AND password='$password'";
$result1 = mysqli_query($conn, $query1);

if ($row = mysqli_fetch_assoc($result1)) {
    $_SESSION['idnumber']       = $row['idnumber'];
    $_SESSION['first_name']     = $row['first_name'];
    $_SESSION['last_name']      = $row['last_name'];
    $_SESSION['department']     = $row['department'];
    $_SESSION['role']           = $row['role'];

    header("Location: faculty-dashboard.php");
    exit();
}

// ---- Check Student ----
$query  = "SELECT * FROM student WHERE idnumber='$id' AND password='$password'";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['idnumber']   = $row['idnumber'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name']  = $row['last_name'];
    $_SESSION['role']       = $row['role'];
    $_SESSION['section']    = $row['section'];
    $_SESSION['department'] = $row['department'];

    header("Location: student-dashboard.php");
    exit();
}


// ---- Invalid Login ----
$_SESSION['msg'] = 'Invalid ID or Password. Please try again.';
        header("Location: pages-login.php");
        exit();

?>
<?php
session_start();
include "conn/conn.php"; // connection to the database

// Activity log function (moved to the top)
function logActivity($conn, $user_id, $role, $action) {
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, role, activity) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_id, $role, $action);
    $stmt->execute();
}

// Remember Me cookies
if (isset($_POST['remember']) && $_POST['remember'] === 'true') {
    setcookie("remember_idnumber", $_POST['idnumber'], time() + (86400 * 30), "/");
    setcookie("remember_password", $_POST['password'], time() + (86400 * 30), "/"); // Encrypt if needed
} else {
    setcookie("remember_idnumber", "", time() - 3600, "/");
    setcookie("remember_password", "", time() - 3600, "/");
}

$id       = mysqli_real_escape_string($conn, $_POST['idnumber']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// ---- Check Superadmin ----
$query2 = "SELECT * FROM superadmin WHERE idnumber='$id' AND password='$password'";
$result2 = mysqli_query($conn, $query2);
if ($row = mysqli_fetch_assoc($result2)) {
    if ($row['status'] !== 'active') {
        $_SESSION['msg'] = 'Your account is inactive. Please contact the administrator.';
        header("Location: pages-login.php");
        exit();
    }
    $_SESSION['idnumber']   = $row['idnumber'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name']  = $row['last_name'];
    $_SESSION['role']       = 'superadmin';

    logActivity($conn, $row['idnumber'], 'superadmin', 'Logged in');
    header("Location: superadmin-dashboard.php");
    exit();
}

// ---- Check Admin ----
$query3 = "SELECT * FROM admin WHERE idnumber='$id' AND password='$password'";
$result3 = mysqli_query($conn, $query3);
if ($row = mysqli_fetch_assoc($result3)) {
    if ($row['status'] !== 'active') {
        $_SESSION['msg'] = 'Your account is inactive. Please contact the administrator.';
        header("Location: pages-login.php");
        exit();
    }
    $_SESSION['idnumber']   = $row['idnumber'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name']  = $row['last_name'];
    $_SESSION['department'] = $row['department'];
    $_SESSION['position']   = $row['position'];
    $_SESSION['role']       = 'admin';

    logActivity($conn, $row['idnumber'], 'admin', 'Logged in');
    header("Location: admin-dashboard.php");
    exit();
}

// ---- Check Faculty ----
$query1 = "SELECT * FROM faculty WHERE idnumber='$id' AND password='$password'";
$result1 = mysqli_query($conn, $query1);
if ($row = mysqli_fetch_assoc($result1)) {
    if ($row['status'] !== 'active') {
        $_SESSION['msg'] = 'Your account is inactive. Please contact the administrator.';
        header("Location: pages-login.php");
        exit();
    }
    $_SESSION['idnumber']   = $row['idnumber'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name']  = $row['last_name'];
    $_SESSION['department'] = $row['department'];
    $_SESSION['role']       = $row['role'];
    $_SESSION['status']     = $row['status'];

    logActivity($conn, $row['idnumber'], 'faculty', 'Logged in');
    header("Location: faculty-dashboard.php");
    exit();
}

// ---- Check Student ----
$query = "SELECT * FROM student WHERE idnumber='$id' AND password='$password'";
$result = mysqli_query($conn, $query);
if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['idnumber']   = $row['idnumber'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name']  = $row['last_name'];
    $_SESSION['role']       = $row['role'];
    $_SESSION['section']    = $row['section'];
    $_SESSION['department'] = $row['department'];

    logActivity($conn, $row['idnumber'], 'student', 'Logged in');
    header("Location: student-dashboard.php");
    exit();
}

// ---- Invalid Login ----
$_SESSION['msg'] = 'Invalid ID or Password. Please try again.';
$_SESSION['login_failed'] = true;
header("Location: pages-login.php");
exit();


?>

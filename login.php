<?php
session_start();
include "conn/conn.php";

// Sanitize input
$id = trim($_POST['idnumber']);
$password = trim($_POST['password']);

function tryLogin($conn, $table, $id, $password)
{
    $stmt = $conn->prepare("SELECT * FROM $table WHERE idnumber = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify hashed or plain password
        if (password_verify($password, $row['password']) || $password === $row['password']) {

            // ✅ Handle "Remember My ID"
            if (isset($_POST['remember'])) {
                setcookie('remember_idnumber', $id, time() + (86400 * 30), "/"); // 30 days
            } else {
                setcookie('remember_idnumber', '', time() - 3600, "/"); // delete if unchecked
            }

            // ✅ Check account status if present
            if (array_key_exists('status', $row) && $row['status'] !== 'active') {
                // Skip this table and try the next table
                return false;
            }

            // ✅ Set session
            $_SESSION['idnumber']   = $row['idnumber'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name']  = $row['last_name'];
            $_SESSION['role']       = $row['role'];

            if (isset($row['college']))      $_SESSION['college']      = $row['college'];
            if (isset($row['faculty_rank'])) $_SESSION['faculty_rank'] = $row['faculty_rank'];
            if (isset($row['position']))     $_SESSION['position']     = $row['position'];
            if (isset($row['section']))      $_SESSION['section']      = $row['section'];

            // ✅ Log activity
            $activity = "Logged in";
            $stmtLog = $conn->prepare("INSERT INTO activity_logs (user_id, role, activity, timestamp) VALUES (?, ?, ?, NOW())");
            $stmtLog->bind_param("sss", $row['idnumber'], $row['role'], $activity);
            $stmtLog->execute();

            // ✅ Redirect by role
            switch (strtolower($_SESSION['role'])) {
                case 'superadmin':
                    header("Location: superadmin-dashboard.php");
                    break;
                case 'admin':
                    header("Location: admin-dashboard.php");
                    break;
                case 'registrar':
                    header("Location: register-dashboard.php");
                    break;
                case 'faculty':
                    header("Location: faculty-dashboard.php");
                    break;
                case 'student':
                    header("Location: student-dashboard.php");
                    break;

                default:
                    $_SESSION['msg'] = "Unknown role detected.";
                    $_SESSION['msg_type'] = "error";
                    header("Location: pages-login.php");
            }
            exit();
        }
    }
    return false;
}

// Try each user table
if (tryLogin($conn, "superadmin", $id, $password)) exit();
if (tryLogin($conn, "admin", $id, $password)) exit();
if (tryLogin($conn, "registrar", $id, $password)) exit();
if (tryLogin($conn, "faculty", $id, $password)) exit();
if (tryLogin($conn, "student", $id, $password)) exit();


// ❌ Invalid login
$_SESSION['msg'] = "Invalid ID or Password.";
$_SESSION['msg_type'] = "error";

// ✅ Keep entered ID number in session so it stays on reload
$_SESSION['entered_idnumber'] = $id;

header("Location: pages-login.php");
exit();

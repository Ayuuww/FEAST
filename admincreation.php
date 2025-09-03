<?php
session_start();
include 'conn/conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    // Sanitize inputs
    $id           = mysqli_real_escape_string($conn, trim($_POST['idnumber']));
    $first_name   = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $mid_name     = mysqli_real_escape_string($conn, trim($_POST['mid_name']));
    $last_name    = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $password     = trim($_POST['password']); // don't escape before hashing
    $department   = mysqli_real_escape_string($conn, trim($_POST['department']));
    $position     = mysqli_real_escape_string($conn, trim($_POST['position']));
    $faculty_rank = isset($_POST['faculty_rank']) && !empty(trim($_POST['faculty_rank']))
        ? mysqli_real_escape_string($conn, trim($_POST['faculty_rank']))
        : null;

    // ✅ Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if admin with same ID already exists
    $check_query = "SELECT idnumber FROM admin WHERE idnumber = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['msg'] = 'Admin with this ID already exists!';
        $_SESSION['msg_type'] = 'warning';
        header("Location: superadmin-admincreation.php");
        exit();
    }
    $stmt->close();

    // Insert into admin table
    $insert_query = "INSERT INTO admin (
        idnumber, first_name, mid_name, last_name, password, department, position, faculty_rank
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssssss", $id, $first_name, $mid_name, $last_name, $hashed_password, $department, $position, $faculty_rank);

    if ($stmt->execute()) {
        // Check and insert into faculty if not already present
        $faculty_check = $conn->prepare("SELECT idnumber FROM faculty WHERE idnumber = ?");
        $faculty_check->bind_param("s", $id);
        $faculty_check->execute();
        $faculty_check->store_result();

        if ($faculty_check->num_rows == 0) {
            $faculty_insert = $conn->prepare("INSERT INTO faculty (
                idnumber, first_name, mid_name, last_name, password, department, faculty_rank
            ) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $faculty_insert->bind_param("sssssss", $id, $first_name, $mid_name, $last_name, $hashed_password, $department, $faculty_rank);
            $faculty_insert->execute();
            $faculty_insert->close();
        }
        $faculty_check->close();

        $_SESSION['msg'] = 'Admin created successfully!';
        $_SESSION['msg_type'] = 'success';
        header("Location: superadmin-admincreation.php");
        exit();
    } else {
        $_SESSION['msg'] = 'Failed to create admin.';
        $_SESSION['msg_type'] = 'danger';
        header("Location: superadmin-admincreation.php");
        exit();
    }

    $stmt->close();
    header("Location: superadmin-admincreation.php");
    exit();
} else {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='superadmin-admincreation.php';</script>";
}
?>

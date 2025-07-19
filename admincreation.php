<?php
session_start();
include 'conn/conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    // Sanitize inputs
    $id         = mysqli_real_escape_string($conn, trim($_POST['idnumber']));
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $mid_name   = mysqli_real_escape_string($conn, trim($_POST['mid_name']));
    $last_name  = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $password   = mysqli_real_escape_string($conn, trim($_POST['password']));
    $department = mysqli_real_escape_string($conn, trim($_POST['department']));
    $position   = mysqli_real_escape_string($conn, trim($_POST['position']));
    $faculty_rank = isset($_POST['faculty_rank']) && !empty(trim($_POST['faculty_rank'])) 
                    ? mysqli_real_escape_string($conn, trim($_POST['faculty_rank'])) 
                    : null;

    // Hash the password before storing (for security)
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if admin with same ID already exists
    $check_query = "SELECT idnumber FROM admin WHERE idnumber = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['msg'] = 'Admin with this ID already exists!';
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
                idnumber, first_name, mid_name, last_name, department, faculty_rank
            ) VALUES (?, ?, ?, ?, ?, ?)");
            $faculty_insert->bind_param("ssssss", $id, $first_name, $mid_name, $last_name, $department, $faculty_rank);
            $faculty_insert->execute();
            $faculty_insert->close();
        }
        $faculty_check->close();

        $_SESSION['msg'] = 'Admin account successfully created.';
    } else {
        $_SESSION['msg'] = 'Error creating admin account: ' . $stmt->error;
    }
    $stmt->close();

    header("Location: superadmin-admincreation.php");
    exit();
} else {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='superadmin-admincreation.php';</script>";
}
?>

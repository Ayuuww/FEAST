<?php
session_start();
include 'conn/conn.php'; // Connection to the database

$faculty_rank = $_POST['faculty_rank'] ?? null;

// Submit form data
if (isset($_POST['submit'])) {
    $id         = $_POST['idnumber'];
    $first_name = $_POST['first_name'];
    $mid_name   = $_POST['mid_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $password   = $_POST['password'];
    $department = $_POST['department'];
    $position   = $_POST['position'];
    $faculty    = $_POST['faculty']; // 'yes' or 'no'

    // Check if admin with same ID already exists
    $check_query = "SELECT * FROM admin WHERE idnumber = '$id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['msg'] = 'Admin with this ID already exists!';
        header("Location: superadmin-admincreation.php");
        exit();
    }

    // Proceed with insertion to admin table
   $sql = " INSERT INTO admin ( idnumber, first_name, mid_name, last_name, email, password,
                            department, position, faculty, faculty_rank)
                            
            VALUES ('$id', '$first_name', '$mid_name', '$last_name', '$email', '$password',
                '$department', '$position', '$faculty', " . 
                ($faculty_rank ? "'$faculty_rank'" : "NULL") . ")";


    if (mysqli_query($conn, $sql)) {

        // IF marked as faculty, also insert into faculty table (if not already present)
        if (strtolower($faculty) === 'yes') {
            $faculty_check = "SELECT idnumber FROM faculty WHERE idnumber = '$id'";
            $faculty_result = mysqli_query($conn, $faculty_check);

            if (mysqli_num_rows($faculty_result) == 0) {
                $faculty_insert = "INSERT INTO faculty (
                    idnumber, first_name, mid_name, last_name, department, faculty_rank
                ) VALUES (
                    '$id', '$first_name', '$mid_name', '$last_name', '$department', '$faculty_rank'
                )";
                mysqli_query($conn, $faculty_insert);
            }
        }


        $_SESSION['msg'] = 'Admin account successfully created.';
    } else {
        $_SESSION['msg'] = 'Error creating admin account: ' . mysqli_error($conn);
    }

    header("Location: superadmin-admincreation.php");
    exit();
} else {
    echo "<script>alert('Please fill in all fields.');</script>";
}
?>

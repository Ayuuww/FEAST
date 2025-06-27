<?php
session_start();
include 'conn/conn.php';// Connection to the database

// Submit form data
if (isset($_POST['submit'])) {
    $id         = $_POST['idnumber'];
    $first_name = $_POST['first_name'];
    $mid_name   = $_POST['mid_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $password   = $_POST['password'];
    $department = $_POST['department'];

    // Check if admin with same ID already exists
    $check_query            = "SELECT * FROM admin WHERE idnumber = '$id'";
    $check_result           = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['msg']    = 'Admin with this ID already exists!';
        header("Location: superadmin-admincreation.php");
        exit();
    }

    // Proceed with insertion
    $sql    = "INSERT INTO admin   (   idnumber, 
                                    first_name, 
                                    mid_name, 
                                    last_name, 
                                    email, 
                                    password, 
                                    department)

                VALUES          (   '$id', 
                                    '$first_name', 
                                    '$mid_name', 
                                    '$last_name', 
                                    '$email', 
                                    '$password', 
                                    '$department')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg']    = 'Admin account successfully created.';
    } else {
        $_SESSION['msg']    = 'Error creating admin account: ' . mysqli_error($conn);
    }

    header("Location: superadmin-admincreation.php");
    exit();
} else {
    echo "<script>alert('Please fill in all fields.');</script>";
}

?>

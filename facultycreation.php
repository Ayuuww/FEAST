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
    $faculty_rank = $_POST['faculty_rank'];

    // Check if admin with same ID already exists
    $check_query            = "SELECT * FROM faculty WHERE idnumber = '$id'";
    $check_result           = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['msg']    = 'Faculty with this ID already exists!';
        header("Location: superadmin-facultycreation.php");
        exit();
    }

    // Proceed with insertion
    $sql    = "INSERT INTO faculty   (  idnumber, 
                                        first_name, 
                                        mid_name, 
                                        last_name, 
                                        email, 
                                        password, 
                                        department,
                                        faculty_rank)

                    VALUES          (   '$id', 
                                        '$first_name', 
                                        '$mid_name', 
                                        '$last_name', 
                                        '$email', 
                                        '$password', 
                                        '$department',
                                        '$faculty_rank')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg']    = 'faculty account successfully created.';
    } else {
        $_SESSION['msg']    = 'Error creating faculty account: ' . mysqli_error($conn);
    }

    header("Location: superadmin-facultycreation.php");
    exit();
} else {
    echo "<script>alert('Please fill in all fields.');</script>";
}

?>

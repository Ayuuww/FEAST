<?php
session_start();
include 'conn/conn.php';// Connection to the database

// Submit form data

$id         = $_POST['idnumber'];
$first_name = $_POST['first_name'];
$mid_name   = $_POST['mid_name'];
$last_name  = $_POST['last_name'];
$email      = $_POST['email'];
$password   = $_POST['password'];
$department = $_POST['department'];
$role       = $_POST['role'];

if (isset($_POST['submit'])) {

    // Check if student and faculty membes with same ID already exists
    $check_query            = "SELECT * FROM register WHERE idnumber = '$id'";
    $check_result           = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['msg']    = 'ID already exists!';
        header("Location: pages-register.php");
        exit();
    }
    // Proceed with insertion

    $sql    = "INSERT INTO register (   idnumber, 
                                        first_name, 
                                        mid_name, 
                                        last_name, 
                                        email, 
                                        password, 
                                        department, 
                                        role) 
    
                            VALUES (    '$id', 
                                        '$first_name', 
                                        '$mid_name', 
                                        '$last_name', 
                                        '$email', 
                                        '$password', 
                                        '$department', 
                                        '$role')";

$query      = mysqli_query($conn, $sql);

    if ($query) {
        $_SESSION['msg'] = 'Account successfully registered. Wait for the admin to approve!';
            header("Location: pages-register.php");
            exit;
        
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
} 
else {
    echo "<script>alert('Please fill in all fields.');</script>";
}
 




?>
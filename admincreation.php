<?php
session_start();
include 'conn/conn.php';

// Create a new admin account

$id         = $_POST['idnumber'];
$first_name = $_POST['first_name'];
$mid_name   = $_POST['mid_name'];
$last_name  = $_POST['last_name'];
$email      = $_POST['email'];
$password   = $_POST['password'];
$department = $_POST['department'];

if (isset($_POST['submit'])) {
    $sql    = "INSERT INTO admin (      idnumber, 
                                        first_name, 
                                        mid_name, 
                                        last_name, 
                                        email, 
                                        password, 
                                        department) 
    
                            VALUES (    '$id', 
                                        '$first_name', 
                                        '$mid_name', 
                                        '$last_name', 
                                        '$email', 
                                        '$password', 
                                        '$department')";

$query      = mysqli_query($conn, $sql);

    if ($query) {
        header("Location: superadmin-admincreation.php");
        echo "<script>alert('Admin Created!');</script>";
        
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
} 
else {
    echo "<script>alert('Please fill in all fields.');</script>";
}
 




?>
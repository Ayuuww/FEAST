<?php
session_start();
include 'conn/conn.php';

// Create a new super admin account

$id         = $_POST['idnumber'];
$first_name = $_POST['first_name'];
$mid_name   = $_POST['mid_name'];
$last_name  = $_POST['last_name'];
$email      = $_POST['email'];
$password   = $_POST['password'];

if (isset($_POST['submit'])) {
    $sql    = "INSERT INTO super (      idnumber, 
                                        first_name, 
                                        mid_name, 
                                        last_name, 
                                        email, 
                                        password) 
    
                            VALUES (    '$id', 
                                        '$first_name', 
                                        '$mid_name', 
                                        '$last_name', 
                                        '$email', 
                                        '$password')";

$query      = mysqli_query($conn, $sql);

    if ($query) {
        header("Location: superadmin-superadmincreation.php");
        echo "<script>alert('Super Admin Created!');</script>";
        
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
} 
else {
    echo "<script>alert('Please fill in all fields.');</script>";
}
 




?>
<?php
session_start();
include 'conn/conn.php';

$id         = $_POST['idnumber'];
$first_name = $_POST['first_name'];
$mid_name   = $_POST['mid_name'];
$last_name  = $_POST['last_name'];
$email      = $_POST['email'];
$password   = $_POST['password'];
$department = $_POST['department'];
$role       = $_POST['role'];

if (isset($_POST['submit'])) {
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
        echo "<script>alert('Registration successful! Wait for approval. Thank You!');</script>";
        header("Location: pages-login.php");
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
} 
else {
    echo "<script>alert('Please fill in all fields.');</script>";
}
 




?>
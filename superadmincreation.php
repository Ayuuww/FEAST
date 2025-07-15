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

    // Check if admin with same ID already exists
    $check_query            = "SELECT * FROM superadmin WHERE idnumber = '$id'";
    $check_result           = mysqli_query($conn, $check_query);

    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['msg']    = 'Super Admin with this ID already exists!';
        header("Location: superadmin-superadmincreation.php");
        exit();
    }

    // Proceed with insertion
    $sql    = "INSERT INTO superadmin   (   idnumber, 
                                            first_name, 
                                            mid_name, 
                                            last_name, 
                                            email, 
                                            password)

                        VALUES          (   '$id', 
                                            '$first_name', 
                                            '$mid_name', 
                                            '$last_name', 
                                            '$email', 
                                            '$password')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg']    = 'Super Admin account successfully created.';
    } else {
        $_SESSION['msg']    = 'Error creating super admin account: ' . mysqli_error($conn);
    }

    header("Location: superadmin-superadmincreation.php");
    exit();

} else {
    echo "<script>alert('Please fill in all fields.');</script>";
}

?>

<?php
session_start();
include 'conn/conn.php';// Connection to the database

// Deleting subject
if (isset($_POST['delete'])) {
    $subject_code = $_POST['code'];

    // Check if subject code exists
    $check_query    = "SELECT * FROM subject WHERE code='$subject_code'";
    $check_result   = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        $_SESSION['msg'] = 'Subject does not exist!';
        header("Location: superadmin-subjectlist.php");
        exit();
        
    } else {
        // Delete the subject
        $delete_query = "DELETE FROM subject WHERE code='$subject_code'";
        
        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['msg'] = 'Subject deleted successfully!';
            header("Location: superadmin-subjectlist.php");
            exit();
        } else {
            echo "<script>alert('Error deleting subject: " . mysqli_error($conn) . "');</script>";
        }
    }
}



?>
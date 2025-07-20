<?php

include 'conn/conn.php';// Connection to the database

// Approval and rejection of student and faculty members
if (isset($_POST['approve'])) {
    $id = $_POST['idnumber'];
    
    // Update the status to 'approved'
    $sql = "UPDATE register SET status = 'approved' WHERE idnumber='$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Approved successfully!');</script>";
    } else {
        echo "<script>alert('Error approving faculty member: " . mysqli_error($conn) . "');</script>";
    }
} elseif (isset($_POST['reject'])) {
    $id = $_POST['idnumber'];
    
    // Update the status to 'rejected'
    $sql = "UPDATE register SET status = 'disapproved' WHERE idnumber='$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Rejected successfully!');</script>";
    } else {
        echo "<script>alert('Error rejecting faculty member: " . mysqli_error($conn) . "');</script>";
    }
}



?>
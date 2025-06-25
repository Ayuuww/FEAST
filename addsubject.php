<?php 

session_start();
include 'conn/conn.php';

// Adding subject with faculty name
if (isset($_POST['addsubject'])) {
    $subject_code = $_POST['code'];
    $subject_title = $_POST['title'];
    $faculty_id = $_POST['faculty_id'];


    // Check if subject code already exists
    $check_query = "SELECT * FROM subject WHERE code='$subject_code'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Subject already exists!');</script>";
    } else {
        // Check if faculty ID exists
        $faculty_check = mysqli_query($conn, "SELECT * FROM register WHERE idnumber = '$faculty_id'");
        if (mysqli_num_rows($faculty_check) == 0) {
            echo "<script>alert('Invalid faculty ID. Cannot assign subject.');</script>";
        } else {
            $query = "INSERT INTO subject ( code, 
                                            title, 
                                            faculty_id) 

                                VALUES (    '$subject_code', 
                                            '$subject_title', 
                                            '$faculty_id')";
            
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Subject added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding subject: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}



?>
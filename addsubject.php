<?php 
session_start();
include 'conn/conn.php';// Connection to the database

if (isset($_POST['addsubject'])) {
    $subject_code   = $_POST['code'];
    $subject_title  = $_POST['title'];
    $faculty_id     = $_POST['faculty_id'];

    // Check if subject with same code and faculty already exists
    $check_query = "SELECT * FROM subject WHERE code = '$subject_code' AND faculty_id = '$faculty_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['msg'] = 'Subject already exists for this faculty!';
                    header("Location: superadmin-subjectadding.php");
                    exit;
    }  else {
            // Inserting new subject 
            $query = "INSERT INTO subject ( code, 
                                            title, 
                                            faculty_id)
                                            
                                VALUES (    '$subject_code', 
                                            '$subject_title', 
                                            '$faculty_id')";
            
            if (mysqli_query($conn, $query)) {
                $_SESSION['msg'] = 'Subject added successfully!';
                    header("Location: superadmin-subjectadding.php");
                    exit;

            } else {
                $_SESSION['msg'] = 'Error Adding Subject!';
                    header("Location: superadmin-subjectadding.php");
                    exit;
            }
        }
    
}
?>

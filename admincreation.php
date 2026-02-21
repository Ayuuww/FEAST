<?php
session_start();
include 'conn/conn.php';

// Check if registrar is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
    header("Location: pages-login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $idnumber     = trim($_POST['idnumber']);
    $first_name   = trim($_POST['first_name']);
    $mid_name     = trim($_POST['mid_name']);
    $last_name    = trim($_POST['last_name']);
    $password     = $_POST['password'];
    $position     = trim($_POST['position']);
    $faculty_rank = $_POST['faculty_rank'] ?? NULL;

    // College assignment
    $main_college = $_POST['main_college'];

    // Grab the program(s). It is now submitted as an array `main_program[]`
    $programs_post = $_POST['main_program'] ?? [];

    if (!is_array($programs_post)) {
        $programs_post = [$programs_post];
    }

    // Server-Side Validation: Ensure Non-Deans can only have 1 program maximum
    if (stripos($position, 'Dean') === false && count($programs_post) > 1) {
        $programs_post = [$programs_post[0]];
    }

    // Hash the default password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // ✅ Get a base program for the 'faculty' mirrored table (The "Mother Program")
    // Because the UI allows Deans to select multiple, we grab the very first one in the array.
    $mother_program = !empty($programs_post) ? $programs_post[0] : '';

    $conn->begin_transaction();

    try {
        // 1. Insert into Admin Table
        $stmt_admin = $conn->prepare("
            INSERT INTO admin 
            (idnumber, first_name, mid_name, last_name, password, position, faculty_rank, role, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'admin', 'active')
        ");
        $stmt_admin->bind_param("sssssss", $idnumber, $first_name, $mid_name, $last_name, $hashed_password, $position, $faculty_rank);
        $stmt_admin->execute();

        // 2. Insert into Faculty Table (Mirror record with Mother Program)
        $stmt_faculty = $conn->prepare("
            INSERT INTO faculty 
            (idnumber, first_name, mid_name, last_name, password, role, status, college, program, faculty_rank)
            VALUES (?, ?, ?, ?, ?, 'faculty', 'active', ?, ?, ?)
        ");
        $stmt_faculty->bind_param("ssssssss", $idnumber, $first_name, $mid_name, $last_name, $hashed_password, $main_college, $mother_program, $faculty_rank);
        $stmt_faculty->execute();

        // 3. Insert into admin_college mappings using INSERT IGNORE to prevent duplicate crashes
        $stmt_dept = $conn->prepare("INSERT IGNORE INTO admin_college (admin_idnumber, college_name, program_name) VALUES (?, ?, ?)");

        // Filter array to ensure no duplicates are passed from the front end
        $unique_programs = array_unique($programs_post);

        // If no programs were selected, just map them to the college
        if (empty($unique_programs)) {
            $empty_prog = '';
            $stmt_dept->bind_param("sss", $idnumber, $main_college, $empty_prog);
            $stmt_dept->execute();
        } else {
            // Loop and insert a record for every program selected
            foreach ($unique_programs as $prog) {
                $prog_val = trim($prog);
                $stmt_dept->bind_param("sss", $idnumber, $main_college, $prog_val);
                $stmt_dept->execute();
            }
        }

        $conn->commit();
        $_SESSION['success_message'] = "Admin + Faculty account created successfully for $first_name $last_name!";
        header("Location: register-admincreation.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        if ($e->getCode() == 1062) { // Duplicate Entry Code
            $_SESSION['error_message'] = "Error: An account with ID Number '$idnumber' already exists.";
        } else {
            $_SESSION['error_message'] = "Database Error: " . $e->getMessage();
        }
        header("Location: register-admincreation.php");
        exit();
    }
} else {
    header("Location: register-admincreation.php");
    exit();
}

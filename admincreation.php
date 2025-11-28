<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
    header("Location: pages-login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $idnumber   = $_POST['idnumber'];
    $first_name = $_POST['first_name'];
    $mid_name   = $_POST['mid_name'];
    $last_name  = $_POST['last_name'];
    $password   = $_POST['password'];
    $position   = $_POST['position'];
    $faculty_rank = $_POST['faculty_rank'] ?? NULL;

    // ✅ Main assignment (required)
    $main_college = $_POST['main_college'];
    // Use empty string '' if NULL, to match database (NOT NULL)
    $main_program = $_POST['main_program'] ?? '';

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // ✅ Optional additional assignments
    $college = $_POST['college'] ?? [];
    $programs_by_dept = $_POST['programs'] ?? [];

    // ✅ Use an array to track added entries and prevent duplicates
    $added_assignments = [];

    $conn->begin_transaction();

    try {
        // ✅ 1. Insert into admin table
        $stmt_admin = $conn->prepare("
            INSERT INTO admin 
            (idnumber, first_name, mid_name, last_name, password, position, faculty_rank, role, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'admin', 'active')
        ");
        $stmt_admin->bind_param(
            "sssssss",
            $idnumber,
            $first_name,
            $mid_name,
            $last_name,
            $hashed_password,
            $position,
            $faculty_rank
        );
        $stmt_admin->execute();

        // ✅ 2. Insert into faculty (mirror record)
        $stmt_faculty = $conn->prepare("
            INSERT INTO faculty 
            (idnumber, first_name, mid_name, last_name, password, role, status, college, program, faculty_rank)
            VALUES (?, ?, ?, ?, ?, 'faculty', 'active', ?, ?, ?)
        ");
        $stmt_faculty->bind_param(
            "ssssssss",
            $idnumber,
            $first_name,
            $mid_name,
            $last_name,
            $hashed_password,
            $main_college,
            $main_program,
            $faculty_rank
        );
        $stmt_faculty->execute();

        // ✅ 3. Prepare statement for admin_college
        $stmt_dept = $conn->prepare("
            INSERT INTO admin_college (admin_idnumber, college_name, program_name)
            VALUES (?, ?, ?)
        ");

        // ✅ 4. Insert the MAIN college/program assignment
        // This is the primary admin role
        $stmt_dept->bind_param("sss", $idnumber, $main_college, $main_program);
        $stmt_dept->execute();
        // Track it to avoid duplicates
        $added_assignments[$main_college . "::" . $main_program] = true;


        // ✅ 5. Loop and insert *ADDITIONAL* assignments (from multi-select)
        foreach ($college as $dept_name) {
            if (isset($programs_by_dept[$dept_name]) && !empty($programs_by_dept[$dept_name])) {
                // Admin is assigned to specific programs in this dept
                foreach ($programs_by_dept[$dept_name] as $prog_name) {
                    $key = $dept_name . "::" . $prog_name;
                    // Skip if this was already added as the "Main" assignment
                    if (isset($added_assignments[$key])) {
                        continue;
                    }

                    $stmt_dept->bind_param("sss", $idnumber, $dept_name, $prog_name);
                    $stmt_dept->execute();
                    $added_assignments[$key] = true; // Track it
                }
            } else {
                // Admin is assigned to the whole college (program_name = '')
                $prog_name = '';
                $key = $dept_name . "::" . $prog_name;
                // Skip if this was already added as the "Main" assignment
                if (isset($added_assignments[$key])) {
                    continue;
                }

                $stmt_dept->bind_param("sss", $idnumber, $dept_name, $prog_name);
                $stmt_dept->execute();
                $added_assignments[$key] = true; // Track it
            }
        }

        $conn->commit();
        $_SESSION['success_message'] = "Admin + Faculty account created successfully for $first_name $last_name!";
        header("Location: register-admincreation.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        if ($e->getCode() == 1062) { // Duplicate entry
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

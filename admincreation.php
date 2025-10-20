<?php
session_start();
include 'conn/conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    // Sanitize inputs
    $id = trim($_POST['idnumber']);
    $first_name = trim($_POST['first_name']);
    $mid_name = trim($_POST['mid_name']);
    $last_name = trim($_POST['last_name']);
    $password = trim($_POST['password']);
    $position = trim($_POST['position']);
    $is_faculty = $_POST['is_faculty'] ?? "No";
    $departments = $_POST['departments'] ?? [];
    $faculty_rank = $_POST['faculty_rank'] ?? null;

    // ✅ --- START: SERVER-SIDE VALIDATION ---
    $errors = [];
    if (empty($id)) { $errors[] = "ID Number is required."; }
    if (empty($first_name)) { $errors[] = "First Name is required."; }
    if (empty($last_name)) { $errors[] = "Last Name is required."; }
    if (empty($position)) { $errors[] = "Position is required."; }
    if (empty($is_faculty)) { $errors[] = "Faculty status is required."; }

    if ($is_faculty === "Yes") {
        if (empty($departments)) { $errors[] = "At least one Department is required for a faculty member."; }
        if (empty($faculty_rank)) { $errors[] = "Faculty Rank is required."; }
    }

    if (!empty($errors)) {
        $_SESSION['msg'] = implode('<br>', $errors);
        $_SESSION['msg_type'] = 'danger'; // Use 'danger' for errors
        header("Location: superadmin-admincreation.php");
        exit();
    }
    // ✅ --- END: SERVER-SIDE VALIDATION ---

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if admin with same ID already exists
    $stmt = $conn->prepare("SELECT idnumber FROM admin WHERE idnumber = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['msg'] = 'Admin with this ID already exists!';
        $_SESSION['msg_type'] = 'warning';
        header("Location: superadmin-admincreation.php");
        exit();
    }
    $stmt->close();

    // Insert into admin table
    $stmt = $conn->prepare("INSERT INTO admin (idnumber, first_name, mid_name, last_name, password, position, faculty_rank, is_faculty) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $id, $first_name, $mid_name, $last_name, $hashed_password, $position, $faculty_rank, $is_faculty);

    if ($stmt->execute()) {
        // Insert assigned departments into admin_departments
        if (!empty($departments)) {
            $dept_stmt = $conn->prepare("INSERT INTO admin_departments (admin_idnumber, department_name) VALUES (?, ?)");
            foreach ($departments as $dept) {
                $dept_stmt->bind_param("ss", $id, $dept);
                $dept_stmt->execute();
            }
            $dept_stmt->close();
        }

        // If 'Yes', also create/update a record in the faculty table
        if ($is_faculty === "Yes") {
            // Use the FIRST selected department as the primary for the faculty table
            $primary_department = $departments[0]; 

            $faculty_check = $conn->prepare("SELECT idnumber FROM faculty WHERE idnumber = ?");
            $faculty_check->bind_param("s", $id);
            $faculty_check->execute();
            $faculty_check->store_result();

            if ($faculty_check->num_rows == 0) {
                $faculty_insert = $conn->prepare("INSERT INTO faculty (idnumber, first_name, mid_name, last_name, department, faculty_rank) VALUES (?, ?, ?, ?, ?, ?)");
                $faculty_insert->bind_param("ssssss", $id, $first_name, $mid_name, $last_name, $primary_department, $faculty_rank);
                $faculty_insert->execute();
                $faculty_insert->close();
            }
            $faculty_check->close();
        }

        $_SESSION['msg'] = 'Admin created successfully!';
        $_SESSION['msg_type'] = 'success';
    } else {
        $_SESSION['msg'] = 'Failed to create admin: ' . $stmt->error;
        $_SESSION['msg_type'] = 'danger';
    }

    $stmt->close();
    header("Location: superadmin-admincreation.php");
    exit();

} else {
    // Redirect if accessed directly
    header("Location: superadmin-admincreation.php");
    exit();
}
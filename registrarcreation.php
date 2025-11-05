<?php
session_start();
include 'conn/conn.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Collect form data safely
  $idnumber        = trim($_POST['idnumber']);
  $first_name      = trim($_POST['first_name']);
  $mid_name        = trim($_POST['mid_name']);
  $last_name       = trim($_POST['last_name']);
  $employment_role = $_POST['employment_role'];
  $faculty_rank    = !empty($_POST['faculty_rank']) ? trim($_POST['faculty_rank']) : NULL;
  $department = !empty($_POST['department']) ? trim($_POST['department']) : NULL;
  $program    = !empty($_POST['program']) ? trim($_POST['program']) : NULL;

  // Defaults
  $status   = "active";
  $role     = "registrar"; // all entries belong to registrar
  $password_plain = "ILOVEDMMMSU";
  $password = password_hash($password_plain, PASSWORD_DEFAULT);

  // ✅ Step 1 — Insert into registrar table
  $query = "INSERT INTO registrar (idnumber, first_name, mid_name, last_name, password, department, program, status, role, employment_role, faculty_rank)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("sssssssssss", $idnumber, $first_name, $mid_name, $last_name, $password, $department, $program, $status, $role, $employment_role, $faculty_rank);

  if ($stmt->execute()) {

    // ✅ Step 2 — If Teaching, also insert into faculty table
    if (strtolower($employment_role) === 'teaching') {
      $faculty_role = "faculty";

      $query2 = "INSERT INTO faculty (idnumber, first_name, mid_name, last_name, department, program, faculty_rank, role, status)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt2 = $conn->prepare($query2);
      $stmt2->bind_param("sssssssss", $idnumber, $first_name, $mid_name, $last_name, $department, $program, $faculty_rank, $faculty_role, $status);
      $stmt2->execute();
      $stmt2->close();
    }

    // ✅ Success Message
    $_SESSION['msg'] = "Account successfully created!";
    $_SESSION['msg_type'] = "success";
  } else {
    // ❌ Error Message
    $_SESSION['msg'] = "Error: Could not create account (duplicate ID or database error).";
    $_SESSION['msg_type'] = "error";
  }

  $stmt->close();
  $conn->close();

  // Redirect back
  header("Location: register-registrarcreation.php");
  exit();
}

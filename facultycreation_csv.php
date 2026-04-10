<?php
session_start();
// Use the recommended error reporting setup
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'conn/conn.php';

// Check if the user is logged in (Crucial security step)
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

// Set a default location for redirecting back
$redirect_page = "admin-facultycreation.php";

// -------------------------------
// CHECK IF CSV UPLOAD (Bulk Creation)
// -------------------------------
if (isset($_POST['bulk_upload']) && !empty($_FILES['csv_file']['name'])) {

  $file = $_FILES['csv_file']['tmp_name'];
  $allowed_mime_types = ['text/csv', 'application/csv', 'application/vnd.msexcel', 'text/plain'];
  $file_mime_type = mime_content_type($file);

  if (!in_array($file_mime_type, $allowed_mime_types)) {
    $_SESSION['msg'] = "Invalid file type. Please upload a valid CSV file.";
    $_SESSION['msg_type'] = "danger";
    header("Location: " . $redirect_page);
    exit();
  }

  if (($handle = fopen($file, "r")) !== false) {

    $row_count = 0;
    $inserted = 0;
    $skipped_duplicates = 0;
    $skipped_invalid = 0;
    $failed_rows = [];

    // Define expected columns: id, first, mid, last, rank, college, program
    $expected_columns = 7;

    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
      $row_count++;

      // Check if the number of columns is correct on the first row (header check)
      if ($row_count == 1) {
        if (count($data) != $expected_columns) {
          $_SESSION['msg'] = "CSV format error: Expected **$expected_columns** columns but found **" . count($data) . "**. Check your template. (Order: idnumber, first_name, mid_name, last_name, faculty_rank, college, program)";
          $_SESSION['msg_type'] = "danger";
          fclose($handle);
          header("Location: " . $redirect_page);
          exit();
        }
        continue; // Skip header
      }

      // Ensure we have exactly the expected number of fields
      if (count($data) < $expected_columns) {
        $skipped_invalid++;
        $failed_rows[] = "Row $row_count: Missing columns/data. Expected $expected_columns fields.";
        continue;
      }

      list($id, $first, $mid, $last, $rank, $college, $program) = $data;

      // 1. Clean and Sanitize values
      $id   = trim(filter_var($id, FILTER_SANITIZE_STRING));
      $first  = trim(filter_var($first, FILTER_SANITIZE_STRING));
      $mid   = trim(filter_var($mid, FILTER_SANITIZE_STRING));
      $last  = trim(filter_var($last, FILTER_SANITIZE_STRING));
      $rank  = trim(filter_var($rank, FILTER_SANITIZE_STRING));
      $college = trim(filter_var($college, FILTER_SANITIZE_STRING));
      $program = trim(filter_var($program, FILTER_SANITIZE_STRING));

      // 2. Basic Validation (Check for required fields)
      if (empty($id) || empty($first) || empty($last) || empty($rank) || empty($college) || empty($program)) {
        $skipped_invalid++;
        $failed_rows[] = "Row $row_count (ID: $id): Missing required field (ID, Name, Rank, College, or Program).";
        continue;
      }

      // Default password and hashing
      $password_hash = password_hash("ILOVEDMMMSU", PASSWORD_DEFAULT);

      // 3. Check if faculty already exists
      $check = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE idnumber = ?");
      $check->bind_param("s", $id);
      $check->execute();
      $check->bind_result($exists);
      $check->fetch();
      $check->close();

      if ($exists > 0) {
        $skipped_duplicates++;
        continue; // do not insert, move to the next row
      }

      // 4. Insert faculty
      try {
        $stmt = $conn->prepare("
          INSERT INTO faculty 
          (idnumber, first_name, mid_name, last_name, password, faculty_rank, college, program) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssss", $id, $first, $mid, $last, $password_hash, $rank, $college, $program);

        if ($stmt->execute()) {
          $inserted++;
        } else {
          $skipped_invalid++;
          $failed_rows[] = "Row $row_count (ID: $id): DB insertion failed. Check data length/format.";
        }
        $stmt->close();
      } catch (mysqli_sql_exception $e) {
        $error_message = $e->getMessage();

        // --- TRANSLATE TECHNICAL ERROR ---
        if (strpos($error_message, 'FOREIGN KEY constraint fails') !== false) {
          $user_error = "Required data is missing: The **Rank, College, or Program** provided in the CSV does not match any existing entry in the system's reference list (`adds` table).";
        } else {
          $user_error = "Database error. " . $error_message; // Fallback for other errors
        }
        // ---------------------------------------------
        $skipped_invalid++;
        $failed_rows[] = "Row $row_count (ID: $id): $user_error";
      }
    }

    fclose($handle);

    // Prepare the result message
    $total_processed = $row_count - 1; // Subtract header
    $message = "CSV Bulk Upload Complete. Processed $total_processed record(s):<br>";
    $message .= "✅ **$inserted** faculty account(s) created.<br>";
    $message .= "⚠️ **$skipped_duplicates** duplicate ID(s) skipped.<br>";

    if ($skipped_invalid > 0) {
      $message .= "❌ **$skipped_invalid** invalid row(s) skipped. <a href='#' onclick='showFailureDetails(event);'>**Click here for details.**</a>";
      $_SESSION['msg_type'] = "warning";
      $_SESSION['failure_details'] = $failed_rows; // Store details for SweetAlert
    } else {
      $_SESSION['msg_type'] = "success";
    }

    $_SESSION['msg'] = $message;

    // Redirect with results
    header("Location: " . $redirect_page);
    exit();
  }
}


// -----------------------------------
// NORMAL SINGLE FACULTY CREATION
// -----------------------------------

// Check if this is a single submission (via button 'submit')
if (isset($_POST['submit'])) {

  // Sanitize and Validate Single Input (using prepared statements for safe insertion)
  $idnumber  = trim(filter_var($_POST['idnumber'], FILTER_SANITIZE_STRING));
  $first_name = trim(filter_var($_POST['first_name'], FILTER_SANITIZE_STRING));
  $mid_name  = trim(filter_var($_POST['mid_name'], FILTER_SANITIZE_STRING));
  $last_name = trim(filter_var($_POST['last_name'], FILTER_SANITIZE_STRING));
  $password  = trim($_POST['password']);
  $rank    = trim(filter_var($_POST['faculty_rank'], FILTER_SANITIZE_STRING));
  $college  = trim(filter_var($_POST['college'], FILTER_SANITIZE_STRING));
  $program  = trim(filter_var($_POST['program'], FILTER_SANITIZE_STRING));

  // Basic check for required fields
  if (empty($idnumber) || empty($first_name) || empty($last_name) || empty($rank) || empty($college) || empty($program)) {
    $_SESSION['msg'] = "All fields are required. Please go back and complete the form.";
    $_SESSION['msg_type'] = "warning";
    header("Location: " . $redirect_page);
    exit();
  }

  $hashed = password_hash($password, PASSWORD_DEFAULT);

  // Check duplicate
  $check = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE idnumber = ?");
  $check->bind_param("s", $idnumber);
  $check->execute();
  $check->bind_result($exists);
  $check->fetch();
  $check->close();

  if ($exists > 0) {
    $_SESSION['msg'] = "ID number already exists. Please enter a different one.";
    $_SESSION['msg_type'] = "warning";
    header("Location: " . $redirect_page);
    exit();
  }

  // Insert faculty
  try {
    $stmt = $conn->prepare("
    INSERT INTO faculty 
    (idnumber, first_name, mid_name, last_name, password, faculty_rank, college, program)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
  ");
    $stmt->bind_param("ssssssss", $idnumber, $first_name, $mid_name, $last_name, $hashed, $rank, $college, $program);

    if ($stmt->execute()) {
      $_SESSION['msg'] = "Faculty account for **$first_name $last_name** has been created successfully.";
      $_SESSION['msg_type'] = "success";
    } else {
      throw new mysqli_sql_exception("Failed to execute statement.");
    }

    $stmt->close();
  } catch (mysqli_sql_exception $e) {
    $_SESSION['msg'] = "Failed to create faculty account. Ensure Academic Rank, College, and Program are correctly set up in the system's reference list.";
    $_SESSION['msg_type'] = "danger";
  }

  header("Location: " . $redirect_page);
  exit();
}

// Redirect if accessed without POST data
if (!isset($_POST['bulk_upload']) && !isset($_POST['submit'])) {
  $_SESSION['msg'] = "Invalid access method.";
  $_SESSION['msg_type'] = "danger";
  header("Location: " . $redirect_page);
  exit();
}

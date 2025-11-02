<?php
include 'conn/conn.php';

if (isset($_POST['department'])) {
  $department = mysqli_real_escape_string($conn, $_POST['department']);

  $query = "SELECT DISTINCT program_name FROM adds 
            WHERE department_name = '$department' 
            AND program_name IS NOT NULL 
            AND program_name != ''";
  $result = mysqli_query($conn, $query);

  $options = '<option value="" disabled selected>Select Program</option>';
  while ($row = mysqli_fetch_assoc($result)) {
    $program = htmlspecialchars($row['program_name']);
    $options .= "<option value='$program'>$program</option>";
  }

  echo $options;
}

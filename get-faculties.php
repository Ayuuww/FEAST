<?php
include 'conn/conn.php';

if (isset($_GET['department'])) {
    $department = mysqli_real_escape_string($conn, $_GET['department']);
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

    $query = "SELECT idnumber, last_name, first_name, mid_name 
              FROM faculty 
              WHERE department = '$department'";

    if (!empty($search)) {
        $query .= " AND (last_name LIKE '%$search%' 
                     OR first_name LIKE '%$search%' 
                     OR mid_name LIKE '%$search%')";
    }

    $query .= " ORDER BY last_name ASC";

    $result = mysqli_query($conn, $query);
    $faculties = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $faculties[] = [
            "idnumber" => $row['idnumber'],
            "full_name" => $row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['mid_name']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($faculties);
}

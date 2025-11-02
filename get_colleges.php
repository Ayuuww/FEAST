<?php
// get_colleges.php
include 'conn/conn.php';
header('Content-Type: application/json');

$q = $conn->query("SELECT id, college_name FROM colleges ORDER BY college_name ASC");
$out = [];
while ($r = $q->fetch_assoc()) $out[] = $r;
echo json_encode($out);

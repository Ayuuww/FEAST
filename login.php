<?php

session_start();
include "conn/conn.php";


$id			= $_POST['idnumber'];
$password 	= $_POST['password'];
$count 		= 0;

$query 		= 'SELECT * , COUNT(idnumber) AS counter FROM register WHERE idnumber="'.$id.'" AND password="'.$password.'" ';

$result 	= mysqli_query($conn,$query); 


while ($row = $result->fetch_assoc()) {
 	$count 	= $row['counter'];
	$id 	= $row['idnumber'];
	$_SESSION['role'] = $row['role'];

 }
//  if(isset($_POST['login'])){

    
// 	$id    = $_POST['idnumber'];
	
// 	$sql    = "INSERT INTO 
// 						recent(us)
								
// 				VALUES ('$usn')";
	
// 	$result = mysqli_query($conn,$sql);		
	
// 	}

 if($count == 1){
	$_SESSION['idnumber'] = $id;
 	$_SESSION['password'] = $password;
 	
 	if ($_SESSION['role'] == 'faculty') {
 		header("Location: index.php");
 	}
     elseif($_SESSION['role'] == 'superadmin') {
 		header("Location: superadmin-dashboard.php");
 	}
		


 } else {
 	header("Location: pages-login.php?error=Invalid ID or Password");
	echo "<script>alert('Invalid ID or Password');</script>";	

 }

?>
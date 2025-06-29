<?php
// This file handles the logout functionality for the users
// logout.php
session_start();
session_destroy();
header("Location: pages-login.php"); // Redirect to the login page
exit();

?>
<?php
session_start();
session_unset();
session_destroy();

// ✅ Keep the "remember_idnumber" cookie so the ID is pre-filled next time.
// (Uncomment this line only if you want to clear it manually.)
// setcookie('remember_idnumber', '', time() - 3600, "/");

header("Location: pages-login.php");
exit();

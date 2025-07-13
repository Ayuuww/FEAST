<?php
session_start();
include 'conn/conn.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idnumber = mysqli_real_escape_string($conn, $_POST['idnumber']);
    $newpass  = mysqli_real_escape_string($conn, $_POST['newpassword']);
    $hashed   = password_hash($newpass, PASSWORD_DEFAULT); // Always hash passwords

    // Check if user exists
    $result = $conn->query("SELECT * FROM superadmin WHERE idnumber = '$idnumber'");
    if ($result->num_rows > 0) {
        // Update password
        $conn->query("UPDATE superadmin SET password = '$hashed' WHERE idnumber = '$idnumber'");
        $msg = "Password successfully updated. <a href='pages-login.php'>Login here</a>";
    } else {
        $msg = "ID Number not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <?php include 'header.php'; ?>
</head>
<body>
  <main class="min-vh-100 d-flex justify-content-center align-items-center bg-light">
    <div class="card shadow p-4" style="width: 100%; max-width: 450px;">
      <h5 class="text-center mb-3">Reset Password</h5>

      <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-floating mb-3">
          <input type="text" class="form-control" name="idnumber" placeholder="ID Number" required>
          <label>ID Number</label>
        </div>

        <div class="form-floating mb-3">
          <input type="password" class="form-control" name="newpassword" placeholder="New Password" required>
          <label>New Password</label>
        </div>

        <button class="btn btn-primary w-100">Reset Password</button>
        <a href="pages-login.php" class="btn btn-secondary w-100 mt-2">Back to Login</a>
      </form>
    </div>
  </main>
</body>
</html>

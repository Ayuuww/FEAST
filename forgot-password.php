<?php
session_start();
include 'conn/conn.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idnumber = mysqli_real_escape_string($conn, $_POST['idnumber']);

    $result = $conn->query("SELECT * FROM superadmin WHERE idnumber = '$idnumber'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(32));

        // Save token to DB
        $conn->query("UPDATE superadmin SET reset_token = '$token', reset_token_expire = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE idnumber = '$idnumber'");

        // Simulate email by showing link (localhost)
        $msg = "Copy and paste this reset link in your browser:<br>
                <a href='http://localhost/FEAST/reset-password.php?token=$token'>http://localhost/FEAST/reset-password.php?token=$token</a>";
    } else {
        $msg = "ID Number not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password (Localhost)</title>
  <?php include 'header.php'; ?>
</head>
<body>
  <main class="min-vh-100 d-flex justify-content-center align-items-center bg-light">
    <div class="card shadow p-4" style="width: 100%; max-width: 450px;">
      <h5 class="text-center mb-3">Forgot Password</h5>

      <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-floating mb-3">
          <input type="text" class="form-control" name="idnumber" placeholder="Enter your ID number" required>
          <label>ID Number</label>
        </div>
        <button class="btn btn-primary w-100">Generate Reset Link</button>
        <a href="pages-login.php" class="btn btn-secondary w-100 mt-2">Back to Login</a>
      </form>
    </div>
  </main>
</body>
</html>

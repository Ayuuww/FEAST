<?php
session_start();
include 'conn/conn.php';

if (isset($_POST['login'])) {
  $idnumber = trim($_POST['idnumber']);
  $password = trim($_POST['password']);
  $remember = isset($_POST['remember']); // checkbox

  // ✅ Check login credentials
  $query = "SELECT * FROM users WHERE idnumber = ? AND password = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $idnumber, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // --- Store session variables ---
    $_SESSION['idnumber'] = $user['idnumber'];
    $_SESSION['role'] = $user['role'];

    // ✅ Handle Remember Me (remember only ID)
    if ($remember) {
      setcookie('remember_idnumber', $idnumber, time() + (86400 * 30), "/"); // 30 days
    } else {
      // Clear if unchecked
      setcookie('remember_idnumber', '', time() - 3600, "/");
    }

    // ✅ Optional: never remember password for security
    setcookie('remember_password', '', time() - 3600, "/");

    // Redirect based on role
    if ($user['role'] === 'superadmin') {
      header("Location: superadmin-dashboard.php");
    } elseif ($user['role'] === 'admin') {
      header("Location: admin-dashboard.php");
    } else {
      header("Location: faculty-dashboard.php");
    }
    exit();
  } else {
    // ❌ Invalid login
    $_SESSION['msg'] = "Invalid ID number or password.";
    $_SESSION['login_failed'] = true;
    header("Location: pages-login.php");
    exit();
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <style>
    body {
      background: linear-gradient(135deg, #0bb35fff 0%, #3a6073 100%);
      background-attachment: fixed;
      font-family: 'Poppins', sans-serif;
      color: #333;
    }

    .login-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .login-card {
      display: flex;
      flex-wrap: wrap;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(18px);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
      max-width: 950px;
      width: 100%;
    }

    /* Left Side */
    .login-left {
      flex: 1;
      background: rgba(255, 255, 255, 0.25);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 60px 40px;
      text-align: center;
      color: #fff;
    }

    .login-left img {
      width: 150px;
      margin-bottom: 25px;
      animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }
    }

    /* Right Side (Form) */
    .login-right {
      flex: 1;
      background: #fff;
      padding: 50px;
      border-radius: 0 20px 20px 0;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(15px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-right h5 {
      font-weight: 600;
      color: #333;
      margin-bottom: 30px;
    }

    .form-floating label {
      color: #777;
    }

    .btn-login {
      background: linear-gradient(90deg, #00b09b, #96c93d);
      border: none;
      transition: all 0.3s ease;
      font-weight: 600;
    }

    .btn-login:hover {
      background: linear-gradient(90deg, #96c93d, #00b09b);
      transform: scale(1.02);
    }

    .form-check-label {
      color: #555;
    }

    @media (max-width: 768px) {
      .login-left {
        display: none;
      }

      .login-right {
        border-radius: 20px;
      }
    }
  </style>
</head>

<body>
  <div class="login-wrapper">
    <div class="login-card">

      <!-- Left side: Logo and title -->
      <div class="login-left">
        <img src="pics/DMMMSUlogosignup.png" alt="Logo">
        <h3 class="fw-bold">FEAST DMMMSU-NLUC</h3>
        <p class="mt-2">Faculty Effectiveness and Assessment System with Tracking and Analytics</p>
      </div>

      <!-- Right side: Login form -->
      <div class="login-right">
        <h5 class="text-center">Login to Your Account</h5>

        <?php if (isset($_SESSION['msg'])): ?>
          <script>
            document.addEventListener("DOMContentLoaded", function() {
              let message = <?= json_encode($_SESSION['msg']) ?>;
              let icon = 'error';
              let title = 'Login Failed';

              if (message.toLowerCase().includes('inactive')) {
                title = 'Account Inactive';
              }

              Swal.fire({
                icon: icon,
                title: title,
                text: message,
                confirmButtonColor: '#198754'
              });
            });
          </script>
          <?php
          unset($_SESSION['msg']);
          unset($_SESSION['login_failed']);
          ?>
        <?php endif; ?>

        <form method="post" action="login.php" class="needs-validation" novalidate>
          <div class="form-floating mb-3">
            <input type="text" name="idnumber" class="form-control" id="idnumber"
              value="<?= (isset($_COOKIE['remember_idnumber']) && !isset($_SESSION['login_failed'])) ? $_COOKIE['remember_idnumber'] : '' ?>"
              placeholder="ID Number" pattern="^[0-9\-]+$" required>
            <label for="idnumber">ID Number</label>
            <div class="invalid-feedback">Please enter a valid ID number.</div>
          </div>

          <div class="form-floating mb-3">
            <input type="password" name="password" class="form-control" id="password"
              value="<?= (isset($_COOKIE['remember_password']) && !isset($_SESSION['login_failed'])) ? $_COOKIE['remember_password'] : '' ?>"
              placeholder="Password" required>
            <label for="password">Password</label>
            <div class="invalid-feedback">Please enter your password.</div>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe"
              <?= isset($_COOKIE['remember_idnumber']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="rememberMe">Remember my ID</label>
          </div>

          <button class="btn btn-login text-white w-100 py-2" name="login">Login</button>
        </form>
      </div>

    </div>
  </div>

  <!-- Vendor JS -->
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
<?php unset($_SESSION['login_failed']); ?>
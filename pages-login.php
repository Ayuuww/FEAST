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
      background: #ffffff;
      /* Solid white background to blend with logos */
      font-family: 'Poppins', sans-serif;
      color: #333;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-container {
      width: 100%;
      max-width: 450px;
      padding: 40px;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
      border: 1px solid #f0f0f0;
      text-align: center;
    }

    .logo-container {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 20px;
      margin-bottom: 20px;
    }

    .logo-container img {
      height: 80px;
      width: auto;
      object-fit: contain;
    }

    .login-header h4 {
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 5px;
      letter-spacing: 0.5px;
    }

    .login-header p {
      font-size: 0.85rem;
      color: #666;
      margin-bottom: 30px;
      line-height: 1.4;
    }

    .form-floating label {
      color: #888;
      font-size: 0.95rem;
    }

    .form-control {
      border: 1px solid #ddd;
      border-radius: 8px;
    }

    .form-control:focus {
      border-color: #00b09b;
      box-shadow: 0 0 0 0.2rem rgba(0, 176, 155, 0.15);
    }

    .btn-login {
      background: #00b09b;
      background: linear-gradient(90deg, #00b09b, #96c93d);
      border: none;
      color: white;
      font-weight: 600;
      font-size: 1rem;
      padding: 12px;
      border-radius: 8px;
      transition: all 0.3s ease;
      margin-top: 10px;
    }

    .btn-login:hover {
      background: linear-gradient(90deg, #009987, #85b836);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 176, 155, 0.3);
    }

    .form-check-label {
      color: #555;
      font-size: 0.9rem;
    }

    .forgot-password-link {
      color: #00b09b;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .forgot-password-link:hover {
      color: #008a79;
      text-decoration: underline;
    }

    .toggle-password {
      color: #adb5bd;
      transition: color 0.3s ease;
    }

    .toggle-password:hover {
      color: #00b09b;
    }

    /* Modal Styling */
    .modal-content.custom-modal {
      border-radius: 12px;
      border: none;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>

  <div class="login-container">

    <div class="logo-container">
      <img src="pics/DMMMSUlogosignup.png" alt="DMMMSU Logo">
      <img src="pics/CISlogo.png" alt="CIS Logo">
    </div>

    <div class="login-header">
      <h4>FEASTa</h4>
      <p>Faculty Effectiveness and Assessment System with Tracking and Analytics <br> <strong>DMMMSU-NLUC</strong></p>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          let message = <?= json_encode($_SESSION['msg'] ?? '') ?>;
          let type = <?= json_encode($_SESSION['msg_type'] ?? 'error') ?>;
          let title = "Login Failed";
          let icon = "error";

          if (type === "inactive") {
            title = "Account Inactive";
            icon = "warning";
          } else if (type === "error") {
            title = "Invalid Login";
            icon = "error";
          }

          Swal.fire({
            title: title,
            text: message,
            icon: icon,
            confirmButtonColor: '#00b09b',
            timer: 4000,
            timerProgressBar: true
          });
        });
      </script>
      <?php
      unset($_SESSION['msg']);
      unset($_SESSION['login_failed']);
      unset($_SESSION['msg_type']);
      ?>
    <?php endif; ?>

    <form method="post" action="login.php" class="needs-validation" novalidate>
      <div class="form-floating mb-3 text-start">
        <input type="text" name="idnumber" class="form-control" id="idnumber"
          value="<?= isset($_SESSION['entered_idnumber']) ? htmlspecialchars($_SESSION['entered_idnumber']) : ((isset($_COOKIE['remember_idnumber']) && !isset($_SESSION['login_failed'])) ? htmlspecialchars($_COOKIE['remember_idnumber']) : '') ?>"
          placeholder="ID Number" pattern="^[0-9\-]+$" required>
        <label for="idnumber">ID Number</label>
        <div class="invalid-feedback">Please enter a valid ID number.</div>
      </div>

      <div class="form-floating mb-3 position-relative text-start">
        <input type="password" name="password" class="form-control" id="password"
          value="<?= (isset($_COOKIE['remember_password']) && !isset($_SESSION['login_failed'])) ? htmlspecialchars($_COOKIE['remember_password']) : '' ?>"
          placeholder="Password" required>
        <label for="password">Password</label>

        <span class="toggle-password" onclick="togglePassword()"
          style="position:absolute; top:50%; right:15px; transform:translateY(-50%); cursor:pointer; font-size: 1.2rem;">
          <i class="bi bi-eye-fill" id="togglePasswordIcon"></i>
        </span>
        <div class="invalid-feedback">Please enter your password.</div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check text-start">
          <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe"
            <?= isset($_COOKIE['remember_idnumber']) ? 'checked' : '' ?>>
          <label class="form-check-label" for="rememberMe">Remember me</label>
        </div>

        <a href="#" class="forgot-password-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
          Forgot Password?
        </a>
      </div>

      <button class="btn btn-login w-100" name="login">Sign In</button>
    </form>

  </div>

  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0 pb-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center py-4 px-4">
          <i class="bi bi-shield-lock mb-3" style="font-size: 3.5rem; color: #00b09b;"></i>
          <h4 class="fw-bold mb-3" style="color: #333;">Forgot your password?</h4>
          <p class="fs-6" style="color: #666; line-height: 1.5;">
            For security reasons, please contact your <strong>Administrator</strong> or proceed directly to the <strong>Registrar's Office</strong> to request a password reset.
          </p>
        </div>
        <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
          <button type="button" class="btn btn-login text-white px-5 rounded-pill m-0" data-bs-dismiss="modal">I Understand</button>
        </div>
      </div>
    </div>
  </div>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    function togglePassword() {
      const password = document.getElementById("password");
      const icon = document.getElementById("togglePasswordIcon");

      if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("bi-eye-fill");
        icon.classList.add("bi-eye-slash-fill");
      } else {
        password.type = "password";
        icon.classList.remove("bi-eye-slash-fill");
        icon.classList.add("bi-eye-fill");
      }
    }
  </script>

</body>

</html>
<?php unset($_SESSION['login_failed']); ?>
<?php unset($_SESSION['entered_idnumber']); ?>
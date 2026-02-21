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
      background: #636161ff;
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

    /* NEW: Forgot Password Link Styling */
    .forgot-password-link {
      color: #00b09b;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .forgot-password-link:hover {
      color: #96c93d;
      text-decoration: underline;
    }

    /* NEW: Modal Custom Styling */
    .modal-content.custom-modal {
      border-radius: 15px;
      border: none;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 768px) {
      .login-card {
        flex-direction: column;
      }

      .login-left {
        width: 100%;
        display: block;
        border-radius: 20px 20px 0 0;
        padding: 40px 20px;
      }

      .login-right {
        width: 100%;
        border-radius: 0 0 20px 20px;
        padding: 40px 20px;
      }
    }
  </style>
</head>

<body>
  <div class="login-wrapper">
    <div class="login-card">

      <div class="login-left">
        <img src="pics/DMMMSUlogosignup.png" alt="Logo">
        <h3 class="fw-bold">FEASTa DMMMSU-NLUC</h3>
        <p class="mt-2">Faculty Effectiveness and Assessment System with Tracking and Analytics</p>
      </div>

      <div class="login-right">
        <h5 class="text-center">Login to Your Account</h5>

        <?php if (isset($_SESSION['msg'])): ?>
          <script>
            document.addEventListener("DOMContentLoaded", function() {
              let message = <?= json_encode($_SESSION['msg'] ?? '') ?>;
              let type = <?= json_encode($_SESSION['msg_type'] ?? 'error') ?>;
              let title = "Login Failed";
              let icon = "error";

              // Determine popup type
              if (type === "inactive") {
                title = "Account Inactive";
                icon = "warning";
              } else if (type === "error") {
                title = "Invalid Login";
                icon = "error";
              }

              // SweetAlert2 popup
              Swal.fire({
                title: title,
                text: message,
                icon: icon,
                confirmButtonColor: '#198754',
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
          <div class="form-floating mb-3">
            <input type="text" name="idnumber" class="form-control" id="idnumber"
              value="<?=
                      isset($_SESSION['entered_idnumber'])
                        ? htmlspecialchars($_SESSION['entered_idnumber'])
                        : ((isset($_COOKIE['remember_idnumber']) && !isset($_SESSION['login_failed']))
                          ? $_COOKIE['remember_idnumber']
                          : '')
                      ?>"
              placeholder="ID Number" pattern="^[0-9\-]+$" required>
            <label for="idnumber">ID Number</label>
            <div class="invalid-feedback">Please enter a valid ID number.</div>
          </div>

          <div class="form-floating mb-3 position-relative">
            <input type="password" name="password" class="form-control" id="password"
              value="<?= (isset($_COOKIE['remember_password']) && !isset($_SESSION['login_failed'])) ? $_COOKIE['remember_password'] : '' ?>"
              placeholder="Password" required>
            <label for="password">Password</label>

            <span class="toggle-password" onclick="togglePassword()"
              style="position:absolute; top:50%; right:15px; transform:translateY(-50%); cursor:pointer; font-size: 1.1rem; color: #6c757d;">
              <i class="bi bi-eye-fill" id="togglePasswordIcon"></i>
            </span>

            <div class="invalid-feedback">Please enter your password.</div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe"
                <?= isset($_COOKIE['remember_idnumber']) ? 'checked' : '' ?>>
              <label class="form-check-label" for="rememberMe">Remember my ID</label>
            </div>

            <a href="#" class="forgot-password-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
              Forgot Password?
            </a>
          </div>

          <button class="btn btn-login text-white w-100 py-2" name="login">Login</button>
        </form>
      </div>

    </div>
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
          <p class="fs-6" style="color: #666;">
            For security reasons, please <strong>contact your administrator</strong> or proceed directly to the <strong>Registrar's Office</strong> to request a password reset.
          </p>
        </div>
        <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
          <button type="button" class="btn btn-login text-white px-5 rounded-pill" data-bs-dismiss="modal">I Understand</button>
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
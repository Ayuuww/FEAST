<?php
session_start();
include 'conn/conn.php'; // DB connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>FEAST - Login</title>
  <?php include 'header.php' ?>

  <style>
    body,
    html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Segoe UI', sans-serif;
    }

    .login-screen {
      display: flex;
      height: 100vh;
      overflow: hidden;
    }

    .login-panel {
      width: 40%;
      min-width: 320px;
      max-width: 450px;
      background: #fff;
      border-radius: 12px 0 0 12px;
      padding: 40px 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      box-shadow: 2px 0 12px rgba(0, 0, 0, 0.1);
      z-index: 10;
    }

    .login-logo {
      width: 100px;
      height: auto;
      margin-bottom: 20px;
    }

    .login-panel h2 {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 30px;
    }

    .login-form {
      width: 100%;
    }

    .login-input {
      width: 100%;
      padding: 14px 16px;
      margin-bottom: 20px;
      background: #f2f2f2;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 13px;
      letter-spacing: 1px;
    }

    .login-input:focus {
      outline: 2px solid #1e90ff;
    }

    .remember-me {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .remember-me input {
      margin-right: 8px;
    }

    .login-btn {
      width: 100%;
      padding: 12px;
      background-color: #28a745;
      border: none;
      border-radius: 6px;
      color: white;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: 0.2s ease-in-out;
    }

    .login-btn:hover {
      background-color: #218838;
    }

    .video-panel {
      width: 100%;
      position: relative;
    }

    .video-panel video {
      object-fit: cover;
      width: 100%;
      height: 100%;
    }

    @media (max-width: 768px) {
      .login-screen {
        flex-direction: column;
      }

      .login-panel {
        width: 100% !important;
        border-radius: 0;
        box-shadow: none;
      }

      .video-panel {
        display: none !important;
      }
    }
  </style>
</head>

<body>

  <main>
    <section class="login-screen">

      <!-- Left Login Panel -->
      <div class="login-panel">
        <!-- Title -->
        <h1 style="font-size: 30px; font-weight: bold; margin-bottom: 10px; text-transform: uppercase;">
          FEAST DMMMSU-NLUC
        </h1>

        <!-- Logo -->
        <img src="pics/DMMMSUlogo.png" alt="Logo" class="login-logo mt-5"> <!-- Set your own logo path -->

        <h2>Sign in</h2>

        <form class="login-form" method="POST" action="login-copy.php" novalidate>
          <!-- ID Number -->
          <input type="text" name="idnumber" class="login-input" placeholder="USERNAME"
            pattern="^[0-9\-]+$" required
            value="<?= (isset($_COOKIE['remember_idnumber']) && !isset($_SESSION['login_failed'])) ? $_COOKIE['remember_idnumber'] : '' ?>">

          <!-- Password -->
          <input type="password" name="password" class="login-input" placeholder="PASSWORD"
            required
            value="<?= (isset($_COOKIE['remember_password']) && !isset($_SESSION['login_failed'])) ? $_COOKIE['remember_password'] : '' ?>">

          <!-- Remember Me -->
          <div class="remember-me">
            <input type="checkbox" name="remember" id="rememberMe"
              <?= isset($_COOKIE['remember_idnumber']) ? 'checked' : '' ?>>
            <label for="rememberMe">Stay signed in</label>
          </div>

          <!-- Login Button -->
          <button class="login-btn" name="login">Login</button>
        </form>
      </div>

      <!-- Right Video Background Panel -->
      <div class="video-panel">
        <video autoplay muted loop playsinline poster="media/static-image.jpg">
          <source src="media/log-in.mp4" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>
    </section>
  </main>

  <!-- ======= Footer ======= -->
  <?php include 'footer.php'?>
  <!-- End Footer -->

  <?php if (isset($_SESSION['msg'])): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          icon: "error",
          title: "Login Failed",
          text: <?= json_encode($_SESSION['msg']) ?>,
          confirmButtonColor: '#3085d6'
        });
      });
    </script>
    <?php unset($_SESSION['msg']);
    unset($_SESSION['login_failed']); ?>
  <?php endif; ?>

</body>

</html>
<?php unset($_SESSION['login_failed']); ?>
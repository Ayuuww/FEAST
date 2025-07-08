<?php
session_start();
$message = $_SESSION['error_message'] ?? "An unknown error occurred.";
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Evaluation Error</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.1);
      padding: 2rem;
    }
    .error-icon {
      font-size: 3rem;
      color: #dc3545;
    }
    .btn-back {
      margin-top: 1.5rem;
      padding: 0.5rem 1.5rem;
      font-size: 1rem;
      border-radius: 50px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card text-center mx-auto" style="max-width: 500px;">
      <div class="error-icon mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
      </div>
      <h4 class="mb-3 text-danger">Oops! Something went wrong</h4>
      <div class="alert alert-warning" role="alert">
        <?= htmlspecialchars($message) ?>
      </div>
      <a href="student-evaluate.php" class="btn btn-secondary btn-back">
        <i class="bi bi-arrow-left"></i> Go Back to Evaluation
      </a>
    </div>
  </div>
</body>
</html>

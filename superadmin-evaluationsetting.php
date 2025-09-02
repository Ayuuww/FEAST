<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $semester = $_POST['semester'];
    $academic_year = $_POST['academic_year'];

    // Get the current user's ID and role from the session
    $userId = $_SESSION['idnumber'];
    $userRole = $_SESSION['role'];

    // Fetch the old settings to log the change
    $current = mysqli_query($conn, "SELECT * FROM evaluation_settings WHERE id = 1");
    $setting = mysqli_fetch_assoc($current);
    $old_semester = $setting['semester'];
    $old_year = $setting['academic_year'];

    // Replace old setting or insert new one
    $stmt = $conn->prepare("REPLACE INTO evaluation_settings (id, semester, academic_year) VALUES (1, ?, ?)");
    $stmt->bind_param("ss", $semester, $academic_year);
    $stmt->execute();
    
    // Log the activity to the activity_logs table
    $activity_description = "Updated evaluation settings from '{$old_semester} - {$old_year}' to '{$semester} - {$academic_year}'";
    $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, role, activity) VALUES (?, ?, ?)");
    $log_stmt->bind_param("sss", $userId, $userRole, $activity_description);
    $log_stmt->execute();
    $log_stmt->close();
    
    $_SESSION['msg'] = "Evaluation settings updated!";
    header("Location: superadmin-evaluationsetting.php?success=1");
    exit();
}

// Fetch current settings
$current = mysqli_query($conn, "SELECT * FROM evaluation_settings WHERE id = 1");
$setting = mysqli_fetch_assoc($current);
$current_semester = $setting['semester'];
$current_year = $setting['academic_year'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Evaluation Setting </title>

  <?php include 'header.php' ?>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
  </style>
  
</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Setting of Evaluation</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Evaluation</li>
          <li class="breadcrumb-item active">Setting</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row align-items-center justify-content-center">
        <div class="col-md-4">
          <div class="card p-4">
            <?php if (!empty($success_msg)): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success_msg) ?>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
              </div>
            <?php endif; ?>
            <h5 class="text-center"><strong>Set Default Evaluation Period</strong></h5>

            <form method="POST" action="">
              <div class="mb-3">
                <div class="form-floating">
                  <select class="form-select" id="semester" name="semester" required>
                    <option value="1st Semester" <?= $current_semester == '1st Semester' ? 'selected' : '' ?>>1st Semester</option>
                    <option value="2nd Semester" <?= $current_semester == '2nd Semester' ? 'selected' : '' ?>>2nd Semester</option>
                    <option value="Summer" <?= $current_semester == 'Summer' ? 'selected' : '' ?>>Summer</option>
                  </select>
                  <label for="semester" class="form-label">Semester</label>
                </div>
              </div>
              <div class="mb-3">
                <div class="form-floating">
                  <input type="text" class="form-control" id="academic_year" name="academic_year" required placeholder="e.g. 2025-2026" value="<?= $current_year ?>">
                  <label for="academic_year" class="form-label">Academic Year</label>
                </div>
              </div>
              <div class="d-grid gap-2 col-6 mx-auto">
                <button type="submit" class="btn btn-success" onclick="" id="save_settings">Save Settings</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php' ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    // Auto dismiss bootstrap alert
    setTimeout(() => {
      const alert = document.querySelector('.alert');
      if (alert) {
        alert.classList.remove('show');
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 500); // optional DOM cleanup
      }
    }, 3000);

    // Show SweetAlert on successful save
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Evaluation settings have been updated.',
        timer: 2000,
        showConfirmButton: false,
      }).then(() => {
        // Clean up the URL so alert doesn't reappear on refresh
        const cleanUrl = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
      });
    }
  </script>

</body>

</html>
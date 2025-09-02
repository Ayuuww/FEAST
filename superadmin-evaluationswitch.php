<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

// Get the super admin's ID from the session
$superadminId = $_SESSION['idnumber'];

// Fetch current evaluation status
$eval_status = 'off';
$status_query = mysqli_query($conn, "SELECT status FROM evaluation_switch LIMIT 1");
if ($row = mysqli_fetch_assoc($status_query)) {
    $eval_status = $row['status'];
}

// Handle toggle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = isset($_POST['status']) && $_POST['status'] === 'on' ? 'on' : 'off';
    
    // Log the activity
    $activity_description = "Evaluation turned " . ($new_status === 'on' ? 'on' : 'off');
    $role = 'superadmin';

    // Check your activity_logs table's user_id type. If it's an INT, use 'i'. If VARCHAR, use 's'.
    // Assuming it is VARCHAR based on your session ID.
    $log_stmt = $conn->prepare("INSERT INTO activity_logs (timestamp, role, activity, user_id) VALUES (NOW(), ?, ?, ?)");
    $log_stmt->bind_param("sss", $role, $activity_description, $superadminId);
    $log_stmt->execute();
    $log_stmt->close();

    // Check if a row exists in evaluation_switch
    $check_stmt = $conn->prepare("SELECT id FROM evaluation_switch LIMIT 1");
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update the existing row
        $update_stmt = $conn->prepare("UPDATE evaluation_switch SET status = ?, user_id = ?");
        // Check the data type of evaluation_switch.user_id. It should match superadmin.idnumber.
        // Assuming it is VARCHAR.
        $update_stmt->bind_param("ss", $new_status, $superadminId); 
        $update_stmt->execute(); // This is line 44
        $update_stmt->close();
    } else {
        // Insert a new row if one doesn't exist
        $insert_stmt = $conn->prepare("INSERT INTO evaluation_switch (status, user_id) VALUES (?, ?)");
        // Check the data type of evaluation_switch.user_id. It should be VARCHAR.
        $insert_stmt->bind_param("ss", $new_status, $superadminId);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    
    $check_stmt->close();

    $eval_status = $new_status; // Update local variable for display
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Evaluation On/Off </title>

  <?php include 'header.php' ?>

</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Evaluation On/Off</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Evaluation</li>
          <li class="breadcrumb-item active">On/Off</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12">
          <div class="card shadow-sm">
            <div class="card-body text-center p-4">
              <h5 class="card-title">Evaluation Switch</h5>

              <!-- Status Message -->
              <?php if ($eval_status === 'off'): ?>
                <div class="alert alert-warning">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>
                  Evaluation is currently <strong>CLOSED</strong>.
                </div>
              <?php else: ?>
                <div class="alert alert-success">
                  <i class="bi bi-check-circle-fill me-2"></i>
                  Evaluation is currently <strong>OPEN</strong>.
                </div>
              <?php endif; ?>

              <!-- Toggle Form -->
              <form method="POST">
                <input type="hidden" name="status" id="statusHidden">
                <div class="form-check form-switch d-flex justify-content-center align-items-center gap-3 mt-3">
                  <input class="form-check-input fs-4" type="checkbox" id="evaluationToggle"
                    <?= $eval_status === 'on' ? 'checked' : '' ?>>
                  <label class="form-check-label fs-5" for="evaluationToggle">
                    <?= $eval_status === 'on' ? 'Turn OFF Evaluation' : 'Turn ON Evaluation' ?>
                  </label>
                </div>
              </form>
              
            </div>
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
    document.getElementById('evaluationToggle').addEventListener('change', function(e) {
      e.preventDefault();

      const isChecked = this.checked;
      const form = this.closest('form');

      Swal.fire({
        title: `Are you sure you want to ${isChecked ? 'TURN ON' : 'TURN OFF'} the evaluation?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isChecked ? '#198754' : '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${isChecked ? 'turn ON' : 'turn OFF'}`
      }).then((result) => {
        if (result.isConfirmed) {
          // Set hidden input value and submit the form
          document.getElementById('statusHidden').value = isChecked ? 'on' : 'off';
          form.submit();
        } else {
          // Revert the checkbox toggle
          this.checked = !isChecked;
        }
      });
    });
  </script>

</body>

</html>
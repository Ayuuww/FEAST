<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

$superadminId = $_SESSION['idnumber'];

// Fetch evaluation switch (single row)
$query = mysqli_query($conn, "SELECT * FROM evaluation_switch LIMIT 1");
$switch = mysqli_fetch_assoc($query);

// If table empty, insert default row
if (!$switch) {
  mysqli_query($conn, "INSERT INTO evaluation_switch (status, start_date, end_date) VALUES ('off', NULL, NULL)");
  $query = mysqli_query($conn, "SELECT * FROM evaluation_switch LIMIT 1");
  $switch = mysqli_fetch_assoc($query);
}

// Get ID of the switch, default to 1 if not set (for the UPDATE query)
$switch_id = $switch['id'] ?? 1;

$current_status = $switch['status'];
$start_date = $switch['start_date'];
$end_date = $switch['end_date'];

$today = date('Y-m-d');

// Auto OFF if date passed AND current status is on
if ($current_status === 'on' && $end_date && $today > $end_date) {
  mysqli_query($conn, "UPDATE evaluation_switch SET status='off' WHERE id=$switch_id");
  $current_status = 'off';

  mysqli_query(
    $conn,
    "INSERT INTO activity_logs (timestamp, role, activity, user_id)
         VALUES (NOW(), 'system', 'Evaluation auto turned OFF (end date reached)', 'system')"
  );
}

// Handle toggle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ✅ Get the ID of the switch to ensure we update the right row
  $posted_switch_id = $_POST['switch_id'];

  // Get status from the hidden input, which is updated by the toggle's JS
  $new_status = $_POST['status'];
  $new_start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : NULL;
  $new_end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;

  // Update table
  $stmt = $conn->prepare("UPDATE evaluation_switch 
         SET status=?, start_date=?, end_date=?, user_id=? WHERE id=?");
  $stmt->bind_param("ssssi", $new_status, $new_start_date, $new_end_date, $superadminId, $posted_switch_id);
  $stmt->execute();
  $stmt->close();

  // Log action
  $msg = "Evaluation turned $new_status | Start: $new_start_date | End: $new_end_date";
  $log = $conn->prepare("INSERT INTO activity_logs (timestamp, role, activity, user_id)
         VALUES (NOW(), 'superadmin', ?, ?)");
  $log->bind_param("ss", $msg, $superadminId);
  $log->execute();
  $log->close();

  // Add a success message to session to show after redirect
  $_SESSION['msg'] = 'Evaluation settings updated successfully!';
  $_SESSION['msg_type'] = 'success'; // for a success alert

  header("Location: superadmin-evaluationswitch.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>

  <style>
    .form-switch .form-check-label {
      cursor: pointer;
    }
  </style>
</head>

<body>

  <?php include 'superadmin-header.php'; ?>
  <?php include 'superadmin-sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Evaluation Switch</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Evaluation</li>
          <li class="breadcrumb-item active">Switch</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row justify-content-center">
        <div class="col-lg-6">

          <div class="card shadow-sm p-4">
            <h5 class="card-title text-center pb-0 fs-4">Evaluation Control</h5>
            <p class="text-center small">Set the start and end dates and toggle the evaluation status.</p>

            <div id="statusAlert" class="alert <?= $current_status === 'on' ? 'alert-success' : 'alert-warning' ?> text-center">
              Evaluation is <strong><?= $current_status === 'on' ? 'OPEN' : 'CLOSED' ?></strong>
            </div>

            <form method="POST" id="evaluationForm">

              <input type="hidden" name="status" id="statusHidden" value="<?= $current_status ?>">
              <input type="hidden" name="switch_id" value="<?= $switch_id ?>">

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="start_date" class="form-label">Start Date</label>
                  <input type="date" name="start_date" id="start_date" class="form-control" required value="<?= $start_date ?>">
                </div>

                <div class="col-md-6 mb-3">
                  <label for="end_date" class="form-label">End Date</label>
                  <input type="date" name="end_date" id="end_date" class="form-control" required value="<?= $end_date ?>">
                </div>
              </div>

              <hr>

              <div class="form-check form-switch d-flex justify-content-center align-items-center gap-3 mt-3">
                <input class="form-check-input fs-3" type="checkbox" id="evaluationToggle" <?= $current_status === 'on' ? 'checked' : '' ?>>
                <label class="form-check-label fs-5" for="evaluationToggle" id="toggleLabel">
                  <?= $current_status === 'on' ? 'Evaluation is ON' : 'Evaluation is OFF' ?>
                </label>
              </div>

              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-success btn-lg">Update Settings</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include 'footer.php'; ?>

  <script src="assets/js/main.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {

      // ✅ ADD THIS PHP BLOCK TO TRIGGER THE TOAST
      <?php
      if (isset($_SESSION['msg'])) {
        $message = htmlspecialchars($_SESSION['msg'], ENT_QUOTES);
        $icon = $_SESSION['msg_type'] === 'success' ? 'success' : 'error';

        echo "
          Swal.fire({
            toast: true,
            position: 'top',
            icon: '$icon',
            title: '$message',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
          });
        ";

        unset($_SESSION['msg']);
        unset($_SESSION['msg_type']);
      }
      ?>

      const toggle = document.getElementById('evaluationToggle');
      const toggleLabel = document.getElementById('toggleLabel');
      const statusHidden = document.getElementById('statusHidden');
      const statusAlert = document.getElementById('statusAlert');
      const form = document.getElementById('evaluationForm');

      // 1. Update the hidden field and labels when the toggle is clicked
      toggle.addEventListener('change', function() {
        if (this.checked) {
          toggleLabel.textContent = 'Evaluation is ON';
          statusHidden.value = 'on';
        } else {
          toggleLabel.textContent = 'Evaluation is OFF';
          statusHidden.value = 'off';
        }
      });

      // 2. Intercept the form submission
      form.addEventListener('submit', function(e) {
        // Prevent the form from submitting immediately
        e.preventDefault();

        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        // --- Validation ---
        if (!startDate || !endDate) {
          Swal.fire({
            icon: 'error',
            title: 'Missing Dates',
            text: 'Please select both a start and end date.'
          });
          return; // Stop execution
        }

        if (endDate < startDate) {
          Swal.fire({
            icon: 'error',
            title: 'Invalid Date Range',
            text: 'The end date cannot be before the start date.'
          });
          return; // Stop execution
        }

        // --- Confirmation ---
        const newStatus = toggle.checked ? 'ON' : 'OFF';
        const confirmColor = toggle.checked ? '#198754' : '#d33';

        Swal.fire({
          title: `Confirm Changes`,
          html: `You are about to set the evaluation to <strong>${newStatus}</strong>.<br><br>` +
            `<strong>Start:</strong> ${startDate}<br>` +
            `<strong>End:</strong> ${endDate}`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: confirmColor,
          cancelButtonColor: '#6c757d',
          confirmButtonText: `Yes, update settings`
        }).then((result) => {
          if (result.isConfirmed) {
            // If confirmed, *now* submit the form
            form.submit();
          }
        });
      });

    });
  </script>

</body>

</html>
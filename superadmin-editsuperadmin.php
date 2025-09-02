<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Ensure user is logged in as superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "Superadmin ID is missing.";
  exit();
}

$superadmin_id = $_GET['id'];

// Fetch superadmin details
$stmt = $conn->prepare("SELECT * FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $superadmin_id);
$stmt->execute();
$result = $stmt->get_result();
$superadmin = $result->fetch_assoc();

if (!$superadmin) {
  echo "Superadmin not found.";
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_status = $_POST['status'];

  $stmt = $conn->prepare("UPDATE superadmin SET status = ? WHERE idnumber = ?");
  $stmt->bind_param("ss", $new_status, $superadmin_id);
  $stmt->execute();

  header("Location: superadmin-editsuperadmin.php?id=$superadmin_id&update=success");
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit Admin Status</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include 'header.php'; ?>
</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Superadmin Status</h1>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="card col-md-6 p-4">
          <h5 class="card-title">Superadmin Details</h5>

          <!-- Display update message -->
          <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
            <script>
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Superadmin status updated successfully!',
                timer: 2000,
                showConfirmButton: false,
              });

              // Optional: Remove ?update=success from URL after showing the alert
              if (window.location.search.includes('update=success')) {
                const url = new URL(window.location);
                url.searchParams.delete('update');
                window.history.replaceState({}, document.title, url.pathname + url.search);
              }
            </script>
          <?php endif; ?>

          <?php if ($superadmin): ?>
            <form method="POST">
              <div class="mb-3 form-floating">
                <input type="text" class="form-control" value="<?= $superadmin['first_name'] . ' ' . $superadmin['mid_name'] . ' ' . $superadmin['last_name']; ?>" disabled>
                <label>Full Name</label>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" value="<?= $superadmin['idnumber'] ?>" disabled>
                    <label>ID Number</label>
                  </div>
                </div>

                <div class="col-md-6 mb-3 ">
                  <div class="form-floating">
                    <select name="status" class="form-select" required>
                      <option value="active" <?= $superadmin['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                      <option value="inactive" <?= $superadmin['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                    <label>Status</label>
                  </div>
                </div>
              </div>

              <button type="submit" class="btn btn-success">Update Status</button>
              <a href="superadmin-superadminlist.php" class="btn btn-secondary">Back</a>
            </form>
          <?php else: ?>
            <div class="alert alert-danger">Superadmin not found.</div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main><!-- end main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php' ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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

</body>

</html>
<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Superadmin login check
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Get superadmin ID
if (!isset($_GET['id'])) {
  echo "Superadmin ID is missing.";
  exit();
}

$superadmin_id = $_GET['id'];

// Fetch superadmin info
$stmt = $conn->prepare("SELECT * FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $superadmin_id);
$stmt->execute();
$result = $stmt->get_result();
$superadmin = $result->fetch_assoc();

if (!$superadmin) {
  echo "Superadmin not found.";
  exit();
}

// Fetch dropdowns from adds table
$positions = [];
$ranks = [];
$departments = [];

$pos_result = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL ORDER BY position_name ASC");
while ($row = $pos_result->fetch_assoc()) {
  $positions[] = $row['position_name'];
}

$rank_result = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL ORDER BY rank_name ASC");
while ($row = $rank_result->fetch_assoc()) {
  $ranks[] = $row['rank_name'];
}

$dept_result = $conn->query("SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL ORDER BY department_name ASC");
while ($row = $dept_result->fetch_assoc()) {
  $departments[] = $row['department_name'];
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_status = $_POST['status'];
  $new_position = $_POST['position'];
  $new_rank = $_POST['faculty_rank'];
  $new_department = $_POST['department'];
  $new_faculty = $_POST['faculty'];

  $stmt = $conn->prepare("UPDATE superadmin 
                          SET status = ?, position = ?, faculty_rank = ?, department = ?, faculty = ? 
                          WHERE idnumber = ?");
  $stmt->bind_param("ssssss", $new_status, $new_position, $new_rank, $new_department, $new_faculty, $superadmin_id);
  $stmt->execute();

  header("Location: superadmin-editsuperadmin.php?id=$superadmin_id&update=success");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Superadmin Status</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="superadmin-superadminlist.php">Superadmin</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Superadmin Information</h5>

              <?php if ($superadmin): ?>
                <form method="POST">

                  <!-- Full Name -->
                  <div class="mb-3">
                    <div class="form-floating">
                      <input type="text" class="form-control" value="<?= $superadmin['first_name'] . ' ' . $superadmin['mid_name'] . ' ' . $superadmin['last_name'] ?>" disabled>
                      <label>Full Name</label>
                    </div>
                  </div>

                  <div class="row">
                    <!-- ID Number -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <input type="text" class="form-control" value="<?= $superadmin['idnumber'] ?>" disabled>
                        <label>ID Number</label>
                      </div>
                    </div>

                    <!-- Department -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <input type="text" class="form-control" value="<?php echo $superadmin['department']; ?>" disabled>
                        <label class="form-label">Department</label>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="status" class="form-select" required>
                          <option value="active" <?= $superadmin['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                          <option value="inactive" <?= $superadmin['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <label>Status</label>
                      </div>
                    </div>

                    <!-- Position -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="position" class="form-select" required>
                          <option value="" disabled>-- Select Position --</option>
                          <?php foreach ($positions as $pos): ?>
                            <option value="<?= htmlspecialchars($pos) ?>" <?= $superadmin['position'] === $pos ? 'selected' : '' ?>>
                              <?= htmlspecialchars($pos) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <label>Position</label>
                      </div>
                    </div>

                    <!-- Current Rank -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <input type="text" class="form-control"
                          value="<?php echo !empty($superadmin['faculty_rank']) ? $superadmin['faculty_rank'] : 'Not Set'; ?>"
                          disabled>
                        <label class="form-label">Current Faculty Rank</label>
                      </div>
                    </div>

                    <!-- Faculty Rank -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="faculty_rank" class="form-select" required>
                          <option value="" disabled>-- Select Rank --</option>
                          <?php foreach ($ranks as $rank): ?>
                            <option value="<?= htmlspecialchars($rank) ?>" <?= $superadmin['faculty_rank'] === $rank ? 'selected' : '' ?>>
                              <?= htmlspecialchars($rank) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <label>Faculty Rank</label>
                      </div>
                    </div>

                    
                  </div>

                  <button type="submit" class="btn btn-success">Update  Status</button>
                  <a href="superadmin-superadminlist.php" class="btn btn-secondary">Back</a>
                </form>
              <?php else: ?>
                <div class="alert alert-danger">Superadmin not found.</div>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!-- end main -->

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

  <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Updated Successfully',
        text: 'Superadmin info has been updated!',
        timer: 2000,
        showConfirmButton: false
      });
    </script>
  <?php endif; ?>

</body>

</html>
<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
  header("Location: superadmin-department-info.php");
  exit();
}

// Fetch current record
$stmt = $conn->prepare("SELECT * FROM department_info WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$dept = $result->fetch_assoc();

if (!$dept) {
  header("Location: superadmin-department-info.php");
  exit();
}

$updated = false;

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $department_name = $_POST['department_name'];
  $college_name = $_POST['college_name'];
  $website = $_POST['website'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];

  $stmt = $conn->prepare("UPDATE department_info SET department_name=?, college_name=?, website=?, phone=?, email=? WHERE id=?");
  $stmt->bind_param("sssssi", $department_name, $college_name, $website, $phone, $email, $id);
  $stmt->execute();

  $updated = true; // trigger sweetalert2
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <script src="sweetalert2/sweetalert2@11.js"></script>
</head>

<body>

  <?php include 'superadmin-header.php'; ?>
  <?php include 'superadmin-sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Department Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Department Information</li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Edit Department Info</h5>

              <form method="POST" class="p-4 r">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Department Name</label>
                    <input type="text" name="department_name" value="<?= htmlspecialchars($dept['department_name']) ?>" class="form-control" disabled>
                  </div>

                  <div class="col-md-8">
                    <label class="form-label fw-semibold">College Name</label>
                    <input type="text" name="college_name" value="<?= htmlspecialchars($dept['college_name']) ?>" class="form-control" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Website</label>
                    <input type="text" name="website" value="<?= htmlspecialchars($dept['website']) ?>" class="form-control" placeholder="Website">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($dept['phone']) ?>" class="form-control" placeholder="Phone">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($dept['email']) ?>" class="form-control" placeholder="Email">
                  </div>
                </div>

                <div class="mt-4 text-end">
                  <button type="submit" class="btn btn-success btn-sm px-4">
                    <i class="bi bi-check-circle"></i> Update
                  </button>
                  <a href="superadmin-department-info.php" class="btn btn-secondary btn-sm px-4">
                    <i class="bi bi-x-circle"></i> Cancel
                  </a>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

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
  <script src="chart/chart.js"></script>

  <!-- SweetAlert2 Success Message -->
  <?php if ($updated): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Updated Successfully!',
        text: 'Department information has been updated.',
        confirmButtonColor: '#198754',
        confirmButtonText: 'OK'
      }).then(() => {
        window.location.href = 'superadmin-department-info.php';
      });
    </script>
  <?php endif; ?>

</body>

</html>
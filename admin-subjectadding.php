<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// ✅ Get the admin's department + position
$admin_info_stmt = $conn->prepare("SELECT department, position FROM admin WHERE idnumber = ? LIMIT 1");
$admin_info_stmt->bind_param("s", $admin_id);
$admin_info_stmt->execute();
$admin_result = $admin_info_stmt->get_result();
$admin_data = $admin_result->fetch_assoc();
$admin_info_stmt->close();

$admin_dept = $admin_data['department'] ?? '';
$admin_position = $admin_data['position'] ?? '';

// ✅ Restrict allowed positions
$allowed_positions = ['Dean', 'Chair Person', 'Program Chair'];
if (!in_array($admin_position, $allowed_positions)) {
  $_SESSION['access_denied'] = "Access denied. Your position ($admin_position) is not allowed to add subjects.";
  header("Location: admin-dashboard.php");
  exit();
}

// Get real faculty in same department
$faculty_result = mysqli_query($conn, "SELECT idnumber, first_name, mid_name, last_name 
                                       FROM faculty 
                                       WHERE status = 'active' AND department = '$admin_dept'");

// Get admin-as-faculty in same department (optional, if needed)
$admin_result = mysqli_query($conn, "SELECT idnumber, first_name, mid_name, last_name 
                                     FROM admin 
                                     WHERE department = '$admin_dept'");

$faculty_data = [];
$faculty_ids = [];

while ($row = mysqli_fetch_assoc($faculty_result)) {
  $faculty_data[] = $row;
  $faculty_ids[] = $row['idnumber'];
}

$admin_data = [];
while ($row = mysqli_fetch_assoc($admin_result)) {
  $admin_data[] = $row;
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

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Subject</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Subject</li>
          <li class="breadcrumb-item active">Add Subject</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <?php if (isset($_SESSION['msg'])): ?>
      <script>
        Swal.fire({
          icon: '<?= $_SESSION['msg_type'] ?? 'info' ?>', // success, error, warning, info
          title: '<?= $_SESSION['msg_type'] === "success" ? "Success!" : "Notice" ?>',
          text: '<?= $_SESSION['msg'] ?>',
          confirmButtonColor: '#3085d6'
        });
      </script>
      <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <!-- Super Admin Creation Section -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add New Subject</h5>

              <?php if (isset($_SESSION['msg'])): ?>
                <script>
                  Swal.fire({
                    icon: '<?= $_SESSION['msg_type'] ?? 'info' ?>', // success, error, warning, info
                    title: '<?= $_SESSION['msg_type'] === "success" ? "Success!" : "Notice" ?>',
                    text: '<?= $_SESSION['msg'] ?>',
                    confirmButtonColor: '#3085d6'
                  });
                </script>
                <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
              <?php endif; ?>


              <form class="row g-3 needs-validation " novalidate method="post" action="addsubject.php">

                <!-- Subject Code -->
                <div class="col-md-2">
                  <div class="form-floating">
                    <input type="text" name="code" class="form-control" id="idnumber" placeholder="Subject Code" required>
                    <label for="idnumber" class="form-label">Subject Code</label>
                  </div>
                </div>

                <!-- Subject title -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="title" class="form-control" placeholder="Descriptive Title" required>
                    <label class="form-label">Descriptive Title</label>
                  </div>
                </div>

                <!-- Faculty Name Dropdown -->
                <div class="col-md-4">
                  <div class="form-floating">
                    <select name="faculty_id" class="form-select" required>
                      <option value="">-- Select Faculty --</option>

                      <?php foreach ($faculty_data as $f): ?>
                        <option value="<?= $f['idnumber'] ?>"><?= $f['last_name'] ?>, <?= $f['first_name'] ?></option>
                      <?php endforeach; ?>

                      <!-- <?php foreach ($admin_data as $a): ?>
                        <?php if (in_array($a['idnumber'], $faculty_ids)) continue; ?>
                        <option value="<?= $a['idnumber'] ?>"><?= $a['last_name'] ?>, <?= $a['first_name'] ?></option>
                      <?php endforeach; ?> -->

                    </select>
                    <label for="faculty_id">Faculty</label>
                  </div>
                </div>


                <!-- Admin-as-Faculty Dropdown -->
                <!-- <div class="col-md-2">
                      <div class="form-floating">
                        <select name="admin_id" class="form-select">
                          <option value="">-- Select Admin as Faculty --</option>
                          <?php while ($a = mysqli_fetch_assoc($admin_result)): ?>
                            <option value="<?= $a['idnumber'] ?>"><?= $a['first_name'] ?> <?= $a['last_name'] ?></option>
                          <?php endwhile; ?>
                        </select>
                        <label for="admin_id">Faculty as Admin</label>
                      </div>
                    </div> -->


                <!-- Submit -->
                <div class="col-4 offset-4">
                  <button class="btn btn-success w-100" name="addsubject" id="create" type="submit">Add Subject</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Super Admin Creation Section -->



  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php' ?>>
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

  <script>
    const facultySelect = document.querySelector('select[name="faculty_id"]');
    const adminSelect = document.querySelector('select[name="admin_id"]');

    facultySelect.addEventListener('change', () => {
      if (facultySelect.value) {
        adminSelect.disabled = true;
      } else {
        adminSelect.disabled = false;
      }
    });

    adminSelect.addEventListener('change', () => {
      if (adminSelect.value) {
        facultySelect.disabled = true;
      } else {
        facultySelect.disabled = false;
      }
    });
  </script>

  <script>
    setTimeout(() => {
      const alert = document.querySelector('.alert');
      if (alert) {
        alert.classList.remove('show');
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 500); // Remove from DOM
      }
    }, 5000); // 5 seconds
  </script>

  <?php if (isset($_SESSION['msg'])): ?>
    <script>
      Swal.fire({
        icon: '<?= $_SESSION['msg_type'] ?? 'info' ?>',
        title: '<?= $_SESSION['msg_type'] === "success" ? "Success!" : "Notice" ?>',
        text: <?= json_encode($_SESSION['msg']) ?>,
        confirmButtonColor: '#3085d6'
      });
    </script>
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
  <?php endif; ?>


</body>

</html>
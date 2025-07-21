<?php

session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get the admin's department
$dept_query = mysqli_query($conn, "SELECT department FROM admin WHERE idnumber = '$admin_id' LIMIT 1");
$admin_dept = '';

if ($dept_query && mysqli_num_rows($dept_query) > 0) {
  $admin_data = mysqli_fetch_assoc($dept_query);
  $admin_dept = $admin_data['department'];
}

// Get real faculty in same department
$faculty_result = mysqli_query($conn, "SELECT idnumber, first_name, mid_name, last_name 
                                       FROM faculty 
                                       WHERE status = 'active' AND department = '$admin_dept'");

// Get admin-as-faculty in same department (excluding self if needed)
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
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Adding Subject</title>

  <?php include 'header.php' ?>

</head>

<body>

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <!-- Evaluate Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-evaluate.php">
              <i class="bi bi-circle"></i><span>Form</span>
            </a>
          </li>
          <li>
            <a href="admin-evaluatedfaculty.php">
              <i class="bi bi-circle"></i><span>Evaluated Faculty</span>
            </a>
          </li>
        </ul>
      </li><!-- End Evaluate Nav -->

      <!-- Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" data-bs-target="#subject" data-bs-toggle="collapse" href="#">
          <i class="ri-book-line"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="subject" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-subjectlist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="admin-subjectadding.php" class="active">
              <i class="bi bi-circle"></i><span>Add Subject</span>
            </a>
          </li>
        </ul>
      </li><!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-studentsubject.php">
          <i class="ri-book-fill"></i>
          <span>Assign Subject</span>
        </a>
      </li><!-- End Student Subject Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-evaluatedsubject.php">
          <i class="bi bi-book-fill"></i>
          <span>Subject Evaluated</span>
        </a>
      </li><!-- End Profile Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-user-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End Sign out Nav -->


    </ul>

  </aside><!-- End Sidebar-->

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

    <?php
    if (isset($_SESSION['msg'])) {
      $msg = $_SESSION['msg'];
      $type = $_SESSION['msg_type'] ?? 'info'; // Can be 'success', 'warning', 'danger', 'info'
      echo "<div class='alert alert-$type alert-dismissible fade show mt-3' role='alert'>
                $msg
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
      unset($_SESSION['msg'], $_SESSION['msg_type']);
    }
    ?>

    <!-- Super Admin Creation Section -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add New Subject</h5>

              <!-- <?php if (isset($_SESSION['msg'])): ?>
                  <div class="alert alert-info alert-dismissible fade show mt-3 mb-3" role="alert" style="margin: auto;">
                    <?= $_SESSION['msg']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  <?php unset($_SESSION['msg']); ?>
                <?php endif; ?> -->

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

                      <?php foreach ($admin_data as $a): ?>
                        <?php if (in_array($a['idnumber'], $faculty_ids)) continue; ?>
                        <option value="<?= $a['idnumber'] ?>"><?= $a['last_name'] ?>, <?= $a['first_name'] ?></option>
                      <?php endforeach; ?>

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

</body>

</html>
<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Superadmin login check
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}


// Get admin ID
if (!isset($_GET['id'])) {
  echo "Admin ID is missing.";
  exit();
}

$admin_id = $_GET['id'];

// Fetch admin info
$stmt = $conn->prepare("SELECT * FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
  echo "Admin not found.";
  exit();
}

// Fetch admin positions from 'adds' table
$positions = [];
$position_result = $conn->query("SELECT position_name FROM adds WHERE position_name IS NOT NULL ORDER BY position_name ASC");
while ($row = $position_result->fetch_assoc()) {
  $positions[] = $row['position_name'];
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_status = $_POST['status'];
  $new_position = $_POST['position'];

  $stmt = $conn->prepare("UPDATE admin SET status = ?, position = ? WHERE idnumber = ?");
  $stmt->bind_param("sss", $new_status, $new_position, $admin_id);
  $stmt->execute();

  header("Location: superadmin-editadmin.php?id=$admin_id&update=success");
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
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="superadmin-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <!-- Subject Nav -->
      <!-- <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-subjectlist.php" >
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-subjectadding.php">
                <i class="bi bi-circle"></i><span>Add Subject</span>
              </a>
            </li>
          </ul>
        </li> -->
      <!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <!-- <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-studentsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Assign Subject</span>
          </a>
        </li> -->
      <!-- End Student Subject Nav -->

      <!-- Reports Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="reports" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-individualreport.php">
              <i class="bi bi-circle"></i><span>Invidiual Report</span>
            </a>
          </li>
          <li>
            <a href="superadmin-acknowledgementreport.php">
              <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
            </a>
          </li>
          <li>
            <a href="superadmin-pastrecords.php">
              <i class="bi bi-circle"></i><span>Past Record</span>
            </a>
          </li>
        </ul>
      </li><!-- End Reports Nav -->

      <!-- Evaluation Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#evaluation" data-bs-toggle="collapse" href="#">
          <i class="ri-settings-4-line"></i><span>Evaluation</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="evaluation" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-evaluationsetting.php">
              <i class="bi bi-circle"></i><span>Setting</span>
            </a>
          </li>
          <li>
            <a href="superadmin-evaluationswitch.php">
              <i class="bi bi-circle"></i><span>On/Off</span>
            </a>
          </li>
        </ul>
      </li><!-- End Evalutaion Nav -->

      <li class="nav-heading">Account Management</li>

      <!-- Management Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="superadmin-addsmanagement.php">
          <i class="ri-settings-line"></i>
          <span>Manage</span>
        </a>
      </li><!-- End Management Nav -->

      <!-- Faculty Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people-fill"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-facultylist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-facultycreation.php">
              <i class="bi bi-circle"></i><span>Add New Faculty</span>
            </a>
          </li>
        </ul>
      </li><!-- End Faculty Nav -->

      <!-- Student Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-studentlist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-studentcreation.php">
              <i class="bi bi-circle"></i><span>Add New Student</span>
            </a>
          </li>
        </ul>
      </li><!-- End Student Nav -->

      <!-- Admin Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="admin-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-adminlist.php" class="active">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-admincreation.php">
              <i class="bi bi-circle"></i><span>Add New Admin</span>
            </a>
          </li>
        </ul>
      </li><!-- End Admin Nav -->

      <!-- Super Admin Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-fill"></i><span>Super Admin</span><i
            class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-superadminlist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-superadmincreation.php">
              <i class="bi bi-circle"></i><span>Add New SuperAdmin</span>
            </a>
          </li>
        </ul>
      </li><!-- End Super Admin Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="superadmin-user-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End Sign Out Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Admin Status</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="superadmin-adminlist.php">Admin</a></li>
          <li class="breadcrumb-item">List</li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Admin Information</h5>

              <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
              <?php endif; ?>

              <?php if ($admin): ?>
                <form method="POST">

                  <div class="col-md-12 mb-3">
                    <div class="form-floating">
                      <input type="text" class="form-control" value="<?php echo $admin['first_name'] . ' ' . $admin['mid_name'] . ' ' . $admin['last_name']; ?>" disabled>
                      <label class="form-label">Full Name</label>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <input type="text" class="form-control" value="<?php echo $admin['idnumber']; ?>" disabled>
                        <label class="form-label">ID Number</label>
                      </div>
                    </div>

                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <input type="text" class="form-control" value="<?php echo $admin['department']; ?>" disabled>
                        <label class="form-label">Department</label>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="status" class="form-select" required>
                          <option value="active" <?php if ($admin['status'] === 'active') echo 'selected'; ?>>Active</option>
                          <option value="inactive" <?php if ($admin['status'] === 'inactive') echo 'selected'; ?>>Inactive</option>
                        </select>
                        <label class="form-label">Status</label>
                      </div>
                    </div>

                    <!-- Position -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="position" class="form-select" required>
                          <option value="" disabled>Select Position</option>
                          <?php foreach ($positions as $position): ?>
                            <option value="<?= htmlspecialchars($position) ?>" <?= $admin['position'] === $position ? 'selected' : '' ?>>
                              <?= htmlspecialchars($position) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <label class="form-label">Position</label>
                      </div>
                    </div>
                  </div>

                  <button type="submit" class="btn btn-success">Update</button>
                  <a href="superadmin-adminlist.php" class="btn btn-secondary">Back</a>

                </form>
              <?php else: ?>
                <div class="alert alert-danger">Admin not found or has been removed.</div>
              <?php endif; ?>


            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- ======= Footer ======= -->
  <?php include 'footer.php'?>
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
    document.querySelector('select[name="role"]').addEventListener('change', function() {
      const adminOptions = document.getElementById('admin-options');
      const selects = adminOptions.querySelectorAll('select');

      if (this.value === 'admin') {
        adminOptions.style.display = 'block';
        selects.forEach(s => s.setAttribute('required', 'required'));
      } else {
        adminOptions.style.display = 'none';
        selects.forEach(s => s.removeAttribute('required'));
      }
    });

    window.addEventListener('DOMContentLoaded', function() {
      const roleSelect = document.querySelector('select[name="role"]');
      const adminOptions = document.getElementById('admin-options');
      const selects = adminOptions.querySelectorAll('select');

      if (roleSelect.value === 'admin') {
        adminOptions.style.display = 'block';
        selects.forEach(s => s.setAttribute('required', 'required'));
      } else {
        adminOptions.style.display = 'none';
        selects.forEach(s => s.removeAttribute('required'));
      }
    });
  </script>

  <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Updated Successfully',
        text: 'Admin info has been updated!',
        confirmButtonColor: '#198754' // Bootstrap green
      }).then(() => {
        // Remove the query param from URL without reloading the page
        if (history.pushState) {
          const url = new URL(window.location);
          url.searchParams.delete('update');
          window.history.pushState({}, '', url);
        }
      });

      Swal.fire({
        icon: 'success',
        title: 'Updated Successfully',
        text: 'Admin info has been updated!',
        timer: 2000,
        showConfirmButton: false
      });
    </script>
  <?php endif; ?>

</body>

</html>
<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

$idnumber = $_SESSION['idnumber'];
$success_msg = "";
$error_msg = "";

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update_profile'])) {
        $first_name = $_POST['first_name'];
        $mid_name = $_POST['mid_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        $stmt = $conn->prepare("UPDATE superadmin SET first_name=?, mid_name=?, last_name=?, email=? WHERE idnumber=?");
        $stmt->bind_param("sssss", $first_name, $mid_name, $last_name, $email, $idnumber);
        if ($stmt->execute()) {
            $success_msg = "Profile updated successfully.";
        } else {
            $error_msg = "Failed to update profile.";
        }
    }

    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $retype = $_POST['renew_password'];

        $query = $conn->prepare("SELECT password FROM superadmin WHERE idnumber = ?");
        $query->bind_param("s", $idnumber);
        $query->execute();
        $query->bind_result($db_password);
        $query->fetch();
        $query->close();

        if ($current !== $db_password) {
            $error_msg = "Incorrect current password.";
        } elseif ($new !== $retype) {
            $error_msg = "New passwords do not match.";
        } else {
            $update = $conn->prepare("UPDATE superadmin SET password=? WHERE idnumber=?");
            $update->bind_param("ss", $new, $idnumber);
            if ($update->execute()) {
                $success_msg = "Password updated successfully.";
            } else {
                $error_msg = "Failed to update password.";
            }
        }
    }
}

// Fetch current profile data
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, email, role FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $idnumber);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>FEAST / Profile</title>
    <?php include 'header.php'?>
  </head>

  <body>

    <?php include 'superadmin-header.php'?>

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
        <li class="nav-item">
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
        </li><!-- End Subject Nav -->

        <!-- Student Subject Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-studentsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Assign Subject</span>
          </a>
        </li><!-- End Student Subject Nav -->

        <!-- Report Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-reports.php">
            <i class="bi bi-journal-text"></i>
            <span>Reports</span>
          </a>
        </li><!-- End Report Nav -->
        
        <li class="nav-heading">Account Management</li>

        <!-- Faculty Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-people-fill"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
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
              <a href="superadmin-studentapproval.php">
                <i class="bi bi-circle"></i><span>Approval</span>
              </a>
            </li>
          </ul>
        </li><!-- End Student Nav -->

        <!-- Admin Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="admin-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-adminlist.php" >
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
          <a class="nav-link collapse" href="superadmin-user-profile.php">
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
        <h1>Profile</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Profile</li>
          </ol>
        </nav>
      </div>

      <section class="section profile">
        <div class="row">

          <!-- EDIT PROFILE - LEFT -->
          <div class="col-lg-6">
            <div class="card">
              <div class="card-body pt-3">
                <h5 class="card-title">Edit Profile</h5>
                <form method="POST">
                  <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Last Name</label>
                    <div class="col-sm-8">
                      <input name="last_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['last_name']) ?>">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">First Name</label>
                    <div class="col-sm-8">
                      <input name="first_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['first_name']) ?>">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Middle Name</label>
                    <div class="col-sm-8">
                      <input name="mid_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['mid_name']) ?>">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Email</label>
                    <div class="col-sm-8">
                      <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Role</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control text-capitalize" readonly value="<?= htmlspecialchars($data['role']) ?>">
                    </div>
                  </div>
                  <div class="text-center">
                    <button type="submit" name="update_profile" class="btn btn-success">Save Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- CHANGE PASSWORD - RIGHT -->
          <div class="col-lg-6">
            <div class="card">
              <div class="card-body pt-3">
                <h5 class="card-title">Change Password</h5>
                <form method="POST">
                  <div class="row mb-3">
                    <label class="col-sm-5 col-form-label">Current Password</label>
                    <div class="col-sm-7">
                      <input name="current_password" type="password" class="form-control" required>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-5 col-form-label">New Password</label>
                    <div class="col-sm-7">
                      <input name="new_password" type="password" class="form-control" required>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-5 col-form-label">Re-enter New Password</label>
                    <div class="col-sm-7">
                      <input name="renew_password" type="password" class="form-control" required>
                    </div>
                  </div>
                  <div class="text-center" style="margin-top: 123px;">
                    <button type="submit" name="change_password" class="btn btn-success">Change Password</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

        </div>
      </section>
    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
      <div class="copyright">
        &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script data-cfasync="false" src="assets/js/email-decode.min.js"></script>
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

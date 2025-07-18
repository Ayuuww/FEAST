<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a faculty
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
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

        $stmt = $conn->prepare("UPDATE faculty SET first_name=?, mid_name=?, last_name=? WHERE idnumber=?");
        $stmt->bind_param("sssss", $first_name, $mid_name, $last_name, $idnumber);
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

        $query = $conn->prepare("SELECT password FROM faculty WHERE idnumber = ?");
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
            $update = $conn->prepare("UPDATE faculty SET password=? WHERE idnumber=?");
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
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, role, faculty_rank, department FROM faculty WHERE idnumber = ?");
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

    
    <?php include 'faculty-header.php'?>
    
    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-dashboard.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li><!-- End Dashboard Nav -->

        <!-- Evaluate Nav -->
        <!-- <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="faculty-peer-evaluate.php" >
                <i class="bi bi-circle"></i><span>Form</span>
              </a>
            </li>
            <li>
              <a href="faculty-peer-evaluatedpeer.php" >
                <i class="bi bi-circle"></i><span>Evaluated Peer</span>
              </a>
            </li>
          </ul>
        </li>End Evaluate Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-evaluatedsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Subject</span>
          </a>
        </li><!-- End Profile Nav -->

        <!-- <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-records.php">
            <i class="ri-record-circle-fill"></i>
            <span>Records</span>
          </a>
        </li>End Records Nav -->

        <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapse" href="faculty-user-profile.php">
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
        <h1>Profile</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li><li class="breadcrumb-item active">Profile</li></ol></nav>
      </div>

      <section class="section profile">
        <div class="row justify-content-center">
          <div class="col-xl-8">
            <?php if ($success_msg): ?><div class="alert alert-success"><?= $success_msg ?></div><?php endif; ?>
            <?php if ($error_msg): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>

            <div class="card">
              <div class="card-body pt-3">
                <ul class="nav nav-tabs nav-tabs-bordered">
                  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button></li>
                  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button></li>
                </ul>

                <div class="tab-content pt-2">
                  <!-- Profile Edit Tab -->
                  <div class="tab-pane fade show active pt-3" id="profile-edit">
                    <form method="POST">
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">Last Name</label>
                        <div class="col-md-8 col-lg-9"><input name="last_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['last_name']) ?>"></div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">First Name</label>
                        <div class="col-md-8 col-lg-9"><input name="first_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['first_name']) ?>"></div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">Middle Name</label>
                        <div class="col-md-8 col-lg-9"><input name="mid_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['mid_name']) ?>"></div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">Department/College</label>
                        <div class="col-md-8 col-lg-9"><input type="text" class="form-control text-capitalize" readonly value="<?= htmlspecialchars($data['department']) ?>"></div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">Academic Rank</label>
                        <div class="col-md-8 col-lg-9"><input type="text" class="form-control text-capitalize" readonly value="<?= htmlspecialchars($data['faculty_rank']) ?>"></div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">Role</label>
                        <div class="col-md-8 col-lg-9"><input type="text" class="form-control text-capitalize" readonly value="<?= htmlspecialchars($data['role']) ?>"></div>
                      </div>
                      <div class="text-center">
                        <button type="submit" name="update_profile" class="btn btn-success">Save Changes</button>
                      </div>
                    </form>
                  </div>

                  <!-- Change Password Tab -->
                  <div class="tab-pane fade pt-3" id="profile-change-password">
                    <form method="POST">
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                        <div class="col-md-8 col-lg-9"><input name="current_password" type="password" class="form-control" required></div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">New Password</label>
                        <div class="col-md-8 col-lg-9"><input name="new_password" type="password" class="form-control" required></div>
                      </div>
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                        <div class="col-md-8 col-lg-9"><input name="renew_password" type="password" class="form-control" required></div>
                      </div>
                      <div class="text-center">
                        <button type="submit" name="change_password" class="btn btn-success">Change Password</button>
                      </div>
                    </form>
                  </div>

                </div><!-- End tab-content -->
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

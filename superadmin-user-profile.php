<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

$idnumber = $_SESSION['idnumber'];
$swal = ""; // For SweetAlert2 messages

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['update_profile'])) {
    $first_name    = $_POST['first_name'];
    $mid_name      = $_POST['mid_name'];
    $last_name     = $_POST['last_name'];
    $faculty_rank  = $_POST['faculty_rank'];
    $position      = $_POST['position'];

    // Always update superadmin
    $stmt = $conn->prepare("UPDATE superadmin 
                    SET first_name=?, mid_name=?, last_name=?, faculty_rank=? 
                    WHERE idnumber=?");
    $stmt->bind_param("sssss", $first_name, $mid_name, $last_name, $faculty_rank, $idnumber);
    $superadmin_updated = $stmt->execute();
    $stmt->close();

    // Check if superadmin is also a faculty
    $check = $conn->prepare("SELECT idnumber FROM faculty WHERE idnumber=?");
    $check->bind_param("s", $idnumber);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      // Update faculty too
      $stmt = $conn->prepare("UPDATE faculty 
                        SET first_name=?, mid_name=?, last_name=?, faculty_rank=?
                        WHERE idnumber=?");
      $stmt->bind_param("sssss", $first_name, $mid_name, $last_name, $faculty_rank, $idnumber);
      $stmt->execute();
      $stmt->close();
    }
    $check->close();

    $swal = "Swal.fire({
      icon: 'success',
      title: 'Profile Updated',
      text: 'Your profile has been updated successfully!',
      confirmButtonColor: '#198754'
    });";
  }


  if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $retype  = $_POST['renew_password'];

    // Get password from superadmin
    $query = $conn->prepare("SELECT password FROM superadmin WHERE idnumber=?");
    $query->bind_param("s", $idnumber);
    $query->execute();
    $query->bind_result($db_password);
    $query->fetch();
    $query->close();

    if (!password_verify($current, $db_password)) {
      $swal = "Swal.fire({
          icon: 'error',
          title: 'Incorrect Password',
          text: 'Your current password is incorrect.',
          confirmButtonColor: '#dc3545'
        });";
    } elseif ($new !== $retype) {
      $swal = "Swal.fire({
          icon: 'warning',
          title: 'Password Mismatch',
          text: 'New passwords do not match.',
          confirmButtonColor: '#ffc107'
        });";
    } else {
      $hashed_new = password_hash($new, PASSWORD_DEFAULT);

      // Update superadmin
      $update = $conn->prepare("UPDATE superadmin SET password=? WHERE idnumber=?");
      $update->bind_param("ss", $hashed_new, $idnumber);
      $superadmin_updated = $update->execute();
      $update->close();

      // Check if also faculty
      $check = $conn->prepare("SELECT idnumber FROM faculty WHERE idnumber=?");
      $check->bind_param("s", $idnumber);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
        $update = $conn->prepare("UPDATE faculty SET password=? WHERE idnumber=?");
        $update->bind_param("ss", $hashed_new, $idnumber);
        $update->execute();
        $update->close();
      }
      $check->close();

      $swal = "Swal.fire({
          icon: 'success',
          title: 'Password Changed',
          text: 'Your password has been updated successfully!',
          confirmButtonColor: '#198754'
        });";
    }
  }
}

// Fetch profile data
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, role, faculty_rank, position FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $idnumber);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

  <style>
    /* 🌿 Modern Profile Page Design */
    body {
      background: #f8fafc;
    }

    .profile .card {
      border: none;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }

    .profile .card-body {
      padding: 2rem 2.5rem;
      background: #fff;
    }

    .profile .nav-tabs {
      border-bottom: none;
      background: #f1f5f9;
      border-radius: 10px;
      overflow: hidden;
    }

    .profile .nav-link {
      color: #6c757d;
      font-weight: 600;
      border: none;
      transition: 0.3s;
    }

    .profile .nav-link.active {
      background-color: #198754;
      color: #fff;
      border-radius: 8px;
    }

    .profile .form-control {
      border-radius: 12px;
      padding: 10px 14px;
      transition: all 0.2s ease;
    }

    .profile .form-control:focus {
      border-color: #198754;
      box-shadow: 0 0 0 0.15rem rgba(25, 135, 84, 0.25);
    }

    /* 💡 Readonly field design */
    .profile input[readonly] {
      background-color: #f3f4f6;
      border: 1px solid #dee2e6;
      color: #6c757d;
      cursor: not-allowed;
    }

    .profile input[readonly]:hover {
      background-color: #e9ecef;
    }

    .btn-success {
      border-radius: 12px;
      padding: 10px 25px;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-success:hover {
      background-color: #157347;
      transform: translateY(-2px);
    }

    .profile .form-label i {
      color: #198754;
      margin-right: 6px;
    }
  </style>

</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

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
      <div class="row justify-content-center">
        <div class="col-xl-6">
          <div class="card">
            <div class="card-body pt-3">
              <ul class="nav nav-tabs nav-tabs-bordered mb-4">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-edit"><i class="bi bi-pencil-square"></i> Edit Profile</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password"><i class="bi bi-shield-lock"></i> Change Password</button></li>
              </ul>

              <div class="tab-content pt-2">
                <!-- Profile Edit Tab -->
                <div class="tab-pane fade show active pt-3" id="profile-edit">
                  <form method="POST">

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-person-badge"></i> Last Name</label>
                      <div class="col-md-8 col-lg-9"><input name="last_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['last_name']) ?>"></div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-person"></i> First Name</label>
                      <div class="col-md-8 col-lg-9"><input name="first_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['first_name']) ?>"></div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-person-lines-fill"></i> Middle Name</label>
                      <div class="col-md-8 col-lg-9"><input name="mid_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['mid_name']) ?>"></div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-award"></i> Faculty Rank</label>
                      <div class="col-md-8 col-lg-9 position-relative">
                        <input name="faculty_rank" type="text" class="form-control text-capitalize"
                          value="<?= htmlspecialchars($data['faculty_rank']) ?>" readonly title="This field is managed by the system.">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-briefcase"></i> Designation</label>
                      <div class="col-md-8 col-lg-9 position-relative">
                        <input name="position" type="text" class="form-control text-capitalize"
                          value="<?= htmlspecialchars($data['position']) ?>" readonly title="This field is managed by the system.">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-person-gear"></i> Role</label>
                      <div class="col-md-8 col-lg-9"><input type="text" class="form-control text-capitalize" readonly value="<?= htmlspecialchars($data['role']) ?>"></div>
                    </div>

                    <div class="text-center mt-4">
                      <button type="submit" name="update_profile" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Save Changes</button>
                    </div>
                  </form>
                </div>

                <!-- Change Password Tab -->
                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <form method="POST">
                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-lock"></i> Current Password</label>
                      <div class="col-md-8 col-lg-9"><input name="current_password" type="password" class="form-control" required></div>
                    </div>
                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-shield-check"></i> New Password</label>
                      <div class="col-md-8 col-lg-9"><input name="new_password" type="password" class="form-control" required></div>
                    </div>
                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-repeat"></i> Re-enter New Password</label>
                      <div class="col-md-8 col-lg-9"><input name="renew_password" type="password" class="form-control" required></div>
                    </div>
                    <div class="text-center mt-4">
                      <button type="submit" name="change_password" class="btn btn-success"><i class="bi bi-arrow-repeat me-1"></i> Change Password</button>
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
  <?php include 'footer.php' ?>
  <!-- End Footer -->

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

  <!-- SweetAlert2 -->
  <script src="sweetalert2\sweetalert2@11.js"></script>

  <?php if (!empty($swal)): ?>
    <script>
      <?= $swal ?>
    </script>
  <?php endif; ?>

</body>

</html>
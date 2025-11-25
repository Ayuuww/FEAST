<?php
session_start();
include 'conn/conn.php';

// ✅ Check if logged in and role = student
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
  header("Location: pages-login.php");
  exit();
}

$idnumber = $_SESSION['idnumber'];
$success_msg = "";
$error_msg = "";

// ✅ Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $mid_name   = $_POST['mid_name'];
    $last_name  = $_POST['last_name'];
    $section    = $_POST['section'];

    $stmt = $conn->prepare("UPDATE student SET first_name=?, mid_name=?, last_name=?, section=? WHERE idnumber=?");
    $stmt->bind_param("sssss", $first_name, $mid_name, $last_name, $section, $idnumber);

    if ($stmt->execute()) {
      $success_msg = "Profile updated successfully.";
    } else {
      $error_msg = "Failed to update profile.";
    }
  }

  // ✅ Change password
  if (isset($_POST['change_password'])) {
    $current  = $_POST['current_password'];
    $new      = $_POST['new_password'];
    $retype   = $_POST['renew_password'];

    $query = $conn->prepare("SELECT password FROM student WHERE idnumber = ?");
    $query->bind_param("s", $idnumber);
    $query->execute();
    $query->bind_result($db_password);
    $query->fetch();
    $query->close();

    if (!password_verify($current, $db_password)) {
      $error_msg = "Incorrect current password.";
    } elseif ($new !== $retype) {
      $error_msg = "New passwords do not match.";
    } else {
      $hashed_new = password_hash($new, PASSWORD_DEFAULT);
      $update = $conn->prepare("UPDATE student SET password=? WHERE idnumber=?");
      $update->bind_param("ss", $hashed_new, $idnumber);
      if ($update->execute()) {
        $success_msg = "Password updated successfully.";
      } else {
        $error_msg = "Failed to update password.";
      }
    }
  }
}

// ✅ Fetch student data
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, role, section, college, program FROM student WHERE idnumber = ?");
$stmt->bind_param("s", $idnumber);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>

  <style>
    /* 🌿 Modern Student Profile Design (same as Superadmin) */
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

    .profile .form-control,
    .profile .form-select {
      border-radius: 12px;
      padding: 10px 14px;
      transition: all 0.2s ease;
    }

    .profile .form-control:focus,
    .profile .form-select:focus {
      border-color: #198754;
      box-shadow: 0 0 0 0.15rem rgba(25, 135, 84, 0.25);
    }

    /* 💡 Readonly inputs */
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

  <?php include 'student-header.php' ?>
  <?php include 'student-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="student-dashboard.php">Home</a></li>
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
                <!-- 🧾 Edit Profile -->
                <div class="tab-pane fade show active pt-3" id="profile-edit">
                  <form method="POST">
                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-person-badge"></i> Last Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="last_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['last_name']) ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-person"></i> First Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="first_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['first_name']) ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-person-lines-fill"></i> Middle Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="mid_name" type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($data['mid_name']) ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-grid-3x3-gap"></i> Section</label>
                      <div class="col-md-8 col-lg-9">
                        <select name="section" class="form-select text-capitalize" required>
                          <option value="" disabled <?= empty($data['section']) ? 'selected' : '' ?>>Select Section</option>
                          <?php
                          $result = mysqli_query($conn, "SELECT DISTINCT TRIM(section_name) AS section_name
                                FROM adds
                                WHERE section_name IS NOT NULL AND TRIM(section_name) <> ''
                                ORDER BY section_name ASC");
                          while ($row = mysqli_fetch_assoc($result)) {
                            $section = htmlspecialchars($row['section_name']);
                            $selected = ($data['section'] == $row['section_name']) ? 'selected' : '';
                            echo "<option value=\"$section\" $selected>$section</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-building"></i> College</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control text-capitalize" readonly value="<?= htmlspecialchars($data['college']) ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-building"></i> Program</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control text-capitalize" readonly value="<?= htmlspecialchars($data['program']) ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-md-4 col-lg-3 col-form-label"><i class="bi bi-person-gear"></i> Role</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control text-capitalize" readonly value="<?= htmlspecialchars($data['role']) ?>">
                      </div>
                    </div>

                    <div class="text-center mt-4">
                      <button type="submit" name="update_profile" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Save Changes</button>
                    </div>
                  </form>
                </div>

                <!-- 🔐 Change Password -->
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
  </main>

  <?php include 'footer.php' ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="sweetalert2/sweetalert2@11.js"></script>

  <?php if ($success_msg): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '<?= $success_msg ?>',
        confirmButtonColor: '#198754'
      });
    </script>
  <?php endif; ?>

  <?php if ($error_msg): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= $error_msg ?>',
        confirmButtonColor: '#dc3545'
      });
    </script>
  <?php endif; ?>
</body>

</html>
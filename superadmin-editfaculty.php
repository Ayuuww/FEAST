<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


// Check if superadmin is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Check if faculty ID is provided
if (!isset($_GET['id'])) {
  echo "Faculty ID is missing.";
  exit();
}

$faculty_id = $_GET['id'];

// Fetch faculty data
$stmt = $conn->prepare("SELECT * FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();

if (!$faculty && $_SERVER["REQUEST_METHOD"] != "POST") {
  echo "Faculty not found.";
  exit();
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $faculty_id = $_GET['id'];

  // Re-fetch faculty data in case it's not loaded yet
  $stmt = $conn->prepare("SELECT * FROM faculty WHERE idnumber = ?");
  $stmt->bind_param("s", $faculty_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $faculty = $result->fetch_assoc();

  if (!$faculty) {
    echo "Faculty not found.";
    exit();
  }

  $new_status = $_POST['status'];
  $new_role   = $_POST['role'];

  if ($new_role === 'admin') {
    // Check if already an admin
    $checkAdmin = $conn->prepare("SELECT idnumber FROM admin WHERE idnumber = ?");
    $checkAdmin->bind_param("s", $faculty_id);
    $checkAdmin->execute();
    $adminResult = $checkAdmin->get_result();

    if ($adminResult->num_rows > 0) {
      $_SESSION['msg'] = "This faculty is already an Admin.";
      header("Location: superadmin-editfaculty.php?id=$faculty_id");
      exit();
    }

    // Proceed to insert to admin if not yet
    $position = $_POST['position'] ?? '';
    $is_faculty = $_POST['is_faculty'] ?? 'no';

    $role = 'admin';
    $insertAdmin = $conn->prepare(" INSERT INTO admin (idnumber, first_name, mid_name, last_name, password, role, status, department, position, faculty_rank) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $insertAdmin->bind_param(
      "ssssssssss",
      $faculty['idnumber'],
      $faculty['first_name'],
      $faculty['mid_name'],
      $faculty['last_name'],
      $faculty['password'],
      $role,
      $new_status,
      $faculty['department'],
      $position,
      $is_faculty
    );

    try {
      $insertAdmin->execute();

      // Optional: Clear from faculty table
      $clearFaculty = $conn->prepare("UPDATE faculty SET password = '' WHERE idnumber = ?");
      $clearFaculty->bind_param("s", $faculty_id);
      $clearFaculty->execute();

      $_SESSION['msg'] = "Faculty successfully converted to Admin.";
      header("Location: superadmin-adminlist.php");
      exit();
    } catch (mysqli_sql_exception $e) {
      $_SESSION['msg'] = "Database Error: " . $e->getMessage();
      header("Location: superadmin-editfaculty.php?id=$faculty_id");
      exit();
    }
  } else {
    $faculty_rank = $_POST['faculty_rank'] ?? null;

    $stmt = $conn->prepare("UPDATE faculty SET status = ?, role = ?, faculty_rank = ? WHERE idnumber = ?");
    $stmt->bind_param("ssss", $new_status, $new_role, $faculty_rank, $faculty_id);
    $stmt->execute();

    $success = "Faculty updated successfully!";

    // Re-fetch updated faculty data
    $stmt = $conn->prepare("SELECT * FROM faculty WHERE idnumber = ?");
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculty = $result->fetch_assoc();
  }
}

// Fetch admin position form the 'adds' table
$positions = [];
$position_result = $conn->query("SELECT position_name FROM adds WHERE position_name IS NOT NULL");
while ($row = $position_result->fetch_assoc()) {
  $positions[] = $row['position_name'];
}

// Fetch faculty ranks from the 'adds' table
$rankQuery = $conn->query("SELECT rank_name FROM adds WHERE rank_name IS NOT NULL ORDER BY rank_name ASC");
$facultyRanks = [];
while ($row = $rankQuery->fetch_assoc()) {
  $facultyRanks[] = $row['rank_name'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit Faculty Status</title>
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
        <a class="nav-link collapse" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people-fill"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-facultylist.php" class="active">
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
        <a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="admin-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-adminlist.php">
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
      <h1>Edit Faculty Status</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="superadmin-facultylist.php">Faculty</a></li>
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
              <h5 class="card-title">Faculty Information</h5>

              <?php if (isset($success)): ?>
                <script>
                  Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: <?= json_encode($success) ?>,
                    timer: 2000,
                    showConfirmButton: false
                  });
                </script>
              <?php endif; ?>

              <?php if ($faculty): ?>
                <form method="POST">

                  <div class="col-md-12 mb-3">
                    <div class="form-floating">
                      <input type="text" class="form-control" value="<?php echo $faculty['first_name'] . ' ' . $faculty['mid_name'] . ' ' . $faculty['last_name']; ?>" disabled>
                      <label class="form-label">Full Name</label>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <input type="text" class="form-control" value="<?php echo $faculty['idnumber']; ?>" disabled>
                        <label class="form-label">ID Number</label>
                      </div>
                    </div>

                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <input type="text" class="form-control" value="<?php echo $faculty['department']; ?>" disabled>
                        <label class="form-label">Department</label>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="role" class="form-select" required>
                          <option value="faculty" <?php if ($faculty['role'] == 'faculty') echo 'selected'; ?>>Faculty</option>
                          <option value="admin">Admin</option>
                        </select>
                        <label class="form-label">Role</label>
                      </div>
                    </div>

                    <div class="col-md-6 mb-3">
                      <div class=" form-floating">
                        <select name="status" class="form-select" required>
                          <option value="active" <?php if ($faculty['status'] == 'active') echo 'selected'; ?>>Active</option>
                          <option value="inactive" <?php if ($faculty['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                        </select>
                        <label class="form-label">Current Status</label>
                      </div>
                    </div>
                  </div>

                  <div id="admin-options" style="display: none;">
                    <div class="mb-3 form-floating">
                      <select class="form-select" name="position" required>
                        <option value="" disabled selected>-- Select Position --</option>
                        <?php foreach ($positions as $position): ?>
                          <option value="<?= htmlspecialchars($position) ?>"><?= htmlspecialchars($position) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <label class="form-label">Position</label>
                    </div>


                    <div class="mb-3 form-floating">
                      <select class="form-select" name="is_faculty">
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                      </select>
                      <label class="form-label">Still a Faculty?</label>
                    </div>
                  </div>

                  <!-- Current Rank -->
                  <div class="col-md-12 mb-3">
                    <div class="form-floating">
                      <input type="text" class="form-control" value="<?php echo $faculty['faculty_rank'] ?? 'Not Set'; ?>" disabled>
                      <label class="form-label">Current Faculty Rank</label>
                    </div>
                  </div>

                  <!-- Promoting section -->
                  <div class="col-md-12 mb-3">
                    <div class="form-floating">
                      <select class="form-select" name="faculty_rank">
                        <option value="" disabled <?php if (empty($faculty['faculty_rank'])) echo 'selected'; ?>>Select Rank</option>
                        <?php foreach ($facultyRanks as $rank): ?>
                          <option value="<?= htmlspecialchars($rank) ?>" <?= ($faculty['faculty_rank'] ?? '') === $rank ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rank) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <label>Faculty Rank Promotion</label>
                    </div>
                  </div>

                  <button type="submit" class="btn btn-success">Update Status</button>
                  <a href="superadmin-facultylist.php" class="btn btn-secondary">Back</a>

                  <?php if (isset($_SESSION['msg'])): ?>
                    <script>
                      Swal.fire({
                        icon: 'info',
                        title: 'Notice',
                        text: <?= json_encode($_SESSION['msg']) ?>,
                        confirmButtonColor: '#3085d6'
                      });
                    </script>
                    <?php unset($_SESSION['msg']); ?>
                  <?php endif; ?>


                </form>
              <?php else: ?>
                <div class="alert alert-info">This faculty member has been moved to the admin list.</div>
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

</body>

</html>
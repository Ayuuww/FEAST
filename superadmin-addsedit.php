<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

$id = $_GET['id'] ?? '';
$type = $_GET['type'] ?? '';
$message = '';

if (!$id || !$type) {
  header("Location: superadmin-addsmanagement.php");
  exit();
}

switch ($type) {
  case 'Rank':
    $column = 'rank_name';
    break;
  case 'Position':
    $column = 'position_name';
    break;
  case 'Section':
    $column = 'section_name';
    break;
  case 'Department':
    $column = 'department_name';
    break;
  default:
    $column = '';
}

if (!$column) {
  header("Location: superadmin-addsmanagement.php");
  exit();
}

// Fetch current value
$stmt = $conn->prepare("SELECT $column FROM adds WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($current);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $new_value = trim($_POST['value']);
  if ($new_value) {
    // Check if new value already exists in the column (excluding current ID)
    $check = $conn->prepare("SELECT id FROM adds WHERE $column = ? AND id != ?");
    $check->bind_param("si", $new_value, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $message = "$type already exists!";
    } else {
      // Proceed with update
      $stmt = $conn->prepare("UPDATE adds SET $column = ? WHERE id = ?");
      $stmt->bind_param("si", $new_value, $id);
      if ($stmt->execute()) {
        $message = "$type updated successfully!";
        $current = $new_value;
      } else {
        $message = "Update failed.";
      }
      $stmt->close();
    }

    $check->close();
  }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit <?= htmlspecialchars($type) ?> | Superadmin</title>
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
        <a class="nav-link collapse" href="superadmin-addsmanagement.php">
          <i class="ri-settings-line"></i>
          <span>Manage</span>
        </a>
      </li><!-- End Management Nav -->

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
      <h1>Edit <?= htmlspecialchars($type) ?> Name</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="superadmin-addsmanagement.php">Manage</a></li>
          <li class="breadcrumb-item active">Edit <?= htmlspecialchars($type) ?></li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="card col-md-6">
          <div class="card-body pt-4">

            <form method="POST" class="row g-3">
              <div class="col-md-6">
                <label for="value" class="form-label">New <?= htmlspecialchars($type) ?> Name</label>
                <input type="text" name="value" class="form-control" value="<?= htmlspecialchars($current) ?>" required>
              </div>
              <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-success me-2">Update</button>
                <a href="superadmin-addsmanagement.php" class="btn btn-secondary">Back</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Bootstrap & other JS -->
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <?php if ($message): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          icon: '<?= strpos($message, "successfully") !== false ? "success" : "error" ?>',
          title: '<?= $type ?> Update',
          text: '<?= addslashes($message) ?>',
          timer: 2000,
          showConfirmButton: false,
        });
      });
    </script>
  <?php endif; ?>

</body>

</html>
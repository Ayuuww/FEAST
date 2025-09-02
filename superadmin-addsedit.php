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
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

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

  <!-- ======= Footer ======= -->
  <?php include 'footer.php'?>
  <!-- End Footer -->

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
<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

$id = $_GET['id'] ?? '';
$type = $_GET['type'] ?? '';
$message = '';

if (!$id || !$type) {
  header("Location: register-addsmanagement.php");
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
  case 'Program':
    $column = 'program_name';
    break;
  default:
    $column = '';
}

if (!$column) {
  header("Location: register-addsmanagement.php");
  exit();
}

// 🟢 Fetch current values
if ($type === 'Program') {
  $stmt = $conn->prepare("SELECT program_name, department_name FROM adds WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($current_program, $current_department);
  $stmt->fetch();
  $stmt->close();
} else {
  $stmt = $conn->prepare("SELECT $column FROM adds WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($current_value);
  $stmt->fetch();
  $stmt->close();
}

// 🟡 Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if ($type === 'Program') {
    $new_program = trim($_POST['program_name']);
    $new_department = trim($_POST['department_name']);

    if ($new_program && $new_department) {
      // Check for duplicates
      $check = $conn->prepare("SELECT id FROM adds WHERE LOWER(program_name)=LOWER(?) AND LOWER(department_name)=LOWER(?) AND id != ?");
      $check->bind_param("ssi", $new_program, $new_department, $id);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
        $message = "Program already exists in this department!";
      } else {
        $stmt = $conn->prepare("UPDATE adds SET program_name = ?, department_name = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_program, $new_department, $id);
        if ($stmt->execute()) {
          $message = "Program updated successfully!";
          $current_program = $new_program;
          $current_department = $new_department;
        } else {
          $message = "Update failed.";
        }
        $stmt->close();
      }
      $check->close();
    } else {
      $message = "Please fill in all fields.";
    }
  } else {
    $new_value = trim($_POST['value']);
    if ($new_value) {
      $check = $conn->prepare("SELECT id FROM adds WHERE $column = ? AND id != ?");
      $check->bind_param("si", $new_value, $id);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
        $message = "$type already exists!";
      } else {
        $new_value = trim($_POST['value']);
        if ($new_value) {

          // Check for duplicates (same name but different ID)
          $check = $conn->prepare("SELECT id FROM adds WHERE $column = ? AND id != ?");
          $check->bind_param("si", $new_value, $id);
          $check->execute();
          $check->store_result();

          if ($check->num_rows > 0) {
            $message = "$type already exists!";
          } else {

            // --- GET OLD VALUE BEFORE UPDATE ---
            $old_value_query = $conn->prepare("SELECT $column FROM adds WHERE id = ?");
            $old_value_query->bind_param("i", $id);
            $old_value_query->execute();
            $old_value_query->bind_result($old_value);
            $old_value_query->fetch();
            $old_value_query->close();

            // --- CASCADE UPDATE (IMPORTANT PART) ---
            // If editing department_name → update ALL rows with the same old department_name
            if ($column === 'department_name') {
              $stmt = $conn->prepare("UPDATE adds SET department_name = ? WHERE department_name = ?");
              $stmt->bind_param("ss", $new_value, $old_value);
            } else {
              // Normal update for Rank / Position / Section
              $stmt = $conn->prepare("UPDATE adds SET $column = ? WHERE id = ?");
              $stmt->bind_param("si", $new_value, $id);
            }

            if ($stmt->execute()) {
              $_SESSION['msg'] = "$type updated successfully!";
              $_SESSION['msg_type'] = "success";
              header("Location: register-addsmanagement.php");
              exit();
            } else {
              $message = "Update failed.";
            }
            $stmt->close();
          }
          $check->close();
        }
      }

      $check->close();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit <?= htmlspecialchars($type) ?> | register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include 'head.php'; ?>
</head>

<body>

  <?php include 'register-header.php' ?>
  <?php include 'register-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit <?= htmlspecialchars($type) ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="register-addsmanagement.php">Manage</a></li>
          <li class="breadcrumb-item active">Edit <?= htmlspecialchars($type) ?></li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="card col-md-6">
          <div class="card-body pt-4">
            <form method="POST" class="row g-3">

              <?php if ($type === 'Program'): ?>
                <div class="col-md-12">
                  <label class="form-label">Program Name</label>
                  <input type="text" name="program_name" class="form-control" value="<?= htmlspecialchars($current_program ?? '') ?>" required>
                </div>

                <div class="col-md-12">
                  <label class="form-label">Select College</label>
                  <select name="department_name" class="form-select" required>
                    <option value="">-- Choose College --</option>
                    <?php
                    $departments = $conn->query("SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != '' ORDER BY department_name ASC");
                    while ($dept = $departments->fetch_assoc()):
                      $selected = ($current_department === $dept['department_name']) ? 'selected' : '';
                      echo "<option value='" . htmlspecialchars($dept['department_name']) . "' $selected>" . htmlspecialchars($dept['department_name']) . "</option>";
                    endwhile;
                    ?>
                  </select>
                </div>

              <?php else: ?>
                <div class="col-md-12">
                  <label class="form-label">New <?= htmlspecialchars($type) ?> Name</label>
                  <input type="text" name="value" class="form-control" value="<?= htmlspecialchars($current_value ?? '') ?>" required>
                </div>
              <?php endif; ?>

              <div class="col-md-12 d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-success me-2">Update</button>
                <a href="register-addsmanagement.php" class="btn btn-secondary">Back</a>
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

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <?php if ($message): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          icon: '<?= strpos($message, "successfully") !== false ? "success" : "warning" ?>',
          title: '<?= addslashes($type) ?> Update',
          text: '<?= addslashes($message) ?>',
          timer: 2000,
          showConfirmButton: false,
        });
      });
    </script>
  <?php endif; ?>
</body>

</html>
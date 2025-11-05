<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check registrar login
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// Check if admin ID is provided
if (!isset($_GET['id'])) {
  echo "Admin ID missing.";
  exit();
}

$admin_id = $_GET['id']; // original id (before edit)

// ✅ Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin) {
  echo "Admin not found.";
  exit();
}

// --- Fetch all available departments & programs ---
$departments = [];
$dept_res = $conn->query("SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL AND department_name!='' ORDER BY department_name ASC");
while ($r = $dept_res->fetch_assoc()) {
  $departments[$r['department_name']] = [];
}
$prog_res = $conn->query("SELECT department_name, program_name FROM adds WHERE department_name IS NOT NULL AND program_name IS NOT NULL ORDER BY department_name, program_name");
while ($r = $prog_res->fetch_assoc()) {
  $departments[$r['department_name']][] = $r['program_name'];
}

// ✅ Get current admin assigned departments/programs
$dept_stmt = $conn->prepare("SELECT department_name, program_name FROM admin_departments WHERE admin_idnumber = ?");
$dept_stmt->bind_param("s", $admin_id);
$dept_stmt->execute();
$res = $dept_stmt->get_result();

$admin_departments = [];
while ($row = $res->fetch_assoc()) {
  $dept = $row['department_name'];
  if (!isset($admin_departments[$dept])) $admin_departments[$dept] = [];
  if (!empty($row['program_name'])) $admin_departments[$dept][] = $row['program_name'];
}

// ✅ Dropdown options for position and rank
$positions = [];
$ranks = [];

$res_pos = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL AND position_name!='' ORDER BY position_name ASC");
while ($row = $res_pos->fetch_assoc()) $positions[] = $row['position_name'];

$res_rank = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name!='' ORDER BY rank_name ASC");
while ($row = $res_rank->fetch_assoc()) $ranks[] = $row['rank_name'];

// ✅ Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_id = trim($_POST['idnumber']);
  $position = trim($_POST['position']);
  $faculty_rank = trim($_POST['faculty_rank']) !== '' ? trim($_POST['faculty_rank']) : null;
  $status = trim($_POST['status']);
  $departments_post = $_POST['departments'] ?? [];
  $programs_post = $_POST['programs'] ?? [];

  if ($new_id === '') {
    $_SESSION['error'] = "ID number is required.";
    header("Location: register-editadmin.php?id=" . urlencode($admin_id));
    exit();
  }

  $conn->begin_transaction();
  try {
    // 1) Update admin basic info
    $update_admin = $conn->prepare("
      UPDATE admin 
      SET idnumber=?, position=?, faculty_rank=?, status=? 
      WHERE idnumber=?
    ");
    $update_admin->bind_param("sssss", $new_id, $position, $faculty_rank, $status, $admin_id);
    $update_admin->execute();

    // 2) Clear old department/programs
    $del = $conn->prepare("DELETE FROM admin_departments WHERE admin_idnumber = ?");
    $del->bind_param("s", $admin_id);
    $del->execute();

    // 3) Insert new departments/programs
    $ins = $conn->prepare("INSERT INTO admin_departments (admin_idnumber, department_name, program_name) VALUES (?, ?, ?)");
    foreach ($departments_post as $dept) {
      $progs = $programs_post[$dept] ?? [null];
      foreach ($progs as $prog) {
        $prog_val = $prog ?: null;
        $ins->bind_param("sss", $new_id, $dept, $prog_val);
        $ins->execute();
      }
    }

    $conn->commit();

    header("Location: register-editadmin.php?id=" . urlencode($new_id) . "&update=success");
    exit();
  } catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Update failed: " . $e->getMessage();
    header("Location: register-editadmin.php?id=" . urlencode($admin_id));
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
</head>

<body>
  <?php include 'register-header.php'; ?>
  <?php include 'register-sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="register-adminlist.php">Admin List</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title text-center">Edit Admin Details</h5>

          <?php if (!empty($_SESSION['error'])): ?>
            <script>
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode($_SESSION['error']) ?>
              });
            </script>
            <?php unset($_SESSION['error']); ?>
          <?php endif; ?>

          <form method="POST">
            <input type="hidden" name="idnumber" value="<?= htmlspecialchars($admin['idnumber']) ?>">

            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" value="<?= htmlspecialchars($admin['idnumber']) ?>" disabled>
                  <label>ID Number</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" value="<?= htmlspecialchars($admin['first_name'] . ' ' . $admin['mid_name'] . ' ' . $admin['last_name']) ?>" disabled>
                  <label>Full Name</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select class="form-select" name="position" required>
                    <?php foreach ($positions as $p): ?>
                      <option value="<?= htmlspecialchars($p) ?>" <?= $p === $admin['position'] ? 'selected' : '' ?>><?= htmlspecialchars($p) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <label>Position</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select class="form-select" name="status">
                    <option value="active" <?= $admin['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $admin['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                  </select>
                  <label>Status</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select class="form-select" name="faculty_rank">
                    <option value="">-- Select Rank --</option>
                    <?php foreach ($ranks as $rank): ?>
                      <option value="<?= htmlspecialchars($rank) ?>" <?= $rank === $admin['faculty_rank'] ? 'selected' : '' ?>><?= htmlspecialchars($rank) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <label>Faculty Rank</label>
                </div>
              </div>

              <!-- Department(s) and Programs -->
              <div class="col-12">
                <label class="fw-bold">Assigned Department(s)</label>
                <select id="departmentSelect" name="departments[]" multiple></select>
              </div>
              <div id="programContainer" class="mt-3"></div>
            </div>

            <div class="col-12 mt-3">
              <button type="submit" class="btn btn-success">Update Admin</button>
              <a href="register-adminlist.php" class="btn btn-secondary">Back</a>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <!-- Vendor JS Files -->
  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>

  <script src="assets/js/choices.min.js"></script>
  <script src="sweetalert2/sweetalert2@11.js"></script>

  <script>
    const departmentPrograms = <?= json_encode($departments, JSON_UNESCAPED_UNICODE) ?>;
    const adminDepartments = <?= json_encode($admin_departments, JSON_UNESCAPED_UNICODE) ?>;

    document.addEventListener('DOMContentLoaded', () => {
      const deptSelect = document.getElementById('departmentSelect');
      const programContainer = document.getElementById('programContainer');

      Object.keys(departmentPrograms).forEach(dept => {
        const opt = document.createElement('option');
        opt.value = dept;
        opt.textContent = dept;
        if (adminDepartments.hasOwnProperty(dept)) opt.selected = true;
        deptSelect.appendChild(opt);
      });

      const deptChoices = new Choices(deptSelect, {
        removeItemButton: true
      });
      loadPrograms();
      deptSelect.addEventListener('change', loadPrograms);

      function loadPrograms() {
        programContainer.innerHTML = '';
        const selected = Array.from(deptSelect.selectedOptions).map(o => o.value);

        selected.forEach(dept => {
          const div = document.createElement('div');
          div.classList.add('mt-3', 'p-3', 'border', 'rounded');
          div.innerHTML = `<label class="fw-bold text-primary">${dept} Programs</label>
                           <select name="programs[${dept}][]" multiple></select>`;
          programContainer.appendChild(div);

          const select = div.querySelector('select');
          const ch = new Choices(select, {
            removeItemButton: true
          });
          const available = departmentPrograms[dept] || [];

          const opts = available.map(p => ({
            value: p,
            label: p,
            selected: adminDepartments[dept] ? adminDepartments[dept].includes(p) : false
          }));
          ch.setChoices(opts, 'value', 'label', true);
        });
      }
    });

    <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
      Swal.fire({
        icon: 'success',
        title: 'Updated!',
        text: 'Admin record updated successfully.'
      }).then(() => {
        const url = new URL(window.location);
        url.searchParams.delete('update');
        window.history.replaceState({}, '', url);
      });
    <?php endif; ?>
  </script>
</body>

</html>
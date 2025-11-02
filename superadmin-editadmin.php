<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check superadmin login
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Check if admin ID is provided
if (!isset($_GET['id'])) {
  echo "Admin ID missing.";
  exit();
}

$admin_id = $_GET['id'];            // original id (before edit)

// ✅ Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admin WHERE idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin) {
  echo "Admin not found.";
  exit();
}

// --- Build list of departments (distinct) and programs per department ---
// Get all distinct departments first
$departments_result = $conn->query("SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != '' ORDER BY department_name ASC");
$departments = [];
while ($r = $departments_result->fetch_assoc()) {
  $deptName = $r['department_name'];
  $departments[$deptName] = []; // initialize with empty array so departments with no programs still appear
}
// Now fetch programs and populate map
$programs_result = $conn->query("SELECT department_name, program_name FROM adds WHERE department_name IS NOT NULL AND program_name IS NOT NULL ORDER BY department_name, program_name");
while ($r = $programs_result->fetch_assoc()) {
  $d = $r['department_name'];
  $p = $r['program_name'];
  if (!isset($departments[$d])) $departments[$d] = []; // safety
  if ($p && !in_array($p, $departments[$d])) $departments[$d][] = $p;
}

// ✅ Fetch admin's assigned departments/programs
$dept_stmt = $conn->prepare("SELECT department_name, program_name FROM admin_departments WHERE admin_idnumber = ?");
$dept_stmt->bind_param("s", $admin_id);
$dept_stmt->execute();
$dept_result = $dept_stmt->get_result();

$admin_departments = [];
while ($row = $dept_result->fetch_assoc()) {
  $dept = $row['department_name'];
  if (!isset($admin_departments[$dept])) $admin_departments[$dept] = [];
  if (!empty($row['program_name'])) $admin_departments[$dept][] = $row['program_name'];
}

// ✅ Fetch position & rank dropdown options
$positions = [];
$res_pos = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL AND position_name!='' ORDER BY position_name ASC");
while ($row = $res_pos->fetch_assoc()) $positions[] = $row['position_name'];

$ranks = [];
$res_rank = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name!='' ORDER BY rank_name ASC");
while ($row = $res_rank->fetch_assoc()) $ranks[] = $row['rank_name'];

// ✅ Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Collect posted values
  $new_id = trim($_POST['idnumber']);
  $first = trim($_POST['first_name']);
  $mid = trim($_POST['mid_name']);
  $last = trim($_POST['last_name']);
  $position = trim($_POST['position']);
  $faculty_rank = trim($_POST['faculty_rank']) !== '' ? trim($_POST['faculty_rank']) : null;
  $status = trim($_POST['status']);
  $departments_post = $_POST['departments'] ?? [];
  $programs_post = $_POST['programs'] ?? []; // programs_post is an associative array keyed by dept

  // Basic validation
  if ($new_id === '' || $first === '' || $last === '' || $position === '' || $faculty_rank === null) {
    $_SESSION['error'] = "Please fill required fields (ID, first name, last name, position, faculty rank).";
    header("Location: superadmin-editadmin.php?id=" . urlencode($admin_id));
    exit();
  }

  $conn->begin_transaction();
  try {
    // 1) Update admin table: change idnumber and other fields
    $update_admin = $conn->prepare("
      UPDATE admin 
      SET idnumber = ?, first_name = ?, mid_name = ?, last_name = ?, position = ?, faculty_rank = ?, status = ?
      WHERE idnumber = ?
    ");
    $update_admin->bind_param("ssssssss", $new_id, $first, $mid, $last, $position, $faculty_rank, $status, $admin_id);
    $update_admin->execute();

    // 2) Delete admin_departments rows for the ORIGINAL id (important)
    $del_stmt = $conn->prepare("DELETE FROM admin_departments WHERE admin_idnumber = ?");
    $del_stmt->bind_param("s", $admin_id);
    $del_stmt->execute();

    // 3) Insert the new admin_departments rows using the NEW id
    $insert_dept = $conn->prepare("INSERT INTO admin_departments (admin_idnumber, department_name, program_name) VALUES (?, ?, ?)");

    foreach ($departments_post as $dept) {
      // ensure dept exists in adds (optional safety)
      if (!array_key_exists($dept, $departments)) continue;

      $selectedPrograms = $programs_post[$dept] ?? [];

      if (!empty($selectedPrograms)) {
        foreach ($selectedPrograms as $prog) {
          // if program is empty string treat as NULL
          $prog_val = $prog === '' ? null : $prog;
          $insert_dept->bind_param("sss", $new_id, $dept, $prog_val);
          $insert_dept->execute();
        }
      } else {
        // store department with NULL program
        $prog_val = null;
        $insert_dept->bind_param("sss", $new_id, $dept, $prog_val);
        $insert_dept->execute();
      }
    }

    $conn->commit();

    // redirect to new id
    header("Location: superadmin-editadmin.php?id=" . urlencode($new_id) . "&update=success");
    exit();
  } catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Update failed: " . $e->getMessage();
    header("Location: superadmin-editadmin.php?id=" . urlencode($admin_id));
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
  <?php include 'superadmin-header.php'; ?>
  <?php include 'superadmin-sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Admin</h1>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-8">
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
                <div class="row g-3">

                  <!-- ID Number -->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" name="idnumber" value="<?= htmlspecialchars($admin['idnumber']) ?>" readonly>
                      <label>ID Number</label>
                    </div>
                  </div>

                  <!-- First Name -->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($admin['first_name']) ?>" required>
                      <label>First Name</label>
                    </div>
                  </div>

                  <!-- Middle Name -->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" name="mid_name" value="<?= htmlspecialchars($admin['mid_name']) ?>">
                      <label>Middle Name</label>
                    </div>
                  </div>

                  <!-- Last Name -->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($admin['last_name']) ?>" required>
                      <label>Last Name</label>
                    </div>
                  </div>

                  <!-- Position -->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <select class="form-select" name="position" required>
                        <?php foreach ($positions as $pos): ?>
                          <option value="<?= htmlspecialchars($pos) ?>" <?= $pos === $admin['position'] ? 'selected' : '' ?>><?= htmlspecialchars($pos) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <label>Position</label>
                    </div>
                  </div>

                  <!-- Faculty Rank (required for all admins as you said) -->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <select class="form-select" name="faculty_rank" required>
                        <option value="">-- Select Rank --</option>
                        <?php foreach ($ranks as $rank): ?>
                          <option value="<?= htmlspecialchars($rank) ?>" <?= $rank === $admin['faculty_rank'] ? 'selected' : '' ?>><?= htmlspecialchars($rank) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <label>Faculty Rank</label>
                    </div>
                  </div>

                  <!-- Status -->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <select class="form-select" name="status" required>
                        <option value="active" <?= $admin['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $admin['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                      </select>
                      <label>Status</label>
                    </div>
                  </div>

                  <!-- Departments multi-select -->
                  <div class="col-12">
                    <label class="fw-bold">Assigned Department(s)</label>
                    <select id="departmentSelect" name="departments[]" multiple required></select>
                  </div>

                  <!-- Programs per department -->
                  <div class="col-12 mt-3" id="programContainer"></div>

                </div>

                <div class="text-center mt-4">
                  <button type="submit" class="btn btn-success">Save Changes</button>
                  <a href="superadmin-adminlist.php" class="btn btn-secondary">Cancel</a>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/choices.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // department -> programs map (JS)
    const departmentPrograms = <?= json_encode($departments, JSON_UNESCAPED_UNICODE) ?>;
    const adminDepartments = <?= json_encode($admin_departments, JSON_UNESCAPED_UNICODE) ?>;

    document.addEventListener('DOMContentLoaded', () => {
      const deptSelect = document.getElementById('departmentSelect');
      const programContainer = document.getElementById('programContainer');

      // ✅ Only populate once
      Object.keys(departmentPrograms).forEach(dept => {
        const opt = document.createElement('option');
        opt.value = dept;
        opt.textContent = dept;
        if (adminDepartments.hasOwnProperty(dept)) opt.selected = true;
        deptSelect.appendChild(opt);
      });

      // ✅ Initialize Choices AFTER populating options
      const deptChoices = new Choices(deptSelect, {
        removeItemButton: true,
        placeholderValue: 'Select departments...'
      });

      // ✅ Load program options dynamically
      loadPrograms();
      deptSelect.addEventListener('change', loadPrograms);

      function loadPrograms() {
        programContainer.innerHTML = '';
        const selectedDepts = Array.from(deptSelect.selectedOptions).map(o => o.value);

        selectedDepts.forEach(dept => {
          const div = document.createElement('div');
          div.classList.add('mt-3', 'p-3', 'border', 'rounded');
          div.innerHTML = `<label class="fw-bold text-primary">Programs under ${dept}</label>
                       <select name="programs[${escapeHtmlAttr(dept)}][]" multiple></select>`;
          programContainer.appendChild(div);

          const select = div.querySelector('select');
          const programChoices = new Choices(select, {
            removeItemButton: true
          });

          const available = departmentPrograms[dept] || [];

          const choicesArr = available.map(p => ({
            value: p,
            label: p,
            selected: adminDepartments[dept] ? adminDepartments[dept].includes(p) : false
          }));

          programChoices.setChoices(choicesArr, 'value', 'label', true);
        });
      }

      function escapeHtmlAttr(s) {
        return s.replace(/["'<>`]/g, '');
      }
    });


    <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
      Swal.fire({
        icon: 'success',
        title: 'Updated Successfully!',
        text: 'Admin record has been updated.',
        confirmButtonColor: '#198754'
      }).then(() => {
        const url = new URL(window.location);
        url.searchParams.delete('update');
        window.history.replaceState({}, '', url);
      });
    <?php endif; ?>
  </script>
</body>

</html>
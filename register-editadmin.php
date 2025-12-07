<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check registrar login
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// --- INITIAL VALIDATION & DATA FETCH ---
$admin_id = $_GET['id'] ?? null;

if (!$admin_id) {
  $_SESSION['msg'] = "Admin ID missing.";
  $_SESSION['msg_type'] = "danger";
  header("Location: register-adminlist.php");
  exit();
}

// 1. Fetch Admin Details
$stmt = $conn->prepare("
    SELECT idnumber, first_name, mid_name, last_name, position, faculty_rank, status 
    FROM admin 
    WHERE idnumber = ?
");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin) {
  $_SESSION['msg'] = "Admin not found.";
  $_SESSION['msg_type'] = "danger";
  header("Location: register-adminlist.php");
  exit();
}

// 2. Fetch all available college & programs
$college_programs = [];

$dept_res = $conn->query("
    SELECT DISTINCT college_name 
    FROM adds 
    WHERE college_name IS NOT NULL AND college_name != '' 
    ORDER BY college_name ASC
");
while ($r = $dept_res->fetch_assoc()) {
  $college_programs[$r['college_name']] = [];
}

$prog_res = $conn->query("
    SELECT college_name, program_name 
    FROM adds 
    WHERE college_name IS NOT NULL AND program_name IS NOT NULL 
    ORDER BY college_name, program_name
");
while ($r = $prog_res->fetch_assoc()) {
  if (isset($college_programs[$r['college_name']])) {
    $college_programs[$r['college_name']][] = $r['program_name'];
  }
}

// 3. Get current admin assigned college/programs
$dept_stmt = $conn->prepare("
    SELECT college_name, program_name 
    FROM admin_college 
    WHERE admin_idnumber = ?
");
$dept_stmt->bind_param("s", $admin_id);
$dept_stmt->execute();
$res = $dept_stmt->get_result();

$admin_assignments = [];
while ($row = $res->fetch_assoc()) {
  $dept = $row['college_name'];
  if (!isset($admin_assignments[$dept])) {
    $admin_assignments[$dept] = [];
  }
  if (!empty($row['program_name'])) {
    $admin_assignments[$dept][] = $row['program_name'];
  }
}
$dept_stmt->close();

// 4. Dropdown options for position and rank
$positions = [];
$ranks = [];

$res_pos = $conn->query("
    SELECT DISTINCT position_name 
    FROM adds 
    WHERE position_name IS NOT NULL AND position_name != '' 
    ORDER BY position_name ASC
");
while ($row = $res_pos->fetch_assoc()) {
  $positions[] = $row['position_name'];
}

$res_rank = $conn->query("
    SELECT DISTINCT rank_name 
    FROM adds 
    WHERE rank_name IS NOT NULL AND rank_name != '' 
    ORDER BY rank_name ASC
");
while ($row = $res_rank->fetch_assoc()) {
  $ranks[] = $row['rank_name'];
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // The original ID used for WHERE clauses in the database
  $original_id = $admin_id;

  // Data from POST
  $new_id = trim($_POST['idnumber']);
  $position = trim($_POST['position']);
  $faculty_rank = trim($_POST['faculty_rank']) !== '' ? trim($_POST['faculty_rank']) : null;
  $status = trim($_POST['status']);
  $college_post = $_POST['college'] ?? [];
  $programs_post = $_POST['programs'] ?? [];

  if ($new_id === '') {
    $_SESSION['msg'] = "ID number is required.";
    $_SESSION['msg_type'] = "danger";
    header("Location: register-editadmin.php?id=" . urlencode($original_id));
    exit();
  }

  // Check for duplicate ID (against other admins)
  $chk = $conn->prepare("
        SELECT idnumber 
        FROM admin 
        WHERE idnumber = ? AND idnumber != ?
    ");
  $chk->bind_param("ss", $new_id, $original_id);
  $chk->execute();
  if ($chk->get_result()->num_rows > 0) {
    $_SESSION['msg'] = "ID number already exists for another admin.";
    $_SESSION['msg_type'] = "danger";
    header("Location: register-editadmin.php?id=" . urlencode($original_id));
    exit();
  }
  $chk->close();

  $conn->begin_transaction();

  try {
    // 1. Update ADMIN table
    $update_admin = $conn->prepare("
            UPDATE admin
            SET idnumber = ?, position = ?, faculty_rank = ?, status = ?
            WHERE idnumber = ?
        ");
    $update_admin->bind_param("sssss", $new_id, $position, $faculty_rank, $status, $original_id);
    $update_admin->execute();

    // 2. Update FACULTY table (if the admin is also listed as faculty)
    // 2. Update faculty table (if exists)
    $update_faculty = $conn->prepare("
    UPDATE faculty 
    SET idnumber = ?, faculty_rank = ?, college = ?, program = ?
    WHERE idnumber = ?
");

    // For faculty: first selected college/program OR null
    $firstCollege = !empty($college_post) ? $college_post[0] : null;
    $firstProgram = isset($programs_post[$firstCollege][0]) ? $programs_post[$firstCollege][0] : null;

    $update_faculty->bind_param(
      "sssss",
      $new_id,
      $faculty_rank,
      $firstCollege,
      $firstProgram,
      $original_id
    );
    $update_faculty->execute();
    $update_faculty->close();

    // 3. Update admin_college mapping
    $del = $conn->prepare("
            DELETE FROM admin_college 
            WHERE admin_idnumber = ?
        ");
    $del->bind_param("s", $original_id);
    $del->execute();
    $del->close();

    // Insert new assignments using the new ID
    $ins = $conn->prepare("
            INSERT INTO admin_college (admin_idnumber, college_name, program_name) 
            VALUES (?, ?, ?)
        ");

    if (!empty($college_post)) {
      foreach ($college_post as $dept) {
        $progs = $programs_post[$dept] ?? [null];

        // If no specific programs are selected for a college, insert the college assignment with a null program
        if (empty($progs)) {
          $null_prog = null;
          $ins->bind_param("sss", $new_id, $dept, $null_prog);
          $ins->execute();
        } else {
          foreach ($progs as $prog) {
            $prog_val = trim($prog) !== '' ? trim($prog) : null;
            $ins->bind_param("sss", $new_id, $dept, $prog_val);
            $ins->execute();
          }
        }
      }
    }
    $ins->close();

    $conn->commit();

    $_SESSION['msg'] = "Admin record updated successfully!";
    $_SESSION['msg_type'] = "success";
    header("Location: register-editadmin.php?id=" . urlencode($new_id));
    exit();
  } catch (Exception $e) {
    $conn->rollback();
    $_SESSION['msg'] = "Update failed: A database error occurred.";
    $_SESSION['msg_type'] = "danger";
    header("Location: register-editadmin.php?id=" . urlencode($original_id));
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <link rel="stylesheet" href="assets/css/choices.min.css">
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
          <li class="breadcrumb-item active">Edit <?= htmlspecialchars($admin['idnumber']) ?></li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="card col-lg-8">
          <div class="card-body">
            <h5 class="card-title text-center">Update Admin Details</h5>

            <form method="POST" class="row g-3">
              <div class="col-md-4">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    value="<?= htmlspecialchars($admin['first_name'] . ' ' . $admin['mid_name'] . ' ' . $admin['last_name']) ?>"
                    disabled>
                  <label>Full Name</label>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    name="idnumber"
                    id="idnumber"
                    value="<?= htmlspecialchars($admin['idnumber']) ?>"
                    required>
                  <label for="idnumber">ID Number (Editable)</label>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-floating">
                  <select class="form-select" name="status" id="status">
                    <option value="active" <?= $admin['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $admin['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                  </select>
                  <label for="status">Status</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select class="form-select" name="position" id="position" required>
                    <option value="" disabled>Select Position</option>
                    <?php foreach ($positions as $p): ?>
                      <option
                        value="<?= htmlspecialchars($p) ?>"
                        <?= $p === $admin['position'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <label for="position">Position</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select class="form-select" name="faculty_rank" id="faculty_rank">
                    <option value="">-- No Specific Rank --</option>
                    <?php foreach ($ranks as $rank): ?>
                      <option
                        value="<?= htmlspecialchars($rank) ?>"
                        <?= $rank === $admin['faculty_rank'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($rank) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <label for="faculty_rank">Faculty Rank (Optional)</label>
                </div>
              </div>

              <div class="col-12 mt-4">
                <label class="fw-bold mb-2">Assigned College(s)</label>
                <select id="collegeSelect" name="college[]" multiple></select>
              </div>

              <div id="programContainer" class="col-12"></div>

              <div class="col-12 mt-4 d-flex">
                <a href="register-adminlist.php" class="btn btn-secondary me-2">Back to List</a>
                <button type="submit" class="btn btn-success">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <script src="assets/js/choices.min.js"></script>
  <script src="sweetalert2/sweetalert2@11.js"></script>

  <script>
    const collegePrograms = <?= json_encode($college_programs, JSON_UNESCAPED_UNICODE) ?>;
    const adminAssignments = <?= json_encode($admin_assignments, JSON_UNESCAPED_UNICODE) ?>;

    document.addEventListener('DOMContentLoaded', () => {
      const collegeSelect = document.getElementById('collegeSelect');
      const programContainer = document.getElementById('programContainer');

      // Populate college dropdown
      Object.keys(collegePrograms).forEach(dept => {
        const opt = document.createElement('option');
        opt.value = dept;
        opt.textContent = dept;
        if (adminAssignments.hasOwnProperty(dept)) {
          opt.selected = true;
        }
        collegeSelect.appendChild(opt);
      });

      const collegeChoices = new Choices(collegeSelect, {
        removeItemButton: true,
        shouldSort: false
      });

      loadPrograms();
      collegeSelect.addEventListener('change', loadPrograms);

      function loadPrograms() {
        programContainer.innerHTML = '';

        const selectedOptions = collegeChoices.getValue(true);

        selectedOptions.forEach(dept => {
          const programs = collegePrograms[dept] || [];
          if (programs.length === 0) return;

          const div = document.createElement('div');
          div.classList.add('mt-3', 'p-3', 'border', 'rounded', 'bg-light');

          div.innerHTML = `
                    <label class="fw-bold text-primary">${dept} Programs (Optional)</label>
                    <select name="programs[${dept}][]" multiple></select>
                `;
          programContainer.appendChild(div);

          const select = div.querySelector('select');
          const programChoices = new Choices(select, {
            removeItemButton: true,
            shouldSort: false,
            placeholderValue: 'Select one or more programs'
          });

          const opts = programs.map(p => ({
            value: p,
            label: p,
            selected: adminAssignments[dept] ? adminAssignments[dept].includes(p) : false
          }));

          programChoices.setChoices(opts, 'value', 'label', true);
        });
      }

      <?php if (isset($_SESSION['msg'])): ?>
        const msg = '<?= addslashes($_SESSION['msg']) ?>';
        const type = '<?= $_SESSION['msg_type'] ?>';

        Swal.fire({
          icon: type,
          title: type === 'success' ? 'Success!' : 'Error/Warning',
          text: msg,
          timer: type === 'success' ? 3000 : false,
          showConfirmButton: type !== 'success',
        }).then(() => {
          if (type === 'success') {
            const url = new URL(window.location);
            url.searchParams.delete('update');
            window.history.replaceState({}, '', url);
          }
        });

        <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
      <?php endif; ?>
    });
  </script>
</body>

</html>
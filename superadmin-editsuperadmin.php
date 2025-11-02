<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ✅ Check login & role
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// ✅ Check superadmin ID in URL
if (!isset($_GET['id'])) {
  echo "Superadmin ID is missing.";
  exit();
}

$superadmin_id = $_GET['id'];

// ✅ Fetch superadmin info
$stmt = $conn->prepare("SELECT * FROM superadmin WHERE idnumber = ?");
$stmt->bind_param("s", $superadmin_id);
$stmt->execute();
$result = $stmt->get_result();
$superadmin = $result->fetch_assoc();

if (!$superadmin) {
  echo "Superadmin not found.";
  exit();
}

// ✅ Fetch dropdowns from adds table
$positions = [];
$ranks = [];
$departments = [];

$pos_result = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL ORDER BY position_name ASC");
while ($row = $pos_result->fetch_assoc()) {
  $positions[] = $row['position_name'];
}

$rank_result = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL ORDER BY rank_name ASC");
while ($row = $rank_result->fetch_assoc()) {
  $ranks[] = $row['rank_name'];
}

$dept_result = $conn->query("SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL ORDER BY department_name ASC");
while ($row = $dept_result->fetch_assoc()) {
  $departments[] = $row['department_name'];
}

// ✅ Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_status = $_POST['status'] ?? 'active';
  $new_position = $_POST['position'] ?? null;
  $new_rank = $_POST['faculty_rank'] ?? null;
  $new_department = $_POST['department'] ?? null;
  $new_program = $_POST['program'] ?? null;

  // Validation
  if (empty($new_position)) {
    $_SESSION['msg'] = "Position is required.";
    $_SESSION['msg_type'] = "danger";
    header("Location: superadmin-editsuperadmin.php?id=$superadmin_id");
    exit();
  }

  $stmt = $conn->prepare("
      UPDATE superadmin
      SET status = ?, position = ?, faculty_rank = ?, department = ?, program = ?
      WHERE idnumber = ?
  ");
  $stmt->bind_param("ssssss", $new_status, $new_position, $new_rank, $new_department, $new_program, $superadmin_id);
  $stmt->execute();

  header("Location: superadmin-editsuperadmin.php?id=$superadmin_id&update=success");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
</head>

<body>
  <?php include 'superadmin-header.php' ?>
  <?php include 'superadmin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Superadmin Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="superadmin-superadminlist.php">Superadmin</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Superadmin Information</h5>

              <?php if ($superadmin): ?>
                <form method="POST">

                  <div class="row">
                    <!-- Full Name -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <input type="text" class="form-control"
                          value="<?= htmlspecialchars($superadmin['first_name'] . ' ' . $superadmin['mid_name'] . ' ' . $superadmin['last_name']) ?>" disabled>
                        <label>Full Name</label>
                      </div>
                    </div>

                    <!-- ID -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <input type="text" class="form-control" value="<?= htmlspecialchars($superadmin['idnumber']) ?>" disabled>
                        <label>ID Number</label>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <!-- Department -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="department" id="department" class="form-select" required>
                          <option value="">-- Select Department --</option>
                          <?php foreach ($departments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept) ?>" <?= $superadmin['department'] === $dept ? 'selected' : '' ?>>
                              <?= htmlspecialchars($dept) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <label>Department</label>
                      </div>
                    </div>

                    <!-- Program -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="program" id="program" class="form-select">
                          <option value="">-- Select Program --</option>
                          <?php
                          if (!empty($superadmin['department'])) {
                            $stmt_prog = $conn->prepare("SELECT DISTINCT program_name FROM adds WHERE department_name = ? AND program_name IS NOT NULL ORDER BY program_name ASC");
                            $stmt_prog->bind_param("s", $superadmin['department']);
                            $stmt_prog->execute();
                            $res_prog = $stmt_prog->get_result();
                            while ($prog = $res_prog->fetch_assoc()):
                          ?>
                              <option value="<?= htmlspecialchars($prog['program_name']) ?>" <?= $superadmin['program'] === $prog['program_name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prog['program_name']) ?>
                              </option>
                          <?php endwhile;
                          } ?>
                        </select>
                        <label>Program</label>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="status" class="form-select">
                          <option value="active" <?= $superadmin['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                          <option value="inactive" <?= $superadmin['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <label>Status</label>
                      </div>
                    </div>

                    <!-- Position -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="position" class="form-select" required>
                          <option value="">-- Select Position --</option>
                          <?php foreach ($positions as $pos): ?>
                            <option value="<?= htmlspecialchars($pos) ?>" <?= $superadmin['position'] === $pos ? 'selected' : '' ?>>
                              <?= htmlspecialchars($pos) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <label>Position</label>
                      </div>
                    </div>
                  </div>

                  <!-- Faculty Rank -->
                  <div class="mb-3">
                    <div class="form-floating">
                      <select name="faculty_rank" class="form-select">
                        <option value="">-- Select Faculty Rank --</option>
                        <?php foreach ($ranks as $rank): ?>
                          <option value="<?= htmlspecialchars($rank) ?>" <?= $superadmin['faculty_rank'] === $rank ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rank) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <label>Faculty Rank</label>
                    </div>
                  </div>

                  <button type="submit" class="btn btn-success">Update Information</button>
                  <a href="superadmin-superadminlist.php" class="btn btn-secondary">Back</a>
                </form>
              <?php else: ?>
                <div class="alert alert-danger">Superadmin not found.</div>
              <?php endif; ?>
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
  <script src="assets/js/main.js"></script>

  <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Updated Successfully',
        text: 'Superadmin info has been updated!',
        timer: 2000,
        showConfirmButton: false
      });
    </script>
  <?php endif; ?>

  <script>
    document.getElementById('department').addEventListener('change', function() {
      const dept = this.value;
      const programSelect = document.getElementById('program');

      // Clear current options
      programSelect.innerHTML = '<option value="">-- Loading Programs... --</option>';

      if (dept) {
        fetch(`get_programs.php?department=${encodeURIComponent(dept)}`)
          .then(response => response.json())
          .then(programs => {
            programSelect.innerHTML = '<option value="">-- Select Program --</option>';
            programs.forEach(prog => {
              const option = document.createElement('option');
              option.value = prog;
              option.textContent = prog;
              programSelect.appendChild(option);
            });
          })
          .catch(() => {
            programSelect.innerHTML = '<option value="">-- Error Loading Programs --</option>';
          });
      } else {
        programSelect.innerHTML = '<option value="">-- Select Department First --</option>';
      }
    });
  </script>

</body>

</html>
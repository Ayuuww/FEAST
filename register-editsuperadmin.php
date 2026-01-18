<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ✅ Check login & role
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
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
$college = [];

$pos_result = $conn->query("SELECT DISTINCT position_name FROM adds WHERE position_name IS NOT NULL ORDER BY position_name ASC");
while ($row = $pos_result->fetch_assoc()) {
  $positions[] = $row['position_name'];
}

$rank_result = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL ORDER BY rank_name ASC");
while ($row = $rank_result->fetch_assoc()) {
  $ranks[] = $row['rank_name'];
}

$dept_result = $conn->query("SELECT DISTINCT college_name FROM adds WHERE college_name IS NOT NULL ORDER BY college_name ASC");
while ($row = $dept_result->fetch_assoc()) {
  $college[] = $row['college_name'];
}

// ✅ Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $new_id = $_POST['idnumber'];
  $new_status = $_POST['status'] ?? 'active';
  $new_position = $_POST['position'] ?? null;
  $new_rank = $_POST['faculty_rank'] ?? null;
  $new_college = $_POST['college'] ?? null;
  $new_program = $_POST['program'] ?? null;

  // Prevent duplicate ID number
  $check = $conn->prepare("SELECT idnumber FROM superadmin WHERE idnumber = ? AND idnumber != ?");
  $check->bind_param("ss", $new_id, $superadmin_id);
  $check->execute();
  $res = $check->get_result();

  if ($res->num_rows > 0) {
    $_SESSION['msg'] = "ID Number already exists!";
    $_SESSION['msg_type'] = "danger";
    header("Location: register-editsuperadmin.php?id=$superadmin_id");
    exit();
  }

  // 🔄 Update SUPERADMIN table
  $stmt = $conn->prepare("
    UPDATE superadmin
    SET idnumber = ?, status = ?, position = ?, faculty_rank = ?, college = ?, program = ?
    WHERE idnumber = ?
  ");
  $stmt->bind_param("sssssss", $new_id, $new_status, $new_position, $new_rank, $new_college, $new_program, $superadmin_id);
  $stmt->execute();

  // 🔄 Update FACULTY table too (superadmin is also a faculty)
  $faculty_update = $conn->prepare("
    UPDATE faculty
    SET idnumber = ?, college = ?, program = ?, faculty_rank = ?
    WHERE idnumber = ?
  ");
  $faculty_update->bind_param("sssss", $new_id, $new_college, $new_program, $new_rank, $superadmin_id);
  $faculty_update->execute();

  header("Location: register-editsuperadmin.php?id=$new_id&update=success");
  exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
</head>

<body>
  <?php include 'register-header.php' ?>
  <?php include 'register-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Superadmin Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="register-superadminlist.php">Superadmin</a></li>
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
                        <input type="text" class="form-control" name="idnumber" value="<?= htmlspecialchars($superadmin['idnumber']) ?>" required>
                        <label>ID Number</label>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <!-- college -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="college" id="college" class="form-select" required>
                          <option value="">-- Select College --</option>
                          <?php foreach ($college as $dept): ?>
                            <option value="<?= htmlspecialchars($dept) ?>" <?= $superadmin['college'] === $dept ? 'selected' : '' ?>>
                              <?= htmlspecialchars($dept) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <label>College</label>
                      </div>
                    </div>

                    <!-- Program -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="program" id="program" class="form-select">
                          <option value="">-- Select Program --</option>
                          <?php
                          if (!empty($superadmin['college'])) {
                            $stmt_prog = $conn->prepare("SELECT DISTINCT program_name FROM adds WHERE college_name = ? AND program_name IS NOT NULL ORDER BY program_name ASC");
                            $stmt_prog->bind_param("s", $superadmin['college']);
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
                  <a href="register-superadminlist.php" class="btn btn-secondary">Back</a>
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
    document.getElementById('college').addEventListener('change', function() {
      const dept = this.value;
      const programSelect = document.getElementById('program');

      // Clear current options
      programSelect.innerHTML = '<option value="">-- Loading Programs... --</option>';

      if (dept) {
        fetch(`get_programs.php?college=${encodeURIComponent(dept)}`)
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
        programSelect.innerHTML = '<option value="">-- Select college First --</option>';
      }
    });
  </script>

</body>

</html>
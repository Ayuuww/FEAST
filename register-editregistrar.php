<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Ensure registrar logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// Ensure registrar ID in URL
if (!isset($_GET['id'])) {
  echo "Registrar ID is missing.";
  exit();
}

$registrar_id = $_GET['id'];

// Fetch registrar info
$stmt = $conn->prepare("SELECT * FROM registrar WHERE idnumber = ?");
$stmt->bind_param("s", $registrar_id);
$stmt->execute();
$res = $stmt->get_result();
$registrar = $res->fetch_assoc();
$stmt->close();

if (!$registrar && $_SERVER["REQUEST_METHOD"] != "POST") {
  echo "Registrar not found.";
  exit();
}

// Fetch rank options for dropdown (adds.rank_name)
$rankQuery = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != '' ORDER BY rank_name ASC");
$facultyRanks = [];
while ($r = $rankQuery->fetch_assoc()) {
  $facultyRanks[] = $r['rank_name'];
}

// Fetch departments/programs from adds table
$adds_data_result = $conn->query("
  SELECT DISTINCT department_name, program_name 
  FROM adds 
  WHERE department_name IS NOT NULL AND department_name != ''
  ORDER BY department_name, program_name ASC
");
$departmentPrograms = [];
while ($r = $adds_data_result->fetch_assoc()) {
  $d = $r['department_name'];
  $p = $r['program_name'];
  if (!isset($departmentPrograms[$d])) $departmentPrograms[$d] = [];
  if ($p && !in_array($p, $departmentPrograms[$d])) $departmentPrograms[$d][] = $p;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Collect POST safely
  $new_status      = $_POST['status'] ?? 'active';
  $new_employment  = $_POST['employment_role'] ?? 'Non-Teaching';
  $new_faculty_rank = !empty($_POST['faculty_rank']) ? trim($_POST['faculty_rank']) : null;
  $new_department  = !empty($_POST['department']) ? trim($_POST['department']) : null;
  $new_program     = !empty($_POST['program']) ? trim($_POST['program']) : null;

  // Normalize employment role
  $new_employment = ($new_employment === 'Teaching') ? 'Teaching' : 'Non-Teaching';

  try {
    // Begin transaction
    $conn->begin_transaction();

    // 1) Update registrar table
    $upd = $conn->prepare("
      UPDATE registrar
      SET status = ?, faculty_rank = ?, employment_role = ?, department = ?, program = ?
      WHERE idnumber = ?
    ");
    $upd->bind_param("ssssss", $new_status, $new_faculty_rank, $new_employment, $new_department, $new_program, $registrar_id);
    $upd->execute();
    $upd->close();

    // 2) Handle faculty record depending on employment role
    // Check if faculty record exists
    $chk = $conn->prepare("SELECT idnumber FROM faculty WHERE idnumber = ?");
    $chk->bind_param("s", $registrar_id);
    $chk->execute();
    $chkRes = $chk->get_result();
    $faculty_exists = ($chkRes && $chkRes->num_rows > 0);
    $chk->close();

    if ($new_employment === 'Teaching') {
      // Ensure required fields for faculty are present
      // If not provided, rollback and show error
      if (empty($new_department)) {
        throw new Exception("Please select a Department when setting Employment Role to Teaching.");
      }

      if ($faculty_exists) {
        // Update existing faculty
        $upf = $conn->prepare("
          UPDATE faculty
          SET first_name = (SELECT first_name FROM registrar WHERE idnumber = ?),
              mid_name   = (SELECT mid_name FROM registrar WHERE idnumber = ?),
              last_name  = (SELECT last_name FROM registrar WHERE idnumber = ?),
              department = ?, program = ?, faculty_rank = ?, status = 'active'
          WHERE idnumber = ?
        ");
        // bind: registrar_id used in subselects too
        $upf->bind_param("ssssss", $registrar_id, $registrar_id, $registrar_id, $new_department, $new_program, $new_faculty_rank, $registrar_id);
        $upf->execute();
        $upf->close();
      } else {
        // Insert new faculty record
        // Get password from registrar table to keep same credentials (may be null)
        $pstmt = $conn->prepare("SELECT password, first_name, mid_name, last_name FROM registrar WHERE idnumber = ?");
        $pstmt->bind_param("s", $registrar_id);
        $pstmt->execute();
        $pstmt->bind_result($reg_password, $reg_fname, $reg_mname, $reg_lname);
        $pstmt->fetch();
        $pstmt->close();

        $faculty_role = 'faculty';
        $faculty_status = 'active';

        $ins = $conn->prepare("
          INSERT INTO faculty
            (idnumber, first_name, mid_name, last_name, password, department, program, faculty_rank, role, status)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->bind_param(
          "ssssssssss",
          $registrar_id,
          $reg_fname,
          $reg_mname,
          $reg_lname,
          $reg_password,
          $new_department,
          $new_program,
          $new_faculty_rank,
          $faculty_role,
          $faculty_status
        );
        $ins->execute();
        $ins->close();
      }
    } else {
      // Non-Teaching: if a faculty record exists, set to inactive (do not delete)
      if ($faculty_exists) {
        $upf2 = $conn->prepare("UPDATE faculty SET status = 'inactive' WHERE idnumber = ?");
        $upf2->bind_param("s", $registrar_id);
        $upf2->execute();
        $upf2->close();
      }
    }

    // Commit transaction
    $conn->commit();

    $_SESSION['msg'] = "Registrar updated successfully.";
    $_SESSION['msg_type'] = "success";

    // Refresh local $registrar variable
    $rstmt = $conn->prepare("SELECT * FROM registrar WHERE idnumber = ?");
    $rstmt->bind_param("s", $registrar_id);
    $rstmt->execute();
    $rres = $rstmt->get_result();
    $registrar = $rres->fetch_assoc();
    $rstmt->close();
  } catch (Exception $e) {
    // Rollback and set error message
    if ($conn->errno) $conn->rollback();
    $_SESSION['msg'] = "Error: " . $e->getMessage();
    $_SESSION['msg_type'] = "error";
  }

  // Redirect (Post/Redirect/Get) to avoid resubmission
  header("Location: register-editregistrar.php?id=" . urlencode($registrar_id));
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <script src="sweetalert2/sweetalert2@11.js"></script>
  <link rel="stylesheet" href="assets/js/choices.min.css" />
</head>

<body>
  <?php include 'register-header.php'; ?>
  <?php include 'register-sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Registrar Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="register-registrarlist.php">Registrar List</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Registrar Details</h5>

              <?php if (isset($_SESSION['msg'])): ?>
                <script>
                  Swal.fire({
                    icon: '<?= ($_SESSION['msg_type'] ?? '') === 'success' ? 'success' : 'error' ?>',
                    title: <?= json_encode($_SESSION['msg']) ?>,
                    showConfirmButton: true
                  });
                </script>
                <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
              <?php endif; ?>

              <?php if ($registrar): ?>
                <form method="POST" class="row g-3">

                  <div class="col-md-6">
                    <div class="form-floating">
                      <input class="form-control" type="text" value="<?= htmlspecialchars($registrar['idnumber']) ?>" disabled>
                      <label>ID Number</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <input class="form-control" type="text" value="<?= htmlspecialchars($registrar['first_name'] . ' ' . $registrar['mid_name'] . ' ' . $registrar['last_name']) ?>" disabled>
                      <label>Full Name</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <select name="employment_role" id="employment_role" class="form-select" required>
                        <option value="Non-Teaching" <?= ($registrar['employment_role'] ?? 'Non-Teaching') === 'Non-Teaching' ? 'selected' : '' ?>>Non-Teaching</option>
                        <option value="Teaching" <?= ($registrar['employment_role'] ?? '') === 'Teaching' ? 'selected' : '' ?>>Teaching</option>
                      </select>
                      <label>Employment Role</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <select name="status" class="form-select" required>
                        <option value="active" <?= ($registrar['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($registrar['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                      </select>
                      <label>Status</label>
                    </div>
                  </div>

                  <!-- Faculty-specific fields (shown when Teaching) -->
                  <div id="teachingFields" class="row g-3" style="display: none;">

                    <!-- Faculty Rank -->
                    <div class="col-md-6">
                      <div class="form-floating">
                        <select name="faculty_rank" id="faculty_rank" class="form-select">
                          <option value="">-- Select Rank --</option>
                          <?php foreach ($facultyRanks as $rank): ?>
                            <option value="<?= htmlspecialchars($rank) ?>" <?= (isset($registrar['faculty_rank']) && $registrar['faculty_rank'] === $rank) ? 'selected' : '' ?>>
                              <?= htmlspecialchars($rank) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <label>Faculty Rank</label>
                      </div>
                    </div>

                    <!-- Department (College) -->
                    <div class="col-12">
                      <div class="card p-3 shadow-sm border border-primary-subtle">
                        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-building"></i> College / Department</h6>
                        <div class="form-floating">
                          <select name="department" id="department" class="form-select">
                            <option value="">-- Select College / Department --</option>
                            <?php foreach (array_keys($departmentPrograms) as $dpt): ?>
                              <option value="<?= htmlspecialchars($dpt) ?>" <?= (isset($registrar['department']) && $registrar['department'] === $dpt) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dpt) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                          <label>College / Department</label>
                        </div>
                      </div>
                    </div>

                    <!-- Program -->
                    <div class="col-12">
                      <div class="card p-3 shadow-sm border border-success-subtle">
                        <h6 class="fw-bold text-success mb-2"><i class="bi bi-journal-bookmark"></i> Program</h6>
                        <div class="form-floating">
                          <select name="program" id="program" class="form-select">
                            <option value="">-- Select Program --</option>
                          </select>
                          <label>Program</label>
                        </div>
                      </div>
                    </div>

                  </div>


                  <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    <a href="register-registrarlist.php" class="btn btn-secondary">Back</a>
                  </div>

                </form>
              <?php else: ?>
                <div class="alert alert-info">Registrar not found or may have been deleted.</div>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/choices.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // departmentPrograms mapping from PHP
    const departmentPrograms = <?= json_encode($departmentPrograms) ?>;
    const deptSelect = document.getElementById('department');
    const programSelect = document.getElementById('program');
    const employmentRole = document.getElementById('employment_role');
    const teachingFields = document.getElementById('teachingFields');

    // Load programs for selected department and mark previously selected program
    function loadPrograms() {
      const dept = deptSelect.value;
      const programs = departmentPrograms[dept] || [];
      programSelect.innerHTML = `<option value="">-- Select Program --</option>`;
      programs.forEach(p => {
        const opt = new Option(p, p);
        // mark selected if matches registrar current program
        if (p === <?= json_encode($registrar['program'] ?? '') ?>) opt.selected = true;
        programSelect.appendChild(opt);
      });
    }

    // Show/hide teaching fields based on employment role
    function toggleTeachingFields() {
      if (employmentRole.value === 'Teaching') {
        teachingFields.style.display = 'flex';
      } else {
        teachingFields.style.display = 'none';
      }
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
      // set up initial visibility
      toggleTeachingFields();
      // load programs if any
      loadPrograms();
      // events
      deptSelect && deptSelect.addEventListener('change', loadPrograms);
      employmentRole && employmentRole.addEventListener('change', toggleTeachingFields);
    });
  </script>
</body>

</html>
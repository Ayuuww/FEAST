<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get the admin's position (for the access check)
$admin_info_stmt = $conn->prepare("SELECT position FROM admin WHERE idnumber = ? LIMIT 1");
$admin_info_stmt->bind_param("s", $admin_id);
$admin_info_stmt->execute();
$admin_result = $admin_info_stmt->get_result();
$admin_data = $admin_result->fetch_assoc();
$admin_position = $admin_data['position'] ?? '';
$admin_info_stmt->close();

// Check if admin has the correct position to be on this page
$allowed_positions = ['Dean', 'Chair Person', 'Program Chair', 'Director']; // adjust spelling to match DB values
if (!in_array($admin_position, $allowed_positions)) {
  $_SESSION['access_denied'] = "Access denied. Your position ($admin_position) is not allowed to add subjects.";
  header("Location: admin-dashboard.php");
  exit();
}

// --- ✅ 1. THIS IS THE MISSING BLOCK ---
// Get all departments this admin is assigned to
$dept_stmt = $conn->prepare("SELECT DISTINCT department_name FROM admin_departments WHERE admin_idnumber = ?");
$dept_stmt->bind_param("s", $admin_id);
$dept_stmt->execute();
$dept_result = $dept_stmt->get_result();

$departments = []; // This admin's departments
while ($row = $dept_result->fetch_assoc()) {
  $departments[] = $row['department_name'];
}
$dept_stmt->close();
// --- END OF MISSING BLOCK ---


// --- ✅ 2. Fetch ALL Department/Program relationships for the dropdowns ---
$adds_query = "SELECT DISTINCT department_name, program_name 
               FROM adds 
               WHERE department_name IS NOT NULL AND department_name != '' 
                 AND program_name IS NOT NULL AND program_name != ''
               ORDER BY department_name, program_name";
$adds_result = $conn->query($adds_query);

// This array will hold ALL programs: $dept_programs['Department'] = ['Prog1', 'Prog2']
$dept_programs = [];
while ($row = $adds_result->fetch_assoc()) {
  $dept = $row['department_name'];
  $prog = $row['program_name'];
  if (!isset($dept_programs[$dept])) {
    $dept_programs[$dept] = [];
  }
  $dept_programs[$dept][] = $prog;
}
// --- End new query ---


// --- ✅ 3. Fetch faculty ONLY from the admin's assigned department + program ---
$faculty_data = [];

$dept_prog_stmt = $conn->prepare("
    SELECT department_name, program_name
    FROM admin_departments
    WHERE admin_idnumber = ?
");
$dept_prog_stmt->bind_param("s", $admin_id);
$dept_prog_stmt->execute();
$dept_prog_result = $dept_prog_stmt->get_result();

$conditions = [];
$params = [];
$types = '';

while ($row = $dept_prog_result->fetch_assoc()) {
  $conditions[] = "(department = ? AND program = ?)";
  $params[] = $row['department_name'];
  $params[] = $row['program_name'];
  $types .= "ss";
}
$dept_prog_stmt->close();

if (!empty($conditions)) {
  $faculty_query = "
        SELECT idnumber, first_name, mid_name, last_name
        FROM faculty
        WHERE status = 'active' 
        AND (" . implode(" OR ", $conditions) . ")
        ORDER BY last_name, first_name
    ";

  $faculty_stmt = $conn->prepare($faculty_query);
  $faculty_stmt->bind_param($types, ...$params);
  $faculty_stmt->execute();
  $faculty_result = $faculty_stmt->get_result();

  while ($row = $faculty_result->fetch_assoc()) {
    $faculty_data[] = $row;
  }
  $faculty_stmt->close();
}

// --- End faculty fetch ---
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
</head>

<body>

  <?php include 'admin-header.php' ?>
  <?php include 'admin-sidebar.php' ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Subject</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Subject</li>
          <li class="breadcrumb-item active">Add Subject</li>
        </ol>
      </nav>
    </div><?php if (isset($_SESSION['msg'])): ?>
      <script>
        // SweetAlert for success/error messages
        document.addEventListener("DOMContentLoaded", function() {
          Swal.fire({
            icon: '<?= $_SESSION['msg_type'] ?? 'info' ?>',
            title: '<?= $_SESSION['msg_type'] === "success" ? "Success!" : "Notice" ?>',
            text: '<?= addslashes($_SESSION['msg']) ?>',
            confirmButtonColor: '#198754'
          });
        });
      </script>
      <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add New Subject</h5>

              <form class="row g-3 needs-validation " novalidate method="post" action="addsubject.php">

                <div class="col-md-2">
                  <div class="form-floating">
                    <input type="text" name="code" class="form-control" id="idnumber" placeholder="Subject Code" required>
                    <label for="idnumber" class="form-label">Subject Code</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="title" class="form-control" placeholder="Descriptive Title" required>
                    <label class="form-label">Descriptive Title</label>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-floating">
                    <select name="faculty_id" class="form-select" required>
                      <option value="">-- Select Faculty --</option>
                      <?php foreach ($faculty_data as $f): ?>
                        <option value="<?= $f['idnumber'] ?>"><?= $f['last_name'] ?>, <?= $f['first_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                    <label for="faculty_id">Faculty</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <h6>Where to Assign (College)</h6>
                  <div class="form-floating">
                    <select name="department" id="department" class="form-select" required>
                      <option value="" disabled selected>-- Select Department --</option>
                      <?php foreach (array_keys($dept_programs) as $dept): ?>
                        <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <label for="department">College</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <h6>Where to Assign (Program)</h6>
                  <div class="form-floating">
                    <select name="program" id="program" class="form-select" required disabled>
                      <option value="" disabled selected>-- Select Program --</option>
                    </select>
                    <label for="program">Program</label>
                  </div>
                </div>

                <div class="col-4 offset-4">
                  <button class="btn btn-success w-100" name="addsubject" id="create" type="submit">Add Subject</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main><?php include 'footer.php'; ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>


  <script>
    // Pass the PHP array of ALL programs to JavaScript
    const allData = <?php echo json_encode($dept_programs); ?>;

    document.addEventListener('DOMContentLoaded', function() {
      const deptSelect = document.getElementById('department');
      const progSelect = document.getElementById('program');

      deptSelect.addEventListener('change', function() {
        // Clear and disable children
        progSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';
        progSelect.disabled = true;

        const selectedDept = this.value;

        // Check if the selected department has any programs in our list
        if (!selectedDept || !allData[selectedDept]) {
          progSelect.innerHTML = '<option value="" disabled selected>No programs found</option>';
          return;
        }

        const programs = allData[selectedDept];

        if (programs.length > 0) {
          programs.forEach(prog => {
            const option = new Option(prog, prog);
            progSelect.add(option);
          });
          progSelect.disabled = false;
        } else {
          progSelect.innerHTML = '<option value="" disabled selected>No programs found</option>';
        }
      });
    });
  </script>

</body>

</html>
<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a registrar
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// --- ✅ FIX 1: This query is NOW ONLY for Departments and Programs ---
$query_dept_prog = "SELECT DISTINCT department_name, program_name 
                  FROM adds 
                  WHERE department_name IS NOT NULL AND department_name != '' 
                    AND program_name IS NOT NULL AND program_name != ''
                  ORDER BY department_name, program_name";

$result_dept_prog = $conn->query($query_dept_prog);
if (!$result_dept_prog) {
  die("Query Failed: " . $conn->error);
}

// This array is now simpler: $data['Department'] = ['Program 1', 'Program 2']
$data = [];
while ($row = $result_dept_prog->fetch_assoc()) {
  $dept = $row['department_name'];
  $prog = $row['program_name'];

  if (!isset($data[$dept])) {
    $data[$dept] = [];
  }
  if (!in_array($prog, $data[$dept])) {
    $data[$dept][] = $prog;
  }
}

// --- ✅ FIX 2: Add a NEW, SEPARATE query just for sections ---
$sections_result = $conn->query("SELECT DISTINCT section_name 
                                FROM adds 
                                WHERE section_name IS NOT NULL AND section_name != '' 
                                ORDER BY section_name ASC");
if (!$sections_result) {
  die("Query Failed: " . $conn->error);
}
// --- End data fetch ---

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
      <h1>Student</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item ">Student</li>
          <li class="breadcrumb-item active">Add New Student</li>
        </ol>
      </nav>
    </div>
    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title text-center">Create New Student</h5>
              <form class="row g-3 needs-validation" novalidate method="post" action="studentcreation.php">

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="idnumber" class="form-control" id="idnumber" placeholder="ID Number" pattern="^[0-9\-]+$" required>
                    <label for="idnumber" class="form-label">ID Number</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                    <label class="form-label">First Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="mid_name" class="form-control" placeholder="Middle Name" required>
                    <label class="form-label">Middle Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    <label class="form-label">Last Name</label>
                  </div>
                </div>

                <input type="hidden" name="password" value="ILOVEDMMMSU">

                <div class="col-md-6">
                  <div class="form-floating">
                    <select name="section" id="section" class="form-select" required>
                      <option value="" disabled selected>Select Section</option>
                      <?php while ($row = $sections_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['section_name']) ?>"><?= htmlspecialchars($row['section_name']) ?></option>
                      <?php endwhile; ?>
                    </select>
                    <label for="section">Section</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select name="department" id="department" class="form-select" required>
                      <option value="" disabled selected>Select Department</option>
                    </select>
                    <label for="department">College</label>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-floating">
                    <select name="program" id="program" class="form-select" required disabled>
                      <option value="" disabled selected>Select Program</option>
                    </select>
                    <label for="program">Program</label>
                  </div>
                </div>

                <div class="col-4 offset-4">
                  <button class="btn btn-success w-100" name="submit" id="create" type="submit">Create Account</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    const allData = <?php echo json_encode($data); ?>;

    document.addEventListener('DOMContentLoaded', function() {
      const deptSelect = document.getElementById('department');
      const progSelect = document.getElementById('program');
      // const secSelect = document.getElementById('section'); // No longer needed

      // 1. Populate Departments
      const departments = Object.keys(allData);
      departments.forEach(dept => {
        const option = new Option(dept, dept);
        deptSelect.add(option);
      });

      // 2. Department Change Event
      deptSelect.addEventListener('change', function() {
        // Clear and disable program dropdown
        progSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';
        progSelect.disabled = true;

        // --- We no longer touch the section dropdown ---

        const selectedDept = this.value;
        if (!selectedDept) return;

        const programs = allData[selectedDept] || [];

        if (programs.length > 0) {
          programs.forEach(prog => {
            const option = new Option(prog, prog);
            progSelect.add(option);
          });
          progSelect.disabled = false;
        }
      });

      // --- DELETED the 'progSelect.addEventListener' block ---

    });
  </script>

  <?php if (isset($_SESSION['msg'])): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          icon: '<?= $_SESSION['msg_type'] === 'success' ? 'success' : ($_SESSION['msg_type'] === 'danger' ? 'error' : ($_SESSION['msg_type'] === 'warning' ? 'warning' : 'info')) ?>',
          title: '<?= addslashes($_SESSION['msg']) ?>',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
        });
      });
    </script>
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
  <?php endif; ?>

</body>

</html>
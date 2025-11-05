<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "Student ID is missing.";
  exit();
}

$student_id = $_GET['id'];

// Fetch student data
$stmt = $conn->prepare("SELECT * FROM student WHERE idnumber = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
  echo "Student not found.";
  exit();
}

// --- ✅ MODIFICATION 1: Fetch all data for dynamic dropdowns ---
$query = "SELECT DISTINCT department_name, program_name, section_name 
          FROM adds 
          WHERE department_name IS NOT NULL AND department_name != '' 
            AND program_name IS NOT NULL AND program_name != ''
          ORDER BY department_name, program_name, section_name";

$result = $conn->query($query);
if (!$result) {
  die("Query Failed: " . $conn->error);
}

$data = [];
while ($row = $result->fetch_assoc()) {
  $dept = $row['department_name'];
  $prog = $row['program_name'];
  $sect = $row['section_name']; // This might be NULL, and that's okay

  if (!isset($data[$dept])) {
    $data[$dept] = [];
  }
  if (!isset($data[$dept][$prog])) {
    $data[$dept][$prog] = [];
  }
  if ($sect && !in_array($sect, $data[$dept][$prog])) {
    $data[$dept][$prog][] = $sect;
  }
}
// --- End data fetch ---


// --- ✅ MODIFICATION 2: Handle form submission with 'program' ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_department = $_POST['department'];
  $new_program = $_POST['program']; // Added this
  $new_section = $_POST['section'];

  // Added 'program = ?'
  $update = $conn->prepare("UPDATE student SET department = ?, program = ?, section = ? WHERE idnumber = ?");
  // Added '$new_program' and changed type string to "ssss"
  $update->bind_param("ssss", $new_department, $new_program, $new_section, $student_id);

  if ($update->execute()) {
    // We must re-fetch the student data AFTER update
    $stmt = $conn->prepare("SELECT * FROM student WHERE idnumber = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    // Set success message in session
    $_SESSION['msg'] = 'Student information updated successfully!';
    $_SESSION['msg_type'] = 'success';

    header("Location: register-editstudent.php?id=$student_id");
    exit();
  } else {
    echo "Update failed.";
  }
}

// Section fetch for dropdown
$sections_result = $conn->query("SELECT DISTINCT section_name FROM adds WHERE section_name IS NOT NULL AND section_name != '' ORDER BY section_name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

  <?php include 'register-header.php' ?>
  <?php include 'register-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Student</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="register-studentlist.php">Student</a></li>
          <li class="breadcrumb-item">List</li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          Swal.fire({
            icon: '<?= $_SESSION['msg_type'] ?>',
            title: '<?= $_SESSION['msg_type'] === 'success' ? 'Success!' : 'Error!' ?>',
            text: '<?= addslashes($_SESSION['msg']) ?>',
            timer: 2000,
            showConfirmButton: false,
          });
        });
      </script>
      <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>


    <section class="section ">
      <div class="row justify-content-center">
        <div class="card col-lg-6 ">
          <div class="card-body ">
            <h5 class="card-title">Student Information</h5>

            <form method="POST">
              <div class="row">
                
                <div class="col-md-3 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($student['idnumber']) ?>" disabled>
                    <label class="form-label">ID Number</label>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control"
                      value="<?= htmlspecialchars($student['first_name'] . ' ' . $student['mid_name'] . ' ' . $student['last_name']) ?>"
                      disabled>
                    <label class="form-label">Full Name</label>
                  </div>
                </div>


                <div class="col-md-3">
                  <div class="form-floating mb-3">
                    <select class="form-select" name="section" id="section" required>
                      <option value="" disabled>Select Section</option>
                      <?php while ($row = $sections_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['section_name']) ?>" <?= $student['section'] === $row['section_name'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($row['section_name']) ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                    <label for="section">Section</label>
                  </div>
                </div>

              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <select name="department" class="form-select" id="department" required>
                      <option value="" disabled>Select Department</option>
                    </select>
                    <label for="department">Department</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <select class="form-select" name="program" id="program" required>
                      <option value="" disabled>Select Program</option>
                    </select>
                    <label for="program">Program</label>
                  </div>
                </div>


              </div>

              <button type="submit" class="btn btn-success">Update Student</button>
              <a href="register-studentlist.php" class="btn btn-secondary">Back</a>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main><?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    const allData = <?php echo json_encode($data); ?>;

    // Get student's current values
    const currentDept = "<?= htmlspecialchars($student['department']) ?>";
    const currentProg = "<?= htmlspecialchars($student['program']) ?>";
    // const currentSect = "<?= htmlspecialchars($student['section']) ?>"; // Not needed by JS

    document.addEventListener('DOMContentLoaded', function() {
      const deptSelect = document.getElementById('department');
      const progSelect = document.getElementById('program');
      // --- Section select is no longer controlled by JavaScript ---

      // --- Function to populate Programs ---
      function populatePrograms(selectedDept) {
        progSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';
        progSelect.disabled = true;

        const programs = allData[selectedDept] || {};
        const programNames = Object.keys(programs);

        if (programNames.length > 0) {
          programNames.forEach(prog => {
            const option = new Option(prog, prog);
            progSelect.add(option);
          });
          progSelect.disabled = false;
        }
      }

      // --- 1. Populate Departments on Page Load ---
      const departments = Object.keys(allData);
      departments.forEach(dept => {
        const option = new Option(dept, dept);
        deptSelect.add(option);
      });

      // --- 2. Set Initial Values for Dept and Prog ---
      if (currentDept) {
        deptSelect.value = currentDept;
        populatePrograms(currentDept); // Load programs
        if (currentProg) {
          progSelect.value = currentProg; // Set selected program
        }
      }

      // --- 3. Add Event Listener for Dept ---
      deptSelect.addEventListener('change', function() {
        populatePrograms(this.value);
      });

      // --- 4. DELETED progSelect.addEventListener ---
      // (It is no longer needed as it only controlled sections)
    });
  </script>

</body>

</html>
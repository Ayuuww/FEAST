<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Restrict access
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// ✅ --- FIX 1: Handle Delete Request ---
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  try {
    $stmt = $conn->prepare("DELETE FROM department_info WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['alert'] = [
      'type' => 'success',
      'title' => 'Deleted!',
      'text' => 'Department information has been deleted.'
    ];
  } catch (mysqli_sql_exception $e) {
    $_SESSION['alert'] = [
      'type' => 'error',
      'title' => 'Error!',
      'text' => 'Could not delete department. It might be in use.'
    ];
  }
  header("Location: register-department-info.php");
  exit();
}


// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ✅ --- FIX 2: Changed 'college_name' to 'program_name' ---
  $dept = trim($_POST['department_name']);
  $program = trim($_POST['program_name']); // <-- RENAMED
  $website = trim($_POST['website']);
  $phone = trim($_POST['phone']);
  $email = trim($_POST['email']);

  // Add Department
  if (isset($_POST['add'])) {
    // Check if combination already exists
    $check = $conn->prepare("SELECT COUNT(*) FROM department_info WHERE department_name = ? AND program_name = ?");
    $check->bind_param("ss", $dept, $program);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
      $_SESSION['alert'] = [
        'type' => 'error',
        'title' => 'Duplicate Entry',
        'text' => 'This department and program combination already exists.'
      ];
    } else {
      // ✅ Updated query to use program_name
      $stmt = $conn->prepare("INSERT INTO department_info (department_name, program_name, website, phone, email) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("sssss", $dept, $program, $website, $phone, $email);
      $stmt->execute();

      $_SESSION['alert'] = [
        'type' => 'success',
        'title' => 'Added Successfully',
        'text' => 'New department info has been added.'
      ];
    }
    header("Location: register-department-info.php");
    exit();
  }

  // Update Department (This is unused in this file, but fixed for your 'edit' page)
  if (isset($_POST['update'])) {
    $id = $_POST['id'];
    // ✅ Updated query to use program_name
    $stmt = $conn->prepare("UPDATE department_info SET department_name=?, program_name=?, website=?, phone=?, email=? WHERE id=?");
    $stmt->bind_param("sssssi", $dept, $program, $website, $phone, $email, $id);
    $stmt->execute();

    $_SESSION['alert'] = [
      'type' => 'success',
      'title' => 'Updated Successfully',
      'text' => 'Department information has been updated.'
    ];
    header("Location: register-department-info.php");
    exit();
  }
}


// Fetch all departments info for the LIST
$list_result = $conn->query("SELECT * FROM department_info ORDER BY department_name ASC");

// Fetch distinct departments from 'adds' table for the DROPDOWN
$departments_result = $conn->query("SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != '' ORDER BY department_name ASC");

// ✅ --- FIX 3: Fetch all Dept/Prog relationships for dependent dropdown ---
$program_query = $conn->query("SELECT DISTINCT department_name, program_name FROM adds WHERE department_name IS NOT NULL AND department_name != '' AND program_name IS NOT NULL AND program_name != '' ORDER BY program_name ASC");
$dept_programs = [];
while ($row = $program_query->fetch_assoc()) {
  $dept_programs[$row['department_name']][] = $row['program_name'];
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
      <h1>Department Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Department Information</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-12">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Add New Department Info</h5>

              <form method="POST" class="mb-4">
                <div class="row gy-3 gx-3">

                  <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Department</label>
                    <select name="department_name" id="department" class="form-select" required>
                      <option value="">Select Department</option>
                      <?php while ($d = $departments_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($d['department_name']) ?>">
                          <?= htmlspecialchars($d['department_name']) ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Program</label>
                    <select name="program_name" id="program" class="form-select" required disabled>
                      <option value="">Select Department First</option>
                    </select>
                  </div>

                  <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold">Website</label>
                    <input type="text" name="website" class="form-control" placeholder="Website">
                  </div>

                  <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="Phone">
                  </div>

                  <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email">
                  </div>

                </div>

                <div class="mt-3 text-end">
                  <button type="submit" name="add" class="btn btn-success btn-sm px-4">
                    Add Department Info
                  </button>
                </div>
              </form>

              <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                  <thead class="table-success">
                    <tr>
                      <th>College/Department</th>
                      <th>Program</th>
                      <th>Website</th>
                      <th>Phone</th>
                      <th>Email</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $list_result->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['department_name']) ?></td>
                        <td><?= htmlspecialchars($row['program_name']) ?></td>
                        <td><?= htmlspecialchars($row['website']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td class="text-center">
                          <a href="register-department-edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-1">
                            Edit
                          </a>
                          <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id'] ?>)">
                            Delete
                          </button>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>

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
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // SweetAlert2 delete confirmation
    function confirmDelete(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "This department info will be permanently deleted.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
      }).then((result) => {
        if (result.isConfirmed) {
          // This now works because of the PHP fix at the top
          window.location.href = "?delete=" + id;
        }
      });
    }

    // General session alert (for add/update errors/success)
    <?php if (isset($_SESSION['alert'])): ?>
      Swal.fire({
        icon: '<?= $_SESSION['alert']['type'] ?>',
        title: '<?= $_SESSION['alert']['title'] ?>',
        text: '<?= $_SESSION['alert']['text'] ?>',
        showConfirmButton: true,
      });
      <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>


    // --- NEW: Dependent Dropdown Logic ---
    const allPrograms = <?= json_encode($dept_programs) ?>;

    document.getElementById('department').addEventListener('change', function() {
      const programSelect = document.getElementById('program');
      const selectedDept = this.value;

      // Clear old options
      programSelect.innerHTML = '<option value="">Select Program</option>';

      if (selectedDept && allPrograms[selectedDept]) {
        // Enable and populate new options
        programSelect.disabled = false;
        allPrograms[selectedDept].forEach(function(program) {
          programSelect.add(new Option(program, program));
        });
      } else {
        // Disable if no dept or no programs
        programSelect.disabled = true;
        programSelect.innerHTML = '<option value="">Select Department First</option>';
      }
    });
  </script>

</body>

</html>
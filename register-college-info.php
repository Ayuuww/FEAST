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
    $stmt = $conn->prepare("DELETE FROM college_info WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['alert'] = [
      'type' => 'success',
      'title' => 'Deleted!',
      'text' => 'college information has been deleted.'
    ];
  } catch (mysqli_sql_exception $e) {
    $_SESSION['alert'] = [
      'type' => 'error',
      'title' => 'Error!',
      'text' => 'Could not delete college. It might be in use.'
    ];
  }
  header("Location: register-college-info.php");
  exit();
}


// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ✅ --- FIX 2: Changed 'college_name' to 'program_name' ---
  $dept = trim($_POST['college_name']);
  $program = trim($_POST['program_name']); // <-- RENAMED
  $website = trim($_POST['website']);
  $phone = trim($_POST['phone']);
  $email = trim($_POST['email']);

  // Add college
  if (isset($_POST['add'])) {
    // Check if combination already exists
    $check = $conn->prepare("SELECT COUNT(*) FROM college_info WHERE college_name = ? AND program_name = ?");
    $check->bind_param("ss", $dept, $program);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
      $_SESSION['alert'] = [
        'type' => 'error',
        'title' => 'Duplicate Entry',
        'text' => 'This college and program combination already exists.'
      ];
    } else {
      // ✅ Updated query to use program_name
      $stmt = $conn->prepare("INSERT INTO college_info (college_name, program_name, website, phone, email) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("sssss", $dept, $program, $website, $phone, $email);
      $stmt->execute();

      $_SESSION['alert'] = [
        'type' => 'success',
        'title' => 'Added Successfully',
        'text' => 'New college info has been added.'
      ];
    }
    header("Location: register-college-info.php");
    exit();
  }

  // Update college (This is unused in this file, but fixed for your 'edit' page)
  if (isset($_POST['update'])) {
    $id = $_POST['id'];
    // ✅ Updated query to use program_name
    $stmt = $conn->prepare("UPDATE college_info SET college_name=?, program_name=?, website=?, phone=?, email=? WHERE id=?");
    $stmt->bind_param("sssssi", $dept, $program, $website, $phone, $email, $id);
    $stmt->execute();

    $_SESSION['alert'] = [
      'type' => 'success',
      'title' => 'Updated Successfully',
      'text' => 'college information has been updated.'
    ];
    header("Location: register-college-info.php");
    exit();
  }
}


// Fetch all college info for the LIST
$list_result = $conn->query("SELECT * FROM college_info ORDER BY college_name ASC");

// Fetch distinct college from 'adds' table for the DROPDOWN
$college_result = $conn->query("SELECT DISTINCT college_name FROM adds WHERE college_name IS NOT NULL AND college_name != '' ORDER BY college_name ASC");

// ✅ --- FIX 3: Fetch all Dept/Prog relationships for dependent dropdown ---
$program_query = $conn->query("SELECT DISTINCT college_name, program_name FROM adds WHERE college_name IS NOT NULL AND college_name != '' AND program_name IS NOT NULL AND program_name != '' ORDER BY program_name ASC");
$dept_programs = [];
while ($row = $program_query->fetch_assoc()) {
  $dept_programs[$row['college_name']][] = $row['program_name'];
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
      <h1>College Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">College Information</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-12">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Add New College Info</h5>

              <form method="POST" class="mb-4">
                <div class="row gy-3 gx-3">

                  <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">College</label>
                    <select name="college_name" id="college" class="form-select" required>
                      <option value="">Select College</option>
                      <?php while ($d = $college_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($d['college_name']) ?>">
                          <?= htmlspecialchars($d['college_name']) ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Program</label>
                    <select name="program_name" id="program" class="form-select" required disabled>
                      <option value="">Select College First</option>
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
                    Add College Info
                  </button>
                </div>
              </form>

              <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                  <thead class="table-success">
                    <tr>
                      <th>College</th>
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
                        <td><?= htmlspecialchars($row['college_name']) ?></td>
                        <td><?= htmlspecialchars($row['program_name']) ?></td>
                        <td><?= htmlspecialchars($row['website']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td class="text-center">
                          <a href="register-college-edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-1">
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
        text: "This college info will be permanently deleted.",
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

    document.getElementById('college').addEventListener('change', function() {
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
        programSelect.innerHTML = '<option value="">Select College First</option>';
      }
    });
  </script>

</body>

</html>
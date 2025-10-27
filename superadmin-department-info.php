<?php
session_start();
include 'conn/conn.php';

// Restrict access
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Handle Add/Update/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $dept = $_POST['department_name'];
  $college = $_POST['college_name'];
  $website = $_POST['website'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];

  if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO department_info (department_name, college_name, website, phone, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $dept, $college, $website, $phone, $email);
    $stmt->execute();
  }

  if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE department_info SET department_name=?, college_name=?, website=?, phone=?, email=? WHERE id=?");
    $stmt->bind_param("sssssi", $dept, $college, $website, $phone, $email, $id);
    $stmt->execute();
  }
}

if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $conn->query("DELETE FROM department_info WHERE id = '$id'");
  header("Location: superadmin-department-info.php?deleted=1");
  exit();
}

// Fetch all departments
$result = $conn->query("SELECT * FROM department_info ORDER BY department_name ASC");

// Fetch distinct department names from adds table
$departments = $conn->query("SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != '' ORDER BY department_name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <!-- SweetAlert2 -->
  <script src="sweetalert2/sweetalert2@11.js"></script>
</head>

<body>

  <?php include 'superadmin-header.php' ?>
  <?php include 'superadmin-sidebar.php' ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Department Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
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

              <!-- Add Department Form -->
              <form method="POST" class="mb-4">
                <div class="row gy-3 gx-3">

                  <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Department</label>
                    <select name="department_name" class="form-select" required>
                      <option value="">Select Department</option>
                      <?php while ($d = $departments->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($d['department_name']) ?>">
                          <?= htmlspecialchars($d['department_name']) ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">College Name</label>
                    <input type="text" name="college_name" class="form-control" placeholder="College Name" required>
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

              <!-- Department List -->
              <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                  <thead class="table-success">
                    <tr>
                      <th>Department</th>
                      <th>College</th>
                      <th>Website</th>
                      <th>Phone</th>
                      <th>Email</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['department_name']) ?></td>
                        <td><?= htmlspecialchars($row['college_name']) ?></td>
                        <td><?= htmlspecialchars($row['website']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td class="text-center">
                          <a href="superadmin-department-edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-1">
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

  <!-- Vendor JS Files -->
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // SweetAlert2 delete confirmation
    function confirmDelete(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "This department will be permanently deleted.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "?delete=" + id;
        }
      });
    }

    // Success notification if deleted
    <?php if (isset($_GET['deleted'])): ?>
      Swal.fire({
        icon: 'success',
        title: 'Deleted!',
        text: 'Department deleted successfully.',
        showConfirmButton: false,
        timer: 1500
      });
    <?php endif; ?>
  </script>

</body>

</html>
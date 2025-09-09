<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "Student ID is missing.";
  exit();
}

$student_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM student WHERE idnumber = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
  echo "Student not found.";
  exit();
}

$departments = [];
$sections = [];

$dept_result = $conn->query("SELECT department_name FROM adds WHERE department_name IS NOT NULL ORDER BY department_name ASC");
while ($row = $dept_result->fetch_assoc()) {
  $departments[] = $row['department_name'];
}

$section_result = $conn->query("SELECT section_name FROM adds WHERE section_name IS NOT NULL ORDER BY section_name ASC");
while ($row = $section_result->fetch_assoc()) {
  $sections[] = $row['section_name'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_department = $_POST['department'];
  $new_section = $_POST['section'];

  $update = $conn->prepare("UPDATE student SET department = ?, section = ? WHERE idnumber = ?");
  $update->bind_param("sss", $new_department, $new_section, $student_id);
  if ($update->execute()) {
    header("Location: superadmin-editstudent.php?id=$student_id&update=success");
    exit();
  } else {
    echo "Update failed.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'superadmin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Student</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item"><a href="superadmin-studentlist.php">Student</a></li>
          <li class="breadcrumb-item">List</li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Student information updated successfully!',
          timer: 2000,
          showConfirmButton: false,
        });

        // Remove ?update=success from URL
        if (window.location.search.includes('update=success')) {
          const url = new URL(window.location);
          url.searchParams.delete('update');
          window.history.replaceState({}, document.title, url.pathname + url.search);
        }
      </script>
    <?php endif; ?>

    <section class="section ">
      <div class="row justify-content-center">
        <div class="card col-lg-6 ">
          <div class="card-body ">
            <h5 class="card-title">Student Information</h5>


            <form method="POST">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control"
                      value="<?= $student['first_name'] . ' ' . $student['mid_name'] . ' ' . $student['last_name'] ?>"
                      disabled>
                    <label class="form-label">Full Name</label>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" value="<?= $student['idnumber'] ?>" disabled>
                    <label class="form-label">ID Number</label>
                  </div>
                </div>

              </div>

              <div class="row">

                <div class="col-md-6 ">
                  <div class="form-floating mb-3">
                    <select name="department" class="form-select" id="departmentSelect" required>
                      <option value="" disabled <?= empty($student['department']) ? 'selected' : '' ?>>Select Department</option>
                      <?php foreach ($departments as $dept): ?>
                        <option value="<?= htmlspecialchars($dept) ?>" <?= $student['department'] === $dept ? 'selected' : '' ?>>
                          <?= htmlspecialchars($dept) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <label for="departmentSelect">Department</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <select class="form-select" name="section" required>
                      <option value="" disabled <?= empty($student['section']) ? 'selected' : '' ?>>Select Section</option>
                      <?php foreach ($sections as $sec): ?>
                        <option value="<?= htmlspecialchars($sec) ?>" <?= $student['section'] === $sec ? 'selected' : '' ?>>
                          <?= htmlspecialchars($sec) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <label for="section">Section</label>
                  </div>
                </div>

              </div>

              <button type="submit" class="btn btn-success">Update Status</button>
              <a href="superadmin-studentlist.php" class="btn btn-secondary">Back</a>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main><!-- end of main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php' ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <!-- <script>
    setTimeout(function () {
      let alert = document.querySelector('.alert');
      if (alert) {
        alert.classList.remove('show');
        alert.classList.add('hide');
      }
    }, 3000); // Hide after 3 seconds
  </script> -->

</body>

</html>
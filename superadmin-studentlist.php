<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Fetch student data for listing
$query = "SELECT * FROM student WHERE role = 'student'";
$result = mysqli_query($conn, $query);

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
      <h1>List of Students</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Student</li>
          <li class="breadcrumb-item active">List</li>
        </ol>
      </nav>
    </div>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body table-responsive">
              <h5 class="card-title">List of Students</h5>

              <table class="table datatable table-hover">
                <thead>
                  <tr>
                    <th><b>ID Number</b></th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Department</th>
                    <th>Program</th>
                    <th>Section</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // ✅ --- START FIX ---
                  // The 'while' loop should wrap the <tr> tags
                  while ($row = mysqli_fetch_assoc($result)) {
                  ?>
                    <tr>
                      <td><?php echo htmlspecialchars($row['idnumber']); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['first_name']); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['mid_name']); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['last_name']); ?></td>
                      <td class="text-uppercase"><?php echo htmlspecialchars($row['department']); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['program'] ?? 'N/A'); ?></td>
                      <td class="text-uppercase"><?php echo htmlspecialchars($row['section']); ?></td>
                      <td>
                        <a href="superadmin-editstudent.php?id=<?php echo htmlspecialchars($row['idnumber']); ?>" class="btn btn-warning btn-sm">Edit</a>
                      </td>
                    </tr>
                  <?php
                  } // ✅ --- END FIX ---
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main><?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>

  <script src="assets/js/main.js"></script>

</body>

</html>
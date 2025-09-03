<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Fetch student data for listing
$query = "SELECT * FROM faculty  WHERE role = 'faculty'";
$result = mysqli_query($conn, $query);




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
      <h1>List of Faculty Members</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Faculty</li>
          <li class="breadcrumb-item active">List</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section ">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body table-responsive">
              <h5 class="card-title">Datatables</h5>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>
                      <b>ID Number</b>
                    </th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Academic Rank</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                      <td class="text-capitalize"><?php echo $row['idnumber']; ?></td>
                      <td class="text-capitalize"><?php echo $row['first_name']; ?></td>
                      <td class="text-capitalize"><?php echo $row['mid_name']; ?></td>
                      <td class="text-capitalize"><?php echo $row['last_name']; ?></td>
                      <td class="text-capitalize"><?php echo $row['faculty_rank']; ?></td>
                      <td class="text-uppercase"><?php echo $row['department']; ?></td>
                      <td class="text-capitalize"><?php echo $row['status']; ?></td>
                      <td>
                        <a href="superadmin-editfaculty.php?id=<?php echo $row['idnumber']; ?>" class="btn btn-warning btn-sm">Edit</a>
                  </tr>
                <?php
                    }
                ?>
                </tr>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php'?>
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

</body>

</html>
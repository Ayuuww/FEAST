<?php

session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get the admin's department
$dept_query = mysqli_query($conn, "SELECT department FROM admin WHERE idnumber = '$admin_id' LIMIT 1");
$admin_dept = '';

if ($dept_query && mysqli_num_rows($dept_query) > 0) {
  $admin_data = mysqli_fetch_assoc($dept_query);
  $admin_dept = $admin_data['department'];
}


// Fetching subjects and faculty names
$query = "  SELECT subject.*,
            COALESCE(faculty.first_name, admin.first_name) AS first_name,
            COALESCE(faculty.mid_name, admin.mid_name) AS mid_name,
            COALESCE(faculty.last_name, admin.last_name) AS last_name,
            CASE
                WHEN subject.faculty_id IS NOT NULL THEN 'Faculty'
                WHEN subject.admin_id IS NOT NULL THEN 'Admin'
                ELSE 'Unknown'
            END AS handler_role
            FROM subject
            LEFT JOIN faculty ON subject.faculty_id = faculty.idnumber
            LEFT JOIN admin ON subject.admin_id = admin.idnumber
            WHERE subject.department = '$admin_dept'";

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

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>List of Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Subject</li>
          <li class="breadcrumb-item active">List</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($_SESSION['msg'])) : ?>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          Swal.fire({
            icon: '<?php echo $_SESSION['msg_type'] ?? 'info'; ?>', // Use session msg_type for icon
            title: '<?php echo $_SESSION['msg']; ?>',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
        });
      </script>
      <?php
      unset($_SESSION['msg']);
      unset($_SESSION['msg_type']); // Unset msg_type as well
      ?>
    <?php endif; ?>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body table-responsive">
              <h5 class="card-title">Datatables</h5>

              <table class="table datatable">
                <thead>
                  <tr>
                    <th>
                      <b>Subject Code</b>
                    </th>
                    <th>Descriptive Title</th>
                    <th>Faculty Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                      <td class="text-uppercase"><?php echo $row['code']; ?></td>
                      <td class="text-capitalize"><?php echo $row['title']; ?></td>
                      <td class="text-capitalize">
                        <?php echo $row['first_name'] . " " . $row['mid_name'] . " " . $row['last_name']; ?>
                      </td>
                      <td>
                        <form method="post" class="delete-form" action="deletesubject.php">
                          <input type="hidden" name="code" value="<?php echo $row['code']; ?>">
                          <button type="button" class="btn btn-danger btn-sm delete-btn" data-subject="<?php echo htmlspecialchars($row['title']); ?>">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

  <?php include 'footer.php' ?>

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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          const form = this.closest('form');
          const subjectName = this.getAttribute('data-subject');

          Swal.fire({
            title: `Delete "${subjectName}"?`,
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              form.submit();
            }
          });
        });
      });
    });
  </script>

</body>

</html>
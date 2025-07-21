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

// Display messages if set
if (isset($_SESSION['msg'])) {
  echo "<script>alert('" . $_SESSION['msg'] . "');</script>";
  unset($_SESSION['msg']);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Subject List</title>

  <?php include 'header.php' ?>

</head>

<body>

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <!-- Evaluate Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-evaluate.php">
              <i class="bi bi-circle"></i><span>Form</span>
            </a>
          </li>
          <li>
            <a href="admin-evaluatedfaculty.php">
              <i class="bi bi-circle"></i><span>Evaluated Faculty</span>
            </a>
          </li>
        </ul>
      </li><!-- End Evaluate Nav -->

      <!-- Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" data-bs-target="#subject" data-bs-toggle="collapse" href="#">
          <i class="ri-book-line"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="subject" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-subjectlist.php" class="active">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="admin-subjectadding.php">
              <i class="bi bi-circle"></i><span>Add Subject</span>
            </a>
          </li>
        </ul>
      </li><!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-studentsubject.php">
          <i class="ri-book-fill"></i>
          <span>Assign Subject</span>
        </a>
      </li><!-- End Student Subject Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-evaluatedsubject.php">
          <i class="bi bi-book-fill"></i>
          <span>Subject Evaluated</span>
        </a>
      </li><!-- End Profile Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-user-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End Sign out Nav -->


    </ul>

  </aside><!-- End Sidebar-->

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
    </div><!-- End Page Title -->

    <section class="section">
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
                      <b>Subject Code</b>
                    </th>
                    <th>Descriptive Title</th>
                    <th>Faculty Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                  <tr>
                    <td class="text-uppercase"><?php echo $row['code']; ?></td>
                    <td class="text-capitalize"><?php echo $row['title']; ?></td>
                    <td class="text-capitalize">
                      <?php echo $row['first_name'] . " " . $row['mid_name'] . " " . $row['last_name']; ?>
                      <!-- <small class="text-muted">(<?php echo $row['handler_role']; ?>)</small> -->
                    </td>
                    <td>
                      <form method="post" style="display:inline;" action="deletesubject.php">
                        <input type="hidden" name="code" value="<?php echo $row['code']; ?>">
                        <button class="btn btn-danger btn-sm" name="delete" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php } ?>
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
  <?php include 'footer.php' ?>>
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
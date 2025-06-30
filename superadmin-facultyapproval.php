<?php
session_start();
include 'conn/conn.php';// Connection to the database
include 'approval.php';// Contains functions for approving and rejecting faculty members

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

// Fetch faculty data for approval
$query = "SELECT * FROM register WHERE status = 'pending' AND role = 'faculty'";
$result = mysqli_query($conn, $query);



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / FacultyApproval</title>
  <?php include 'header.php'?>
</head>

<body>

  <?php include 'superadmin-header.php'?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="superadmin-dashboard.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </a>
      </li> 
      <!-- End Dashboard Nav -->

      <!-- Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-book"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-subjectlist.php" >
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-subjectadding.php">
              <i class="bi bi-circle"></i><span>Add Subject</span>
            </a>
          </li>
        </ul>
      </li><!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="superadmin-studentsubject.php">
          <i class="bi bi-book-fill"></i>
          <span>Assign Subject</span>
        </a>
      </li><!-- End Student Subject Nav -->

      <!-- Report Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="superadmin-reports.php">
          <i class="bi bi-journal-text"></i>
          <span>Reports</span>
        </a>
      </li><!-- End Report Nav -->

      <li class="nav-heading">Account Management</li>

        <!-- Faculty Nav -->
        <li class="nav-item">
          <a class="nav-link collapse" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-people-fill"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-facultylist.php">
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-facultyapproval.php" class="active">
                <i class="bi bi-circle"></i><span>Approval</span>
              </a>
            </li>
          </ul>
        </li><!-- End Faculty Nav -->
        
        <!-- Student Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-people"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-studentlist.php">
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-studentapproval.php">
                <i class="bi bi-circle"></i><span>Approval</span>
              </a>
            </li>
          </ul>
        </li><!-- End Student Nav -->

        <!-- Admin Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="admin-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-adminlist.php" >
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-admincreation.php">
                <i class="bi bi-circle"></i><span>Add New Admin</span>
              </a>
            </li>
          </ul>
        </li><!-- End Admin Nav -->

        <!-- Super Admin Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person-fill"></i><span>Super Admin</span><i
              class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-superadminlist.php">
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-superadmincreation.php">
                <i class="bi bi-circle"></i><span>Add New SuperAdmin</span>
              </a>
            </li>
          </ul>
        </li><!-- End Super Admin Nav -->

      <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-user-profile.php">
            <i class="bi bi-person"></i>
            <span>Profile</span>
          </a>
        </li><!-- End Profile Page Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
          </a>
        </li><!-- End Sign Out Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>List of Faculty Members Approval</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Faculty</li>
          <li class="breadcrumb-item active">Approval</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section"><!-- Section with datatables -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
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
                    <th>Email</th>
                    <th>Department</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <?php
                      while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <td><?php echo $row['idnumber'];?></td>
                        <td><?php echo $row['first_name'];?></td>
                        <td><?php echo $row['mid_name'];?></td>
                        <td><?php echo $row['last_name'];?></td>
                        <td><?php echo $row['email'];?></td>
                        <td class="text-uppercase"><?php echo $row['department'];?></td>
                        <td>
                      <form method="post" style="display:inline;">
                        <input type="hidden" name="idnumber" value="<?php echo $row['idnumber']; ?>">
                        <button class="btn btn-success btn-sm" name="approve" type="submit">Approve</button>
                        <input type="hidden" name="idnumber" value="<?php echo $row['idnumber']; ?>">
                        <button class="btn btn-danger btn-sm" name="reject" type="submit">Reject</button>
                      </form>
                        </td>
                  </tr>
                    <?php } 
                        ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section><!-- End Section with datatables -->

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer><!-- End Footer -->

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

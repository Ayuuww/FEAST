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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_department = $_POST['department'];
    $new_section = $_POST['section'];

    $update = $conn->prepare("UPDATE student SET department = ?, section = ? WHERE idnumber = ?");
    $update->bind_param("sss", $new_department, $new_section, $student_id);
    if ($update->execute()) {
        header("Location: superadmin-studentlist.php?update=success");
        exit();
    } else {
        echo "Update failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <?php include 'header.php'; ?>
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
      </li><!-- End Dashboard Nav -->

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

        <!-- Reports Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
            <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="reports" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-individualreport.php" >
                <i class="bi bi-circle"></i><span>Invidiual Report</span>
              </a>
            </li>
            <li>
              <a href="superadmin-acknowledgementreport.php">
                <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
              </a>
            </li>
          </ul>
        </li><!-- End Reports Nav -->

        <!-- Evaluation Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#evaluation" data-bs-toggle="collapse" href="#">
            <i class="ri-settings-4-line"></i><span>Evaluation</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="evaluation" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-evaluationsetting.php" >
                <i class="bi bi-circle"></i><span>Setting</span>
              </a>
            </li>
            <li>
              <a href="superadmin-evaluationswitch.php">
                <i class="bi bi-circle"></i><span>On/Off</span>
              </a>
            </li>
          </ul>
        </li><!-- End Evalutaion Nav -->

        <li class="nav-heading">Account Management</li>

        <!-- Faculty Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-people-fill"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-facultylist.php">
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-facultycreation.php">
                <i class="bi bi-circle"></i><span>Add New Faculty</span>
              </a>
            </li>
          </ul>
        </li><!-- End Faculty Nav -->
        
        <!-- Student Nav -->
        <li class="nav-item">
          <a class="nav-link collapse" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-people"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="forms-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-studentlist.php" class="active">
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-studentcreation.php">
                <i class="bi bi-circle"></i><span>Add New Student</span>
              </a>
            </li>
          </ul>
        </li><!-- End Student Nav -->

        <!-- Admin Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="admin-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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

        <section class="section">
            <div class="card col-lg-6">
                <div class="card-body">
                    <h5 class="card-title">Student Information</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" value="<?= $student['idnumber'] ?>" disabled>
                                <label class="form-label">ID Number</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control"
                                    value="<?= $student['first_name'] . ' ' . $student['mid_name'] . ' ' . $student['last_name'] ?>"
                                    disabled>
                                <label class="form-label">Full Name</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select name="department" class="form-select" id="departmentSelect" required>
                                        <option value="" disabled <?= empty($student['department']) ? 'selected' : '' ?>>Select Department</option>
                                        <option value="CIS" <?= $student['department'] == 'CIS' ? 'selected' : '' ?>>CIS</option>
                                        <option value="CVM" <?= $student['department'] == 'CVM' ? 'selected' : '' ?>>CVM</option>
                                        <option value="CAFF" <?= $student['department'] == 'CAFF' ? 'selected' : '' ?>>CAFF</option>
                                        <!-- Optional additional departments -->
                                        <!--
                                        <option value="BEED" <?= $student['department'] == 'BEED' ? 'selected' : '' ?>>BEED</option>
                                        <option value="BSHM" <?= $student['department'] == 'BSHM' ? 'selected' : '' ?>>BSHM</option>
                                        <option value="BSTM" <?= $student['department'] == 'BSTM' ? 'selected' : '' ?>>BSTM</option>
                                        <option value="BSCRIM" <?= $student['department'] == 'BSCRIM' ? 'selected' : '' ?>>BSCRIM</option>
                                        -->
                                    </select>
                                    <label for="departmentSelect">Department</label>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="section" required>
                                        <option value="" disabled <?= empty($student['section']) ? 'selected' : '' ?>>Select Section</option>
                                        <option value="1-A" <?= $student['section'] == '1-A' ? 'selected' : '' ?>>1-A</option>
                                        <option value="1-B" <?= $student['section'] == '1-B' ? 'selected' : '' ?>>1-B</option>
                                        <option value="1-C" <?= $student['section'] == '1-C' ? 'selected' : '' ?>>1-C</option>
                                        <option value="1-D" <?= $student['section'] == '1-D' ? 'selected' : '' ?>>1-D</option>
                                        <option value="2-A" <?= $student['section'] == '2-A' ? 'selected' : '' ?>>2-A</option>
                                        <option value="2-B" <?= $student['section'] == '2-B' ? 'selected' : '' ?>>2-B</option>
                                        <option value="2-C" <?= $student['section'] == '2-C' ? 'selected' : '' ?>>2-C</option>
                                        <option value="2-D" <?= $student['section'] == '2-D' ? 'selected' : '' ?>>2-D</option>
                                        <option value="3-A" <?= $student['section'] == '3-A' ? 'selected' : '' ?>>3-A</option>
                                        <option value="3-B" <?= $student['section'] == '3-B' ? 'selected' : '' ?>>3-B</option>
                                        <option value="3-C" <?= $student['section'] == '3-C' ? 'selected' : '' ?>>3-C</option>
                                        <option value="3-D" <?= $student['section'] == '3-D' ? 'selected' : '' ?>>3-D</option>
                                        <option value="4-A" <?= $student['section'] == '4-A' ? 'selected' : '' ?>>4-A</option>
                                        <option value="4-B" <?= $student['section'] == '4-B' ? 'selected' : '' ?>>4-B</option>
                                        <option value="4-C" <?= $student['section'] == '4-C' ? 'selected' : '' ?>>4-C</option>
                                        <option value="4-D" <?= $student['section'] == '4-D' ? 'selected' : '' ?>>4-D</option>
                                    </select>
                                    <label for="section">Section</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="superadmin-studentlist.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </section>
    </main><!-- end of main -->

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

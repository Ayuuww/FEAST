<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Acknowledgement Report</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <?php include 'header.php'; ?>
  <style>
    .table th,
    .table td {
      text-align: left;
      vertical-align: top;
    }

    .signature-box {
      height: 60px;
      min-width: 250px;
      border-bottom: 1px solid #000;
    }

    .print-btn {
      margin-top: 20px;
    }
  </style>
</head>

<body>

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link" href="admin-dashboard.php">
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
        <a class="nav-link collapsed" data-bs-target="#subject" data-bs-toggle="collapse" href="#">
          <i class="ri-book-line"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="subject" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-subjectlist.php">
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

      <!-- Reports Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="reports" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-individualreport.php">
              <i class="bi bi-circle"></i><span>Invidiual Report</span>
            </a>
          </li>
          <li>
            <a href="admin-acknowledgementreport.php" class="active">
              <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
            </a>
          </li>
          <li>
            <a href="admin-overallreport-set.php">
              <i class="bi bi-circle"></i><span>Overall Report SET</span>
            </a>
          </li>
      </li>
      <li>
        <a href="admin-overallreport-sef.php">
          <i class="bi bi-circle"></i><span>Overall Report SEF</span>
        </a>
      </li>
      <li>
        <a href="admin-overallreport.php">
          <i class="bi bi-circle"></i><span>Overall Report (SET & SEF)</span>
        </a>
      </li>
      <li>
        <a href="admin-pastrecords.php">
          <i class="bi bi-circle"></i><span>Past Record</span>
        </a>
      </li>
    </ul>
    </li><!-- End Reports Nav -->

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
      <h1>Faculty Evaluation Acknowledgement Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Acknowledgement Report</li>
        </ol>
      </nav>
    </div>

    <div class="card p-4 mb-4">
      <form method="GET" action="admin-acknowledgementreport.php">
        <div class="row align-items-end mb-4">
          <div class="col-md-6">
            <label for="faculty_id" class="form-label">Select Faculty</label>
            <select class="form-select" name="faculty_id" id="faculty_id" required>
              <option value="" disabled selected>-- Choose Faculty --</option>
              <?php
              // Get the logged-in admin's department
              $admin_id = $_SESSION['idnumber'];
              $dept_result = mysqli_query($conn, "SELECT department FROM admin WHERE idnumber = '$admin_id'");
              $admin_dept = '';
              if ($dept_row = mysqli_fetch_assoc($dept_result)) {
                $admin_dept = $dept_row['department'];
              }

              // Now filter faculty by the same department
              $faculty_query = mysqli_query($conn, "SELECT idnumber, first_name, mid_name, last_name FROM faculty WHERE department = '$admin_dept' ORDER BY last_name ASC");
              while ($row = mysqli_fetch_assoc($faculty_query)) {
                $full_name = $row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['mid_name'];
                echo "<option value='{$row['idnumber']}' " .
                  (isset($_GET['faculty_id']) && $_GET['faculty_id'] == $row['idnumber'] ? "selected" : "") .
                  ">$full_name</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-auto">
            <button type="submit" class="btn btn-success">Generate</button>
          </div>
        </div>
      </form>


      <?php
      if (isset($_GET['faculty_id'])) {
        $faculty_id = $_GET['faculty_id'];

        // Get faculty info
        $stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
        $stmt->bind_param("s", $faculty_id);
        $stmt->execute();
        $stmt->bind_result($fname, $mname, $lname, $dept, $rank);
        $stmt->fetch();
        $stmt->close();
        $full_name = strtoupper("$fname $mname $lname");
        $dept = strtoupper($dept);
        $rank = ucwords($rank);

        // Get semester/year
        $sem = "N/A";
        $sy = "N/A";
        $q = mysqli_query($conn, "SELECT semester, academic_year FROM admin_evaluation WHERE evaluatee_id = '$faculty_id' ORDER BY evaluation_date DESC LIMIT 1");
        if ($q && mysqli_num_rows($q) > 0) {
          $row = mysqli_fetch_assoc($q);
          $sem = $row['semester'];
          $sy = $row['academic_year'];
        }

        // SET Rating
        $set_q = mysqli_query($conn, "SELECT COUNT(*) as count, AVG(computed_rating) as avg FROM evaluation WHERE faculty_id = '$faculty_id'");
        $set_avg = ($row = mysqli_fetch_assoc($set_q)) ? number_format($row['avg'], 2) : '0.00';

        // SEF Rating
        $sef_q = mysqli_query($conn, "SELECT AVG(computed_rating) as avg FROM admin_evaluation WHERE evaluatee_id = '$faculty_id'");
        $sef_avg = ($row = mysqli_fetch_assoc($sef_q)) ? number_format($row['avg'], 2) : '0.00';

        // Get the latest supervisor (admin evaluator)
        $evaluator_name = '';
        $eval_result = mysqli_query($conn, "SELECT evaluator_id FROM admin_evaluation WHERE evaluatee_id = '$faculty_id' ORDER BY evaluation_date DESC LIMIT 1");
        if ($eval_result && mysqli_num_rows($eval_result) > 0) {
          $admin_row = mysqli_fetch_assoc($eval_result);
          $admin_id = $admin_row['evaluator_id'];

          // Fetch admin info
          $admin_info = mysqli_query($conn, "SELECT first_name, mid_name, last_name FROM admin WHERE idnumber = '$admin_id'");
          if ($admin_info && mysqli_num_rows($admin_info) > 0) {
            $admin = mysqli_fetch_assoc($admin_info);
            $evaluator_name = strtoupper($admin['first_name'] . ' ' . $admin['mid_name'] . ' ' . $admin['last_name']);
          }
        }

      ?>

        <div id="printSection">
          <h5 class="text-center"><strong>FACULTY EVALUATION ACKNOWLEDGEMENT FORM</strong></h5>

          <h6><strong>FACULTY MEMBER INFORMATION</strong></h6>
          <table class="table table-bordered w-100">
            <tr>
              <th>Name of Faculty</th>
              <td><?= $full_name ?></td>
            </tr>
            <tr>
              <th>Department/College</th>
              <td><?= $dept ?></td>
            </tr>
            <tr>
              <th>Current Faculty Rank</th>
              <td><?= $rank ?></td>
            </tr>
            <tr>
              <th>Semester/Term & Academic Year</th>
              <td><?= $sem ?> / <?= $sy ?></td>
            </tr>
          </table>

          <h6><strong>FACULTY EVALUATION SUMMARY</strong></h6>
          <table class="table table-bordered text-center w-100">
            <thead>
              <tr>
                <th>Student Evaluation of Teachers (SET)</th>
                <th>Supervisor's Evaluation of Faculty (SEF)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong><?= $set_avg ?></strong></td>
                <td><strong><?= $sef_avg ?></strong></td>
              </tr>
            </tbody>
          </table>

          <p>
            I acknowledge that I have received and reviewed the faculty evaluation conducted for the period mentioned above.
            I understand that my signature below does not necessarily indicate agreement with the evaluation but confirms that I have been given the opportunity to discuss it with my supervisor.
          </p>

          <h6><strong>SUPERVISOR</strong></h6>
          <table class="table table-bordered w-100">
            <tr>
              <th>Signature</th>
              <td class="signature-box"></td>
              <th>Name</th>
              <td class="signature-box"><?= $evaluator_name ?></td>
              <th>Date Signed</th>
              <td class="signature-box"></td>
            </tr>
          </table>

          <h6><strong>FACULTY</strong></h6>
          <table class="table table-bordered w-100 table-responsive">
            <tr>
              <th>Signature</th>
              <td class="signature-box"></td>
              <th>Name</th>
              <td><?= $full_name ?></td>
              <th>Date Signed</th>
              <td class="signature-box"></td>
            </tr>
          </table>
        </div>

        <div class="text-end mb-3">
          <a href="admin-acknowledgementreport-print.php?faculty_id=<?= $faculty_id ?>" target="_blank" class="btn btn-secondary">
            <i class="bi bi-printer"></i> Print Report
          </a>
        </div>


        <script>
          function printDiv(divId) {
            var content = document.getElementById(divId).innerHTML;
            var myWindow = window.open('', '', 'width=900,height=1000');
            myWindow.document.write('<html><head><title>Print Report</title>');
            myWindow.document.write('<link rel="stylesheet" href="assets/css/style.css">');
            myWindow.document.write('</head><body>');
            myWindow.document.write(content);
            myWindow.document.write('</body></html>');
            myWindow.document.close();
            myWindow.focus();
            myWindow.print();
            myWindow.close();
          }
        </script>

      <?php } ?>
    </div>
  </main>

  <!-- ======= Footer ======= -->
  <?php include 'footer.php' ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

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
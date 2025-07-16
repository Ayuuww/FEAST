<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Reports </title>

  <?php include 'header.php' ?>

  <style>
    .table td,
    .table th {
      text-align: left !important;
      vertical-align: top;
    }

    .signature-cell {
      height: 60px;
      min-width: 250px;
    }

    .wide-cell {
      min-width: 70px;
    }
  </style>

</head>

<body>

  <?php include 'superadmin-header.php' ?>

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
      <!-- <li class="nav-item">
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
        </li> -->
      <!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <!-- <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-studentsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Assign Subject</span>
          </a>
        </li> -->
      <!-- End Student Subject Nav -->

      <!-- Reports Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="reports" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-individualreport.php" class="active">
              <i class="bi bi-circle"></i><span>Invidiual Report</span>
            </a>
          </li>
          <li>
            <a href="superadmin-acknowledgementreport.php">
              <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
            </a>
          </li>
          <li>
            <a href="superadmin-pastrecords.php">
              <i class="bi bi-circle"></i><span>Past Record</span>
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
            <a href="superadmin-evaluationsetting.php">
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

      <!-- Management Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="superadmin-addsmanagement.php">
          <i class="ri-settings-line"></i>
          <span>Manage</span>
        </a>
      </li><!-- End Management Nav -->

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
        <ul id="admin-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-adminlist.php">
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
      <h1>Individual Faculty Evaluation Reports</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Individual Reports</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <div class="card p-4 mb-4">
      <form method="GET" action="superadmin-individualreport.php">
        <div class="row align-items-end mb-4">
          <div class="col-md-6">
            <label for="faculty_id" class="form-label">Select Faculty</label>
            <select class="form-select" name="faculty_id" id="faculty_id" required>
              <option value="" disabled selected>-- Choose Faculty --</option>
              <?php
              $faculty_query = mysqli_query($conn, "SELECT idnumber, first_name, mid_name, last_name FROM faculty ORDER BY last_name ASC");
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
            <button type="submit" class="btn btn-success mt-3 mt-md-0 w-100">Generate</button>
          </div>
        </div>
      </form>

      <?php
      if (isset($_GET['faculty_id'])) {
        $faculty_id = $_GET['faculty_id'];

        // Faculty basic info
        $stmt = $conn->prepare("SELECT last_name, first_name, mid_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
        $stmt->bind_param("s", $faculty_id);
        $stmt->execute();
        $stmt->bind_result($lname, $fname, $mname, $department, $faculty_rank);
        $stmt->fetch();
        $stmt->close();

        $faculty_name = "$fname $mname $lname";

        // Get latest semester/year evaluated by supervisor
        $semester = "N/A";
        $academic_year = "N/A";

        // Try admin_evaluation first
        $eval_q = mysqli_query($conn, "SELECT semester, academic_year FROM admin_evaluation WHERE evaluatee_id = '$faculty_id' ORDER BY evaluation_date DESC LIMIT 1");
        if ($eval_q && mysqli_num_rows($eval_q) > 0) {
          $row = mysqli_fetch_assoc($eval_q);
          $semester = $row['semester'] ?? "N/A";
          $academic_year = $row['academic_year'] ?? "N/A";
        } else {
          // Fallback: Try from student evaluation if supervisor evaluation is missing
          $eval_fallback = mysqli_query($conn, "SELECT semester, academic_year FROM evaluation WHERE faculty_id = '$faculty_id' ORDER BY id DESC LIMIT 1");
          if ($eval_fallback && mysqli_num_rows($eval_fallback) > 0) {
            $row = mysqli_fetch_assoc($eval_fallback);
            $semester = $row['semester'] ?? "N/A";
            $academic_year = $row['academic_year'] ?? "N/A";
          }
        }



        // ===========================
        // B. Summary of Average SET Rating
        // ===========================
        $result = mysqli_query($conn, "SELECT 
                                        e.subject_code,
                                        TRIM(e.student_section) AS student_section,
                                        COUNT(*) AS num_students,
                                        ROUND(AVG(e.computed_rating), 2) AS avg_rating,
                                        ROUND(COUNT(*) * AVG(e.computed_rating), 2) AS weighted_value
                                      FROM evaluation e
                                      WHERE e.faculty_id = '$faculty_id'
                                      GROUP BY e.subject_code, TRIM(e.student_section)");

        $total_students = 0;
        $total_weighted_value = 0;
        $table_rows = '';

        while ($row = mysqli_fetch_assoc($result)) {
            $subject = htmlspecialchars($row['subject_code']);
            $section = htmlspecialchars($row['student_section']);
            $students = $row['num_students'];
            $avg = number_format($row['avg_rating'], 2);
            $weighted = number_format($row['weighted_value'], 2);

            $total_students += $students;
            $total_weighted_value += $row['weighted_value'];

            $table_rows .= "<tr>
                <td>$subject</td>
                <td>$section</td>
                <td>$students</td>
                <td>$avg</td>
                <td>$weighted</td>
            </tr>";
        }


        $overall_set = $total_students ? number_format($total_weighted_value / $total_students, 2) : '0.00';

        // ===========================
        // C. Supervisor Evaluation (SEF)
        // ===========================
        $sef_result = mysqli_query($conn, "SELECT AVG(computed_rating) as sef_rating FROM admin_evaluation WHERE evaluatee_id = '$faculty_id'");
        $sef_rating = mysqli_fetch_assoc($sef_result)['sef_rating'] ?? 0;
        $sef_rating = number_format($sef_rating, 2);

        // ===========================
        // D. Qualitative Comments
        // ===========================
        $comments_q = mysqli_query($conn, "SELECT comment FROM evaluation WHERE faculty_id = '$faculty_id' AND comment IS NOT NULL AND comment <> '' LIMIT 5");

        // ===========================
        // HTML Output Starts Here
        // ===========================
      ?>

        <h3><strong>INDIVIDUAL FACULTY EVALUATION REPORT</strong></h3>

        <h5>A. Faculty Information</h5>
        <table class="table table-bordered">
          <tr>
            <th>Name of Faculty Evaluated:</th>
            <td><?= htmlspecialchars($faculty_name) ?></td>
          </tr>
          <tr>
            <th>Department/College:</th>
            <td><?= htmlspecialchars($department) ?></td>
          </tr>
          <tr>
            <th>Current Faculty Rank:</th>
            <td><?= htmlspecialchars($faculty_rank) ?></td>
          </tr>
          <tr>
            <th>Semester/Term & Academic Year:</th>
            <td><?= htmlspecialchars($semester) ?> / <?= htmlspecialchars($academic_year) ?></td>
          </tr>
        </table>

        <h5>B. Summary of Average SET Rating</h5>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Section</th>
              <th>No. of Students</th>
              <th>Avg. SET Rating</th>
              <th>Weighted Value</th>
            </tr>
          </thead>
          <tbody>
            <?= $table_rows ?>
            <tr>
              <th colspan='2'>TOTAL</th>
              <td><?= $total_students ?></td>
              <td></td>
              <td><?= number_format($total_weighted_value, 2) ?></td>
            </tr>
          </tbody>
        </table>

        <h5>C. SET and SEF Ratings</h5>
        <table class="table table-bordered">
          <tr>
            <th>OVERALL SET Rating</th>
            <td><?= $overall_set ?></td>
          </tr>
          <tr>
            <th>Supervisor (SEF) Rating</th>
            <td><?= $sef_rating ?></td>
          </tr>
        </table>

        <h5>D. Summary of Qualitative Comments and Suggestions</h5>
        <table class="table table-bordered">
          <tr>
            <th>#</th>
            <th>Comments</th>
          </tr>
          <?php
          $count = 1;
          while ($row = mysqli_fetch_assoc($comments_q)) {
            echo "<tr><td>{$count}</td><td>" . htmlspecialchars($row['comment']) . "</td></tr>";
            $count++;
          }
          if ($count == 1) echo "<tr><td colspan='2'>No comments available.</td></tr>";
          ?>
        </table>

        <h5>E. Development Plan (to be accomplished by Supervisor and Faculty)</h5>
        <table class="table table-bordered">
          <tr>
            <th>Areas for Improvement</th>
          </tr>
          <tr>
            <td style="height:60px;"></td>
          </tr>
          <tr>
            <th>Proposed Learning and Development Activities</th>
          </tr>
          <tr>
            <td style="height:60px;"></td>
          </tr>
          <tr>
            <th>Action Plan</th>
          </tr>
          <tr>
            <td style="height:60px;"></td>
          </tr>
        </table>

        <br>
        <table class="table table-bordered">
          <tr>
            <th class="wide-cell">Prepared by (Staff Signature)</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Name:</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Date:</th>
            <td class="signature-cell"></td>
          </tr>
          <tr>
            <th class="wide-cell">Reviewed by (Authorized Official)</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Name:</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Date:</th>
            <td class="signature-cell"></td>
          </tr>
        </table>

        <a href="individualreport-printing.php?faculty_id=<?= $faculty_id ?>" class="btn btn-secondary mt-3 col-md-3 offset-4" target="_blank">
          Print Report
        </a>



      <?php } ?>


    </div>


  </main><!-- End #main -->

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
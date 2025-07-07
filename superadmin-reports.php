<?php
session_start();
include 'conn/conn.php';// Connection to the database

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

        <!-- Report Nav -->
        <li class="nav-item">
          <a class="nav-link collapse" href="superadmin-reports.php">
            <i class="bi bi-journal-text"></i>
            <span>Reports</span>
          </a>
        </li><!-- End Report Nav -->
        
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
        <h1>Reports</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Reports</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <div class="card p-4 mb-4">
        <form method="GET" action="superadmin-reports.php">
          <div class="row align-items-center">
            <div class="col-md-6">
              <label for="faculty_id" class="form-label">Select Faculty</label>
              <select class="form-select" name="faculty_id" id="faculty_id" required>
                <option value="" disabled selected>-- Choose Faculty --</option>
                <?php
                $faculty_query = mysqli_query($conn, "SELECT idnumber, first_name, mid_name, last_name FROM faculty ORDER BY last_name ASC");
                while ($row = mysqli_fetch_assoc($faculty_query)) {
                    $full_name = $row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['mid_name'];
                    echo "<option value='{$row['idnumber']}' " . (isset($_GET['faculty_id']) && $_GET['faculty_id'] == $row['idnumber'] ? "selected" : "") . ">$full_name</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-2 mt-3 mt-md-0">
              <button type="submit" class="btn btn-success">Generate Report</button>
            </div>
          </div>
        </form>

        <?php
          if (isset($_GET['faculty_id'])):
              $faculty_id = $_GET['faculty_id'];

              // Fetch faculty info
              $stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
              $stmt->bind_param("s", $faculty_id);
              $stmt->execute();
              $stmt->bind_result($fname, $mname, $lname, $department, $rank);
              $stmt->fetch();
              $stmt->close();

              $full_name = "$lname, $fname $mname";

              // Try to fetch latest evaluation details
              $semester = "N/A";
              $academic_year = "N/A";

              // Try checking from admin evaluation first
              $eval_q = mysqli_query($conn, "SELECT semester, academic_year FROM evaluation WHERE faculty_id='$faculty_id' ORDER BY id DESC LIMIT 1");
              if (mysqli_num_rows($eval_q) == 0) {
                  // If no record, fallback to evaluation table (if exists)
                  $eval_q = mysqli_query($conn, "SELECT semester, academic_year FROM evaluation WHERE faculty_id='$faculty_id' ORDER BY id DESC LIMIT 1");
              }
              if ($eval = mysqli_fetch_assoc($eval_q)) {
                  $semester = $eval['semester'];
                  $academic_year = $eval['academic_year'];
              }
          ?>
          <div class="card p-4">
            <h5 class="card-title">INDIVIDUAL FACULTY EVALUATION REPORT</h5>
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th style="width: 30%;">Name of Faculty Evaluated:</th>
                  <td><?= htmlspecialchars($full_name) ?></td>
                </tr>
                <tr>
                  <th>Department/College:</th>
                  <td><?= htmlspecialchars($department) ?></td>
                </tr>
                <tr>
                  <th>Current Faculty Rank:</th>
                  <td><?= htmlspecialchars($rank) ?></td>
                </tr>
                <tr>
                  <th>Semester/Term & Academic Year:</th>
                  <td><?= htmlspecialchars($semester . ' / ' . $academic_year) ?></td>
                </tr>
              </tbody>
            </table>

            <!-- You can add more evaluation breakdown here -->
            <h5 class="mt-4">Evaluation Summary</h5>

            <?php
            $total_score = 0;
            $total_items = 0;
            $evaluation_sources = [];

            // Student Evaluation
            $student_q = mysqli_query($conn, "SELECT total_score FROM evaluation WHERE faculty_id='$faculty_id'");
            if (mysqli_num_rows($student_q) > 0) {
                $sum = 0; $count = 0;
                while ($r = mysqli_fetch_assoc($student_q)) {
                    $sum += $r['total_score'];
                    $count++;
                }
                $avg = $sum / $count;
                $evaluation_sources[] = [
                    'source' => 'Student Evaluation',
                    'average' => number_format($avg, 2),
                    'responses' => $count
                ];
                $total_score += $sum;
                $total_items += $count;
            }

            // Admin Evaluation
            $admin_q = mysqli_query($conn, "SELECT total_score FROM evaluation WHERE faculty_id='$faculty_id'");
            if (mysqli_num_rows($admin_q) > 0) {
                $sum = 0; $count = 0;
                while ($r = mysqli_fetch_assoc($admin_q)) {
                    $sum += $r['total_score'];
                    $count++;
                }
                $avg = $sum / $count;
                $evaluation_sources[] = [
                    'source' => 'Admin Evaluation',
                    'average' => number_format($avg, 2),
                    'responses' => $count
                ];
                $total_score += $sum;
                $total_items += $count;
            }

            // Faculty Peer Evaluation
            $peer_q = mysqli_query($conn, "SELECT total_score FROM admin_evaluation WHERE evaluatee_id='$faculty_id'");
            if (mysqli_num_rows($peer_q) > 0) {
                $sum = 0; $count = 0;
                while ($r = mysqli_fetch_assoc($peer_q)) {
                    $sum += $r['total_score'];
                    $count++;
                }
                $avg = $sum / $count;
                $evaluation_sources[] = [
                    'source' => 'Peer Evaluation',
                    'average' => number_format($avg, 2),
                    'responses' => $count
                ];
                $total_score += $sum;
                $total_items += $count;
            }
            ?>

            <?php if (!empty($evaluation_sources)): ?>
            <table class="table table-bordered mt-3">
              <thead>
                <tr>
                  <th>Source</th>
                  <th>Average Score</th>
                  <th>Number of Responses</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($evaluation_sources as $eval): ?>
                  <tr>
                    <td><?= $eval['source'] ?></td>
                    <td><?= $eval['average'] ?></td>
                    <td><?= $eval['responses'] ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr class="table-success fw-bold">
                  <td>Total Average</td>
                  <td colspan="2">
                    <?= $total_items > 0 ? number_format($total_score / $total_items, 2) : 'N/A' ?>
                  </td>
                </tr>
              </tbody>
            </table>
            <?php else: ?>
              <div class="alert alert-warning mt-3">No evaluation records found for this faculty.</div>
            <?php endif; ?>

          </div>
        <?php endif; ?>


      </div>


    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <?php include'footer.php'?>
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

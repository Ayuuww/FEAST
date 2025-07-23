<?php
session_start();
include 'conn/conn.php'; // DB connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

// 

$admin_id = $_SESSION['idnumber'];

// Get admin's department
$dept_query = "SELECT department FROM admin WHERE idnumber = ?";
$stmt = $conn->prepare($dept_query);
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$department = $row['department'] ?? '';

// Count faculty in same department
$faculty_query = "SELECT COUNT(*) AS total FROM faculty WHERE department = ? AND status = 'active'";
$stmt = $conn->prepare($faculty_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$faculty_result = $stmt->get_result();
$faculty_row = $faculty_result->fetch_assoc();
$totalfaculty = $faculty_row['total'] ?? 0;

// Count students in same department
$student_query = "SELECT COUNT(*) AS total FROM student WHERE department = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$student_result = $stmt->get_result();
$student_row = $student_result->fetch_assoc();
$totalstudent = $student_row['total'] ?? 0;

// Count total evaluations from students
$student_eval_query = "
  SELECT COUNT(*) AS total FROM evaluation e
  JOIN faculty f ON e.faculty_id = f.idnumber
  WHERE f.department = ?";

$stmt = $conn->prepare($student_eval_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();
$student_eval_count = $result->fetch_assoc()['total'] ?? 0;

// Count total evaluations from admins
$admin_eval_query = "
  SELECT COUNT(*) AS total FROM admin_evaluation ae
  JOIN faculty f ON ae.evaluatee_id = f.idnumber
  WHERE f.department = ? ";

$stmt = $conn->prepare($admin_eval_query);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();
$admin_eval_count = $result->fetch_assoc()['total'] ?? 0;

// Combine both
$total_evaluations = $student_eval_count + $admin_eval_count;

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Home </title>

  <?php include 'header.php' ?>


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
        <a class="nav-link collapsed" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="reports" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-individualreport.php">
              <i class="bi bi-circle"></i><span>Invidiual Report</span>
            </a>
          </li>
          <li>
            <a href="admin-acknowledgementreport.php">
              <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
            </a>
          </li>
          <li>
            <a href="admin-overallreport-set.php">
              <i class="bi bi-circle"></i><span>Overall Report SET</span>
            </a>
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
            <a href="admin-pastrecords.php" >
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
      <h1>Dashboard</h1>

      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">


        <!-- Total Faculty Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card ">

            <div class="card-body">
              <h5 class="card-title">Total<span> | Faculty Members</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <img src="icons/teacher.png" alt="Faculty Icon" class="img-fluid" style="max-height: 50px;">
                </div>
                <div class="ps-3">
                  <h6><?php echo number_format($totalfaculty); ?></h6>

                </div>
              </div>
            </div>

          </div>
        </div><!-- End Total Faculty Card -->

        <!-- Total Student Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Total<span> | Students</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <img src="icons/students.png" alt="Student Icon" class="img-fluid" style="max-height: 50px;">
                </div>
                <div class="ps-3">
                  <h6><?php echo number_format($totalstudent); ?></h6>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Total Student Card -->

        <!-- Total Evaluation Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Total<span> | Evaluations</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <img src="icons/evaluation.png" alt="Evaluation Icon" class="img-fluid" style="max-height: 50px;">
                </div>
                <div class="ps-3">
                  <h6><?php echo number_format($total_evaluations); ?></h6>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Total Evaluation Card -->

      </div>

      <div class="row">
        <!-- Student Evaluation Ratings by Faculty (Same Department) -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><strong>Faculty Evaluation Ratings</strong> (Student Evaluation)</h5>

              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="form-floating">
                    <select id="studentEvalYear" class="form-select">
                      <option value="All">All</option>
                      <?php
                      $years = mysqli_query($conn, "SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
                      while ($y = mysqli_fetch_assoc($years)) {
                        echo '<option value="' . $y['academic_year'] . '">' . $y['academic_year'] . '</option>';
                      }
                      ?>
                    </select>
                    <label>Academic Year</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select id="studentEvalSem" class="form-select">
                      <option value="All">All</option>
                      <option value="1st Semester">1st Semester</option>
                      <option value="2nd Semester">2nd Semester</option>
                      <option value="Summer">Summer</option>
                    </select>
                    <label>Semester</label>
                  </div>
                </div>
              </div>

              <div id="studentEvalChart" style="min-height: 400px;" class="echart"></div>
            </div>
          </div>
        </div>

        <script>
          function loadStudentEvalChart(year = 'All', semester = 'All') {
            fetch(`fetch-student-eval-by-dept.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
              .then(res => res.json())
              .then(data => {
                const chart = echarts.init(document.querySelector("#studentEvalChart"));
                chart.setOption({
                  title: {
                    text: 'Student Evaluation Scores by Faculty',
                    left: 'center'
                  },
                  tooltip: {
                    trigger: 'item',
                    formatter: function(params) {
                      return `<strong>${params.data.name}</strong><br>Avg Rating: ${params.data.value}%`;
                    }
                  },
                  grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                  },
                  xAxis: {
                    type: 'value',
                    name: 'Avg Rating (%)',
                    min: 0,
                    max: 100
                  },
                  yAxis: {
                    type: 'category',
                    data: data.names,
                    inverse: true
                  },
                  series: [{
                    name: 'Rating',
                    type: 'bar',
                    data: data.ratings,
                    itemStyle: {
                      color: '#1976D2'
                    }
                  }],
                  animationDuration: 1000,
                  animationEasing: 'cubicOut'
                });
              });
          }

          document.addEventListener("DOMContentLoaded", () => {
            const yearSel = document.getElementById("studentEvalYear");
            const semSel = document.getElementById("studentEvalSem");

            function reloadChart() {
              loadStudentEvalChart(yearSel.value, semSel.value);
            }

            yearSel.addEventListener("change", reloadChart);
            semSel.addEventListener("change", reloadChart);

            loadStudentEvalChart(); // Initial load
          });
        </script>
        <!-- End of cahrt -->

        <!-- Supervisor Evaluation Ratings by Faculty (Same Department) -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><strong>Faculty Evaluation Ratings</strong> (Supervisor Evaluation)</h5>

              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="form-floating">
                    <select id="supervisorEvalYear" class="form-select">
                      <option value="All">All</option>
                      <?php
                      $years = mysqli_query($conn, "SELECT DISTINCT academic_year FROM admin_evaluation ORDER BY academic_year DESC");
                      while ($y = mysqli_fetch_assoc($years)) {
                        echo '<option value="' . $y['academic_year'] . '">' . $y['academic_year'] . '</option>';
                      }
                      ?>
                    </select>
                    <label>Academic Year</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select id="supervisorEvalSem" class="form-select">
                      <option value="All">All</option>
                      <option value="1st Semester">1st Semester</option>
                      <option value="2nd Semester">2nd Semester</option>
                      <option value="Summer">Summer</option>
                    </select>
                    <label>Semester</label>
                  </div>
                </div>
              </div>

              <div id="supervisorEvalChart" style="min-height: 400px;" class="echart"></div>
            </div>
          </div>
        </div>
        <script>
          function loadSupervisorEvalChart(year = 'All', semester = 'All') {
            fetch(`fetch-supervisor-eval-by-dept.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
              .then(res => res.json())
              .then(data => {
                const chart = echarts.init(document.querySelector("#supervisorEvalChart"));
                chart.setOption({
                  title: {
                    text: 'Supervisor Evaluation Scores by Faculty',
                    left: 'center'
                  },
                  tooltip: {
                    trigger: 'item',
                    formatter: function(params) {
                      return `<strong>${params.data.name}</strong><br>Avg Rating: ${params.data.value}%`;
                    }
                  },
                  grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                  },
                  xAxis: {
                    type: 'value',
                    name: 'Avg Rating (%)',
                    min: 0,
                    max: 100
                  },
                  yAxis: {
                    type: 'category',
                    data: data.names,
                    inverse: true
                  },
                  series: [{
                    name: 'Rating',
                    type: 'bar',
                    data: data.ratings,
                    itemStyle: {
                      color: '#E91E63'
                    }
                  }],
                  animationDuration: 1000,
                  animationEasing: 'cubicOut'
                });
              });
          }

          document.addEventListener("DOMContentLoaded", () => {
            const yearSel = document.getElementById("supervisorEvalYear");
            const semSel = document.getElementById("supervisorEvalSem");

            function reloadChart() {
              loadSupervisorEvalChart(yearSel.value, semSel.value);
            }

            yearSel.addEventListener("change", reloadChart);
            semSel.addEventListener("change", reloadChart);

            loadSupervisorEvalChart(); // Default load
          });
        </script>
        <!-- End of Chart -->

        <!-- Overall Evaluation Ratings by Faculty (Same Department) -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><strong>Faculty Evaluation Ratings</strong> (Overall - Student and Supervisor)</h5>

              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="form-floating">
                    <select id="overallEvalYear" class="form-select">
                      <option value="All">All</option>
                      <?php
                      $years = mysqli_query($conn, "SELECT DISTINCT academic_year FROM (
                          SELECT academic_year FROM evaluation
                          UNION
                          SELECT academic_year FROM admin_evaluation
                        ) AS all_years ORDER BY academic_year DESC");
                      while ($y = mysqli_fetch_assoc($years)) {
                        echo '<option value="' . $y['academic_year'] . '">' . $y['academic_year'] . '</option>';
                      }
                      ?>
                    </select>
                    <label>Academic Year</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select id="overallEvalSem" class="form-select">
                      <option value="All">All</option>
                      <option value="1st Semester">1st Semester</option>
                      <option value="2nd Semester">2nd Semester</option>
                      <option value="Summer">Summer</option>
                    </select>
                    <label>Semester</label>
                  </div>
                </div>
              </div>

              <div id="overallEvalChart" style="min-height: 400px;" class="echart"></div>
            </div>
          </div>
        </div>
        <script>
          function loadOverallEvalChart(year = 'All', semester = 'All') {
            fetch(`fetch-overall-eval-by-dept.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
              .then(res => res.json())
              .then(data => {
                const chart = echarts.init(document.querySelector("#overallEvalChart"));
                chart.setOption({
                  title: {
                    text: 'Combined Evaluation Scores by Faculty',
                    left: 'center'
                  },
                  tooltip: {
                    trigger: 'item',
                    formatter: function(params) {
                      return `<strong>${params.data.name}</strong><br>Avg Rating: ${params.data.value}%`;
                    }
                  },
                  grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                  },
                  xAxis: {
                    type: 'value',
                    name: 'Avg Rating (%)',
                    min: 0,
                    max: 100
                  },
                  yAxis: {
                    type: 'category',
                    data: data.names,
                    inverse: true
                  },
                  series: [{
                    name: 'Rating',
                    type: 'bar',
                    data: data.ratings,
                    itemStyle: {
                      color: '#9C27B0'
                    }
                  }],
                  animationDuration: 1000,
                  animationEasing: 'cubicOut'
                });
              });
          }

          document.addEventListener("DOMContentLoaded", () => {
            const yearSel = document.getElementById("overallEvalYear");
            const semSel = document.getElementById("overallEvalSem");

            function reloadChart() {
              loadOverallEvalChart(yearSel.value, semSel.value);
            }

            yearSel.addEventListener("change", reloadChart);
            semSel.addEventListener("change", reloadChart);

            loadOverallEvalChart(); // Initial load
          });
        </script>


      </div>
    </section>

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
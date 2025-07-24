<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}


// Get total approved faculty
$faculty_query = "SELECT COUNT(*) AS total_faculty FROM faculty WHERE role = 'faculty' and status = 'active'";
$faulty_result = mysqli_query($conn, $faculty_query);
$data = mysqli_fetch_assoc($faulty_result);
$totalfaculty = $data['total_faculty'];

// Get total approved students
$student_query = "SELECT COUNT(*) AS total_student FROM student WHERE role = 'student'";
$student_result = mysqli_query($conn, $student_query);
$data = mysqli_fetch_assoc($student_result);
$totalstudent = $data['total_student'];

// Get total admins
$admin_result  = "SELECT COUNT(*) AS total_admin FROM admin";
$admin_result = mysqli_query($conn, $admin_result);
$data = mysqli_fetch_assoc($admin_result);
$totaladmin = $data['total_admin'];

// Get total super admins
$superadmin_query = "SELECT COUNT(*) AS total_superadmin FROM superadmin";
$superadmin_result = mysqli_query($conn, $superadmin_query);
$data = mysqli_fetch_assoc($superadmin_result);
$totalsuperadmin = $data['total_superadmin'];

// Get total subjects
$subject_query = "SELECT COUNT(*) AS total_subject FROM subject";
$subject_result = mysqli_query($conn, $subject_query);
$data = mysqli_fetch_assoc($subject_result);
$totalsubject = $data['total_subject'];

// Get total evaluation (student/supervisor)
// Total student-submitted evaluations
$studentEvalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM evaluation");
$studentEvalCount = mysqli_fetch_assoc($studentEvalQuery)['total'] ?? 0;

// Total admin/supervisor-submitted evaluations
$adminEvalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM admin_evaluation");
$adminEvalCount = mysqli_fetch_assoc($adminEvalQuery)['total'] ?? 0;

// Total combined
$totalEvaluations = $studentEvalCount + $adminEvalCount;

// Fetching activities
$limit = $_GET['limit'] ?? 10;

$log_query = "SELECT * FROM activity_logs ORDER BY timestamp DESC LIMIT ?";
$stmt = $conn->prepare($log_query);
$stmt->bind_param("i", $limit);
$stmt->execute();
$log_result = $stmt->get_result();


// Function to convert time difference to "x min ago"
function timeAgo($datetime)
{
  $timestamp = strtotime($datetime);
  $difference = time() - $timestamp;

  if ($difference < 0) return "Just now"; // Future time fallback

  if ($difference < 60)
    return "$difference sec";
  elseif ($difference < 3600)
    return floor($difference / 60) . " min";
  elseif ($difference < 86400)
    return floor($difference / 3600) . " hrs";
  elseif ($difference < 604800)
    return floor($difference / 86400) . " days";
  else
    return date("M d, Y", $timestamp);
}

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

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="superadmin-dashboard.php">
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
        </li>End Subject Nav -->

      <!-- Student Subject Nav -->
      <!-- <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-studentsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Assign Subject</span>
          </a>
        </li>End Student Subject Nav -->

      <!-- Reports Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="reports" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-individualreport.php">
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
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">

            <!-- Total Faculty Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card ">

                <div class="card-body">
                  <h5 class="card-title">Total<span> | Faculty</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <img src="icons/teacher.png" alt="Faculty Icon" style="width: 100px; height: 50px;">
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $totalfaculty; ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Total Faculty Card -->

            <!-- Total Student Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card ">

                <div class="card-body">
                  <h5 class="card-title">Total<span> | Students</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <img src="icons/students.png" alt="Student Icon" style="width: 100px; height: 50px;">
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $totalstudent; ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Total Student Card -->

            <!-- Total Admins Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">

                <div class="card-body">
                  <h5 class="card-title">Total <span>| Admins</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <img src="icons/admin.png" alt="Admin Icon" style="width: 50px; height: 50px;">
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $totaladmin; ?></h6>
                    </div>
                  </div>

                </div>
              </div>

            </div><!-- End Total Admin Card -->

            <!-- Total Super Admins Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">

                <div class="card-body">
                  <h5 class="card-title">Total <span>| Super Admins</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <img src="icons/superadmin.png" alt="Admin Icon" style="width: 50px; height: 50px;">
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $totalsuperadmin; ?></h6>
                    </div>
                  </div>

                </div>
              </div>

            </div><!-- End Total Super Admin Card -->

            <!-- Total Subjects Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">

                <div class="card-body">
                  <h5 class="card-title">Total <span>| Subjects</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <img src="icons/books.png" alt="Admin Icon" style="width: 50px; height: 50px;">
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $totalsubject; ?></h6>
                    </div>
                  </div>

                </div>
              </div>

            </div><!-- End Total Subjects Card -->

            <!-- Total Evaluations Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">
                <div class="card-body">
                  <h5 class="card-title">Total <span>| Evaluations</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <img src="icons/evaluation.png" alt="Evaluation Icon" style="width: 50px; height: 50px;">
                    </div>
                    <div class="ps-3">
                      <h6><?= $totalEvaluations ?></h6>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <!-- End Total Evaluations Card -->

            <!-- Average evaluation score per department -->
            <div class="col-lg-12">
              <?php
              $selectedYear = $_GET['year'] ?? 'All';

              // Get list of academic years
              $year_result = mysqli_query($conn, "SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
              $academic_years = [];
              while ($row = mysqli_fetch_assoc($year_result)) {
                $academic_years[] = $row['academic_year'];
              }
              ?>

              <div class="card">
                <!-- Filter for academic year -->
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter by Year</h6>
                    </li>
                    <li><a class="dropdown-item year-filter <?= $selectedYear == 'All' ? 'active' : '' ?>" href="?year=All">All</a></li>
                    <?php foreach ($academic_years as $year): ?>
                      <li><a class="dropdown-item year-filter <?= $selectedYear == $year ? 'active' : '' ?>" href="?year=<?= $year ?>"><?= $year ?></a></li>
                    <?php endforeach; ?>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">
                    <strong>Average Evaluation Scores per Department</strong>
                    <span id="yearLabel" class="text-muted small">| All Years</span><br><br>
                    Student-to-Faculty Evaluation
                  </h5>

                  <!-- Stacked Bar Chart -->
                  <canvas id="stakedBarChart" style="max-height: 400px;"></canvas>
                  <script>
                    let barChart;

                    function fetchChartData(year = 'All') {

                      document.getElementById("yearLabel").textContent = `| ${year === 'All' ? 'All Years' : year}`;

                      fetch(`fetch-chart-data.php?year=${encodeURIComponent(year)}`)
                        .then(response => response.json())
                        .then(chartData => {
                          if (barChart) {
                            barChart.data.labels = chartData.labels;
                            barChart.data.datasets = chartData.datasets;
                            barChart.update();
                          } else {
                            const ctx = document.querySelector('#stakedBarChart').getContext('2d');
                            barChart = new Chart(ctx, {
                              type: 'bar',
                              data: chartData,
                              options: {
                                responsive: true,
                                scales: {
                                  x: {
                                    stacked: true
                                  },
                                  y: {
                                    stacked: true,
                                    title: {
                                      display: true,
                                      text: 'Average Score (%)'
                                    },
                                    suggestedMin: 0,
                                    suggestedMax: 100
                                  }
                                }
                              }
                            });
                          }
                        });
                    }

                    document.addEventListener("DOMContentLoaded", () => {
                      fetchChartData(); // Default load

                      // Event listener for year filter
                      document.querySelectorAll(".year-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          const year = item.getAttribute("href").split("=")[1];
                          fetchChartData(year);

                          // update active state manually
                          document.querySelectorAll(".dropdown-item").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");

                          // update card title
                          document.querySelector(".card-title span").textContent = `| ${year === 'All' ? 'All Years' : year}`;
                        });
                      });
                    });
                  </script>

                </div>
              </div>
            </div>
            <!-- End Stacked Bar Chart -->


            <!-- Supervisor-to-Faculty Evaluation -->
            <div class="col-lg-12">
              <?php
              $selectedYearAdmin = $_GET['year'] ?? 'All';
              $year_result_admin = mysqli_query($conn, "SELECT DISTINCT academic_year FROM admin_evaluation ORDER BY academic_year DESC");
              $admin_years = [];
              while ($row = mysqli_fetch_assoc($year_result_admin)) {
                $admin_years[] = $row['academic_year'];
              }
              ?>

              <div class="card">
                <!-- Filter for academic year -->
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter by Year</h6>
                    </li>
                    <li><a class="dropdown-item admin-year-filter <?= $selectedYearAdmin == 'All' ? 'active' : '' ?>" href="?year=All">All</a></li>
                    <?php foreach ($admin_years as $year): ?>
                      <li><a class="dropdown-item admin-year-filter <?= $selectedYearAdmin == $year ? 'active' : '' ?>" href="?year=<?= $year ?>"><?= $year ?></a></li>
                    <?php endforeach; ?>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">
                    <strong>Average Evaluation Scores per Department</strong>
                    <span id="adminYearLabel" class="text-muted small">| All Years</span><br><br>
                    Supervisor-to-Faculty Evaluation
                  </h5>

                  <canvas id="supervisorBarChart" style="max-height: 400px;"></canvas>

                  <script>
                    let supervisorBarChart;

                    function fetchAdminChartData(year = 'All') {
                      document.getElementById("adminYearLabel").textContent = `| ${year === 'All' ? 'All Years' : year}`;

                      fetch(`fetch-admin-chart-data.php?year=${encodeURIComponent(year)}`)
                        .then(response => response.json())
                        .then(chartData => {
                          if (supervisorBarChart) {
                            supervisorBarChart.data.labels = chartData.labels;
                            supervisorBarChart.data.datasets = chartData.datasets;
                            supervisorBarChart.update();
                          } else {
                            const ctx = document.querySelector('#supervisorBarChart').getContext('2d');
                            supervisorBarChart = new Chart(ctx, {
                              type: 'bar',
                              data: chartData,
                              options: {
                                responsive: true,
                                scales: {
                                  x: {
                                    stacked: true
                                  },
                                  y: {
                                    stacked: true,
                                    title: {
                                      display: true,
                                      text: 'Average Score (%)'
                                    },
                                    suggestedMin: 0,
                                    suggestedMax: 100
                                  }
                                }
                              }
                            });
                          }
                        });
                    }

                    document.addEventListener("DOMContentLoaded", () => {
                      fetchAdminChartData(); // Default load

                      document.querySelectorAll(".admin-year-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          const year = item.getAttribute("href").split("=")[1];
                          fetchAdminChartData(year);

                          document.querySelectorAll(".admin-year-filter").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");

                          document.getElementById("adminYearLabel").textContent = `| ${year === 'All' ? 'All Years' : year}`;
                        });
                      });
                    });
                  </script>
                </div>
              </div>
            </div>
            <!-- End Supervisor-to-Faculty Evaluation Chart -->

            <!-- Overall Evaluation Score per Department (Student + Supervisor) -->
            <div class="col-lg-12">

              <div class="card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter by Year</h6>
                    </li>
                    <li><a class="dropdown-item overall-year-filter active" href="?year=All">All</a></li>
                    <?php foreach ($academic_years as $year): ?>
                      <li><a class="dropdown-item overall-year-filter <?= $selectedYear == $year ? 'active' : '' ?>" href="?year=<?= $year ?>"><?= $year ?></a></li>
                    <?php endforeach; ?>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">
                    <strong>Average Evaluation Scores per Department</strong>
                    <span id="overallYearLabel" class="text-muted small">| All Years</span><br><br>
                    Student and Supervisor Evaluation
                  </h5>

                  <canvas id="overallStackedChart" style="max-height: 400px;"></canvas>
                  <script>
                    let overallChart;

                    function fetchOverallData(year = 'All') {
                      document.getElementById("overallYearLabel").textContent = `| ${year === 'All' ? 'All Years' : year}`;

                      fetch(`fetch-overall-eval-chart-data.php?year=${encodeURIComponent(year)}`)
                        .then(res => res.json())
                        .then(data => {
                          if (overallChart) {
                            overallChart.data.labels = data.labels;
                            overallChart.data.datasets = data.datasets;
                            overallChart.update();
                          } else {
                            const ctx = document.getElementById('overallStackedChart').getContext('2d');
                            overallChart = new Chart(ctx, {
                              type: 'bar',
                              data: data,
                              options: {
                                responsive: true,
                                scales: {
                                  x: {
                                    stacked: true
                                  },
                                  y: {
                                    stacked: true,
                                    title: {
                                      display: true,
                                      text: 'Average Rating (%)'
                                    },
                                    suggestedMax: 100
                                  }
                                }
                              }
                            });
                          }
                        });
                    }

                    document.addEventListener("DOMContentLoaded", () => {
                      fetchOverallData();

                      document.querySelectorAll(".overall-year-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          const year = item.getAttribute("href").split("=")[1];
                          fetchOverallData(year);

                          document.querySelectorAll(".overall-year-filter").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");
                        });
                      });
                    });
                  </script>
                </div>
              </div>
            </div>
            <!-- End of overall evaluation department chart -->

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">

          <!-- Recent Activity -->
          <div class="card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>
                <li><a class="dropdown-item activity-filter" href="#" data-filter="today">Today</a></li>
                <li><a class="dropdown-item activity-filter" href="#" data-filter="month">This Month</a></li>
                <li><a class="dropdown-item activity-filter" href="#" data-filter="year">This Year</a></li>
                <li><a class="dropdown-item activity-filter" href="#" data-filter="all">All</a></li>
              </ul>
            </div>

            <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="activityContainer">
              <h5 class="card-title">Recent Activity <span id="filter-label">| All</span></h5>
              <div class="activity" id="activity-list">
                <div class="activity" id="activity-list"></div>
              </div>

              <div id="loadingIndicator" class="text-center my-2" style="display: none;">
                <div class="spinner-border text-primary" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
              </div>

              <script>
                let offset = 0;
                const limit = 10;
                let loading = false;
                let filter = 'all';
                const activityList = document.getElementById("activity-list");
                const activityContainer = document.getElementById("activityContainer");
                const loadingIndicator = document.getElementById("loadingIndicator");

                function getTimeAgo(datetime) {
                  const timestamp = new Date(datetime).getTime();
                  const now = Date.now();
                  const diff = Math.floor((now - timestamp) / 1000);
                  if (diff < 0) return "Just now";
                  if (diff < 60) return `${diff} sec`;
                  if (diff < 3600) return `${Math.floor(diff / 60)} min`;
                  if (diff < 86400) return `${Math.floor(diff / 3600)} hrs`;
                  if (diff < 604800) return `${Math.floor(diff / 86400)} days`;
                  const d = new Date(timestamp);
                  return `${d.toLocaleString('default', { month: 'short' })} ${d.getDate()}, ${d.getFullYear()}`;
                }

                function loadLogs(reset = false) {
                  if (loading) return;
                  loading = true;
                  loadingIndicator.style.display = 'block';

                  // If resetting, offset must be set BEFORE fetching
                  if (reset) offset = 0;

                  fetch(`activity-fetch.php?limit=${limit}&offset=${offset}&filter=${filter}`)
                    .then(res => res.json())
                    .then(data => {
                      if (reset) {
                        activityList.innerHTML = '';
                      }

                      data.forEach(log => {
                        const timeAgo = getTimeAgo(log.timestamp);
                        const item = `
                          <div class="activity-item d-flex">
                            <div class="activite-label">${timeAgo}</div>
                            <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                            <div class="activity-content">
                              <span class="fw-bold">${log.role.charAt(0).toUpperCase() + log.role.slice(1)}:</span> ${log.activity}
                            </div>
                          </div>`;
                        activityList.insertAdjacentHTML("beforeend", item);
                      });

                      if (data.length > 0) {
                        offset += data.length; // more accurate than += limit
                      }

                      loading = false;
                      loadingIndicator.style.display = 'none';
                    });
                }

                document.addEventListener("DOMContentLoaded", () => {
                  loadLogs();

                  activityContainer.addEventListener("scroll", () => {
                    if (
                      activityContainer.scrollTop + activityContainer.clientHeight >= activityContainer.scrollHeight - 5 &&
                      !loading
                    ) {
                      loadLogs();
                    }
                  });

                  document.querySelectorAll(".activity-filter").forEach(btn => {
                    btn.addEventListener("click", (e) => {
                      e.preventDefault();
                      filter = btn.dataset.filter;
                      offset = 0;
                      document.getElementById("filter-label").textContent = `| ${btn.textContent}`;
                      loadLogs(true);
                    });
                  });
                });
              </script>

            </div>
          </div>
          <!-- End Recent Activity -->

          <!-- Pie Chart -->
          <div class="card">

            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>
                <li><a class="dropdown-item role-filter" data-role="student" href="#">Student</a></li>
                <li><a class="dropdown-item role-filter" data-role="faculty" href="#">Faculty</a></li>
                <li><a class="dropdown-item role-filter" data-role="admin" href="#">Program Chair / Dean</a></li>
              </ul>
            </div>

            <div class="card-body pb-0">
              <h5 class="card-title">Users per Department <span id="roleLabel">| Student</span></h5>

              <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

              <?php
              // STUDENT per department
              $student_query = "SELECT department, COUNT(*) AS total FROM student GROUP BY department";
              $student_result = mysqli_query($conn, $student_query);
              $student_data = [];
              while ($row = mysqli_fetch_assoc($student_result)) {
                $student_data[] = ['name' => $row['department'], 'value' => (int)$row['total']];
              }

              // SECTIONS per department
              $section_query = "SELECT department, section, COUNT(*) AS count FROM student GROUP BY department, section";
              $section_result = mysqli_query($conn, $section_query);
              $student_sections = [];
              foreach ($student_data as $item) {
                $student_sections[$item['name']] = [];
              }
              while ($row = mysqli_fetch_assoc($section_result)) {
                $student_sections[$row['department']][] = $row['section'] . " (" . $row['count'] . ")";
              }

              // FACULTY per department
              $faculty_query = "SELECT department, COUNT(*) AS total FROM faculty WHERE role='faculty' AND status='active' GROUP BY department";
              $faculty_result = mysqli_query($conn, $faculty_query);
              $faculty_data = [];
              while ($row = mysqli_fetch_assoc($faculty_result)) {
                $faculty_data[] = ['name' => $row['department'], 'value' => (int)$row['total']];
              }

              // ADMIN per department (Program Chair or Dean)
              $admin_query = "SELECT department, COUNT(*) AS total FROM admin WHERE position IN ('Program Chair', 'Dean') GROUP BY department";
              $admin_result = mysqli_query($conn, $admin_query);
              $admin_data = [];
              while ($row = mysqli_fetch_assoc($admin_result)) {
                $admin_data[] = ['name' => $row['department'], 'value' => (int)$row['total']];
              }
              ?>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const chart = echarts.init(document.querySelector("#trafficChart"));

                  const dataMap = {
                    student: <?= json_encode($student_data); ?>,
                    faculty: <?= json_encode($faculty_data); ?>,
                    admin: <?= json_encode($admin_data); ?>
                  };

                  const sectionDetails = <?= json_encode($student_sections); ?>;

                  const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#ff9f40', '#4CAF50' ,'#e78ca0ff', '#a220dfff', '#f80800ff', '#0f010cff', '#47a4e2ff', '#1e6928ff'];

                  function updateChart(role) {
                    const labelMap = {
                      student: "Student",
                      faculty: "Faculty",
                      admin: "Program Chair / Dean"
                    };

                    document.getElementById("roleLabel").textContent = "| " + labelMap[role];

                    chart.setOption({
                      color: colors,
                      tooltip: {
                        trigger: 'item',
                        formatter: function(params) {
                          let base = `${params.name}<br/>${params.value} users (${params.percent}%)`;
                          if (role === 'student') {
                            const sectionInfo = sectionDetails[params.name]?.join(', ') || 'No section info';
                            base += `<br/>Sections: ${sectionInfo}`;
                          }
                          return base;
                        }
                      },
                      legend: {
                        top: '5%',
                        left: 'center'
                      },
                      series: [{
                        name: labelMap[role] + ' per Department',
                        type: 'pie',
                        radius: ['40%', '70%'], // <-- Makes it donut
                        avoidLabelOverlap: false,
                        label: {
                          show: false, // <-- Like donut chart, hide inner label
                          position: 'center'
                        },
                        emphasis: {
                          label: {
                            show: true,
                            fontSize: '18',
                            fontWeight: 'bold'
                          }
                        },
                        labelLine: {
                          show: false
                        },
                        data: dataMap[role]
                      }]
                    });

                  }

                  // Initial load
                  updateChart('student');

                  // Dropdown menu listener
                  document.querySelectorAll(".role-filter").forEach(item => {
                    item.addEventListener("click", e => {
                      e.preventDefault();
                      const role = item.getAttribute("data-role");
                      updateChart(role);
                    });
                  });
                });
              </script>
            </div>
          </div>
          <!-- End Pie Chart -->

          <!-- Top 10 rated faculty (student evaluation) -->
          <?php
          // Fetch distinct academic years
          $year_result = mysqli_query($conn, "SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
          $academic_years = [];
          while ($row = mysqli_fetch_assoc($year_result)) {
            $academic_years[] = $row['academic_year'];
          }
          ?>

          <div class="">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title"><strong>Top 10 Highest Rated Faculty </strong>(Student Evaluation)</h5>
                <!-- Filter -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <div class="form-floating">
                      <select id="topRatedYear" class="form-select">
                        <option value="All">All</option>
                        <?php foreach ($academic_years as $year): ?>
                          <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endforeach; ?>
                      </select>
                      <label>Academic Year</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <select id="topRatedSemester" class="form-select">
                        <option value="All">All</option>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                        <option value="Summer">Summer</option>
                      </select>
                      <label>Semester</label>
                    </div>
                  </div>
                </div>

                <div id="verticalBarChart" style="min-height: 400px;" class="echart"></div>
              </div>
            </div>
          </div>

          <script>
            function loadTopRatedChart(year = 'All', semester = 'All') {
              fetch(`fetch-top-rated.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
                .then(res => res.json())
                .then(data => {
                  const chart = echarts.init(document.querySelector("#verticalBarChart"));
                  chart.setOption({
                    title: {
                      text: 'Top 10 Highest Rated Faculty',
                      left: 'center'
                    },
                    tooltip: {
                      trigger: 'item',
                      formatter: function(params) {
                        const d = params.data;
                        return `<strong>${d.name}</strong><br>Department: ${d.department}<br>Average Rating: ${d.value}%`;
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
                      name: 'Average Rating (%)',
                      min: 0,
                      max: 100
                    },
                    yAxis: {
                      type: 'category',
                      data: data.names
                    },
                    series: [{
                      name: 'Rating',
                      type: 'bar',
                      data: data.ratings,
                      itemStyle: {
                        color: '#4CAF50'
                      }
                    }],
                    animationDuration: 1000,
                    animationEasing: 'cubicOut'
                  });
                });
            }

            document.addEventListener("DOMContentLoaded", () => {
              const yearSelect = document.getElementById("topRatedYear");
              const semSelect = document.getElementById("topRatedSemester");

              function reloadChart() {
                loadTopRatedChart(yearSelect.value, semSelect.value);
              }

              yearSelect.addEventListener("change", reloadChart);
              semSelect.addEventListener("change", reloadChart);

              loadTopRatedChart(); // Load default
            });
          </script>
          <!-- End of Bar chart -->

          <!-- Top 10 rated faculty (Supervisor Evaluation) -->
          <?php
          $year_result_admin = mysqli_query($conn, "SELECT DISTINCT academic_year FROM admin_evaluation ORDER BY academic_year DESC");
          $admin_years = [];
          while ($row = mysqli_fetch_assoc($year_result_admin)) {
            $admin_years[] = $row['academic_year'];
          }
          ?>

          <div class="">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title"><strong>Top 10 Highest Rated Faculty </strong>(Supervisor Evaluation)</h5>
                <!-- Filter -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <div class="form-floating">
                      <select id="topRatedAdminYear" class="form-select">
                        <option value="All">All</option>
                        <?php foreach ($admin_years as $year): ?>
                          <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endforeach; ?>
                      </select>
                      <label>Academic Year</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <select id="topRatedAdminSemester" class="form-select">
                        <option value="All">All</option>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                        <option value="Summer">Summer</option>
                      </select>
                      <label>Semester</label>
                    </div>
                  </div>
                </div>

                <div id="adminVerticalBarChart" style="min-height: 400px;" class="echart"></div>
              </div>
            </div>
          </div>

          <script>
            function loadTopRatedAdminChart(year = 'All', semester = 'All') {
              fetch(`fetch-top-rated-admin.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
                .then(res => res.json())
                .then(data => {
                  const chart = echarts.init(document.querySelector("#adminVerticalBarChart"));
                  chart.setOption({
                    title: {
                      text: 'Top 10 Highest Rated Faculty',
                      left: 'center'
                    },
                    tooltip: {
                      trigger: 'item',
                      formatter: function(params) {
                        const d = params.data;
                        return `<strong>${d.name}</strong><br>Department: ${d.department}<br>Average Rating: ${d.value}%`;
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
                      name: 'Average Rating (%)',
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
                        color: '#4CAF50'
                      }
                    }],
                    animationDuration: 1000,
                    animationEasing: 'cubicOut'
                  });
                });
            }

            document.addEventListener("DOMContentLoaded", () => {
              const yearSelect = document.getElementById("topRatedAdminYear");
              const semSelect = document.getElementById("topRatedAdminSemester");

              function reloadChart() {
                loadTopRatedAdminChart(yearSelect.value, semSelect.value);
              }

              yearSelect.addEventListener("change", reloadChart);
              semSelect.addEventListener("change", reloadChart);

              loadTopRatedAdminChart(); // Default load
            });
          </script>
          <!-- End Top 10 Highest Rated (supervisor) -->
           
        </div><!-- End Right side columns -->

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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>
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
$faculty_result = mysqli_query($conn, $faculty_query);
$data = mysqli_fetch_assoc($faculty_result);
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

$year = $_GET['year'] ?? 'All';
$semester = $_GET['semester'] ?? 'All';
$dept = $_GET['dept'] ?? 'All';


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

            <!-- Evaluation Progress Per College (Supervisor to Faculty) -->
            <div class="col-lg-12">
              <?php
              $selectedYear = $_GET['year'] ?? 'All';
              $selectedSemester = $_GET['semester'] ?? 'All';
              $selectedDept = $_GET['dept'] ?? 'All';

              // Academic Years
              $year_result = mysqli_query($conn, "SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
              $academic_years = [];
              while ($row = mysqli_fetch_assoc($year_result)) {
                $academic_years[] = $row['academic_year'];
              }

              // Semesters
              $sem_result = mysqli_query($conn, "SELECT DISTINCT semester FROM evaluation ORDER BY semester ASC");
              $semesters = [];
              while ($row = mysqli_fetch_assoc($sem_result)) {
                $semesters[] = $row['semester'];
              }

              // Departments
              $dept_result = mysqli_query($conn, "SELECT DISTINCT department FROM faculty WHERE department IS NOT NULL ORDER BY department ASC");
              $departments = [];
              while ($row = mysqli_fetch_assoc($dept_result)) {
                $departments[] = $row['department'];
              }
              ?>

              <div class="card">
                <!-- Filters -->
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="max-height:400px;overflow-y:auto;">

                    <!-- Department filter -->
                    <li class="dropdown-header">Department</li>
                    <li><a class="dropdown-item dept-filter <?= $selectedDept == 'All' ? 'active' : '' ?>" href="#" data-dept="All">All</a></li>
                    <?php foreach ($departments as $dept): ?>
                      <li><a class="dropdown-item dept-filter <?= $selectedDept == $dept ? 'active' : '' ?>" href="#" data-dept="<?= $dept ?>"><?= $dept ?></a></li>
                    <?php endforeach; ?>

                    <li>
                      <hr class="dropdown-divider">
                    </li>

                    <!-- Academic Year filter -->
                    <li class="dropdown-header">Academic Year</li>
                    <li><a class="dropdown-item year-filter <?= $selectedYear == 'All' ? 'active' : '' ?>" href="#" data-year="All">All</a></li>
                    <?php foreach ($academic_years as $year): ?>
                      <li><a class="dropdown-item year-filter <?= $selectedYear == $year ? 'active' : '' ?>" href="#" data-year="<?= $year ?>"><?= $year ?></a></li>
                    <?php endforeach; ?>

                    <li>
                      <hr class="dropdown-divider">
                    </li>

                    <!-- Semester filter -->
                    <li class="dropdown-header">Semester</li>
                    <li><a class="dropdown-item semester-filter <?= $selectedSemester == 'All' ? 'active' : '' ?>" href="#" data-semester="All">All</a></li>
                    <?php foreach ($semesters as $sem): ?>
                      <li><a class="dropdown-item semester-filter <?= $selectedSemester == $sem ? 'active' : '' ?>" href="#" data-semester="<?= $sem ?>"><?= $sem ?></a></li>
                    <?php endforeach; ?>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">
                    <strong>Evaluation Progress Per College</strong>
                    <span id="yearLabel" class="text-muted small">| All Years | All Semesters | All Departments</span><br><br>
                    Supervisor-to-Faculty Evaluation
                  </h5>

                  <!-- Stacked Bar Chart -->
                  <canvas id="stakedBarChart" style="max-height: 400px;"></canvas>
                  <script>
                    let barChart;
                    let supYear = 'All';
                    let supSemester = 'All';
                    let supDept = 'All';

                    function fetchSupervisorFacultyData(year = 'All', semester = 'All', dept = 'All') {
                      document.getElementById("yearLabel").textContent =
                        `| ${year === 'All' ? 'All Years' : year} | ${semester === 'All' ? 'All Semesters' : semester} | ${dept === 'All' ? 'All Departments' : dept}`;

                      fetch(`fetch-supervisor-faculty-progress.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}&dept=${encodeURIComponent(dept)}`)
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
                                plugins: {
                                  tooltip: {
                                    callbacks: {
                                      label: function(context) {
                                        return context.dataset.label;
                                      }
                                    }
                                  },
                                  legend: {
                                    labels: {
                                      usePointStyle: false, // 👈 rectangles instead of circles
                                      boxWidth: 40,
                                      boxHeight: 12,
                                      borderRadius: 2
                                    }
                                  }
                                },
                                scales: {
                                  x: {
                                    stacked: true
                                  },
                                  y: {
                                    stacked: true,
                                    title: {
                                      display: true,
                                      text: 'Evaluation Progress (%)'
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
                      fetchSupervisorFacultyData();

                      // Year filter
                      document.querySelectorAll(".year-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          supYear = item.getAttribute("data-year");
                          fetchSupervisorFacultyData(supYear, supSemester, supDept);
                          document.querySelectorAll(".year-filter").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");
                        });
                      });

                      // Semester filter
                      document.querySelectorAll(".semester-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          supSemester = item.getAttribute("data-semester");
                          fetchSupervisorFacultyData(supYear, supSemester, supDept);
                          document.querySelectorAll(".semester-filter").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");
                        });
                      });

                      // Department filter
                      document.querySelectorAll(".dept-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          supDept = item.getAttribute("data-dept");
                          fetchSupervisorFacultyData(supYear, supSemester, supDept);
                          document.querySelectorAll(".dept-filter").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");
                        });
                      });
                    });
                  </script>
                </div>
              </div>
            </div>
            <!-- End Stacked Bar Chart -->


            <!-- Evaluation Progress (Student Evaluation) -->
            <div class="col-lg-12">
              <?php
              // Distinct departments
              $dept_result = mysqli_query($conn, "SELECT DISTINCT department FROM faculty WHERE department IS NOT NULL AND department != '' ORDER BY department ASC");
              $departments = [];
              while ($row = mysqli_fetch_assoc($dept_result)) {
                $departments[] = $row['department'];
              }

              // Distinct academic years
              $year_result = mysqli_query($conn, "SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
              $years = [];
              while ($row = mysqli_fetch_assoc($year_result)) {
                $years[] = $row['academic_year'];
              }

              // Distinct semesters (ADDED BACK)
              $sem_result = mysqli_query($conn, "SELECT DISTINCT semester FROM evaluation ORDER BY semester ASC");
              $semesters = [];
              while ($row = mysqli_fetch_assoc($sem_result)) {
                $semesters[] = $row['semester'];
              }
              ?>

              <div class="card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="max-height: 400px; overflow-y: auto;">

                    <li class="dropdown-header">Department</li>
                    <li><a class="dropdown-item admin-dept-filter active" href="#" data-dept="All">All</a></li>
                    <?php foreach ($departments as $dept): ?>
                      <li><a class="dropdown-item admin-dept-filter" href="#" data-dept="<?= $dept ?>"><?= $dept ?></a></li>
                    <?php endforeach; ?>

                    <li>
                      <hr class="dropdown-divider">
                    </li>

                    <li class="dropdown-header">Academic Year</li>
                    <li><a class="dropdown-item admin-year-filter active" href="#" data-year="All">All</a></li>
                    <?php foreach ($years as $year): ?>
                      <li><a class="dropdown-item admin-year-filter" href="#" data-year="<?= $year ?>"><?= $year ?></a></li>
                    <?php endforeach; ?>

                    <li>
                      <hr class="dropdown-divider">
                    </li>

                    <li class="dropdown-header">Semester</li>
                    <li><a class="dropdown-item admin-semester-filter active" href="#" data-semester="All">All</a></li>
                    <?php foreach ($semesters as $sem): ?>
                      <li><a class="dropdown-item admin-semester-filter" href="#" data-semester="<?= $sem ?>"><?= $sem ?></a></li>
                    <?php endforeach; ?>

                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">
                    <strong>Evaluation Progress Per College</strong>
                    <span id="adminYearLabel" class="text-muted small"></span><br><br>
                    Student-to-Faculty Evaluation
                  </h5>

                  <canvas id="supervisorBarChart" style="max-height: 500px;"></canvas>

                  <script>
                    // --- UPDATED JAVASCRIPT ---

                    // Restore defaults to 'All'
                    let selectedYear = 'All';
                    let selectedSemester = 'All'; // <-- ADDED BACK
                    let selectedDept = 'All';
                    let supervisorBarChart = null;

                    // Updated function (with semester)
                    function fetchAdminChartData(year = 'All', semester = 'All', dept = 'All') {

                      // Update label (with semester)
                      document.getElementById("adminYearLabel").textContent =
                        `| ${year === 'All' ? 'All Years' : year} | ${semester === 'All' ? 'All Semesters' : semester} | ${dept === 'All' ? 'All Departments' : dept}`;

                      // Update fetch URL (with semester)
                      fetch(`fetch-admin-chart-data.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}&dept=${encodeURIComponent(dept)}`)
                        .then(response => response.json())
                        .then(chartData => {
                          if (supervisorBarChart) {
                            // Update existing chart
                            supervisorBarChart.data.labels = chartData.labels;
                            supervisorBarChart.data.datasets = chartData.datasets;
                            supervisorBarChart.data.ratios = chartData.ratios;
                            supervisorBarChart.update();
                          } else {
                            // Create new chart (using the corrected logic from our first fix)
                            const ctx = document.querySelector('#supervisorBarChart').getContext('2d');
                            supervisorBarChart = new Chart(ctx, {
                              type: 'bar',
                              data: {
                                labels: chartData.labels,
                                datasets: chartData.datasets,
                                ratios: chartData.ratios
                              },
                              options: {
                                responsive: true,
                                plugins: {
                                  tooltip: {
                                    callbacks: {
                                      label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.y !== null) {
                                          label += context.parsed.y.toFixed(2) + '%';
                                        }
                                        return label;
                                      },
                                      footer: function(tooltipItems) {
                                        const index = tooltipItems[0].dataIndex;
                                        const ratio = tooltipItems[0].chart.data.ratios[index];
                                        return `Total: ${ratio}`;
                                      }
                                    }
                                  }
                                },
                                scales: {
                                  x: {
                                    stacked: true
                                  },
                                  y: {
                                    stacked: true,
                                    title: {
                                      display: true,
                                      text: 'Evaluation Progress (%)'
                                    },
                                    min: 0,
                                    max: 100
                                  }
                                }
                              }
                            });
                          }
                        })
                        .catch(err => console.error("Fetch error:", err));
                    }

                    document.addEventListener("DOMContentLoaded", () => {
                      // Initial fetch with defaults ('All', 'All', 'All')
                      fetchAdminChartData();

                      // Department filter
                      document.querySelectorAll(".admin-dept-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          selectedDept = item.getAttribute("data-dept");
                          fetchAdminChartData(selectedYear, selectedSemester, selectedDept);
                          document.querySelectorAll(".admin-dept-filter").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");
                        });
                      });

                      // Academic year filter
                      document.querySelectorAll(".admin-year-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          selectedYear = item.getAttribute("data-year");
                          fetchAdminChartData(selectedYear, selectedSemester, selectedDept);
                          document.querySelectorAll(".admin-year-filter").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");
                        });
                      });

                      // Semester filter (ADDED BACK)
                      document.querySelectorAll(".admin-semester-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          selectedSemester = item.getAttribute("data-semester");
                          fetchAdminChartData(selectedYear, selectedSemester, selectedDept); // Pass only 2 params
                          document.querySelectorAll(".admin-year-filter").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");
                        });
                      });

                      // --- SEMESTER EVENT LISTENER REMOVED ---

                    });
                  </script>

                </div>
              </div>
            </div>
            <!-- End Evaluation Progress Evaluation Chart -->

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
                  <h6>Date Filter</h6>
                </li>
                <li><a class="dropdown-item activity-filter" href="#" data-filter="today">Today</a></li>
                <li><a class="dropdown-item activity-filter" href="#" data-filter="month">This Month</a></li>
                <li><a class="dropdown-item activity-filter" href="#" data-filter="year">This Year</a></li>
                <li><a class="dropdown-item activity-filter" href="#" data-filter="all">All</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li class="dropdown-header text-start">
                  <h6>Role Filter</h6>
                </li>
                <li><a class="dropdown-item activity-role-filter" href="#" data-role="student">Student</a></li>
                <li><a class="dropdown-item activity-role-filter" href="#" data-role="faculty">Faculty</a></li>
                <li><a class="dropdown-item activity-role-filter" href="#" data-role="admin">Admin</a></li>
                <li><a class="dropdown-item activity-role-filter" href="#" data-role="superadmin">Superadmin</a></li>
                <li><a class="dropdown-item activity-role-filter" href="#" data-role="all">All Roles</a></li>
              </ul>
            </div>


            <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="activityContainer">
              <h5 class="card-title">Recent Activity <span id="filter-label">| All</span></h5>
              <div class="activity" id="activity-list">
                <div class="activity" id="activity-list"></div>
              </div>

              <div class="activity" id="activity-list"></div>
              <div id="loadingIndicator" class="text-center my-2" style="display: none;">
                <div class="spinner-border text-primary" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
              </div>

              <script>
                let offset = 0;
                const limit = 10;
                let loading = false;
                let filter = 'all';
                let role = 'all';

                const activityList = document.getElementById("activity-list");
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

                  if (reset) offset = 0;

                  fetch(`activity-fetch.php?limit=${limit}&offset=${offset}&filter=${filter}&role=${role}`)
                    .then(res => res.json())
                    .then(data => {
                      if (reset) activityList.innerHTML = '';

                      data.forEach(log => {
                        const timeAgo = getTimeAgo(log.timestamp);
                        const item = `
                          <div class="activity-item d-flex">
                            <div class="activite-label">${timeAgo}</div>
                            <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                            <div class="activity-content">
                              <span class="fw-bold">${log.user_id}:</span> ${log.activity}
                            </div>
                          </div>`;
                        activityList.insertAdjacentHTML("beforeend", item);
                      });

                      if (data.length > 0) offset += data.length;
                      loading = false;
                      loadingIndicator.style.display = 'none';
                    })
                    .catch(err => {
                      console.error("Fetch error:", err);
                      loading = false;
                      loadingIndicator.style.display = 'none';
                    });
                }

                document.addEventListener("DOMContentLoaded", () => {
                  loadLogs();

                  activityContainer.addEventListener("scroll", () => {
                    if (activityContainer.scrollTop + activityContainer.clientHeight >= activityContainer.scrollHeight - 5 && !loading) {
                      loadLogs();
                    }
                  });

                  document.querySelectorAll(".activity-filter").forEach(btn => {
                    btn.addEventListener("click", (e) => {
                      e.preventDefault();
                      filter = btn.dataset.filter;
                      document.getElementById("filter-label").textContent = `| ${btn.textContent}`;
                      loadLogs(true);
                    });
                  });

                  document.querySelectorAll(".activity-role-filter").forEach(btn => {
                    btn.addEventListener("click", (e) => {
                      e.preventDefault();
                      role = btn.dataset.role;
                      document.getElementById("filter-label").textContent = `| ${btn.textContent}`;
                      loadLogs(true);
                    });
                  });
                });
              </script>

            </div>
          </div>
          <!-- End Recent Activity -->

          <!-- Superadmin as Faculty Progress Chart -->
          <?php
          // Use a temporary variable to avoid conflicts if $_SESSION['idnumber'] is used elsewhere
          $faculty_id_for_filters = $_SESSION['idnumber'];

          // Get DISTINCT academic years this faculty has taught
          $year_query = $conn->query("
    SELECT DISTINCT academic_year FROM student_subject
    WHERE faculty_id = '{$faculty_id_for_filters}' ORDER BY academic_year DESC
");
          $sa_years = [];
          while ($row = $year_query->fetch_assoc()) {
            $sa_years[] = $row['academic_year'];
          }

          // Get DISTINCT semesters this faculty has taught
          $sem_query = $conn->query("
    SELECT DISTINCT semester FROM student_subject
    WHERE faculty_id = '{$faculty_id_for_filters}' ORDER BY semester ASC
");
          $sa_semesters = [];
          while ($row = $sem_query->fetch_assoc()) {
            $sa_semesters[] = $row['semester'];
          }
          ?>

          <div class="col-12">
            <div class="card shadow">

              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="max-height: 400px; overflow-y: auto;">

                  <li class="dropdown-header">Academic Year</li>
                  <li><a class="dropdown-item sa-year-filter active" href="#" data-year="All">All</a></li>
                  <?php foreach ($sa_years as $year): ?>
                    <li><a class="dropdown-item sa-year-filter" href="#" data-year="<?= $year ?>"><?= $year ?></a></li>
                  <?php endforeach; ?>

                  <li>
                    <hr class="dropdown-divider">
                  </li>

                  <li class="dropdown-header">Semester</li>
                  <li><a class="dropdown-item sa-sem-filter active" href="#" data-sem="All">All</a></li>
                  <?php foreach ($sa_semesters as $sem): ?>
                    <li><a class="dropdown-item sa-sem-filter" href="#" data-sem="<?= $sem ?>"><?= $sem ?></a></li>
                  <?php endforeach; ?>

                </ul>
              </div>
              <div class="card-body">
                <h5 class="card-title">
                  Your Evaluation Progress
                  <span id="saFacultyTermLabel" class="text-muted small"></span>
                </h5>

                <div id="saFacultyChartContainer">
                  <canvas id="superadminFacultyProgressChart" style="height: 400px;"></canvas>
                </div>

                <script>
                  // Set defaults to 'All'
                  let selectedSaYear = 'All';
                  let selectedSaSem = 'All';
                  let superadminFacultyChart = null; // Hold the chart instance

                  // 1. Re-usable function to fetch and render the chart
                  function fetchSuperadminFacultyData(year, semester) {

                    // Update the title label
                    document.getElementById("saFacultyTermLabel").textContent =
                      `| ${year === 'All' ? 'All Years' : year} | ${semester === 'All' ? 'All Semesters' : semester}`;

                    fetch(`fetch-superadmin-faculty-progress.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
                      .then(response => {
                        if (!response.ok) {
                          // Handle server errors (like 500)
                          throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                      })
                      .then(chartData => {
                        // Check for PHP-level errors returned as JSON
                        if (chartData.error) {
                          console.error("Backend Error:", chartData.error, chartData.sql || '');
                          throw new Error(chartData.error);
                        }

                        const chartContainer = document.getElementById("saFacultyChartContainer");

                        // Check for no data
                        if (chartData.labels.length === 0) {
                          if (superadminFacultyChart) {
                            superadminFacultyChart.destroy();
                            superadminFacultyChart = null;
                          }
                          // Reset canvas container and show "no data" message
                          chartContainer.innerHTML = '<canvas id="superadminFacultyProgressChart" style="height: 400px;"></canvas>';
                          document.getElementById("superadminFacultyProgressChart").replaceWith("⚠️ No evaluation data found for the selected filters.");
                          return;
                        }

                        // If we had a "no data" message, clear it and restore canvas
                        if (!document.getElementById("superadminFacultyProgressChart")) {
                          chartContainer.innerHTML = '<canvas id="superadminFacultyProgressChart" style="height: 400px;"></canvas>';
                        }

                        // 2. Use the robust update/create pattern
                        if (superadminFacultyChart) {
                          // Update existing chart
                          superadminFacultyChart.data.labels = chartData.labels;
                          superadminFacultyChart.data.datasets = chartData.datasets;
                          superadminFacultyChart.data.meta = chartData.meta;
                          superadminFacultyChart.update();
                        } else {
                          // Create new chart
                          const ctx = document.getElementById("superadminFacultyProgressChart").getContext("2d");
                          superadminFacultyChart = new Chart(ctx, {
                            type: "bar",
                            data: {
                              labels: chartData.labels,
                              datasets: chartData.datasets,
                              meta: chartData.meta // Store meta data
                            },
                            options: {
                              responsive: true,
                              plugins: {
                                legend: {
                                  position: "top"
                                },
                                tooltip: {
                                  callbacks: {
                                    label: function(context) {
                                      let datasetLabel = context.dataset.label;
                                      let value = context.raw;
                                      let idx = context.dataIndex;
                                      let done = context.chart.data.meta.done[idx];
                                      let total = context.chart.data.meta.total[idx];

                                      if (datasetLabel.includes("Completed")) {
                                        return `${datasetLabel}: ${value}% (${done}/${total} students)`;
                                      } else {
                                        let pendingCount = total - done;
                                        return `${datasetLabel}: ${value}% (${pendingCount}/${total} students)`;
                                      }
                                    }
                                  }
                                }
                              },
                              scales: {
                                x: {
                                  stacked: true
                                },
                                y: {
                                  stacked: true,
                                  beginAtZero: true,
                                  max: 100,
                                  title: {
                                    display: true,
                                    text: "Evaluation Progress (%)"
                                  }
                                }
                              }
                            }
                          });
                        }
                      })
                      .catch(err => {
                        // This catch block shows the error message
                        console.error("Fetch Error:", err);
                        document.getElementById("saFacultyChartContainer").innerHTML = "Error loading chart data. Check console (F12) for details.";
                      });
                  }

                  // 3. Add Event Listeners
                  document.addEventListener("DOMContentLoaded", () => {
                    // Initial fetch on page load with defaults ("All", "All")
                    fetchSuperadminFacultyData(selectedSaYear, selectedSaSem);

                    // Add listener for all year filter links
                    document.querySelectorAll(".sa-year-filter").forEach(item => {
                      item.addEventListener("click", (e) => {
                        e.preventDefault();
                        selectedSaYear = item.getAttribute("data-year");
                        fetchSuperadminFacultyData(selectedSaYear, selectedSaSem);
                        // Update active class
                        document.querySelectorAll(".sa-year-filter").forEach(i => i.classList.remove("active"));
                        item.classList.add("active");
                      });
                    });

                    // Add listener for all semester filter links
                    document.querySelectorAll(".sa-sem-filter").forEach(item => {
                      item.addEventListener("click", (e) => {
                        e.preventDefault();
                        selectedSaSem = item.getAttribute("data-sem");
                        fetchSuperadminFacultyData(selectedSaYear, selectedSaSem);
                        // Update active class
                        document.querySelectorAll(".sa-sem-filter").forEach(i => i.classList.remove("active"));
                        item.classList.add("active");
                      });
                    });
                  });
                </script>

              </div>
            </div>
          </div>
          <!-- End Superadmin as Faculty Progress Chart -->

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
  <script src="chart/chart.js"></script>

</body>

</html>
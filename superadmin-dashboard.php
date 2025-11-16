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

// --- START: Evaluation Trend Graph Data ---

// 1. Get all student evaluation timestamps
$student_trend_query = "SELECT created_at AS eval_time, 'student' AS type FROM evaluation";
$result_student = $conn->query($student_trend_query);

$all_evals = [];
if ($result_student) {
  while ($row = $result_student->fetch_assoc()) {
    $all_evals[] = $row;
  }
}

// 2. Get all admin evaluation timestamps
$admin_trend_query = "SELECT evaluation_date AS eval_time, 'admin' AS type FROM admin_evaluation";
$result_admin = $conn->query($admin_trend_query);

if ($result_admin) {
  while ($row = $result_admin->fetch_assoc()) {
    $all_evals[] = $row;
  }
}

// 3. Sort all evaluations by timestamp
usort($all_evals, function ($a, $b) {
  return strtotime($a['eval_time']) <=> strtotime($b['eval_time']);
});

// 4. Build the cumulative chart data
$chart_timestamps = [];
$student_eval_counts = [];
$admin_eval_counts = [];
$s_count = 0;
$a_count = 0;

foreach ($all_evals as $eval) {
  if ($eval['type'] === 'student') {
    $s_count++;
  } else {
    $a_count++;
  }

  // Add the timestamp to the X-axis
  $chart_timestamps[] = $eval['eval_time'];

  // Add the *current* cumulative count for *both* series
  $student_eval_counts[] = $s_count;
  $admin_eval_counts[] = $a_count;
}

// --- END: Evaluation Trend Graph Data ---
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

                          const chartCanvas = document.querySelector('#stakedBarChart');
                          let noDataMessage = document.querySelector('#supervisorNoData'); // Using the same ID as my previous fix

                          if (chartData.labels.length === 0) {
                            chartCanvas.style.display = 'none';
                            if (noDataMessage) {
                              noDataMessage.style.display = 'block';
                            } else {
                              const messageEl = document.createElement('p');
                              messageEl.id = 'supervisorNoData';
                              messageEl.textContent = 'No evaluation data found for the selected filters.';
                              messageEl.className = 'text-center text-muted mt-3';
                              chartCanvas.parentNode.appendChild(messageEl);
                            }
                            return;
                          } else {
                            chartCanvas.style.display = 'block';
                            if (noDataMessage) noDataMessage.style.display = 'none';
                          }

                          if (barChart) {
                            barChart.data.labels = chartData.labels;
                            barChart.data.datasets = chartData.datasets;
                            barChart.data.ratios = chartData.ratios;
                            barChart.update();
                          } else {
                            const ctx = chartCanvas.getContext('2d');
                            barChart = new Chart(ctx, {
                              type: 'bar',
                              data: {
                                labels: chartData.labels,
                                datasets: chartData.datasets,
                                ratios: chartData.ratios
                              },
                              options: {
                                // --- 🚀 HORIZONTAL CHART FIX ---
                                indexAxis: 'y', // <-- Makes it horizontal
                                // --- End of Fix ---

                                responsive: true,
                                plugins: {
                                  tooltip: {
                                    callbacks: {
                                      label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        // --- Note: Use .x for horizontal ---
                                        if (context.parsed.x !== null) {
                                          label += context.parsed.x.toFixed(2) + '%';
                                        }
                                        return label;
                                      },
                                      footer: function(tooltipItems) {
                                        const index = tooltipItems[0].dataIndex;
                                        const ratio = tooltipItems[0].chart.data.ratios[index];
                                        return `Total: ${ratio}`;
                                      }
                                    }
                                  },
                                  legend: {
                                    position: 'bottom',
                                    labels: {
                                      usePointStyle: false,
                                      boxWidth: 40,
                                      boxHeight: 12,
                                      borderRadius: 2
                                    }
                                  }
                                },
                                // --- 🚀 SWAP THE SCALES ---
                                scales: {
                                  x: { // This is now the Percentage axis
                                    stacked: true,
                                    title: {
                                      display: true,
                                      text: 'Evaluation Progress (%)'
                                    },
                                    min: 0,
                                    max: 100
                                  },
                                  y: { // This is now the Department axis
                                    stacked: true,
                                    grid: {
                                      display: false // Cleaner look
                                    }
                                  }
                                }
                                // --- End of Scale Swap ---
                              }
                            });
                          }
                        })
                        .catch(err => console.error("Fetch error:", err));
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
                    let selectedYear = 'All';
                    let selectedSemester = 'All';
                    let selectedDept = 'All';
                    let supervisorBarChart = null;

                    function fetchAdminChartData(year = 'All', semester = 'All', dept = 'All') {
                      document.getElementById("adminYearLabel").textContent =
                        `| ${year === 'All' ? 'All Years' : year} | ${semester === 'All' ? 'All Semesters' : semester} | ${dept === 'All' ? 'All Departments' : dept}`;

                      fetch(`fetch-admin-chart-data.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}&dept=${encodeURIComponent(dept)}`)
                        .then(response => response.json())
                        .then(chartData => {

                          // Add a check for no data
                          const chartCanvas = document.querySelector('#supervisorBarChart');
                          const noDataMessage = document.querySelector('#supervisorNoData');
                          if (chartData.labels.length === 0) {
                            chartCanvas.style.display = 'none'; // Hide canvas
                            if (noDataMessage) {
                              noDataMessage.style.display = 'block'; // Show message
                            } else {
                              // Create message if it doesn't exist
                              const messageEl = document.createElement('p');
                              messageEl.id = 'supervisorNoData';
                              messageEl.textContent = 'No evaluation data found for the selected filters.';
                              messageEl.className = 'text-center text-muted mt-3';
                              chartCanvas.parentNode.appendChild(messageEl);
                            }
                            return; // Stop here
                          } else {
                            // Show canvas and hide message if data exists
                            chartCanvas.style.display = 'block';
                            if (noDataMessage) noDataMessage.style.display = 'none';
                          }

                          if (supervisorBarChart) {
                            // Update existing chart
                            supervisorBarChart.data.labels = chartData.labels;
                            supervisorBarChart.data.datasets = chartData.datasets;
                            supervisorBarChart.data.ratios = chartData.ratios;
                            supervisorBarChart.update();
                          } else {
                            // Create new chart
                            const ctx = chartCanvas.getContext('2d');
                            supervisorBarChart = new Chart(ctx, {
                              type: 'bar', // Stays 'bar', indexAxis makes it horizontal
                              data: {
                                labels: chartData.labels,
                                datasets: chartData.datasets,
                                ratios: chartData.ratios
                              },
                              // --- 🚀 THIS IS THE NEW PROFESSIONAL CONFIG ---
                              options: {
                                indexAxis: 'y', // <-- THIS MAKES IT HORIZONTAL
                                responsive: true,
                                maintainAspectRatio: false, // Good for horizontal charts
                                plugins: {
                                  legend: {
                                    position: 'bottom', // <-- Cleaner position
                                  },
                                  tooltip: {
                                    callbacks: {
                                      label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.x !== null) { // <-- Note: changed to .x
                                          label += context.parsed.x.toFixed(2) + '%';
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
                                // --- Swap X and Y scales ---
                                scales: {
                                  x: { // This is now the Percentage axis
                                    stacked: true,
                                    min: 0,
                                    max: 100,
                                    title: {
                                      display: true,
                                      text: 'Evaluation Progress (%)'
                                    }
                                  },
                                  y: { // This is now the Faculty/Subject axis
                                    stacked: true,
                                    ticks: {
                                      autoSkip: false // Ensures all labels are shown
                                    },
                                    grid: {
                                      display: false // <-- Cleaner look
                                    }
                                  }
                                }
                              }
                              // --- END OF NEW CONFIG ---
                            });
                          }
                        })
                        .catch(err => console.error("Fetch error:", err));
                    }

                    document.addEventListener("DOMContentLoaded", () => {
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

                      // Semester filter (Your bug fix is already here and correct)
                      document.querySelectorAll(".admin-semester-filter").forEach(item => {
                        item.addEventListener("click", (e) => {
                          e.preventDefault();
                          selectedSemester = item.getAttribute("data-semester");
                          fetchAdminChartData(selectedYear, selectedSemester, selectedDept);
                          document.querySelectorAll(".admin-semester-filter").forEach(i => i.classList.remove("active"));
                          item.classList.add("active");
                        });
                      });
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

          <!-- Evaluation Trend Graph -->
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Evaluation Trend <span>| All Time</span></h5>
                <div id="evaluationTrendChart" style="height: 350px;"></div>
              </div>
            </div>
          </div>

          <script>
            document.addEventListener("DOMContentLoaded", () => {
              new ApexCharts(document.querySelector("#evaluationTrendChart"), {
                series: [{
                  name: 'Student Evaluations',
                  data: <?= json_encode($student_eval_counts) ?>
                }, {
                  name: 'Admin Evaluations',
                  data: <?= json_encode($admin_eval_counts) ?>
                }],
                chart: {
                  height: 350,
                  type: 'line',
                  toolbar: {
                    show: false
                  },
                  zoom: {
                    enabled: true
                  }
                },
                markers: {
                  size: 4
                },
                colors: ['#4154f1', '#ff771d'], // Blue for students, Orange for admins
                fill: {
                  type: "gradient",
                  gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.4,
                    stops: [0, 90, 100]
                  }
                },
                dataLabels: {
                  enabled: false // Set to true if you want to see the numbers on the line
                },
                stroke: {
                  curve: 'smooth',
                  width: 2
                },
                xaxis: {
                  type: 'datetime',
                  categories: <?= json_encode($chart_timestamps) ?>
                },
                tooltip: {
                  x: {
                    format: 'MMM dd, yyyy'
                  }
                }
              }).render();
            });
          </script>

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
                          throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                      })
                      .then(chartData => {
                        if (chartData.error) {
                          console.error("Backend Error:", chartData.error, chartData.sql || '');
                          throw new Error(chartData.error);
                        }

                        const chartContainer = document.getElementById("saFacultyChartContainer");
                        const chartCanvas = document.getElementById("superadminFacultyProgressChart");

                        // Check for no data
                        if (chartData.labels.length === 0) {
                          if (superadminFacultyChart) {
                            superadminFacultyChart.destroy();
                            superadminFacultyChart = null;
                          }

                          // Check if a "no data" message already exists
                          let noDataEl = document.getElementById("saFacultyNoData");
                          if (!noDataEl) {
                            noDataEl = document.createElement('p');
                            noDataEl.id = 'saFacultyNoData';
                            noDataEl.className = 'text-center text-muted mt-3';
                            noDataEl.textContent = '⚠️ No evaluation data found for the selected filters.';
                            chartContainer.appendChild(noDataEl); // Add message
                          }
                          chartCanvas.style.display = 'none'; // Hide canvas
                          noDataEl.style.display = 'block'; // Show message
                          return;
                        }

                        // If data exists, hide "no data" message and show canvas
                        chartCanvas.style.display = 'block';
                        let noDataEl = document.getElementById("saFacultyNoData");
                        if (noDataEl) noDataEl.style.display = 'none';


                        // 2. Use the robust update/create pattern
                        if (superadminFacultyChart) {
                          // Update existing chart
                          superadminFacultyChart.data.labels = chartData.labels;
                          superadminFacultyChart.data.datasets = chartData.datasets;
                          superadminFacultyChart.data.meta = chartData.meta;
                          superadminFacultyChart.update();
                        } else {
                          // Create new chart
                          const ctx = chartCanvas.getContext("2d");
                          superadminFacultyChart = new Chart(ctx, {
                            type: "bar",
                            data: {
                              labels: chartData.labels,
                              datasets: chartData.datasets,
                              meta: chartData.meta // Store meta data
                            },
                            // --- 🚀 NEW PROFESSIONAL/HORIZONTAL OPTIONS ---
                            options: {
                              indexAxis: 'y', // <-- MAKES IT HORIZONTAL
                              responsive: true,
                              maintainAspectRatio: false,
                              plugins: {
                                legend: {
                                  position: "bottom" // <-- Cleaner position
                                },
                                tooltip: {
                                  callbacks: {
                                    label: function(context) {
                                      let datasetLabel = context.dataset.label;
                                      let value = context.parsed.x; // <-- Use .x for horizontal
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
                              // --- SWAPPED SCALES ---
                              scales: {
                                x: { // This is now the Percentage axis
                                  stacked: true,
                                  beginAtZero: true,
                                  max: 100,
                                  title: {
                                    display: true,
                                    text: "Evaluation Progress (%)"
                                  }
                                },
                                y: { // This is now the Subject axis
                                  stacked: true,
                                  grid: {
                                    display: false // <-- Cleaner look
                                  }
                                }
                              }
                            }
                            // --- END OF NEW OPTIONS ---
                          });
                        }
                      })
                      .catch(err => {
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
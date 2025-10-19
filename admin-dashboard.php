<?php
session_start();
if (isset($_SESSION['access_denied'])) {
  $access_denied = $_SESSION['access_denied'];
  unset($_SESSION['access_denied']);
}
include 'conn/conn.php'; // DB connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

// 

$admin_id = $_SESSION['idnumber'];

// ✅ Get current active evaluation period (latest row)
$setting_query = "SELECT semester, academic_year FROM evaluation_settings ORDER BY updated_at DESC LIMIT 1";
$setting_result = $conn->query($setting_query);

if ($setting_result && $setting_result->num_rows > 0) {
  $setting = $setting_result->fetch_assoc();
  $current_semester = $setting['semester'];
  $current_acad_year = $setting['academic_year'];
} else {
  // Default values in case table is empty
  $current_semester = '1st Semester';
  $current_acad_year = '2025-2026';
}


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
  WHERE f.department = ?
  AND e.academic_year = ?
  AND e.semester = ?";
$stmt = $conn->prepare($student_eval_query);
$stmt->bind_param("sss", $department, $current_acad_year, $current_semester);
$stmt->execute();
$result = $stmt->get_result();
$student_eval_count = $result->fetch_assoc()['total'] ?? 0;

// Count total evaluations from admins
$admin_eval_query = "
  SELECT COUNT(*) AS total FROM admin_evaluation ae
  JOIN faculty f ON ae.evaluatee_id = f.idnumber
  WHERE f.department = ?
  AND ae.academic_year = ?
  AND ae.semester = ?";
$stmt = $conn->prepare($admin_eval_query);
$stmt->bind_param("sss", $department, $current_acad_year, $current_semester);
$stmt->execute();
$result = $stmt->get_result();
$admin_eval_count = $result->fetch_assoc()['total'] ?? 0;

// Combine both
$total_evaluations = $student_eval_count + $admin_eval_count;

/// Student evaluations trend (uses created_at)
$eval_trend_query = "
  SELECT e.created_at AS eval_time
  FROM evaluation e
  JOIN faculty f ON e.faculty_id = f.idnumber
  WHERE f.department = ?
  AND e.academic_year = ?
  AND e.semester = ?
  ORDER BY e.created_at ASC
";
$stmt = $conn->prepare($eval_trend_query);
$stmt->bind_param("sss", $department, $current_acad_year, $current_semester);
$stmt->execute();
$result = $stmt->get_result();

$months = [];
$eval_counts = [];
$count = 0;
while ($row = $result->fetch_assoc()) {
  $count++;
  $months[] = $row['eval_time'];   // exact timestamp
  $eval_counts[] = $count;         // cumulative count
}

// Admin evaluations trend (uses evaluation_date instead of created_at!)
$admin_eval_trend_query = "
  SELECT ae.evaluation_date AS eval_time
  FROM admin_evaluation ae
  JOIN faculty f ON ae.evaluatee_id = f.idnumber
  WHERE f.department = ?
  AND ae.academic_year = ?
  AND ae.semester = ?
  ORDER BY ae.evaluation_date ASC
";
$stmt = $conn->prepare($admin_eval_trend_query);
$stmt->bind_param("sss", $department, $current_acad_year, $current_semester);
$stmt->execute();
$result = $stmt->get_result();

$admin_months = [];
$admin_eval_counts = [];
$admin_count = 0;
while ($row = $result->fetch_assoc()) {
  $admin_count++;
  $admin_months[] = $row['eval_time'];  // exact timestamp
  $admin_eval_counts[] = $admin_count;  // cumulative count
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

</head>

<body>

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->

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

        <!-- Evaluation Trends Chart -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Evaluation Trends <span>| Monthly</span></h5>
              <div id="evalTrendChart"></div>
            </div>
          </div>
        </div>

        <!-- Admin Progress Chart -->
        <div class="col-12">
          <div class="card">
            <!-- Filter -->
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="max-height: 400px; overflow-y: auto;">

                <!-- Academic Year filter -->
                <li class="dropdown-header">Academic Year</li>
                <li><a class="dropdown-item progress-year-filter active" href="#" data-year="All">All</a></li>
                <?php
                $year_result = mysqli_query($conn, "SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
                while ($row = mysqli_fetch_assoc($year_result)): ?>
                  <li><a class="dropdown-item progress-year-filter" href="#" data-year="<?= $row['academic_year'] ?>"><?= $row['academic_year'] ?></a></li>
                <?php endwhile; ?>

                <li>
                  <hr class="dropdown-divider">
                </li>

                <!-- Semester filter -->
                <li class="dropdown-header">Semester</li>
                <li><a class="dropdown-item progress-semester-filter active" href="#" data-semester="All">All</a></li>
                <?php
                $sem_result = mysqli_query($conn, "SELECT DISTINCT semester FROM evaluation ORDER BY semester ASC");
                while ($row = mysqli_fetch_assoc($sem_result)): ?>
                  <li><a class="dropdown-item progress-semester-filter" href="#" data-semester="<?= $row['semester'] ?>"><?= $row['semester'] ?></a></li>
                <?php endwhile; ?>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">
                Evaluation Progress <span id="progressLabel">| All Years | All Semesters</span>
              </h5>
              <canvas id="adminProgressChart" style="max-height: 400px;"></canvas>
            </div>
          </div>
        </div>

        <script>
          let progressYear = 'All';
          let progressSemester = 'All';
          let adminProgressChart = null;

          function fetchProgressData(year = 'All', semester = 'All') {
            document.getElementById("progressLabel").textContent =
              `| ${year === 'All' ? 'All Years' : year} | ${semester === 'All' ? 'All Semesters' : semester}`;

            fetch(`fetch-admin-progress.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
              .then(response => response.json())
              .then(chartData => {

                if (adminProgressChart) {
                  // --- UPDATE BLOCK ---
                  adminProgressChart.data.labels = chartData.labels;
                  adminProgressChart.data.datasets = chartData.datasets;
                  adminProgressChart.data.meta = chartData.meta; // <-- THIS IS THE MISSING LINE
                  adminProgressChart.update();
                } else {
                  // --- CREATE BLOCK (Made more robust) ---
                  const ctx = document.querySelector('#adminProgressChart').getContext('2d');
                  adminProgressChart = new Chart(ctx, {
                    type: 'bar',
                    data: { // Explicitly set data properties
                      labels: chartData.labels,
                      datasets: chartData.datasets,
                      meta: chartData.meta
                    },
                    options: {
                      responsive: true,
                      plugins: {
                        legend: {
                          position: 'top'
                        },
                        tooltip: {
                          callbacks: {
                            label: function(context) {
                              let datasetLabel = context.dataset.label || '';
                              let value = context.raw;
                              let idx = context.dataIndex;

                              // This now reads the CORRECT, updated meta data
                              let done = context.chart.data.meta.done[idx];
                              let total = context.chart.data.meta.total[idx];

                              if (datasetLabel.includes("Completed")) {
                                return `${datasetLabel}: ${value}% (${done}/${total} students)`;
                              } else {
                                let notDone = total - done;
                                return `${datasetLabel}: ${value}% (${notDone}/${total} students)`;
                              }
                            }
                          }
                        }
                      },
                      scales: {
                        x: {
                          stacked: true
                        }, // stack on x-axis
                        y: {
                          stacked: true,
                          max: 100,
                          beginAtZero: true
                        } // force 0–100%
                      }
                    }
                  });
                }
              })
              .catch(err => console.error("Fetch error:", err));
          }


          document.addEventListener("DOMContentLoaded", () => {
            fetchProgressData();

            // Academic year filter
            document.querySelectorAll(".progress-year-filter").forEach(item => {
              item.addEventListener("click", e => {
                e.preventDefault();
                progressYear = item.getAttribute("data-year");
                fetchProgressData(progressYear, progressSemester);
                document.querySelectorAll(".progress-year-filter").forEach(i => i.classList.remove("active"));
                item.classList.add("active");
              });
            });

            // Semester filter
            document.querySelectorAll(".progress-semester-filter").forEach(item => {
              item.addEventListener("click", e => {
                e.preventDefault();
                progressSemester = item.getAttribute("data-semester");
                fetchProgressData(progressYear, progressSemester);
                document.querySelectorAll(".progress-semester-filter").forEach(i => i.classList.remove("active"));
                item.classList.add("active");
              });
            });
          });
        </script>
        <!-- End Admin Progress Chart Card -->

        <!-- Admin as Faculty Progress Chart -->
        <?php
        // Use a temporary variable to avoid conflicts
        $faculty_id_for_filters = $_SESSION['idnumber'];

        // Get DISTINCT academic years this faculty has taught
        $year_query = $conn->query("
    SELECT DISTINCT academic_year FROM student_subject
    WHERE faculty_id = '{$faculty_id_for_filters}' ORDER BY academic_year DESC
");
        $admin_years = [];
        while ($row = $year_query->fetch_assoc()) {
          $admin_years[] = $row['academic_year'];
        }

        // Get DISTINCT semesters this faculty has taught
        $sem_query = $conn->query("
    SELECT DISTINCT semester FROM student_subject
    WHERE faculty_id = '{$faculty_id_for_filters}' ORDER BY semester ASC
");
        $admin_semesters = [];
        while ($row = $sem_query->fetch_assoc()) {
          $admin_semesters[] = $row['semester'];
        }
        ?>

        <div class="col-12">
          <div class="card shadow">

            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="max-height: 400px; overflow-y: auto;">

                <li class="dropdown-header">Academic Year</li>
                <li><a class="dropdown-item admin-faculty-year-filter active" href="#" data-year="All">All</a></li>
                <?php foreach ($admin_years as $year): ?>
                  <li><a class="dropdown-item admin-faculty-year-filter" href="#" data-year="<?= $year ?>"><?= $year ?></a></li>
                <?php endforeach; ?>

                <li>
                  <hr class="dropdown-divider">
                </li>

                <li class="dropdown-header">Semester</li>
                <li><a class="dropdown-item admin-faculty-sem-filter active" href="#" data-sem="All">All</a></li>
                <?php foreach ($admin_semesters as $sem): ?>
                  <li><a class="dropdown-item admin-faculty-sem-filter" href="#" data-sem="<?= $sem ?>"><?= $sem ?></a></li>
                <?php endforeach; ?>

              </ul>
            </div>
            <div class="card-body">
              <h5 class="card-title">
                Your Evaluation Progress
                <span id="adminFacultyTermLabel" class="text-muted small">| All Years | All Semesters</span>
              </h5>

              <div id="adminFacultyChartContainer">
                <canvas id="adminFacultyProgressChart" style="height: 400px;"></canvas>
              </div>

              <script>
                // Set defaults to 'All'
                let selectedAdminFacultyYear = 'All';
                let selectedAdminFacultySem = 'All';
                let adminFacultyChart = null; // Hold the chart instance

                // 1. Re-usable function to fetch and render the chart
                function fetchAdminFacultyProgressData(year = 'All', semester = 'All') {

                  // Update the title label
                  document.getElementById("adminFacultyTermLabel").textContent =
                    `| ${year === 'All' ? 'All Years' : year} | ${semester === 'All' ? 'All Semesters' : semester}`;

                  // Update fetch URL to include filters
                  fetch(`fetch-admin-faculty-progress.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
                    .then(response => response.json())
                    .then(chartData => {
                      const chartContainer = document.getElementById("adminFacultyChartContainer");

                      // Check for errors or no data
                      if (!chartData || chartData.labels.length === 0) {
                        if (adminFacultyChart) {
                          adminFacultyChart.destroy();
                          adminFacultyChart = null;
                        }
                        // Reset canvas container and show "no data" message
                        chartContainer.innerHTML = '<canvas id="adminFacultyProgressChart" style="height: 400px;"></canvas>';
                        document.getElementById("adminFacultyProgressChart").replaceWith("⚠️ No evaluation data found for the selected filters.");
                        return;
                      }

                      // If we had a "no data" message, clear it and restore canvas
                      if (!document.getElementById("adminFacultyProgressChart")) {
                        chartContainer.innerHTML = '<canvas id="adminFacultyProgressChart" style="height: 400px;"></canvas>';
                      }

                      // 2. Use the robust update/create pattern to fix tooltip bug
                      if (adminFacultyChart) {
                        // --- UPDATE BLOCK ---
                        adminFacultyChart.data.labels = chartData.labels;
                        adminFacultyChart.data.datasets = chartData.datasets;
                        adminFacultyChart.data.meta = chartData.meta; // <-- THE FIX
                        adminFacultyChart.update();
                      } else {
                        // --- CREATE BLOCK ---
                        const ctx = document.getElementById("adminFacultyProgressChart").getContext("2d");
                        adminFacultyChart = new Chart(ctx, { // Assign to global var
                          type: "bar",
                          data: { // Pass data as an object
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

                                    // Read from the chart's current data
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
                      console.error("Error loading faculty progress:", err);
                      document.getElementById("adminFacultyChartContainer").innerHTML = "Error loading chart data.";
                    });
                }

                // 3. Add Event Listeners
                document.addEventListener("DOMContentLoaded", () => {
                  // Initial fetch on page load
                  fetchAdminFacultyProgressData();

                  // Add listener for all year filter links
                  document.querySelectorAll(".admin-faculty-year-filter").forEach(item => {
                    item.addEventListener("click", (e) => {
                      e.preventDefault();
                      selectedAdminFacultyYear = item.getAttribute("data-year");
                      fetchAdminFacultyProgressData(selectedAdminFacultyYear, selectedAdminFacultySem);
                      document.querySelectorAll(".admin-faculty-year-filter").forEach(i => i.classList.remove("active"));
                      item.classList.add("active");
                    });
                  });

                  // Add listener for all semester filter links
                  document.querySelectorAll(".admin-faculty-sem-filter").forEach(item => {
                    item.addEventListener("click", (e) => {
                      e.preventDefault();
                      selectedAdminFacultySem = item.getAttribute("data-sem");
                      fetchAdminFacultyProgressData(selectedAdminFacultyYear, selectedAdminFacultySem);
                      document.querySelectorAll(".admin-faculty-sem-filter").forEach(i => i.classList.remove("active"));
                      item.classList.add("active");
                    });
                  });
                });
              </script>
            </div>
          </div>
        </div>
        <!-- End Admin as Faculty Progress Chart -->

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

  <?php if (isset($access_denied)): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: 'Access Denied',
          text: <?= json_encode($access_denied) ?>,
          confirmButtonText: 'OK',
          confirmButtonColor: '#d33',
          footer: 'Need help? Contact Superadmin'
        }).then(() => {
          window.location.href = "admin-dashboard.php"; // ✅ redirect stays on dashboard
        });
      });
    </script>
  <?php endif; ?>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      new ApexCharts(document.querySelector("#evalTrendChart"), {
        series: [{
          name: 'Student Evaluations',
          data: <?= json_encode($eval_counts) ?>
        }, {
          name: 'Admin Evaluations',
          data: <?= json_encode($admin_eval_counts) ?>
        }],
        chart: {
          height: 350,
          type: 'line',
          toolbar: {
            show: false
          }
        },
        markers: {
          size: 4
        },
        colors: ['#4154f1', '#ff771d'], // blue for students, orange for admins
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
          enabled: true
        },
        stroke: {
          curve: 'smooth',
          width: 2
        },
        xaxis: {
          type: 'datetime',
          categories: <?= json_encode($months) ?>
        },
        tooltip: {
          x: {
            format: 'MMM yyyy'
          }
        }
      }).render();
    });
  </script>

</body>

</html>
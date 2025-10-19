<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a student
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
  header("Location: pages-login.php");
  exit();
}

// Top rated subject
$chartLabels = [];
$chartValues = [];

$faculty_id = $_SESSION['idnumber']; // currently logged-in faculty

$subjectQuery = "
    SELECT subject_title, ROUND(AVG(computed_rating), 2) AS avg_rating
    FROM evaluation
    WHERE faculty_id = ?
    GROUP BY subject_title
    ORDER BY avg_rating DESC
    LIMIT 5
";


$stmt = $conn->prepare($subjectQuery);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

$chartLabels = [];
$chartValues = [];
while ($row = $result->fetch_assoc()) {
  $chartLabels[] = $row['subject_title'];
  $chartValues[] = $row['avg_rating'];
}

// Top Rated Subjects by Section (for this faculty)
$sectionQuery = "
    SELECT student_section, ROUND(AVG(computed_rating), 2) AS avg_rating
    FROM evaluation
    WHERE faculty_id = ?
    GROUP BY student_section
    ORDER BY avg_rating DESC
    LIMIT 5
";
$stmt2 = $conn->prepare($sectionQuery);
$stmt2->bind_param("s", $faculty_id);
$stmt2->execute();
$sectionResult = $stmt2->get_result();

$sectionLabels = [];
$sectionValues = [];

while ($row = $sectionResult->fetch_assoc()) {
  $sectionLabels[] = $row['student_section'];
  $sectionValues[] = $row['avg_rating'];
}


?>

<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

  <style>
    .welcome-box {
      background: linear-gradient(to right, #4CAF50, #0e3f10ff);
      animation: slideFadeIn 1s ease-out;
    }

    @keyframes slideFadeIn {
      0% {
        opacity: 0;
        transform: translateY(-30px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animated {
      animation-duration: 1s;
      animation-fill-mode: both;
    }

    .fadeInDown {
      animation-name: fadeInDown;
    }

    .fadeInUp {
      animation-name: fadeInUp;
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

</head>

<body>

  <?php include 'faculty-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'faculty-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">

      <?php
      $faculty_name = 'Faculty';
      $idnumber = $_SESSION['idnumber'] ?? '';
      // Fetch student name from DB
      $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
      $stmt->bind_param("s", $idnumber);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $faculty_name = $student['first_name'] . ' ' . $student['mid_name'] . ' ' . $student['last_name'];
      }
      ?>

      <div class="col-12">
        <div class="welcome-box p-5 text-center text-white rounded shadow-lg">
          <h1 class="animated fadeInDown">Welcome, <span class="text-warning"><?= htmlspecialchars($faculty_name) ?></span>!</h1>
          <p class="lead animated fadeInUp mt-2">We’re glad to have you here. View your evaluation insights and performance summary below.</p>
        </div>
      </div>

      <!-- Faculty Evaluation Progress -->
      <?php
      // Use a temporary variable to avoid conflicts
      $faculty_id_for_filters = $_SESSION['idnumber'];

      // Get DISTINCT academic years this faculty has taught
      $year_query = $conn->query("
    SELECT DISTINCT academic_year FROM student_subject
    WHERE faculty_id = '{$faculty_id_for_filters}' ORDER BY academic_year DESC
");
      $faculty_years = [];
      while ($row = $year_query->fetch_assoc()) {
        $faculty_years[] = $row['academic_year'];
      }

      // Get DISTINCT semesters this faculty has taught
      $sem_query = $conn->query("
    SELECT DISTINCT semester FROM student_subject
    WHERE faculty_id = '{$faculty_id_for_filters}' ORDER BY semester ASC
");
      $faculty_semesters = [];
      while ($row = $sem_query->fetch_assoc()) {
        $faculty_semesters[] = $row['semester'];
      }
      ?>

      <div class="row mt-4">
        <div class="col-lg-12">
          <div class="card shadow">

            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="max-height: 400px; overflow-y: auto;">

                <li class="dropdown-header">Academic Year</li>
                <li><a class="dropdown-item faculty-year-filter active" href="#" data-year="All">All</a></li>
                <?php foreach ($faculty_years as $year): ?>
                  <li><a class="dropdown-item faculty-year-filter" href="#" data-year="<?= $year ?>"><?= $year ?></a></li>
                <?php endforeach; ?>

                <li>
                  <hr class="dropdown-divider">
                </li>

                <li class="dropdown-header">Semester</li>
                <li><a class="dropdown-item faculty-sem-filter active" href="#" data-sem="All">All</a></li>
                <?php foreach ($faculty_semesters as $sem): ?>
                  <li><a class="dropdown-item faculty-sem-filter" href="#" data-sem="<?= $sem ?>"><?= $sem ?></a></li>
                <?php endforeach; ?>

              </ul>
            </div>
            <div class="card-body">
              <h5 class="card-title">
                Your Evaluation Progress (Per Subject)
                <span id="facultyTermLabel" class="text-muted small">| All Years | All Semesters</span>
              </h5>

              <div id="facultyChartContainer">
                <canvas id="facultyProgressChart" style="height: 400px;"></canvas>
              </div>

              <script>
                // Set defaults to 'All'
                let selectedFacultyYear = 'All';
                let selectedFacultySem = 'All';
                let facultyProgressChart = null; // Hold the chart instance

                // 1. Re-usable function to fetch and render the chart
                function fetchFacultyProgressData(year = 'All', semester = 'All') {

                  // Update the title label
                  document.getElementById("facultyTermLabel").textContent =
                    `| ${year === 'All' ? 'All Years' : year} | ${semester === 'All' ? 'All Semesters' : semester}`;

                  // Update fetch URL to include filters
                  fetch(`fetch-faculty-progress.php?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
                    .then(response => response.json())
                    .then(chartData => {
                      const chartContainer = document.getElementById("facultyChartContainer");

                      // Check for errors or no data
                      if (!chartData || chartData.labels.length === 0) {
                        if (facultyProgressChart) {
                          facultyProgressChart.destroy();
                          facultyProgressChart = null;
                        }
                        // Reset canvas container and show "no data" message
                        chartContainer.innerHTML = '<canvas id="facultyProgressChart" style="height: 400px;"></canvas>';
                        document.getElementById("facultyProgressChart").replaceWith("⚠️ No evaluation data found for the selected filters.");
                        return;
                      }

                      // If we had a "no data" message, clear it and restore canvas
                      if (!document.getElementById("facultyProgressChart")) {
                        chartContainer.innerHTML = '<canvas id="facultyProgressChart" style="height: 400px;"></canvas>';
                      }

                      // 2. Use the robust update/create pattern to fix tooltip bug
                      if (facultyProgressChart) {
                        // --- UPDATE BLOCK ---
                        facultyProgressChart.data.labels = chartData.labels;
                        facultyProgressChart.data.datasets = chartData.datasets;
                        facultyProgressChart.data.meta = chartData.meta; // <-- THE FIX
                        facultyProgressChart.update();
                      } else {
                        // --- CREATE BLOCK ---
                        const ctx = document.getElementById("facultyProgressChart").getContext("2d");
                        facultyProgressChart = new Chart(ctx, { // Assign to global var
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
                      document.getElementById("facultyChartContainer").innerHTML = "Error loading chart data.";
                    });
                }

                // 3. Add Event Listeners
                document.addEventListener("DOMContentLoaded", () => {
                  // Initial fetch on page load
                  fetchFacultyProgressData();

                  // Add listener for all year filter links
                  document.querySelectorAll(".faculty-year-filter").forEach(item => {
                    item.addEventListener("click", (e) => {
                      e.preventDefault();
                      selectedFacultyYear = item.getAttribute("data-year");
                      fetchFacultyProgressData(selectedFacultyYear, selectedFacultySem);
                      document.querySelectorAll(".faculty-year-filter").forEach(i => i.classList.remove("active"));
                      item.classList.add("active");
                    });
                  });

                  // Add listener for all semester filter links
                  document.querySelectorAll(".faculty-sem-filter").forEach(item => {
                    item.addEventListener("click", (e) => {
                      e.preventDefault();
                      selectedFacultySem = item.getAttribute("data-sem");
                      fetchFacultyProgressData(selectedFacultyYear, selectedFacultySem);
                      document.querySelectorAll(".faculty-sem-filter").forEach(i => i.classList.remove("active"));
                      item.classList.add("active");
                    });
                  });
                });
              </script>
            </div>
          </div>
        </div>
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
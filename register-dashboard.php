<?php
session_start();
include 'conn/conn.php'; // DB connection

// Check if the user is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// Fetch user's role
$id_number = $_SESSION['idnumber'];
$user_check_query = "SELECT role FROM registrar WHERE idnumber = ?";
$user_stmt = $conn->prepare($user_check_query);
$user_stmt->bind_param("s", $id_number);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_stmt->close();

// If not registrar, redirect
if (!$user_data || $user_data['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <style>
    .welcome-box {
      background: linear-gradient(to right, #4CAF50, #0e3f10);
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
  <?php include 'register-header.php'; ?>
  <?php include 'register-sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <?php
      $registrar_name = 'Registrar';
      $idnumber = $_SESSION['idnumber'] ?? '';

      // Fetch registrar info
      $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM registrar WHERE idnumber = ?");
      $stmt->bind_param("s", $idnumber);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $reg = $result->fetch_assoc();
        $registrar_name = trim($reg['first_name'] . ' ' . $reg['mid_name'] . ' ' . $reg['last_name']);
      }
      $stmt->close();
      ?>

      <div class="col-12">
        <div class="welcome-box p-5 text-center text-white rounded shadow-lg">
          <h1 class="animated fadeInDown">Welcome, <span class="text-warning"><?= htmlspecialchars($registrar_name) ?></span>!</h1>
          <p class="lead animated fadeInUp mt-2">We’re glad to have you here. View your evaluation insights and performance summary below.</p>
          </p>
        </div>
      </div>

      <?php
      // Check if this registrar is also a faculty member
      $is_faculty = false;
      $stmt_fac = $conn->prepare("SELECT idnumber FROM faculty WHERE idnumber = ?");
      $stmt_fac->bind_param("s", $idnumber);
      $stmt_fac->execute();
      $res_fac = $stmt_fac->get_result();
      if ($res_fac->num_rows > 0) {
        $is_faculty = true;
      }
      $stmt_fac->close();

      if ($is_faculty):
        // Fetch faculty evaluation info
        $faculty_id_for_filters = $idnumber;

        // Academic years
        $year_query = $conn->query("
          SELECT DISTINCT academic_year FROM student_subject
          WHERE faculty_id = '{$faculty_id_for_filters}' ORDER BY academic_year DESC
        ");
        $faculty_years = [];
        while ($row = $year_query->fetch_assoc()) {
          $faculty_years[] = $row['academic_year'];
        }
        $year_query->close();

        // Semesters
        $sem_query = $conn->query("
          SELECT DISTINCT semester FROM student_subject
          WHERE faculty_id = '{$faculty_id_for_filters}' ORDER BY semester ASC
        ");
        $faculty_semesters = [];
        while ($row = $sem_query->fetch_assoc()) {
          $faculty_semesters[] = $row['semester'];
        }
        $sem_query->close();
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
                  Your Evaluation Progress (as Faculty)
                  <span id="facultyTermLabel" class="text-muted small">| All Years | All Semesters</span>
                </h5>

                <div id="facultyChartContainer">
                  <canvas id="facultyProgressChart" style="height: 400px;"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
          let selectedFacultyYear = 'All';
          let selectedFacultySem = 'All';
          let facultyProgressChart = null;

          function fetchFacultyProgressData(year = 'All', semester = 'All') {
            document.getElementById("facultyTermLabel").textContent =
              `| ${year === 'All' ? 'All Years' : year} | ${semester === 'All' ? 'All Semesters' : semester}`;

            const facultyId = <?= json_encode($idnumber) ?>;

            fetch(`fetch-faculty-progress.php?faculty_id=${encodeURIComponent(facultyId)}&year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
              .then(response => response.json())
              .then(chartData => {
                const chartContainer = document.getElementById("facultyChartContainer");

                if (!chartData || !chartData.labels || chartData.labels.length === 0) {
                  if (facultyProgressChart) {
                    facultyProgressChart.destroy();
                    facultyProgressChart = null;
                  }
                  chartContainer.innerHTML = '<p class="text-center text-muted mt-4">⚠️ No evaluation data found for the selected filters.</p>';
                  return;
                }

                if (facultyProgressChart) {
                  facultyProgressChart.data.labels = chartData.labels;
                  facultyProgressChart.data.datasets = chartData.datasets;
                  facultyProgressChart.data.meta = chartData.meta;
                  facultyProgressChart.update();
                } else {
                  const ctx = document.getElementById("facultyProgressChart").getContext("2d");
                  facultyProgressChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                      labels: chartData.labels,
                      datasets: chartData.datasets,
                      meta: chartData.meta
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
                console.error("Error loading faculty progress:", err);
                document.getElementById("facultyChartContainer").innerHTML = "Error loading chart data.";
              });
          }

          document.addEventListener("DOMContentLoaded", () => {
            fetchFacultyProgressData();

            document.querySelectorAll(".faculty-year-filter").forEach(item => {
              item.addEventListener("click", (e) => {
                e.preventDefault();
                selectedFacultyYear = item.getAttribute("data-year");
                fetchFacultyProgressData(selectedFacultyYear, selectedFacultySem);
                document.querySelectorAll(".faculty-year-filter").forEach(i => i.classList.remove("active"));
                item.classList.add("active");
              });
            });

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

      <?php endif; ?>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
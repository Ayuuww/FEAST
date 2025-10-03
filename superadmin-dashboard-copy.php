<?php
session_start();
include 'conn/conn.php'; // DB connection

// Check if superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// === Counts ===
$totalfaculty = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM faculty WHERE role='faculty' AND status='active'"))['total'] ?? 0;
$totalstudent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM student WHERE role='student'"))['total'] ?? 0;
$totaladmin   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM admin"))['total'] ?? 0;
$totalsuperadmin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM superadmin"))['total'] ?? 0;
$totalsubject = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM subject"))['total'] ?? 0;

$studentEvalCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM evaluation"))['total'] ?? 0;
$adminEvalCount   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM admin_evaluation"))['total'] ?? 0;
$totalEvaluations = $studentEvalCount + $adminEvalCount;

// === Evaluation Progress Query ===
$sql = "
  SELECT 
    s.department,
    ss.academic_year,
    ss.semester,
    COUNT(DISTINCT ss.student_id, ss.subject_code, ss.faculty_id) AS expected_evals,
    COUNT(DISTINCT e.student_id, e.subject_code, e.faculty_id) AS completed_evals,
    ROUND(
      (COUNT(DISTINCT e.student_id, e.subject_code, e.faculty_id) / 
       COUNT(DISTINCT ss.student_id, ss.subject_code, ss.faculty_id)) * 100, 2
    ) AS progress_percent
  FROM student_subject ss
  INNER JOIN subject s 
    ON ss.subject_code = s.code AND ss.faculty_id = s.faculty_id
  LEFT JOIN evaluation e 
    ON ss.student_id = e.student_id
   AND ss.subject_code = e.subject_code
   AND ss.faculty_id = e.faculty_id
   AND ss.academic_year = e.academic_year
   AND ss.semester = e.semester
  GROUP BY s.department, ss.academic_year, ss.semester
  ORDER BY ss.academic_year ASC, ss.semester ASC
";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[$row['department']][] = [
    'year'     => $row['academic_year'],
    'sem'      => $row['semester'],
    'expected' => (int)$row['expected_evals'],
    'completed' => (int)$row['completed_evals'],
    'percent'  => (float)$row['progress_percent']
  ];
}

echo "<script>var evaluationProgressData = " . json_encode($data) . ";</script>";
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
    </div>

    <section class="section dashboard">
      <div class="row">

        <!-- ====== Left Side Stats ====== -->
        <div class="col-xxl-9">
          <div class="row">

            <!-- Row 1 -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">
                <div class="card-body">
                  <h5 class="card-title">Total | Faculty</h5>
                  <h6><?= $totalfaculty ?></h6>
                </div>
              </div>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">
                <div class="card-body">
                  <h5 class="card-title">Total | Students</h5>
                  <h6><?= $totalstudent ?></h6>
                </div>
              </div>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">
                <div class="card-body">
                  <h5 class="card-title">Total | Admins</h5>
                  <h6><?= $totaladmin ?></h6>
                </div>
              </div>
            </div>

            <!-- Row 2 -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">
                <div class="card-body">
                  <h5 class="card-title">Total | Super Admins</h5>
                  <h6><?= $totalsuperadmin ?></h6>
                </div>
              </div>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">
                <div class="card-body">
                  <h5 class="card-title">Total | Subjects</h5>
                  <h6><?= $totalsubject ?></h6>
                </div>
              </div>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card">
                <div class="card-body">
                  <h5 class="card-title">Total | Evaluations</h5>
                  <h6><?= $totalEvaluations ?></h6>
                </div>
              </div>
            </div>

          </div>
        </div><!-- End Left Side -->


        <!-- ====== Right Side Recent Activity ====== -->
        <div class="col-xxl-3">
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
            <div class="card-body" style="max-height: 800px; overflow-y: auto;" id="activityContainer">
              <h5 class="card-title">Recent Activity <span id="filter-label">| All</span></h5>
              <div id="activity-list"></div>
              <div id="loadingIndicator" class="text-center my-2" style="display: none;">
                <div class="spinner-border text-primary" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity Script -->
        <script>
          let offset = 0;
          const limit = 10;
          let loading = false;
          let filter = 'all';
          let role = 'all';

          const activityList = document.getElementById("activity-list");
          const loadingIndicator = document.getElementById("loadingIndicator");
          const activityContainer = document.getElementById("activityContainer");

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
        <!-- end of recent -->
        <!-- End Right Side -->

      </div>
      <!-- ====== Charts Section ====== -->
      <div class="card mt-4">
        <div class="card-body">
          <h5 class="card-title">Evaluation Completion (Completed vs Expected)</h5>
          <canvas id="evaluationCountsChart1"></canvas>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-body">
          <h5 class="card-title">Evaluation Completion (Completed vs Remaining)</h5>
          <canvas id="evaluationCountsChart2"></canvas>
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
  <script src="chart/chart.js"></script>

  <!-- ====== Chart Scripts ====== -->


  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const data = evaluationProgressData;

      // Collect unique semester-year labels
      const labels = [];
      Object.values(data).forEach(deptData => {
        deptData.forEach(item => {
          const label = `${item.year} • ${item.sem}`;
          if (!labels.includes(label)) labels.push(label);
        });
      });
      labels.sort();

      // === Chart 1: Completed vs Expected ===
      const datasets1 = [];
      Object.keys(data).forEach((dept, index) => {
        datasets1.push({
          label: dept + " (Completed)",
          data: labels.map(label => {
            const found = data[dept].find(item => `${item.year} • ${item.sem}` === label);
            return found ? found.completed : 0;
          }),
          backgroundColor: `hsl(${index * 60},70%,50%)`
        });
        datasets1.push({
          label: dept + " (Expected)",
          data: labels.map(label => {
            const found = data[dept].find(item => `${item.year} • ${item.sem}` === label);
            return found ? found.expected : 0;
          }),
          backgroundColor: `hsl(${index * 60},70%,80%)`
        });
      });

      new Chart(document.getElementById("evaluationCountsChart1"), {
        type: "bar",
        data: {
          labels: labels,
          datasets: datasets1
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: "Completed vs Expected"
            }
          }
        }
      });

      // === Chart 2: Completed vs Remaining (Stacked) ===
      const datasets2 = [];
      Object.keys(data).forEach((dept, index) => {
        datasets2.push({
          label: dept + " (Completed)",
          data: labels.map(label => {
            const found = data[dept].find(item => `${item.year} • ${item.sem}` === label);
            return found ? found.completed : 0;
          }),
          backgroundColor: `hsl(${index * 60},70%,50%)`,
          stack: "Stack 0"
        });
        datasets2.push({
          label: dept + " (Remaining)",
          data: labels.map(label => {
            const found = data[dept].find(item => `${item.year} • ${item.sem}` === label);
            return found ? (found.expected - found.completed) : 0;
          }),
          backgroundColor: `hsl(${index * 60},70%,80%)`,
          stack: "Stack 0"
        });
      });

      new Chart(document.getElementById("evaluationCountsChart2"), {
        type: "bar",
        data: {
          labels: labels,
          datasets: datasets2
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: "Completed vs Remaining"
            }
          },
          scales: {
            x: {
              stacked: true
            },
            y: {
              stacked: true,
              beginAtZero: true
            }
          }
        }
      });
    });
  </script>

</body>

</html>
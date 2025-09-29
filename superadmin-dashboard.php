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
  <script src="chart/chart.js"></script>

</body>

</html>
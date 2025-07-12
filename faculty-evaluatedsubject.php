<?php
session_start();
include 'conn/conn.php';// Connection to the database

// Check if the user is logged in and is a student
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
    header("Location: pages-login.php");
    exit();
}

// Fetching the faculty subjects evaluated
$faculty_id = $_SESSION['idnumber'];

$query = "SELECT 
            e.subject_code,
            s.title AS subject_title,
            e.academic_year,
            e.semester,
            COUNT(e.id) AS total_evaluations
          FROM evaluation e
          JOIN subject s ON e.subject_code = s.code AND e.faculty_id = s.faculty_id
          WHERE e.faculty_id = ?
          GROUP BY e.subject_code, s.title, e.academic_year, e.semester
          ORDER BY e.academic_year DESC, e.semester DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();





?>

<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Subjects </title>

  <?php include 'header.php' ?>
  

  </head>
  <body>
    <?php include 'faculty-header.php'?>
    
    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-dashboard.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li><!-- End Dashboard Nav -->

        <!-- Evaluate Nav -->
        <!-- <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="faculty-peer-evaluate.php" >
                <i class="bi bi-circle"></i><span>Form</span>
              </a>
            </li>
            <li>
              <a href="faculty-peer-evaluatedpeer.php" >
                <i class="bi bi-circle"></i><span>Evaluated Peer</span>
              </a>
            </li>
          </ul>
        </li>End Evaluate Nav -->

        <li class="nav-item">
          <a class="nav-link collapse" href="faculty-evaluatedsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Subject</span>
          </a>
        </li><!-- End Profile Nav -->

        <!-- <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-records.php">
            <i class="ri-record-circle-fill"></i>
            <span>Records</span>
          </a>
        </li>End Records Nav -->
        
        <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-user-profile.php">
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
        <h1>Subjects</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Subject</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section dashboard">
        <div class="row">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Evaluated Subjects You Handle</h5>

                    <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Title</th>
                            <th>Semester</th>
                            <th>School Year</th>
                            <th>Total Evaluations</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['subject_code']) ?></td>
                                <td><?= htmlspecialchars($row['subject_title']) ?></td>
                                <td><?= htmlspecialchars($row['semester']) ?></td>
                                <td><?= htmlspecialchars($row['academic_year']) ?></td>
                                <td><?= $row['total_evaluations'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                    <td colspan="6" class="text-center">No evaluations have been submitted for your subjects yet.</td>
                                    </tr>
                                <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

        </div>
      </section>

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

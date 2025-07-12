<?php
session_start();
include 'conn/conn.php';

// Verify login and role
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
    header("Location: pages-login.php");
    exit();
}

$faculty_id = $_SESSION['idnumber'];

// Fetch evaluations from the database
$filter_year = $_GET['academic_year'] ?? '';

if (!empty($filter_year)) {
    $stmt = $conn->prepare("SELECT subject_code, subject_title, student_section, academic_year, semester, created_at,
        COUNT(*) AS student_count,
        AVG(total_score) AS avg_total_score,
        AVG(computed_rating) AS avg_computed_rating,
        GROUP_CONCAT(comment SEPARATOR ' | ') AS comments
        FROM evaluation
        WHERE faculty_id = ? AND academic_year = ?
        GROUP BY subject_code, student_section, semester, academic_year
        ORDER BY created_at DESC");
    $stmt->bind_param("ss", $faculty_id, $filter_year);
} else {
    $stmt = $conn->prepare("SELECT subject_code, subject_title, student_section, academic_year, semester, created_at,
        COUNT(*) AS student_count,
        AVG(total_score) AS avg_total_score,
        AVG(computed_rating) AS avg_computed_rating,
        GROUP_CONCAT(comment SEPARATOR ' | ') AS comments
        FROM evaluation
        WHERE faculty_id = ?
        GROUP BY subject_code, student_section, semester, academic_year
        ORDER BY created_at DESC");
    $stmt->bind_param("s", $faculty_id);
}


$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Faculty Evaluation Records</title>
  <?php include 'header.php'; ?>
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
          <a class="nav-link collapsed" href="faculty-evaluatedsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Subject</span>
          </a>
        </li><!-- End Profile Nav -->

        <li class="nav-item">
          <a class="nav-link collapse" href="faculty-records.php">
            <i class="ri-record-circle-fill"></i>
            <span>Records</span>
          </a>
        </li><!-- End Profile Nav -->

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
        <h1>My Evaluation</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Evaluation Records</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section">
        <div class="card">
          <div class="card-body table-responsive">
            <h5 class="card-title">
              Evaluation Summary <?= $filter_year ? 'for A.Y. ' . htmlspecialchars($filter_year) : '' ?>
            </h5>


            <?php if ($result->num_rows > 0): ?>
              <form method="GET" class="mb-3">
                <div class="row">
                  <div class="col-md-4">
                    <select name="academic_year" class="form-select" onchange="this.form.submit()">
                      <option value="">-- Select Academic Year --</option>
                      <?php
                      // Fetch distinct academic years from the evaluation table
                      $year_result = $conn->query("SELECT DISTINCT academic_year FROM evaluation WHERE faculty_id = '$faculty_id' ORDER BY academic_year DESC");
                      while ($year_row = $year_result->fetch_assoc()):
                        $selected = (isset($_GET['academic_year']) && $_GET['academic_year'] === $year_row['academic_year']) ? 'selected' : '';
                      ?>
                        <option value="<?= $year_row['academic_year'] ?>" <?= $selected ?>><?= $year_row['academic_year'] ?></option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                </div>
              </form>

              <table class="table table-bordered datatable">
                <thead>
                  <tr>
                    <th>Date Evaluated</th>
                    <th>Subject Code</th>
                    <th>Subject Title</th>
                    <th>Student Section</th>
                    <th>Academic Year</th>
                    <th>Semester</th>
                    <th>Total Score</th>
                    <th>Rating (%)</th>
                    <th>Comment</th>
                    <th># of Students</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= date("F j, Y", strtotime($row['created_at'])) ?></td>
                      <td><?= htmlspecialchars($row['subject_code']) ?></td>
                      <td><?= htmlspecialchars($row['subject_title']) ?></td>
                      <td><?= htmlspecialchars($row['student_section']) ?></td>
                      <td><?= htmlspecialchars($row['academic_year']) ?></td>
                      <td><?= htmlspecialchars($row['semester']) ?></td>
                      <td><?= number_format($row['avg_total_score'], 2) ?></td>
                      <td><?= number_format($row['avg_computed_rating'], 2) ?>%</td>
                      <td><?= htmlspecialchars($row['comments']) ?></td>
                      <td><?= $row['student_count'] ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            <?php else: ?>
              <div class="alert alert-info">No evaluations found for you yet.</div>
            <?php endif; ?>

          </div>
        </div>
      </section>
    </main>

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

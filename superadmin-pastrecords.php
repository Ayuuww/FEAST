<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

$faculty_id = $_GET['faculty_id'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';
$subject_code = $_GET['subject_code'] ?? ''; // moved up

$faculty_list = $conn->query("SELECT idnumber, first_name, mid_name, last_name FROM faculty WHERE status = 'active' ORDER BY last_name ASC");

$params = [];
$sql = "SELECT subject_code, subject_title, student_section, academic_year, semester, created_at,
                COUNT(*) AS student_count,
                AVG(total_score) AS avg_total_score,
                AVG(computed_rating) AS avg_computed_rating,
                GROUP_CONCAT(comment SEPARATOR ' | ') AS comments
          FROM evaluation
          WHERE 1=1";

if ($faculty_id) {
  $sql .= " AND faculty_id = ?";
  $params[] = $faculty_id;
}
if ($academic_year) {
  $sql .= " AND academic_year = ?";
  $params[] = $academic_year;
}
if ($subject_code) {
  $sql .= " AND subject_code = ?";
  $params[] = $subject_code;
}

$sql .= " GROUP BY subject_code, student_section, semester, academic_year ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}



// Get subject list based on selected faculty (optional academic year filter)
$subject_list = null;
if ($faculty_id) {
  $subject_sql = "SELECT DISTINCT subject_code, subject_title 
                    FROM evaluation 
                    WHERE faculty_id = ?";
  $params = [$faculty_id];
  $types = "s";

  if ($academic_year) {
    $subject_sql .= " AND academic_year = ?";
    $params[] = $academic_year;
    $types .= "s";
  }

  $subject_stmt = $conn->prepare($subject_sql);
  $subject_stmt->bind_param($types, ...$params);
  $subject_stmt->execute();
  $subject_list = $subject_stmt->get_result();
}

$stmt->execute();
$result = $stmt->get_result();
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
      <h1>Past Faculty Evaluation Records</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Past Records</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="card">
        <div class="card-body table-responsive">
          <h5 class="card-title">Filter Evaluations</h5>

          <form method="GET" class="row g-3 mb-4">
            <div class="col-md-12">
              <a href="superadmin-pastrecords.php" class="btn btn-secondary btn-sm">Clear Filters</a>
            </div>

            <div class="col-md-4">
              <select name="faculty_id" class="form-select" required onchange="this.form.submit()">
                <option value="">-- Select Faculty --</option>
                <?php mysqli_data_seek($faculty_list, 0);
                while ($faculty = $faculty_list->fetch_assoc()): ?>
                  <?php
                  $full_name = $faculty['last_name'] . ', ' . $faculty['first_name'] . ' ' . $faculty['mid_name'];
                  $selected = $faculty_id == $faculty['idnumber'] ? 'selected' : '';
                  ?>
                  <option value="<?= $faculty['idnumber'] ?>" <?= $selected ?>><?= $full_name ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-4">
              <select name="academic_year" class="form-select" onchange="this.form.submit()">
                <option value="">-- Select Academic Year --</option>
                <?php
                $years = $conn->query("SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
                while ($yr = $years->fetch_assoc()):
                  $sel = ($academic_year == $yr['academic_year']) ? 'selected' : '';
                ?>
                  <option value="<?= $yr['academic_year'] ?>" <?= $sel ?>><?= $yr['academic_year'] ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <?php if ($faculty_id && $subject_list && $subject_list->num_rows > 0): ?>
              <div class="col-md-4">
                <select name="subject_code" class="form-select" onchange="this.form.submit()">
                  <option value="">-- Select Subject --</option>
                  <?php while ($sub = $subject_list->fetch_assoc()):
                    $selected = ($subject_code == $sub['subject_code']) ? 'selected' : '';
                  ?>
                    <option value="<?= $sub['subject_code'] ?>" <?= $selected ?>>
                      <?= $sub['subject_code'] . ' - ' . $sub['subject_title'] ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
            <?php endif; ?>
          </form>



          <?php if ($faculty_id && $result->num_rows > 0): ?>

            <a href="pastrecords-print.php?faculty_id=<?= $faculty_id ?>&academic_year=<?= $academic_year ?>&subject_code=<?= $subject_code ?>" target="_blank" class="btn btn-outline-secondary mb-3">
              <i class="bi bi-printer"></i> Print Evaluation Report
            </a>

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
                  <th>Comments</th>
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
          <?php elseif ($faculty_id): ?>
            <div class="alert alert-warning">No records found for this faculty<?= $academic_year ? " in A.Y. $academic_year" : "" ?>.</div>
          <?php else: ?>
            <div class="alert alert-info">Please select a faculty member to view past evaluations.</div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

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
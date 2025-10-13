<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

$superadmin_id = $_SESSION['idnumber'];
$academic_year = $_GET['academic_year'] ?? '';
$semester      = $_GET['semester'] ?? '';
$subject_code  = $_GET['subject_code'] ?? '';

// --- Get subject list for this superadmin’s evaluations ---
$subject_list = null;
$sqlSubjects = "SELECT DISTINCT subject_code, subject_title 
                FROM evaluation 
                WHERE faculty_id = ?";
$paramsSub = [$superadmin_id];
$typesSub = "s";

if ($academic_year) {
  $sqlSubjects .= " AND academic_year = ?";
  $paramsSub[] = $academic_year;
  $typesSub .= "s";
}
if ($semester) {
  $sqlSubjects .= " AND semester = ?";
  $paramsSub[] = $semester;
  $typesSub .= "s";
}
if ($subject_code) {
  $sqlSubjects .= " AND subject_code = ?";
  $paramsSub[] = $subject_code;
  $typesSub .= "s";
}

$sqlSubjects .= " ORDER BY subject_title ASC";

$stmtSub = $conn->prepare($sqlSubjects);
if ($stmtSub) {
  $stmtSub->bind_param($typesSub, ...$paramsSub);
  $stmtSub->execute();
  $subject_list = $stmtSub->get_result();
  $stmtSub->close();
}

// --- Main query: evaluations of this superadmin (as faculty_id) ---
$params = [$superadmin_id];
$types = "s";
$sql = "SELECT subject_code, subject_title, student_section, academic_year, semester, created_at,
               COUNT(*) AS student_count,
               AVG(total_score) AS avg_total_score,
               AVG(computed_rating) AS avg_computed_rating,
               GROUP_CONCAT(comment SEPARATOR ' | ') AS comments
        FROM evaluation
        WHERE faculty_id = ?";

if ($academic_year) {
  $sql .= " AND academic_year = ?";
  $params[] = $academic_year;
  $types .= "s";
}
if ($semester) {
  $sql .= " AND semester = ?";
  $params[] = $semester;
  $types .= "s";
}
if ($subject_code) {
  $sql .= " AND subject_code = ?";
  $params[] = $subject_code;
  $types .= "s";
}

$sql .= " GROUP BY subject_code, student_section, semester, academic_year
          ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($stmt) {
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
} else {
  $result = false;
}

// --- Academic years dropdown ---
$years_query = $conn->prepare("SELECT DISTINCT academic_year FROM evaluation WHERE faculty_id = ? ORDER BY academic_year DESC");
$years_data = [];
if ($years_query) {
  $years_query->bind_param("s", $superadmin_id);
  $years_query->execute();
  $years_result = $years_query->get_result();
  while ($row = $years_result->fetch_assoc()) {
    $years_data[] = $row;
  }
  $years_query->close();
}

// --- Semesters dropdown ---
$semesters_data = [];
$semester_sql = "SELECT DISTINCT semester FROM evaluation WHERE faculty_id = ?";
$semester_types = "s";
$semester_params = [$superadmin_id];

if ($academic_year) {
  $semester_sql .= " AND academic_year = ?";
  $semester_types .= "s";
  $semester_params[] = $academic_year;
}

$semester_sql .= " ORDER BY 
    CASE semester 
        WHEN '1st Semester' THEN 1 
        WHEN '2nd Semester' THEN 2 
        WHEN 'Summer' THEN 3 
        ELSE 4 
    END";

$semesters_query = $conn->prepare($semester_sql);
if ($semesters_query) {
  $semesters_query->bind_param($semester_types, ...$semester_params);
  $semesters_query->execute();
  $semesters_result = $semesters_query->get_result();
  while ($row = $semesters_result->fetch_assoc()) {
    if (!empty(trim($row['semester']))) {
      $semesters_data[] = $row;
    }
  }
  $semesters_query->close();
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
      <h1>My Past Evaluation Records</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Past Records</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card">
        <div class="card-body table-responsive">
          <h5 class="card-title">Filter Evaluations</h5>

          <form method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-auto">
              <a href="superadmin-pastrecords.php" class="btn btn-secondary btn-sm">Clear Filters</a>
            </div>

            <div class="col-md-3">
              <label for="academic_year" class="form-label">Academic Year</label>
              <select name="academic_year" id="academic_year" class="form-select" onchange="this.form.submit()">
                <option value="">-- All Academic Years --</option>
                <?php foreach ($years_data as $yr): ?>
                  <option value="<?= htmlspecialchars($yr['academic_year']) ?>" <?= ($academic_year == $yr['academic_year']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($yr['academic_year']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3">
              <label for="semester" class="form-label">Semester</label>
              <select name="semester" id="semester" class="form-select" onchange="this.form.submit()">
                <option value="">-- All Semesters --</option>
                <?php foreach ($semesters_data as $sem_opt): ?>
                  <option value="<?= htmlspecialchars($sem_opt['semester']) ?>" <?= ($semester == $sem_opt['semester']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sem_opt['semester']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <?php if ($subject_list && $subject_list->num_rows): ?>
              <div class="col-md-4">
                <label for="subject_code" class="form-label">Subject</label>
                <select name="subject_code" id="subject_code" class="form-select" onchange="this.form.submit()">
                  <option value="">-- All Subjects --</option>
                  <?php foreach ($subject_list as $sub): ?>
                    <option value="<?= htmlspecialchars($sub['subject_code']) ?>" <?= ($subject_code == $sub['subject_code']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($sub['subject_code']) . " - " . htmlspecialchars($sub['subject_title']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endif; ?>
          </form>

          <?php if ($result && $result->num_rows): ?>
            <a href="superadmin-pastrecords-print.php?academic_year=<?= urlencode($academic_year) ?>&semester=<?= urlencode($semester) ?>&subject_code=<?= urlencode($subject_code) ?>" target="_blank" class="btn btn-outline-secondary mb-3">
              <i class="bi bi-printer"></i> Print My Evaluations
            </a>

            <table class="table table-bordered datatable">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Subject</th>
                  <th>Title</th>
                  <th>Section</th>
                  <th>A.Y.</th>
                  <th>Semester</th>
                  <th>Avg Score</th>
                  <th>Rating (%)</th>
                  <th>Comments</th>
                  <th>No. Students</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 0;
                while ($row = $result->fetch_assoc()): $i++; ?>
                  <?php $modalId = "commentModal" . $i; ?>
                  <tr>
                    <td><?= date("F j, Y", strtotime($row['created_at'])) ?></td>
                    <td><?= htmlspecialchars($row['subject_code']) ?></td>
                    <td><?= htmlspecialchars($row['subject_title']) ?></td>
                    <td><?= htmlspecialchars($row['student_section']) ?></td>
                    <td><?= htmlspecialchars($row['academic_year']) ?></td>
                    <td><?= htmlspecialchars($row['semester']) ?></td>
                    <td><?= number_format($row['avg_total_score'], 2) ?></td>
                    <td><?= number_format($row['avg_computed_rating'], 2) ?>%</td>
                    <td>
                      <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
                        <i class="bi bi-chat-dots"></i> View
                      </button>

                      <div class="modal fade" id="<?= $modalId ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-scrollable">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Comments</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <?php foreach (explode(' | ', $row['comments']) as $c): ?>
                                <?php if (trim($c) !== ''): ?>
                                  <div class="mb-2">• <?= htmlspecialchars($c) ?></div>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td><?= $row['student_count'] ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="alert alert-info">
              You have no past evaluation records
              <?php
              $f = [];
              if ($academic_year) $f[] = "for A.Y. " . htmlspecialchars($academic_year);
              if ($semester) $f[] = "in " . htmlspecialchars($semester);
              if ($subject_code) $f[] = "on subject " . htmlspecialchars($subject_code);
              echo implode(" ", $f) . ".";
              ?>
            </div>
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
<?php
session_start();
include 'conn/conn.php'; // DB connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get the active academic year and semester from evaluation_settings
$periodQuery = "SELECT academic_year, semester 
                FROM evaluation_settings 
                ORDER BY updated_at DESC LIMIT 1";
$periodResult = $conn->query($periodQuery);
$active = $periodResult->fetch_assoc();
$current_year = $active['academic_year'];
$current_sem = $active['semester'];

// Selected filters (defaults to active period)
$selected_year = isset($_GET['year']) ? $_GET['year'] : $current_year;
$selected_sem  = isset($_GET['sem']) ? $_GET['sem'] : $current_sem;

// Fetch distinct academic years & semesters for dropdowns
$years = $conn->query("SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
$sems  = $conn->query("SELECT DISTINCT semester FROM evaluation ORDER BY semester DESC");

// Fetch evaluated subjects for selected period
// Fetch all subjects handled by the faculty, with evaluation stats if available
$query = "
  SELECT 
    ss.subject_code,
    subj.subject_title,
    ss.academic_year,
    ss.semester,
    COUNT(DISTINCT e.id) AS evaluated_count,
    COUNT(DISTINCT ss.student_id) AS enrolled_count,
    AVG(e.total_score) AS avg_score,
    AVG(e.computed_rating) AS avg_rating,
    GROUP_CONCAT(DISTINCT e.comment SEPARATOR '||') AS all_comments
  FROM student_subject ss
  JOIN (
      SELECT DISTINCT subject_code, subject_title 
      FROM evaluation
  ) subj ON subj.subject_code = ss.subject_code
  LEFT JOIN evaluation e 
    ON e.subject_code = ss.subject_code
   AND e.academic_year = ss.academic_year
   AND e.semester = ss.semester
   AND e.faculty_id = ss.faculty_id
  WHERE ss.faculty_id = ?
    AND ss.academic_year = ?
    AND ss.semester = ?
  GROUP BY ss.subject_code, subj.subject_title, ss.academic_year, ss.semester
  ORDER BY ss.academic_year DESC, ss.semester DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $admin_id, $selected_year, $selected_sem);
$stmt->execute();
$result = $stmt->get_result();

// ✅ Count evaluations per subject
$countQuery = "SELECT subject_code, COUNT(*) as total 
               FROM evaluation 
               WHERE faculty_id = ? 
                 AND academic_year = ? 
                 AND semester = ?
               GROUP BY subject_code";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("sss", $admin_id, $selected_year, $selected_sem);
$countStmt->execute();
$countResult = $countStmt->get_result();

$subjectCounts = [];
while ($row = $countResult->fetch_assoc()) {
  $subjectCounts[$row['subject_code']] = $row['total'];
}

// ✅ Count enrolled students per subject
$enrolledQuery = "SELECT subject_code, COUNT(*) as total 
                  FROM student_subject 
                  WHERE faculty_id = ? 
                    AND academic_year = ? 
                    AND semester = ?
                  GROUP BY subject_code";
$enrolledStmt = $conn->prepare($enrolledQuery);
$enrolledStmt->bind_param("sss", $admin_id, $selected_year, $selected_sem);
$enrolledStmt->execute();
$enrolledResult = $enrolledStmt->get_result();

$enrolledCounts = [];
while ($row = $enrolledResult->fetch_assoc()) {
  $enrolledCounts[$row['subject_code']] = $row['total'];
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
      <h1>Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Subjects</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Evaluated Subjects You Handle</h5>

            <!-- Filter Form -->
            <form method="get" class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="year" class="form-label">Academic Year</label>
                <select class="form-select" name="year" id="year">
                  <?php while ($y = $years->fetch_assoc()): ?>
                    <option value="<?= $y['academic_year'] ?>" <?= $selected_year == $y['academic_year'] ? 'selected' : '' ?>>
                      <?= $y['academic_year'] ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-md-4">
                <label for="sem" class="form-label">Semester</label>
                <select class="form-select" name="sem" id="sem">
                  <?php while ($s = $sems->fetch_assoc()): ?>
                    <option value="<?= $s['semester'] ?>" <?= $selected_sem == $s['semester'] ? 'selected' : '' ?>>
                      <?= $s['semester'] ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">Filter</button>
              </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
              <table class="table table-bordered table-striped datatable">
                <thead>
                  <tr>
                    <th>Subject Code</th>
                    <th>Title</th>
                    <th>Total Score</th>
                    <th>Computed Rating</th>
                    <th>Comments</th>
                    <th>Semester</th>
                    <th>School Year</th>
                    <th>Total Evaluations</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result->num_rows > 0): ?>
                    <?php $index = 0; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <?php
                      $modalId = "commentsModal" . $index;
                      $comments = explode('||', $row['all_comments']);
                      ?>
                      <tr>
                        <td><?= htmlspecialchars($row['subject_code']) ?></td>
                        <td><?= htmlspecialchars($row['subject_title']) ?></td>
                        <td><?= number_format($row['avg_score'], 2) ?></td>
                        <td><?= number_format($row['avg_rating'], 2) ?>%</td>
                        <td>
                          <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
                            <i class="bi bi-chat-dots"></i> View
                          </button>
                          <!-- Modal -->
                          <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Comments for <?= htmlspecialchars($row['subject_title']) ?></h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                  <?php
                                  $hasComment = false;
                                  foreach ($comments as $comment) {
                                    $clean = trim($comment);
                                    if ($clean !== '') {
                                      $hasComment = true;
                                      echo "<div class='mb-2'>• " . htmlspecialchars($clean) . "</div>";
                                    }
                                  }
                                  if (!$hasComment) {
                                    echo "<div class='text-muted'>No comments available.</div>";
                                  }
                                  ?>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </td>
                        <td><?= htmlspecialchars($row['semester']) ?></td>
                        <td><?= htmlspecialchars($row['academic_year']) ?></td>
                        <td>
                          <?php
                          $evaluated = $row['evaluated_count'] ?? 0;
                          $enrolled  = $row['enrolled_count'] ?? 0;
                          echo "$evaluated / $enrolled";
                          ?>
                        </td>
                      </tr>
                      <?php $index++; ?>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center">No evaluations found for this semester/year.</td>
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
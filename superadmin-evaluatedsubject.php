<?php
session_start();
include 'conn/conn.php'; // DB connection

// Check if the user is logged in
if (!isset($_SESSION['idnumber'])) {
  header("Location: pages-login.php");
  exit();
}

// Fetch user's role and faculty status
$id_number = $_SESSION['idnumber'];
$user_check_query = "SELECT role, faculty FROM superadmin WHERE idnumber = ?";
$user_stmt = $conn->prepare($user_check_query);
$user_stmt->bind_param("s", $id_number);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_stmt->close();

// If not superadmin, redirect
if (!$user_data || $user_data['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

$is_faculty = ($user_data['faculty'] === 'Yes');
$result = null;

// ✅ Get active academic year & semester
$periodQuery = "SELECT academic_year, semester FROM evaluation_settings ORDER BY updated_at DESC LIMIT 1";
$periodResult = $conn->query($periodQuery);
$active = $periodResult->fetch_assoc();
$current_year = $active['academic_year'];
$current_sem  = $active['semester'];

// ✅ Selected filters (defaults to active period)
$selected_year = isset($_GET['year']) ? $_GET['year'] : $current_year;
$selected_sem  = isset($_GET['sem']) ? $_GET['sem'] : $current_sem;

// ✅ Fetch distinct academic years & semesters for dropdowns
$years = $conn->query("SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
$sems  = $conn->query("SELECT DISTINCT semester FROM evaluation ORDER BY semester DESC");

// ✅ Only run query if faculty
if ($is_faculty) {
  $faculty_id = $_SESSION['idnumber'];

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
        GROUP_CONCAT(e.comment SEPARATOR '||') AS all_comments
      FROM student_subject ss
      LEFT JOIN (
          SELECT subject_code, subject_title 
          FROM evaluation 
          GROUP BY subject_code, subject_title
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
  $stmt->bind_param("sss", $faculty_id, $selected_year, $selected_sem);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
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
      <h1>Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Subject</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row">

        <?php if ($is_faculty): ?>
          <!-- Filter -->
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
          <!-- End Filter -->

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Evaluated Subjects You Handle</h5>
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
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
                    <?php if ($result && $result->num_rows > 0): ?>
                      <?php $index = 0; ?>
                      <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['subject_code']) ?></td>
                          <td><?= htmlspecialchars($row['subject_title'] ?? 'N/A') ?></td>
                          <td><?= $row['avg_score'] ? number_format($row['avg_score'], 2) : '0.00' ?></td>
                          <td><?= $row['avg_rating'] ? number_format($row['avg_rating'], 2) . '%' : '0.00%' ?></td>
                          <td>
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#commentModal<?= $index ?>">
                              <i class="bi bi-chat-dots"></i> View
                            </button>

                            <div class="modal fade" id="commentModal<?= $index ?>" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Comments for <?= htmlspecialchars($row['subject_code']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body">
                                    <?php
                                    $comments = isset($row['all_comments']) ? explode('||', $row['all_comments']) : [];
                                    $hasComment = false;
                                    foreach ($comments as $c) {
                                      $c = trim($c);
                                      if ($c !== '') {
                                        $hasComment = true;
                                        echo "<div class='mb-2'>• " . htmlspecialchars($c) . "</div>";
                                      }
                                    }
                                    if (!$hasComment) echo "<p class='text-muted'>No comments available.</p>";
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
                            <?= ($row['evaluated_count'] ?? 0) . ' / ' . ($row['enrolled_count'] ?? 0) ?>
                          </td>
                        </tr>
                        <?php $index++; ?>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="8" class="text-center">No subjects or evaluations found for this semester/year.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Subject Evaluations</h5>
              <p class="text-center text-muted">You do not have a faculty account. Only faculty members can view subject evaluations.</p>
            </div>
          </div>
        <?php endif; ?>

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
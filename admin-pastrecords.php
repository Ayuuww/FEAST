<?php
session_start();
include 'conn/conn.php';
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber']; // This admin's ID is used as faculty_id
$academic_year = $_GET['academic_year'] ?? '';
$semester      = $_GET['semester'] ?? ''; // NEW: Get selected semester
$subject_code  = $_GET['subject_code'] ?? '';

// --- Get list of evaluated subjects for this admin (acting as faculty) ---
$subject_list = null;
$sqlSubjects = "SELECT DISTINCT subject_code, subject_title
                FROM evaluation
                WHERE faculty_id = ?";
$paramsSub = [$admin_id];
$typesSub = "s";

if ($academic_year) {
  $sqlSubjects .= " AND academic_year = ?";
  $paramsSub[] = $academic_year;
  $typesSub .= "s";
}
// NEW: Add semester filter to subject list query
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
if ($stmtSub) { // Check if prepare was successful
  $stmtSub->bind_param($typesSub, ...$paramsSub);
  $stmtSub->execute();
  $subject_list = $stmtSub->get_result();
  $stmtSub->close();
} else {
  error_log("Failed to prepare subject list statement in admin-pastrecords.php: " . $conn->error);
  $subject_list = false; // Indicate an error
}


// --- Build main evaluation query for this admin (acting as faculty) ---
$params = [$admin_id];
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
// NEW: Add semester filter to main evaluation query
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
if ($stmt) { // Check if prepare was successful
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
} else {
  error_log("Failed to prepare main evaluation statement in admin-pastrecords.php: " . $conn->error);
  $result = false; // Indicate an error
}

// Get distinct academic years for the dropdown (for this admin acting as faculty)
$years_query = $conn->prepare("SELECT DISTINCT academic_year FROM evaluation WHERE faculty_id = ? ORDER BY academic_year DESC");
$years_data = [];
if ($years_query) {
  $years_query->bind_param("s", $admin_id);
  $years_query->execute();
  $years_result = $years_query->get_result();
  while ($row = $years_result->fetch_assoc()) {
    $years_data[] = $row;
  }
  $years_query->close();
}

// Get distinct semesters for the dropdown (for this admin acting as faculty), filtered by academic year if selected
$semesters_data = [];
$semester_sql = "SELECT DISTINCT semester FROM evaluation WHERE faculty_id = ?";
$semester_types = "s";
$semester_params = [$admin_id];

if ($academic_year) {
  $semester_sql .= " AND academic_year = ?";
  $semester_types .= "s";
  $semester_params[] = $academic_year;
}

// Order by 1st, 2nd, Summer (assuming these are the common values)
$semester_sql .= " ORDER BY
    CASE semester
        WHEN '1st Semester' THEN 1
        WHEN '2nd Semester' THEN 2
        WHEN 'Summer' THEN 3
        ELSE 4
    END";

$semesters_query = $conn->prepare($semester_sql);
if ($semesters_query) { // Check if prepare was successful
  $semesters_query->bind_param($semester_types, ...$semester_params);
  $semesters_query->execute();
  $semesters_result = $semesters_query->get_result();
  while ($row = $semesters_result->fetch_assoc()) {
    if (!empty(trim($row['semester']))) { // Only add non-empty semesters
      $semesters_data[] = $row;
    }
  }
  $semesters_query->close();
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>My Past Evaluations</title>
  <?php include 'header.php'; ?>
</head>

<body>
  <?php include 'admin-header.php'; ?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <!-- Evaluate Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-evaluate.php">
              <i class="bi bi-circle"></i><span>Form</span>
            </a>
          </li>
          <li>
            <a href="admin-evaluatedfaculty.php">
              <i class="bi bi-circle"></i><span>Evaluated Faculty</span>
            </a>
          </li>
        </ul>
      </li><!-- End Evaluate Nav -->

      <!-- Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#subject" data-bs-toggle="collapse" href="#">
          <i class="ri-book-line"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="subject" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-subjectlist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="admin-subjectadding.php">
              <i class="bi bi-circle"></i><span>Add Subject</span>
            </a>
          </li>
        </ul>
      </li><!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-studentsubject.php">
          <i class="ri-book-fill"></i>
          <span>Assign Subject</span>
        </a>
      </li><!-- End Student Subject Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-evaluatedsubject.php">
          <i class="bi bi-book-fill"></i>
          <span>Subject Evaluated</span>
        </a>
      </li><!-- End Profile Nav -->

      <!-- Reports Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="reports" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-individualreport.php">
              <i class="bi bi-circle"></i><span>Invidiual Report</span>
            </a>
          </li>
          <li>
            <a href="admin-acknowledgementreport.php">
              <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
            </a>
          </li>
          <li>
            <a href="admin-overallreport-set.php">
              <i class="bi bi-circle"></i><span>Overall Report SET</span>
            </a>
          </li>
          <li>
            <a href="admin-overallreport-sef.php">
              <i class="bi bi-circle"></i><span>Overall Report SEF</span>
            </a>
          </li>
          <li>
            <a href="admin-overallreport.php">
              <i class="bi bi-circle"></i><span>Overall Report (SET & SEF)</span>
            </a>
          </li>
          <li>
            <a href="admin-pastrecords.php" class="active">
              <i class="bi bi-circle"></i><span>Past Record</span>
            </a>
          </li>
        </ul>
      </li><!-- End Reports Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-user-profile.php">
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
      <h1>My Past Evaluation Records</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Past Records</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card">
        <div class="card-body table-responsive">
          <h5 class="card-title">Filter Evaluations (Self)</h5>

          <form method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-auto">
              <button type="submit" class="btn btn-secondary btn-sm">Clear Filters</button>
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
                  <?php
                  // Reset pointer for subject_list if it was already iterated
                  if ($subject_list->num_rows > 0) {
                    $subject_list->data_seek(0);
                  }
                  foreach ($subject_list as $sub):
                    $sel = ($subject_code == $sub['subject_code']) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($sub['subject_code']) . "\" $sel>" . htmlspecialchars($sub['subject_code']) . " - " . htmlspecialchars($sub['subject_title']) . "</option>";
                  endforeach;
                  ?>
                </select>
              </div>
            <?php endif; ?>
          </form>

          <?php if ($result && $result->num_rows): // Check if $result is not false and has rows 
          ?>
            <a href="admin-pastrecords-print.php?academic_year=<?= urlencode($academic_year) ?>&semester=<?= urlencode($semester) ?>&subject_code=<?= urlencode($subject_code) ?>" target="_blank" class="btn btn-outline-secondary mb-3">
              <i class="bi bi-printer"></i> Print My Evaluations
            </a>

            <table class="table table-bordered datatable">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Subject</th>
                  <th>Subject Title</th>
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
                <?php
                $modalIndex = 0; // Unique ID for each modal
                while ($row = $result->fetch_assoc()):
                  $modalId = 'commentModal' . $modalIndex;
                ?>
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

                      <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="<?= $modalId ?>Label">Comments</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <?php
                              $comments = explode(' | ', $row['comments']);
                              foreach ($comments as $comment) {
                                $clean = trim($comment);
                                if ($clean !== '') {
                                  echo "<div class='mb-2'>• " . htmlspecialchars($clean) . "</div>";
                                }
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
                    <td><?= $row['student_count'] ?></td>
                  </tr>
                <?php
                  $modalIndex++;
                endwhile;
                ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="alert alert-info">You have no past evaluation records
              <?php
              $filter_info = [];
              if ($academic_year) {
                $filter_info[] = "for A.Y. " . htmlspecialchars($academic_year);
              }
              if ($semester) {
                $filter_info[] = "in " . htmlspecialchars($semester);
              }
              if ($subject_code) {
                $filter_info[] = "on subject " . htmlspecialchars($subject_code);
              }
              echo implode(' ', $filter_info) . ".";
              ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main><!-- End of main -->

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
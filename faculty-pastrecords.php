<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
  header("Location: pages-login.php");
  exit();
}

$faculty_id = $_SESSION['idnumber'];
$academic_year = $_GET['academic_year'] ?? '';
$subject_code  = $_GET['subject_code'] ?? '';

// Get list of evaluated subjects for this faculty
$subject_list = null;
$sqlSubjects = "SELECT DISTINCT subject_code, subject_title 
                FROM evaluation 
                WHERE faculty_id = ?";
$paramsSub = [$faculty_id];
$typesSub = "s";

if ($academic_year) {
  $sqlSubjects .= " AND academic_year = ?";
  $paramsSub[] = $academic_year;
  $typesSub .= "s";
}

if ($subject_code) {
  $sqlSubjects .= " AND subject_code = ?";
  $paramsSub[] = $subject_code;
  $typesSub .= "s";
}

$stmtSub = $conn->prepare($sqlSubjects);
$stmtSub->bind_param($typesSub, ...$paramsSub);
$stmtSub->execute();
$subject_list = $stmtSub->get_result();
$stmtSub->close();

// Evaluation records query
$params = [$faculty_id];
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
if ($subject_code) {
  $sql .= " AND subject_code = ?";
  $params[] = $subject_code;
  $types .= "s";
}

$sql .= " GROUP BY subject_code, student_section, semester, academic_year
          ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>

<head>
  <title>My Past Evaluations</title>
  <?php include 'header.php'; ?>
</head>

<body>
  <?php include 'faculty-header.php' ?>

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
      </li><!-- End Subject Nav -->

      <li class="nav-item">
        <a class="nav-link collapse" href="faculty-pastrecords.php">
          <i class="ri-record-circle-fill"></i>
          <span>Records</span>
        </a>
      </li>
      <!-- End Records Nav -->

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
      <h1>My Past Evaluation Records</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Past Records</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card">
        <div class="card-body table-responsive">
          <h5 class="card-title">Filter Evaluations</h5>

          <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
              <button type="submit" class="btn btn-secondary btn-sm">Clear</button>
            </div>

            <div class="col-md-4">
              <select name="academic_year" class="form-select" onchange="this.form.submit()">
                <option value="">-- Academic Year --</option>
                <?php
                $years = $conn->query("SELECT DISTINCT academic_year FROM evaluation WHERE faculty_id = '$faculty_id' ORDER BY academic_year DESC");
                while ($yr = $years->fetch_assoc()):
                  $sel = ($academic_year == $yr['academic_year']) ? 'selected' : '';
                  echo "<option value=\"{$yr['academic_year']}\" $sel>{$yr['academic_year']}</option>";
                endwhile;
                ?>
              </select>
            </div>

            <?php if ($subject_list && $subject_list->num_rows): ?>
              <div class="col-md-4">
                <select name="subject_code" class="form-select" onchange="this.form.submit()">
                  <option value="">-- Subject --</option>
                  <?php foreach ($subject_list as $sub):
                    $sel = ($subject_code == $sub['subject_code']) ? 'selected' : '';
                    echo "<option value=\"{$sub['subject_code']}\" $sel>{$sub['subject_code']} - {$sub['subject_title']}</option>";
                  endforeach;
                  ?>
                </select>
              </div>
            <?php endif; ?>
          </form>

          <?php if ($result->num_rows): ?>
            <a href="faculty-pastrecords-print.php?academic_year=<?= $academic_year ?>&subject_code=<?= $subject_code ?>" target="_blank" class="btn btn-outline-secondary mb-3">
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
                $modalIndex = 0;
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
            <div class="alert alert-info">You have no past evaluation records<?= $academic_year ? " for A.Y. $academic_year" : "" ?><?= $subject_code ? " on subject $subject_code" : "" ?>.</div>
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
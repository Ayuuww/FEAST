<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Filters
$department = isset($_GET['department']) && $_GET['department'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['department']) : null;
$academic_year = isset($_GET['year']) && $_GET['year'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['year']) : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['semester']) : null;

// Base SQL: Find students who have NOT evaluated their faculty
$sql = " SELECT 
    s.idnumber AS student_id,
    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
    s.department AS student_department,
    subj.code AS subject_code,
    subj.title AS subject_title,
    f.idnumber AS faculty_id,
    CONCAT(f.first_name, ' ', f.last_name) AS faculty_name,
    ss.academic_year,
    ss.semester
FROM student_subject ss
INNER JOIN student s ON s.idnumber = ss.student_id
INNER JOIN subject subj ON subj.code = ss.subject_code
INNER JOIN faculty f ON f.idnumber = ss.faculty_id
LEFT JOIN evaluation e 
    ON e.student_id = ss.student_id
    AND e.faculty_id = ss.faculty_id
    AND e.subject_code = ss.subject_code
    AND e.academic_year = ss.academic_year
    AND e.semester = ss.semester
WHERE e.id IS NULL  /* ❗ means student has not yet evaluated */ ";

// Apply filters dynamically
if ($department) {
  $sql .= " AND s.department = '{$department}'";
}
if ($academic_year) {
  $sql .= " AND ss.academic_year = '{$academic_year}'";
}
if ($semester) {
  $sql .= " AND ss.semester = '{$semester}'";
}

$subject = isset($_GET['subject']) && $_GET['subject'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['subject']) : null;

// Apply subject filter BEFORE ORDER BY
if ($subject) {
  $sql .= " AND subj.code = '{$subject}'";
}

$sql .= " ORDER BY s.last_name, s.first_name ASC";

$result = mysqli_query($conn, $sql);

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

    <section class="section dashboard">
      <div class="row">

        <div class="pagetitle">
          <h1>Evaluation Progress</h1>
          <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Evaluation Progress</li>
            </ol>
          </nav>
        </div><!-- End Page Title -->

        <div class="card">
          <div class="card-body table-responsive">
            <h5 class="card-title">Datatables</h5>
            <h3 class="mb-4 text-center">Students Who Have Not Yet Evaluated Their Instructors</h3>

            <!-- Filter Form -->
            <?php
            // Fetch distinct academic years, semesters, departments, and subjects dynamically
            $years = mysqli_query($conn, "SELECT DISTINCT academic_year FROM student_subject ORDER BY academic_year DESC");
            $sems = mysqli_query($conn, "SELECT DISTINCT semester FROM student_subject ORDER BY semester ASC");
            $depts = mysqli_query($conn, "SELECT DISTINCT department FROM faculty WHERE department IS NOT NULL ORDER BY department ASC");
            $subjects = mysqli_query($conn, "SELECT DISTINCT code, title FROM subject ORDER BY title ASC");

            // Preserve selected filters
            $selected_year = $academic_year ?? '';
            $selected_sem = $semester ?? '';
            $selected_dept = $department ?? '';
            $selected_subject = isset($_GET['subject']) && $_GET['subject'] !== 'All' ? $_GET['subject'] : '';
            ?>

            <form method="get" class="row g-3 mb-4">
              <div class="col-md-2">
                <label for="year" class="form-label fw-semibold">Academic Year</label>
                <select class="form-select" name="year" id="year">
                  <option value="All">All</option>
                  <?php while ($y = mysqli_fetch_assoc($years)): ?>
                    <option value="<?= htmlspecialchars($y['academic_year']) ?>"
                      <?= ($selected_year == $y['academic_year']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($y['academic_year']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="col-md-2">
                <label for="semester" class="form-label fw-semibold">Semester</label>
                <select class="form-select" name="semester" id="semester">
                  <option value="All">All</option>
                  <?php while ($s = mysqli_fetch_assoc($sems)): ?>
                    <option value="<?= htmlspecialchars($s['semester']) ?>"
                      <?= ($selected_sem == $s['semester']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($s['semester']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="col-md-2">
                <label for="department" class="form-label fw-semibold">Department</label>
                <select class="form-select" name="department" id="department">
                  <option value="All">All</option>
                  <?php while ($d = mysqli_fetch_assoc($depts)): ?>
                    <option value="<?= htmlspecialchars($d['department']) ?>"
                      <?= ($selected_dept == $d['department']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($d['department']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label for="subject" class="form-label fw-semibold">Subject</label>
                <select class="form-select" name="subject" id="subject">
                  <option value="All">All</option>
                  <?php while ($subj = mysqli_fetch_assoc($subjects)): ?>
                    <option value="<?= htmlspecialchars($subj['code']) ?>"
                      <?= ($selected_subject == $subj['code']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($subj['title']) ?> (<?= htmlspecialchars($subj['code']) ?>)
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">Filter</button>
              </div>

            </form>
            <!-- End Filter Form -->

            <div class="card shadow-sm">
              <div class="card-body">
                <table class="table datatable table-hover">
                  <thead class="table text-center">
                    <tr>
                      <th>ID Number</th>
                      <th>Student Name</th>
                      <th>College</th>
                      <th>Subject Code</th>
                      <th>Subject Title</th>
                      <th>Faculty Name</th>
                      <th>Academic Year</th>
                      <th>Semester</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                      $i = 1;
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td class='text-center'>{$row['student_id']}</td>
                                <td>{$row['student_name']}</td>
                                <td>{$row['student_department']}</td>
                                <td>{$row['subject_code']}</td>
                                <td>{$row['subject_title']}</td>
                                <td>{$row['faculty_name']}</td>
                                <td>{$row['academic_year']}</td>
                                <td>{$row['semester']}</td>
                                <td class='text-danger fw-bold text-center'>Not Evaluated</td>
                            </tr>";
                        $i++;
                      }
                    } else {
                      echo "<tr><td colspan='9' class='text-center text-muted'>All students in {$department} have completed their evaluations.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
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
  <script src="chart/chart.js"></script>

</body>

</html>
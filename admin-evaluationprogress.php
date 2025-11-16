<?php
session_start();
include 'conn/conn.php'; // DB connection

// Ensure only admin users can access
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// ✅ Fetch all departments assigned to this admin
$dept_query = $conn->prepare("SELECT department_name FROM admin_departments WHERE admin_idnumber = ?");
$dept_query->bind_param("s", $admin_id);
$dept_query->execute();
$dept_result = $dept_query->get_result();

$departments = [];
while ($row = $dept_result->fetch_assoc()) {
  $departments[] = $row['department_name'];
}
$dept_query->close();

// If no departments found, block access
if (empty($departments)) {
  $_SESSION['msg'] = "You are not assigned to any department. Please contact the Superadmin.";
  $_SESSION['msg_type'] = "error";
  header("Location: admin-dashboard.php");
  exit();
}

// Filters
$academic_year = isset($_GET['year']) && $_GET['year'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['year']) : null;
$semester = isset($_GET['semester']) && $_GET['semester'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['semester']) : null;
$subject = isset($_GET['subject']) && $_GET['subject'] !== 'All' ? mysqli_real_escape_string($conn, $_GET['subject']) : null;

// Base query: find students who have NOT evaluated their instructors (within admin’s department)
$sql = "
SELECT 
    s.idnumber AS student_id,
    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
    s.department AS student_department,
    s.section AS student_section,
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
WHERE e.id IS NULL
  AND s.department IN ('" . implode("','", $departments) . "')  /* ✅ Only show students in admin’s department */
";

// Apply filters
if ($academic_year) {
  $sql .= " AND ss.academic_year = '{$academic_year}'";
}
if ($semester) {
  $sql .= " AND ss.semester = '{$semester}'";
}
if ($subject) {
  $sql .= " AND subj.code = '{$subject}'";
}

$sql .= " ORDER BY s.last_name, s.first_name ASC";
$result = mysqli_query($conn, $sql);

// Dropdown values for filters
$years = mysqli_query($conn, "SELECT DISTINCT academic_year FROM student_subject ORDER BY academic_year DESC");
$sems = mysqli_query($conn, "SELECT DISTINCT semester FROM student_subject ORDER BY semester ASC");
$subjects = mysqli_query($conn, "
  SELECT DISTINCT subj.code, subj.title 
  FROM subject subj 
  WHERE subj.department IN ('" . implode("','", $departments) . "')
  ORDER BY subj.title ASC
");

?>


<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->


</head>

<body>

  <?php include 'admin-header.php'; ?>
  <?php include 'admin-sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Evaluation Progress</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Evaluation Progress</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="card">
        <div class="card-body table-responsive">
          <h5 class="card-title">Students Who Have Not Yet Evaluated Their Instructors</h5>

          <!-- Filter Form -->
          <form method="get" class="row g-3 mb-4">
            <div class="col-md-3">
              <label for="year" class="form-label fw-semibold">Academic Year</label>
              <select class="form-select" name="year" id="year">
                <option value="All">All</option>
                <?php while ($y = mysqli_fetch_assoc($years)): ?>
                  <option value="<?= htmlspecialchars($y['academic_year']) ?>" <?= ($academic_year == $y['academic_year']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($y['academic_year']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-3">
              <label for="semester" class="form-label fw-semibold">Semester</label>
              <select class="form-select" name="semester" id="semester">
                <option value="All">All</option>
                <?php while ($s = mysqli_fetch_assoc($sems)): ?>
                  <option value="<?= htmlspecialchars($s['semester']) ?>" <?= ($semester == $s['semester']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['semester']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label for="subject" class="form-label fw-semibold">Subject</label>
              <select class="form-select" name="subject" id="subject">
                <option value="All">All</option>
                <?php while ($sub = mysqli_fetch_assoc($subjects)): ?>
                  <option value="<?= htmlspecialchars($sub['code']) ?>" <?= ($subject == $sub['code']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sub['title']) ?> (<?= htmlspecialchars($sub['code']) ?>)
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
                    <th>Section</th>
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
                              <td>{$row['student_section']}</td>
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
                    echo "<tr><td colspan='9' class='text-center text-muted'>All students have completed their evaluations.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!-- End #main -->

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
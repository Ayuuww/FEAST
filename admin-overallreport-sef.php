<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// 🔹 Fix: Fetch college from the correct table
$stmt = $conn->prepare("SELECT college_name FROM admin_college WHERE admin_idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$college = []; // This will hold all assigned college
while ($row = $result->fetch_assoc()) {
  $college[] = $row['college_name'];
}
$stmt->close();

// This new variable is just for the HTML title
$admin_college_display = !empty($college) ? implode(', ', $college) : 'No college Assigned';

// 🔹 Get filters
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$year_filter     = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';

// 🔹 Fetch distinct semesters
$semesters = [];
$res = $conn->query("SELECT DISTINCT semester FROM admin_evaluation ORDER BY semester ASC");
while ($row = $res->fetch_assoc()) {
  if (!empty($row['semester'])) $semesters[] = $row['semester'];
}
$res->close();

// 🔹 Fetch distinct academic years
$years = [];
$res = $conn->query("SELECT DISTINCT academic_year FROM admin_evaluation ORDER BY academic_year DESC");
while ($row = $res->fetch_assoc()) {
  if (!empty($row['academic_year'])) $years[] = $row['academic_year'];
}
$res->close();

// Fetch all faculty in the admin's assigned college AND programs
$faculties = [];

// ✅ FIRST, get the admin's assignments as pairs
$admin_assignments = [];
// We need to re-fetch the college/program pairs, not just college
$stmt_admin_dept = $conn->prepare("SELECT college_name, program_name FROM admin_college WHERE admin_idnumber = ?");
$stmt_admin_dept->bind_param("s", $admin_id);
$stmt_admin_dept->execute();
$result = $stmt_admin_dept->get_result();
while ($row = $result->fetch_assoc()) {
  $admin_assignments[] = $row;
}
$stmt_admin_dept->close();


if (!empty($admin_assignments)) {
  // ✅ Build the query to check for (dept = ? AND prog = ?) OR (dept = ? AND prog = ?)
  $faculty_query_parts = [];
  $params = [];
  $types = "";

  foreach ($admin_assignments as $assignment) {
    $faculty_query_parts[] = "(college = ? AND program = ?)";
    $params[] = $assignment['college_name'];
    $params[] = $assignment['program_name'];
    $types .= "ss";
  }
  $faculty_where_sql = implode(' OR ', $faculty_query_parts);

  // ✅ This query now finds faculty whose home dept/prog matches the admin's assignments
  $sql = "
        SELECT idnumber, last_name, first_name, mid_name
        FROM faculty
        WHERE ($faculty_where_sql)
        ORDER BY last_name ASC
    ";

  $query = $conn->prepare($sql);
  $query->bind_param($types, ...$params);
  $query->execute();
  $faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
  $query->close();
}

$rows = '';
foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];

  // 🔹 Evaluation query with filters
  $sql = "
    SELECT COUNT(*) AS evals, AVG(computed_rating) AS avg_rating
    FROM admin_evaluation
    WHERE evaluatee_id = ?
  ";
  $params = [$fid];
  $types = "s";

  if (!empty($semester_filter)) {
    $sql .= " AND semester = ?";
    $params[] = $semester_filter;
    $types .= "s";
  }
  if (!empty($year_filter)) {
    $sql .= " AND academic_year = ?";
    $params[] = $year_filter;
    $types .= "s";
  }

  $stmtEval = $conn->prepare($sql);
  $stmtEval->bind_param($types, ...$params);
  $stmtEval->execute();
  $r = $stmtEval->get_result()->fetch_assoc();
  $stmtEval->close();

  $count = (int)$r['evals']; // ✅ This is the count
  $avg = $count ? number_format((float)$r['avg_rating'], 2) : '0.00';
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

  // ✅ MODIFICATION: Added the $count variable in a new <td>
  $rows .= "<tr>
              <td>{$name}</td>
              <td>{$count}</td>
              <td>{$avg}</td>
            </tr>";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

  <?php include 'head.php' ?>
</head>

<body>
  <?php include 'admin-header.php'; ?>

  <?php include 'admin-sidebar.php' ?>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Supervisor Evaluation of Faculty Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Overall Report SEF</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="container mt-4">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title text-center my-3">
                  Overall SEF Report – <?= htmlspecialchars($admin_college_display) ?>
                </h4>

                <form method="GET" class="mb-3">
                  <div class="row align-items-end">
                    <div class="col-md-4">
                      <label for="semester" class="form-label">Select Semester</label>
                      <select name="semester" id="semester" class="form-select">
                        <option value="">-- All Semesters --</option>
                        <?php foreach ($semesters as $sem): ?>
                          <option value="<?= htmlspecialchars($sem) ?>" <?= $semester_filter == $sem ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="col-md-4">
                      <label for="academic_year" class="form-label">Select Academic Year</label>
                      <select name="academic_year" id="academic_year" class="form-select">
                        <option value="">-- All Academic Years --</option>
                        <?php foreach ($years as $yr): ?>
                          <option value="<?= htmlspecialchars($yr) ?>" <?= $year_filter == $yr ? 'selected' : '' ?>>
                            <?= htmlspecialchars($yr) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="col-md-auto">
                      <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-filter"></i> Generate Report
                      </button>
                    </div>
                  </div>
                </form>

                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="table-light">
                      <tr>
                        <th>Faculty Name</th>
                        <th>No. of Evaluations</th>
                        <th>Average SEF Rating</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?= $rows ?>
                    </tbody>
                  </table>
                </div>

                <div class="text-end mt-3">
                  <a href="admin-overallreport-sef-print.php?semester=<?= urlencode($semester_filter) ?>&academic_year=<?= urlencode($year_filter) ?>"
                    class="btn btn-secondary" target="_blank">
                    <i class="bi bi-printer"></i> Print Report
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>

</body>

</html>
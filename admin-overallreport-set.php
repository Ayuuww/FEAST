<?php
session_start();
include 'conn/conn.php';
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Fetch all departments assigned to this admin
$stmt = $conn->prepare("SELECT department_name FROM admin_departments WHERE admin_idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$departments = [];
while ($row = $result->fetch_assoc()) {
  $departments[] = $row['department_name'];
}
$stmt->close();

// Use first department as default (for display/title)
$admin_department = !empty($departments) ? implode(', ', $departments) : 'No Department Assigned';


// Get filter values from request
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$year_filter     = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';

// 🔹 Fetch distinct semesters from evaluation table
$semesters = [];
$res = $conn->query("SELECT DISTINCT semester FROM evaluation ORDER BY semester ASC");
while ($row = $res->fetch_assoc()) {
  if (!empty($row['semester'])) {
    $semesters[] = $row['semester'];
  }
}
$res->close();

// 🔹 Fetch distinct academic years from evaluation table
$years = [];
$res = $conn->query("SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
while ($row = $res->fetch_assoc()) {
  if (!empty($row['academic_year'])) {
    $years[] = $row['academic_year'];
  }
}
$res->close();

// Fetch all faculty in the admin's assigned departments
// Fetch all faculty in the admin's assigned departments AND programs
$faculties = [];

// ✅ FIRST, get the admin's assignments as pairs
$admin_assignments = [];
$stmt_admin_dept = $conn->prepare("SELECT department_name, program_name FROM admin_departments WHERE admin_idnumber = ?");
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
    $faculty_query_parts[] = "(department = ? AND program = ?)";
    $params[] = $assignment['department_name'];
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

// Build table rows
$rows = '';
$total_avg_rating = 0;
$faculty_count_with_evals = 0;
foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];

  // Build evaluation query with optional filters
  $sql = "
    SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating
    FROM evaluation
    WHERE faculty_id = ?
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

  $count = (int)$r['students'];
  $avg = $count ? (float)$r['avg_rating'] : 0.00;

  // ✅ ADD THIS BLOCK
  if ($count > 0) {
    $total_avg_rating += $avg;
    $faculty_count_with_evals++;
  }
  // --- END ADD ---

  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";
  // Format the average for display
  $avg_display = number_format($avg, 2);
  $rows .= "<tr><td>{$name}</td><td>{$count}</td><td>{$avg_display}</td></tr>";
}

$department_average = 0;
if ($faculty_count_with_evals > 0) {
  $department_average = $total_avg_rating / $faculty_count_with_evals;
}
// --- END ADD ---
?>

<!DOCTYPE html>
<html>

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

</head>

<body>

  <?php include 'admin-header.php'; ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->


  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Student Evaluation of Teacher Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Overall Report SET</li>
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
                  Overall SET Report – <?= htmlspecialchars($admin_department) ?>
                </h4>

                <!-- 🔹 Filter Form -->
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
                        <th>No. of Student Evaluations</th>
                        <th>Average SET Rating</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?= $rows ?>
                    </tbody>
                    <tfoot>
                      <tr class="table-light fw-bold">
                        <td colspan="2" class="text-end">Department Average:</td>
                        <td><?= number_format($department_average, 2) ?></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div class="text-end mb-3">
                  <a href="admin-overallreport-set-print.php?semester=<?= urlencode($semester_filter) ?>&academic_year=<?= urlencode($year_filter) ?>"
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

</body>

</html>
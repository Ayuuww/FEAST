<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get admin's college from the correct table
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

// --- Filters ---
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$academic_filter = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';

// 🔹 Fetch distinct semesters
$semesters = [];
$res = $conn->query("SELECT DISTINCT semester FROM evaluation ORDER BY semester ASC");
while ($row = $res->fetch_assoc()) {
  if (!empty($row['semester'])) $semesters[] = $row['semester'];
}
$res->close();

// 🔹 Fetch distinct academic years
$years = [];
$res = $conn->query("SELECT DISTINCT academic_year FROM evaluation ORDER BY academic_year DESC");
while ($row = $res->fetch_assoc()) {
  if (!empty($row['academic_year'])) $years[] = $row['academic_year'];
}
$res->close();

// Get all faculty in this college
// Fetch all faculty in the admin's assigned college AND programs
$faculties = [];

// ✅ FIRST, get the admin's assignments as pairs
$admin_assignments = [];
// We need to re-fetch the college/program pairs
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

// Initialize rows
$overall_rows = '';
$total_set_avg = 0;
$total_sef_avg = 0;
$faculty_with_set = 0;
$faculty_with_sef = 0;

foreach ($faculties as $fac) {
  $fid = $fac['idnumber'];
  $name = "{$fac['last_name']}, {$fac['first_name']} {$fac['mid_name']}";

  // SET (student evaluations)
  $sql = "SELECT COUNT(*) AS students, AVG(computed_rating) AS avg_rating
            FROM evaluation WHERE faculty_id = ?";
  $types = "s";
  $params = [$fid];

  if (!empty($semester_filter)) {
    $sql .= " AND semester = ?";
    $params[] = $semester_filter;
    $types .= "s";
  }
  if (!empty($academic_filter)) {
    $sql .= " AND academic_year = ?";
    $params[] = $academic_filter;
    $types .= "s";
  }

  $stmtEval = $conn->prepare($sql);
  $stmtEval->bind_param($types, ...$params);
  $stmtEval->execute();
  $set_result = $stmtEval->get_result()->fetch_assoc();
  $stmtEval->close();

  $set_count = (int)$set_result['students'];
  // ✅ FIX 1: Get the raw float value for calculations
  $set_avg_raw = $set_count ? (float)$set_result['avg_rating'] : 0.00;
  $set_avg_display = number_format($set_avg_raw, 2); // Formatted string for display

  // SEF (supervisor evaluations)
  $sql = "SELECT COUNT(*) AS admins, AVG(computed_rating) AS avg_rating
            FROM admin_evaluation WHERE evaluatee_id = ?";
  $types = "s";
  $params = [$fid];

  if (!empty($semester_filter)) {
    $sql .= " AND semester = ?";
    $params[] = $semester_filter;
    $types .= "s";
  }
  if (!empty($academic_filter)) {
    $sql .= " AND academic_year = ?";
    $params[] = $academic_filter;
    $types .= "s";
  }

  $stmtEval = $conn->prepare($sql);
  $stmtEval->bind_param($types, ...$params);
  $stmtEval->execute();
  $sef_result = $stmtEval->get_result()->fetch_assoc();
  $stmtEval->close();

  $sef_count = (int)$sef_result['admins'];
  // ✅ FIX 2: Get the raw float value for calculations
  $sef_avg_raw = $sef_count ? (float)$sef_result['avg_rating'] : 0.00;
  $sef_avg_display = number_format($sef_avg_raw, 2); // Formatted string for display

  // ✅ FIX 3: Moved this block AFTER $sef_count is defined
  if ($set_count > 0) {
    $total_set_avg += $set_avg_raw; // Use the raw float
    $faculty_with_set++;
  }
  if ($sef_count > 0) {
    $total_sef_avg += $sef_avg_raw; // Use the raw float
    $faculty_with_sef++;
  }

  // Overall Average
  $overall_avg = ($set_count && $sef_count)
    ? number_format(($set_avg_raw + $sef_avg_raw) / 2, 2)
    : ($set_count ? $set_avg_display : ($sef_count ? $sef_avg_display : '0.00'));

  $overall_rows .= "
      <tr>
        <td>{$name}</td>
        <td class='text-center'>{$set_avg_display}</td>
        <td class='text-center'>{$sef_avg_display}</td>
      </tr>
    ";
}

// ✅ FIX 4: Calculations are now correct and outside the loop
$final_set_average = ($faculty_with_set > 0) ? ($total_set_avg / $faculty_with_set) : 0;
$final_sef_average = ($faculty_with_sef > 0) ? ($total_sef_avg / $faculty_with_sef) : 0;
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
      <h1>Overall Faculty Evaluation Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Reports</li>
          <li class="breadcrumb-item active">Overall Report SET & SEF</li>
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
                  Overall Evaluation Report – <?= htmlspecialchars($admin_college_display) ?>
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
                          <option value="<?= htmlspecialchars($yr) ?>" <?= $academic_filter == $yr ? 'selected' : '' ?>>
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

                <!-- Table -->
                <div class="table-responsive mb-4">
                  <table class="table table-bordered table-hover">
                    <thead class="table-light text-center">
                      <tr>
                        <th>Faculty Name</th>
                        <th>SET Avg</th>
                        <th>SEF Avg</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?= $overall_rows ?>
                    </tbody>
                    <tfoot>
                      <tr class="table-light fw-bold">
                        <td class="text-end">college Average:</td>
                        <td class="text-center"><?= number_format($final_set_average, 2) ?></td>
                        <td class="text-center"><?= number_format($final_sef_average, 2) ?></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div class="text-end mb-3">
                  <a href="admin-overallreport-print.php?semester=<?= urlencode($semester_filter) ?>&academic_year=<?= urlencode($academic_filter) ?>" class="btn btn-secondary" target="_blank">
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

  <!-- Vendor JS Files -->
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
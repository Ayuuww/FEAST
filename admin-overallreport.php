<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get admin's departments from the correct table
$stmt = $conn->prepare("SELECT department_name FROM admin_departments WHERE admin_idnumber = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$departments = []; // This will hold all assigned departments
while ($row = $result->fetch_assoc()) {
  $departments[] = $row['department_name'];
}
$stmt->close();

// This new variable is just for the HTML title
$admin_department_display = !empty($departments) ? implode(', ', $departments) : 'No Department Assigned';

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

// Get all faculty in this department
$faculties = [];
if (!empty($departments)) {
    // Create placeholders like (?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($departments), '?'));
    // Create types string like "sss"
    $types = str_repeat('s', count($departments));

    $sql = "
        SELECT idnumber, last_name, first_name, mid_name
        FROM faculty
        WHERE department IN ($placeholders)
        ORDER BY last_name ASC
    ";

    $query = $conn->prepare($sql);
    $query->bind_param($types, ...$departments); // Bind all departments
    $query->execute();
    $faculties = $query->get_result()->fetch_all(MYSQLI_ASSOC);
    $query->close();
}

// Initialize rows
$overall_rows = '';

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
  $set_avg = $set_count ? number_format((float)$set_result['avg_rating'], 2) : '0.00';

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
  $sef_avg = $sef_count ? number_format((float)$sef_result['avg_rating'], 2) : '0.00';

  // Overall Average
  $overall_avg = ($set_count && $sef_count)
    ? number_format(((float)$set_avg + (float)$sef_avg) / 2, 2)
    : ($set_count ? $set_avg : ($sef_count ? $sef_avg : '0.00'));

  $overall_rows .= "
    <tr>
      <td>{$name}</td>
      <td class='text-center'>{$set_avg}</td>
      <td class='text-center'>{$sef_avg}</td>
    </tr>
  ";
}
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
                  Overall Evaluation Report – <?= htmlspecialchars($admin_department_display) ?>
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
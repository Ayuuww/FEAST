<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

$faculty_id = $_GET['faculty_id'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';
$subject_code = $_GET['subject_code'] ?? '';

if (empty($faculty_id)) {
  echo "Faculty ID is required.";
  exit();
}

// Get faculty full name
$faculty_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
$faculty_stmt->bind_param("s", $faculty_id);
$faculty_stmt->execute();
$faculty_result = $faculty_stmt->get_result()->fetch_assoc();
$faculty_name = $faculty_result ? $faculty_result['last_name'] . ', ' . $faculty_result['first_name'] . ' ' . $faculty_result['mid_name'] : 'Unknown';

// Build evaluation query
$params = [];
$sql = "SELECT subject_code, subject_title, student_section, academic_year, semester, created_at,
               COUNT(*) AS student_count,
               AVG(total_score) AS avg_total_score,
               AVG(computed_rating) AS avg_computed_rating,
               GROUP_CONCAT(comment SEPARATOR ' | ') AS comments
        FROM evaluation
        WHERE faculty_id = ?";
$params[] = $faculty_id;

if ($academic_year) {
  $sql .= " AND academic_year = ?";
  $params[] = $academic_year;
}
if ($subject_code) {
  $sql .= " AND subject_code = ?";
  $params[] = $subject_code;
}

$sql .= " GROUP BY subject_code, student_section, semester, academic_year ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Print - Past Evaluation Records</title>
  <?php include 'header.php' ?>

  <style>
    @media print {
      .no-print {
        display: none !important;
      }

      body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        font-size: 12px;
      }

      th,
      td {
        word-wrap: break-word;
      }
    }
  </style>

</head>

<body onload="window.print()" class="bg-white py-4 px-5">

  <div class="text-center mb-4">
    <h2 class="mb-1">Faculty Evaluation Report</h2>
    <hr class="w-50 mx-auto mb-3">
    <p class="mb-1"><strong>Faculty:</strong> <?= htmlspecialchars($faculty_name) ?></p>
    <?php if ($academic_year): ?>
      <p class="mb-1"><strong>Academic Year:</strong> <?= htmlspecialchars($academic_year) ?></p>
    <?php endif; ?>
    <?php if ($subject_code): ?>
      <p class="mb-1"><strong>Subject Code:</strong> <?= htmlspecialchars($subject_code) ?></p>
    <?php endif; ?>
  </div>

  <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-sm table-striped align-middle text-center" style="font-size: 13px; table-layout: fixed; width: 100%;">
        <thead class="table-light">
          <tr>
            <th style="width: 10%;">Date Evaluated</th>
            <th style="width: 8%;">Subject Code</th>
            <th style="width: 18%;">Subject Title</th>
            <th style="width: 8%;">Section</th>
            <th style="width: 12%;">Academic Year</th>
            <th style="width: 10%;">Semester</th>
            <th style="width: 10%;">Avg Score</th>
            <th style="width: 10%;">Rating (%)</th>
            <th style="width: 24%;">Comments</th>
            <th style="width: 8%;">Students</th>
          </tr>
        </thead>

        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= date("F j, Y", strtotime($row['created_at'])) ?></td>
              <td><?= htmlspecialchars($row['subject_code']) ?></td>
              <td><?= htmlspecialchars($row['subject_title']) ?></td>
              <td><?= htmlspecialchars($row['student_section']) ?></td>
              <td><?= htmlspecialchars($row['academic_year']) ?></td>
              <td><?= htmlspecialchars($row['semester']) ?></td>
              <td><?= number_format($row['avg_total_score'], 2) ?></td>
              <td><?= number_format($row['avg_computed_rating'], 2) ?>%</td>
              <td><?= htmlspecialchars($row['comments']) ?></td>
              <td><?= $row['student_count'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center">No records found for the selected filters.</div>
  <?php endif; ?>

  <div class="no-print text-center mt-4">
    <button onclick="window.print()" class="btn btn-secondary me-2">
      <i class="bi bi-printer"></i> Print Again
    </button>
    <a href="superadmin-pastrecords.php" class="btn btn-secondary">Go Back</a>
  </div>

</body>

</html>
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
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 30px;
    }
    h2 {
      text-align: center;
      margin-bottom: 10px;
    }
    .sub-header {
      text-align: center;
      margin-bottom: 30px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    th, td {
      border: 1px solid #000;
      padding: 6px;
      text-align: center;
      vertical-align: middle;
    }
    th {
      background-color: #f0f0f0;
    }
    .note {
      margin-top: 40px;
      font-style: italic;
    }
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body onload="window.print()">

<h2>Faculty Evaluation Report</h2>
<div class="sub-header">
  <strong>Faculty:</strong> <?= htmlspecialchars($faculty_name) ?><br>
  <?php if ($academic_year): ?>
    <strong>Academic Year:</strong> <?= htmlspecialchars($academic_year) ?><br>
  <?php endif; ?>
  <?php if ($subject_code): ?>
    <strong>Subject Code:</strong> <?= htmlspecialchars($subject_code) ?><br>
  <?php endif; ?>
</div>

<?php if ($result->num_rows > 0): ?>
<table>
  <thead>
    <tr>
      <th>Date Evaluated</th>
      <th>Subject Code</th>
      <th>Subject Title</th>
      <th>Section</th>
      <th>Academic Year</th>
      <th>Semester</th>
      <th>Avg Total Score</th>
      <th>Rating (%)</th>
      <th>Comments</th>
      <th>Students</th>
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
<?php else: ?>
  <p>No records found for the selected filters.</p>
<?php endif; ?>

<div class="note no-print">
  <button onclick="window.print()">Print Again</button>
  <a href="superadmin-pastrecords.php">Go Back</a>
</div>

</body>
</html>

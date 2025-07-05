<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['admin_print_data'])) {
    header("Location: admin-evaluate.php");
    exit();
}

$data = $_SESSION['admin_print_data'];
unset($_SESSION['admin_print_data']); // Prevent reprint on refresh

// Fetch faculty names
function getFacultyName($conn, $id) {
    $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $r = $res->fetch_assoc();
        return $r['first_name'] . ' ' . $r['mid_name'] . ' ' . $r['last_name'];
    }
    return 'Unknown';
}


$evaluatorName = getFacultyName($conn, $data['evaluator_id']);
$evaluateeName = getFacultyName($conn, $data['evaluatee_id']);

$questions = [
    "Comes to class on time regularly.",
    "Submits updated syllabus, grade sheets, and other required reports on time.",
    "Maximizes the allocated time/learning hours effectively.",
    "Provide appropriate learning activities that facilitate critical thinking and creativity of students.",
    "Guides students to learn on their own, reflect on new ideas and experiences, and make decisions in accomplishing given tasks.",
    "Communicates constructive feedback to students for their academic growth.",
    "Demonstrates extensive and broad knowledge of the subject/course.",
    "Simplifies complex ideas in the lesson for ease of understanding.",
    "Integrates contemporary issues and developments in the discipline and/or daily life activities in the syllabus.",
    "Promotes active learning and student engagement by using appropriate teaching and learning resources including ICT Tools and platforms.",
    "Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes",
    "Recognizes and values the unique diversity and individual differences among students.",
    "Assist students with their learning challenges during consultation hours.",
    "Provide immediate feedback on student outputs and performance.",
    "Provides transparent and clear criteria in rating student's performance."
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Peer Evaluation Print</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <style>
    
        @media print {
      body {
        font-size: 16px; /* increase base font */
        line-height: 1.6;
        margin: 0;
        padding: 0;
        background-color: #fff;
      }

      .container {
        width: 100%;
        padding: 1.5cm; /* more padding for space */
      }

      .card-body {
        font-size: 16px; /* larger text inside card */
      }

      h3 {
        font-size: 24px; /* bigger title */
      }

      .table th, .table td {
        padding: 12px;
        font-size: 15px; /* increase table font */
      }

      .table th:first-child,
      .table td:first-child {
        width: 75%;
        text-align: left;
      }

      .table th:last-child,
      .table td:last-child {
        width: 25%;
        text-align: center;
      }

      .no-print {
        display: none !important;
      }
    }

  </style>
</head>
<body onload="window.print()">

<div class="container">
  <div class="card shadow">
    <div class="card-body">
      <div class="text-center mb-4">
        <h3 class="fw-bold text-primary">Faculty Peer Evaluation Summary</h3>
      </div>

      <p><strong>Evaluatee:</strong> <?= htmlspecialchars($evaluateeName) ?></p>
      <p><strong>Academic Rank:</strong> <?= htmlspecialchars($data['faculty_rank']) ?></p>
      <p><strong>College</strong> <?= htmlspecialchars($data['department']) ?></p>
      <p><strong>Rating Period(Academic Year):</strong> <?= htmlspecialchars($data['semester']) ?></p>

      <div class="table-responsive mb-4">
        <table class="table table-bordered text-center align-middle">
          <thead class="table-light">
            <tr>
              <th class="text-start">Question</th>
              <th>Rating</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($questions as $index => $question): ?>
              <tr>
                <td class="text-start"><?= ($index + 1) . '. ' . htmlspecialchars($question) ?></td>
                <td><?= htmlspecialchars($data['answers']["q$index"] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="mb-1">
        <p><strong>Total Score:</strong> <?= htmlspecialchars($data['total_score'] ?? '-') ?> / 75</p>
        <p><strong>Computed Rating:</strong> <?= number_format($data['computed_rating'] ?? 0, 2) ?>%</p>
      </div>

      <?php if (!empty($data['comment'])): ?>
        <div class="mb-1">
          <h6><strong>Additional Comment:</strong></h6>
          <p class="border rounded p-2"><?= nl2br(htmlspecialchars($data['comment'])) ?></p>
        </div>
      <?php endif; ?>

      <div class="row mb-1">
        <div class="col-12">
          <p><strong>Signature of Supervisor: </strong>__________________________________</p>
          <p><strong>Name of Supervisor: </strong> <?= htmlspecialchars($evaluatorName) ?></p>
          <p><strong>Date of Evaluation: </strong> <?= date('F j, Y') ?></p>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 no-print">
          <a href="student-evaluate.php" class="btn btn-secondary">Back to Evaluation</a>
        </div>
        <div class="col-md-4 no-print">
          <button type="button" class="btn btn-secondary w-100" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Again
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

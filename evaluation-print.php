<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['print_data'])) {
  header("Location: student-evaluate.php");
  exit();
}

$data = $_SESSION['print_data']; // Use before unsetting
unset($_SESSION['print_data']); // Prevent reprint on refresh

$student_name = 'Unknown Student';
$student_id = $data['student_id'] ?? '';

if ($student_id) {
  $stu_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM student WHERE idnumber = ?");
  $stu_stmt->bind_param("s", $student_id);
  $stu_stmt->execute();
  $stu_result = $stu_stmt->get_result();

  if ($stu_result->num_rows > 0) {
    $stu = $stu_result->fetch_assoc();
    $student_name = $stu['first_name'] . ' ' . $stu['mid_name'] . ' ' . $stu['last_name'];
  }
}


$faculty_name = 'Unknown Faculty';
$faculty_id = $data['faculty_id'] ?? '';

if ($faculty_id) {
  $fac_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
  $fac_stmt->bind_param("s", $faculty_id);
  $fac_stmt->execute();
  $fac_result = $fac_stmt->get_result();

  if ($fac_result->num_rows > 0) {
    $fac = $fac_result->fetch_assoc();
    $faculty_name = $fac['first_name'] . ' ' . $fac['mid_name'] . ' ' . $fac['last_name'];
  }
}

// Questions list - must match the form
$questions = [
  "Comes to class on time regularly.",
  "Explains learning outcomes, expectations, grading system, and various requirements of the subject/course.",
  "Maximizes the allocated time/learning hours effectively.",
  "Facilitates students to think critically and creatively by providing appropriate learning activities.",
  "Guides students to learn on their own, reflect on new ideas and experiences, and make decisions in accomplishing given tasks.",
  "Communicates constructive feedback to students for their academic growth.",
  "Demonstrates extensive and broad knowledge of the subject/course.",
  "Simplifies complex ideas in the lesson for ease of understanding.",
  "Relates the subject matter to contemporary issues and developments in the discipline and/or daily life activities.",
  "Promotes active learning and student engagement by using appropriate teaching and learning resources including ICT Tools and platforms.",
  "Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes",
  "Recognizes and values the unique diversity and individuality difference among students.",
  "Assist students with their learning challenges during consultation hours.",
  "Provide immediate feedback on student outputs and performance.",
  "Provides transparent and clear criteria in rating student's performance."
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Evaluation Summary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media print {
      .no-print {
        display: none !important;
      }

      body {
        background: #fff;
        margin: 0;
        padding: 0;
        font-size: 15px;
        line-height: 1.5;
      }

      .container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 1.2cm;
      }

      .card,
      .card-body {
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
      }

      .table {
        width: 100%;
        table-layout: fixed;
      }

      .table th,
      .table td {
        font-size: 14px;
        padding: 10px;
        word-wrap: break-word;
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

      h3 {
        font-size: 22px;
      }

      .p-2 {
        padding: 0.5rem !important;
      }

      .mb-1,
      .mb-3,
      .mb-4 {
        margin-bottom: 0.8rem !important;
      }
    }
  </style>

</head>

<body onload="window.print()">

  <div class="container">
    <div class="card shadow my-4">
      <div class="card-body">
        <div class="text-center mb-4">
          <h3 class="fw-bold text-primary">Faculty Evaluation Summary</h3>
        </div>

        <div class="row mb-3">
          <div class="col-12">
            <p><strong>Name of Faculty being Evaluated:</strong> <span class="text-capitalize fw-bold"><?= htmlspecialchars($faculty_name) ?></span></p>
            <p><strong>Department/College:</strong> <?= htmlspecialchars($data['department']) ?></p>
            <p><strong>Subject Code/Title:</strong> <?= htmlspecialchars($data['subject_code'] . "/" . $data['subject_title']) ?></p>
            <p><strong>Academic Year:</strong> <?= htmlspecialchars($data['academic_year']) ?></p>
          </div>
        </div>

        <div class="table-responsive mb-4">
          <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
              <tr>
                <th class="text-start">Benchmark Statement</th>
                <th>Rating (1-5)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($questions as $index => $question): ?>
                <tr>
                  <td class="text-start"><?= ($index + 1) . ". " . htmlspecialchars($question) ?></td>
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
            <p><strong>Signature of Evaluator: </strong>__________________________________</p>
            <p><strong>Name of Evaluator/ID Number: </strong> <?= htmlspecialchars($student_name . " / " . $student_id) ?></p>
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
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
function getFacultyName($conn, $id)
{
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

$verification = [
  "• Daily time record<br>• Faculty schedule and timetable<br>• Informal interview with students",
  '• Documents submission log<br>• Submission Receipts or Acknowledgment Emails',
  '• Course syllabus<br>• Learning Plan<br>• Classroom Observation<br>• Informal interview with students<br>• LMS Logs',
  '• Course Syllabus<br>• LMS Logs<br>• Informal interview with students',
  '• Course Syllabus<br>• Learning Plan<br>• Student Work Samples<br>• Classroom Observation<br>• LMS Logs<br>• Informal interview with students<br>• Faculty Consultation Log<br>',
  '• Graded Student Work with Feedback<br>• Faculty Consultation Log<br>• Informal interview with students<br>• Emails or Official correspondence<br>• LMS Logs<br>•',
  '• Course Syllabus<br>• Learning Plan<br>• IMs developed by the faculty<br>• Informal interview with students<br>• Mentorship or Thesis/Dissertation Advisory records',
  '• Learning Plan<br>• Course Syllabus<br>• Classroom Observation<br>• Informal interview with students<br>• Lecture notes and presentations<br>• LMS Logs',
  '• Course syllabus<br>• Learning Plan<br>• Classroom Observation<br>• LMS Logs<br>• IMs developed by the faculty<br>• Participation in Conferences, Webinars, and Training',
  '• Course Syllabus<br>• Learning Plan<br>• Classroom Observation<br>• Informal interview with students<br>• LMS Logs<br>• Multimedia Lecture Materials<br>• Student Work Samples<br>',
  '• Course Syllabus<br>• Learning Plan<br>• Informal interview with students<br>• Assessment tools and rubrics<br>• Exam and Quiz Samples<br>• Graded Student Work Samples<br>• LMS records',
  '• Course Syllabus<br>• Learning Plan<br>• IMs developed by the faculty<br>• Classroom Observation <br>•  Informal interview with students',
  '• Course Syllabus<br>• Faculty Consultation Log<br>• Advisory Records<br>• LMS Logs<br>• Emails or Official Correspondence',
  '• Graded Student Work Samples<br>• Assessment tools and rubrics<br>• Informal interview with students<br>• LMS Logs<br>• Emails or Official Correspondence<br>• Faculty Consultation Log<br>• Advising Reports',
  '• Course Syllabus<br>• Learning Plan<br>• Classroom Observation<br>• Informal interview with students<br>• LMS Logs<br>• Multimedia Lecture Materials<br>• Student Work Samples<br>'
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
        font-size: 15px;
        line-height: 1.5;
        margin: 0;
        padding: 0;
        background-color: #fff;
      }

      .container {
        width: 100%;
        max-width: 100%;
        padding: 1.2cm;
      }

      .card,
      .card-body {
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
      }

      h3 {
        font-size: 22px;
      }

      .table {
        table-layout: fixed;
        width: 100%;
      }

      .table th,
      .table td {
        padding: 10px;
        font-size: 14px;
        word-wrap: break-word;
      }

      .table th:first-child,
      .table td:first-child {
        width: 45%;
        text-align: left;
      }

      .table th:nth-child(2),
      .table td:nth-child(2) {
        width: 40%;
        text-align: left;
      }

      .table th:last-child,
      .table td:last-child {
        width: 15%;
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
          <h3 class="fw-bold text-primary">Supervisor-to-Faculty Evaluation Summary</h3>
        </div>

        <p><strong>Evaluatee:</strong> <?= htmlspecialchars($evaluateeName) ?></p>
        <p><strong>Academic Rank:</strong> <?= htmlspecialchars($data['faculty_rank']) ?></p>
        <p><strong>College:</strong> <?= htmlspecialchars($data['department']) ?></p>
        <p><strong>Rating Period(Academic Year):</strong> <?= htmlspecialchars($data['academic_year']) ?></p>

        <div class="table-responsive mb-4">
          <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
              <tr>
                <th class="text-start">Question</th>
                <th>Suggested Means of Verification</th>
                <th>Rating</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($questions as $index => $question): ?>
                <tr>
                  <td class="text-start"><?= ($index + 1) . '. ' . htmlspecialchars($question) ?></td>
                  <td class="text-start"><?= $verification[$index] ?></td>
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
            <p><strong>Position of Supervisor: </strong> <?= htmlspecialchars($data['evaluator_position'] ?? 'Not Set') ?></p>
            <p><strong>Date of Evaluation: </strong> <?= date('F j, Y') ?></p>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 no-print">
            <a href="admin-evaluate.php" class="btn btn-secondary">Back to Evaluation</a>
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
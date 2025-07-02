<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['peer_print_data'])) {
    header("Location: faculty-peer-evaluate.php");
    exit();
}

$data = $_SESSION['peer_print_data'];
unset($_SESSION['peer_print_data']); // Prevent reprint on refresh

// Fetch faculty names
function getFacultyName($conn, $id) {
    $stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM register WHERE idnumber = ?");
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
    "Demonstrates professional behavior in the workplace.",
    "Collaborates well with other faculty members.",
    "Is open to feedback and professional improvement.",
    "Shares knowledge and resources with colleagues.",
    "Contributes positively to department goals.",
    "Is punctual and consistent in work commitments."
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

      <p><strong>Evaluator:</strong> <?= htmlspecialchars($evaluatorName) ?></p>
      <p><strong>Evaluatee:</strong> <?= htmlspecialchars($evaluateeName) ?></p>
      <p><strong>School Year:</strong> <?= htmlspecialchars($data['school_year']) ?></p>
      <p><strong>Semester:</strong> <?= htmlspecialchars($data['semester']) ?></p>
      <p><strong>Average Rating:</strong> <?= htmlspecialchars($data['average_rating']) ?> / 5</p>

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

      <?php if (!empty($data['comment'])): ?>
        <div class="mb-3">
          <h6><strong>Additional Comment:</strong></h6>
          <p class="border rounded p-2"><?= nl2br(htmlspecialchars($data['comment'])) ?></p>
        </div>
      <?php endif; ?>

      <div class="row no-print">
        <div class="col-md-6">
          <a href="faculty-peer-evaluate.php" class="btn btn-secondary">Back to Evaluation</a>
        </div>
        <div class="col-md-6 text-end">
          <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer"></i> Print
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['print_data'])) {
    header("Location: student-evaluate.php");
    exit();
}

$data = $_SESSION['print_data'];
unset($_SESSION['print_data']); // Prevent reprint on refresh


$faculty_name = '';
$faculty_id = $data['faculty_id'];

$fac_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM register WHERE idnumber = ?");
$fac_stmt->bind_param("s", $faculty_id);
$fac_stmt->execute();
$fac_result = $fac_stmt->get_result();

if ($fac_result->num_rows > 0) {
    $fac = $fac_result->fetch_assoc();
    $faculty_name = $fac['first_name'] . ' ' . $fac['mid_name'] . ' ' . $fac['last_name'];
} else {
    $faculty_name = 'Unknown Faculty';
}


// Define the questions (same order as in your form)
$questions = [
  "The instructor demonstrates mastery of the subject.",
  "The instructor encourages student participation.",
  "The instructor communicates clearly.",
  "The instructor is fair in grading.",
  "The instructor is punctual and prepared.",
  "The instructor provides timely feedback on assignments."
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Evaluation Print</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
      @media print {
    .no-print {
      display: none !important;
    }

    body {
      background-color: #fff;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 100% !important;
      max-width: none !important;
      padding: 0 1cm !important;
    }

    .card {
      border: none !important;
      box-shadow: none !important;
    }

    .card-body {
      font-size: 14px;
    }

    .card-body p {
      text-align: left;
      margin: 4px 0;
    }

    .table {
      width: 100% !important;
      table-layout: fixed;
    }

    .table th:first-child,
    .table td:first-child {
      width: 80%;  /* Question column */
      text-align: left;
    }

    .table th:last-child,
    .table td:last-child {
      width: 20%;  /* Rating column */
      text-align: center;
    }

    .table th, .table td {
      padding: 10px;
      font-size: 13px;
    }
  }

  </style>



  
</head>
<body onload="window.print()">

<div class="container">
  <div class="card shadow">
    <div class="card-body">

      <div class="text-center mb-4">
        <h3 class="fw-bold text-primary">Faculty Evaluation Summary</h3>
      </div>

      <div class="row mb-3">
        <div class="col-12">
          <p><strong>Student ID:</strong> <?= htmlspecialchars($data['student_id']) ?></p>
          <p><strong>School Year:</strong> <?= htmlspecialchars($data['school_year']) ?></p>
          <p><strong>Semester:</strong> <?= htmlspecialchars($data['semester']) ?></p>
          <p><strong>Subject Code:</strong> <?= htmlspecialchars($data['subject_code']) ?></p>
          <p><strong>Descriptive Title:</strong> <?= htmlspecialchars($data['subject_title']) ?></p>
          <p><strong>Instructor Name:</strong> <span class="text-capitalize fw-bold"><?= htmlspecialchars($faculty_name) ?></span></p>
        </div>
      </div>

      <div class="table-responsive mb-4">
        <table class="table table-bordered text-center align-middle">
          <thead class="table-light">
            <tr>
              <th class="text-start">Question</th>
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

      <?php if (!empty($data['comment'])): ?>
        <div class="mb-3">
          <h6><strong>Additional Comment:</strong></h6>
          <p class="border rounded p-2"><?= nl2br(htmlspecialchars($data['comment'])) ?></p>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-4 no-print">
            <a href="student-evaluate.php" class="btn btn-secondary">Back to Evaluation</a>
        </div>

        <!-- Print Button -->
        <div class="col-md-4 mb-3 no-print">
        <button type="button" class="btn btn-secondary btn-block w-50" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Form
        </button>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

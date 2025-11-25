<?php
session_start();
include 'conn/conn.php';

// ✅ Check if evaluator is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

// ✅ Function to log activity
function logActivity($conn, $user_id, $role, $action)
{
  $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, role, activity) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $user_id, $role, $action);
  $stmt->execute();
  $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $evaluator_id       = $_SESSION['idnumber'];
  $evaluatee_id       = $_POST['evaluatee_id'] ?? '';
  $academic_year      = $_POST['academic_year'] ?? '';
  $semester           = $_POST['semester'] ?? '';
  $comments           = $_POST['comments'] ?? '';
  $evaluator_position = $_POST['evaluator_position'] ?? '';

  // ✅ Get the faculty's actual college (fix)
  $faculty_college = 'Unknown';
  $dept_stmt = $conn->prepare("SELECT college FROM faculty WHERE idnumber = ?");
  $dept_stmt->bind_param("s", $evaluatee_id);
  $dept_stmt->execute();
  $dept_result = $dept_stmt->get_result();
  if ($dept_row = $dept_result->fetch_assoc()) {
    $faculty_college = $dept_row['college'];
  }
  $dept_stmt->close();

  // ✅ Collect scores for all 15 questions
  $total_score = 0;
  $num_questions = 0;
  $questions_data = [];
  for ($i = 0; $i < 15; $i++) {
    $question_name = "q" . $i;
    if (isset($_POST[$question_name])) {
      $score = intval($_POST[$question_name]);
      $total_score += $score;
      $questions_data[$question_name] = $score;
      $num_questions++;
    } else {
      $_SESSION['msg'] = "Please answer all evaluation questions.";
      header("Location: admin-evaluate.php");
      exit();
    }
  }

  if ($num_questions !== 15) {
    $_SESSION['msg'] = "Evaluation failed: All 15 questions must be answered.";
    header("Location: admin-evaluate.php");
    exit();
  }

  $computed_rating = round(($total_score / ($num_questions * 5)) * 100, 2);

  // ✅ Prevent duplicate evaluation
  $check_query = "SELECT 1 FROM admin_evaluation
                    WHERE evaluator_id = ? AND evaluatee_id = ? AND academic_year = ? AND semester = ?";
  $stmt_check = $conn->prepare($check_query);
  $stmt_check->bind_param("ssss", $evaluator_id, $evaluatee_id, $academic_year, $semester);
  $stmt_check->execute();
  $result_check = $stmt_check->get_result();

  if ($result_check->num_rows > 0) {
    $_SESSION['msg'] = "You have already evaluated this faculty member for this school year and semester.";
    header("Location: admin-evaluate.php");
    exit();
  }
  $stmt_check->close();

  // ✅ Insert into admin_evaluation
  $insert_query = "INSERT INTO admin_evaluation
        (evaluator_id, evaluatee_id, evaluator_position, academic_year, semester, total_score, computed_rating, comments, college)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt_insert = $conn->prepare($insert_query);
  $stmt_insert->bind_param(
    "sssssidss",
    $evaluator_id,
    $evaluatee_id,
    $evaluator_position,
    $academic_year,
    $semester,
    $total_score,
    $computed_rating,
    $comments,
    $faculty_college // ✅ fixed: use the faculty's actual college
  );

  if ($stmt_insert->execute()) {
    $form_data_json = json_encode($questions_data);

    // ✅ Also save detailed evaluation data
    $insert_submissions_query = "INSERT INTO admin_evaluation_submissions
            (evaluator_id, evaluatee_id, semester, academic_year, total_score, rating_percent, comment, form_data)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_submissions = $conn->prepare($insert_submissions_query);
    $stmt_submissions->bind_param(
      "ssssidss",
      $evaluator_id,
      $evaluatee_id,
      $semester,
      $academic_year,
      $total_score,
      $computed_rating,
      $comments,
      $form_data_json
    );
    $stmt_submissions->execute();
    $stmt_submissions->close();

    // ✅ Log activity
    $faculty_stmt = $conn->prepare("SELECT first_name, mid_name, last_name, faculty_rank FROM faculty WHERE idnumber = ?");
    $faculty_stmt->bind_param("s", $evaluatee_id);
    $faculty_stmt->execute();
    $faculty_res = $faculty_stmt->get_result();
    $faculty_row = $faculty_res->fetch_assoc();
    $faculty_stmt->close();

    $faculty_full_name = $faculty_row
      ? trim($faculty_row['first_name'] . ' ' . (!empty($faculty_row['mid_name']) ? substr($faculty_row['mid_name'], 0, 1) . '. ' : '') . $faculty_row['last_name'])
      : $evaluatee_id;

    $activity_message = "Evaluated Faculty: $faculty_full_name for $academic_year $semester";
    logActivity($conn, $evaluator_id, $_SESSION['role'], $activity_message);

    // ✅ Prepare success sessions
    $_SESSION['admin_eval_success'] = true;
    $_SESSION['last_evaluated_faculty_id'] = $evaluatee_id;
    $_SESSION['admin_print_data'] = [
      'evaluator_id'      => $evaluator_id,
      'evaluatee_id'      => $evaluatee_id,
      'academic_year'     => $academic_year,
      'semester'          => $semester,
      'comment'           => $comments,
      'evaluator_position' => $evaluator_position,
      'college'        => $faculty_college,
      'faculty_rank'      => $faculty_row['faculty_rank'] ?? 'N/A',
      'answers'           => $questions_data,
      'total_score'       => $total_score,
      'computed_rating'   => $computed_rating
    ];
  } else {
    $_SESSION['msg'] = "Error submitting evaluation: " . $stmt_insert->error;
  }

  $stmt_insert->close();
} else {
  $_SESSION['msg'] = "Invalid request method.";
}

header("Location: admin-evaluate.php");
exit();
?>

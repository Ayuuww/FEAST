<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if evaluator is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

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

  $faculty_college = 'Unknown';
  $faculty_rank = 'N/A';
  $faculty_fname = '';
  $faculty_mname = '';
  $faculty_lname = '';

  $dept_stmt = $conn->prepare("SELECT first_name, mid_name, last_name, college, faculty_rank FROM faculty WHERE idnumber = ?");
  $dept_stmt->bind_param("s", $evaluatee_id);
  $dept_stmt->execute();
  $dept_result = $dept_stmt->get_result();
  if ($dept_row = $dept_result->fetch_assoc()) {
    $faculty_college = $dept_row['college'];
    $faculty_rank = $dept_row['faculty_rank'];
    $faculty_fname = $dept_row['first_name'];
    $faculty_mname = $dept_row['mid_name'];
    $faculty_lname = $dept_row['last_name'];
  }
  $dept_stmt->close();

  // ==========================================
  // DYNAMIC SCORING LOGIC (TEMPLATE AWARE)
  // ==========================================

  // ✅ Get highest rating scale dynamically
  $scale_res = $conn->query("SELECT MAX(scale_value) as max_val FROM evaluation_rating_scales");
  $max_rating_value = $scale_res->fetch_assoc()['max_val'] ?? 5;

  // ✅ Fetch ONLY active questions from the CURRENTLY EQUIPPED SEF Template
  $active_questions = [];
  $q_res = $conn->query("
      SELECT q.id 
      FROM admin_evaluation_questions q
      JOIN admin_evaluation_categories c ON q.category_id = c.id
      JOIN sef_templates t ON c.template_id = t.id
      WHERE q.status = 'active' AND t.is_equipped = 1
  ");

  if ($q_res) {
    while ($row = $q_res->fetch_assoc()) {
      $active_questions[] = $row['id'];
    }
  }

  $total_questions = count($active_questions);

  if ($total_questions === 0) {
    $_SESSION['msg'] = "Evaluation failed: The administrator has not set up any SEF evaluation questions in the Active Rubric.";
    header("Location: admin-evaluate.php");
    exit();
  }

  // ✅ Compute max possible score based on dynamic rating
  $max_possible_score = $total_questions * $max_rating_value;
  $questions_data = [];
  $total_score = 0;

  foreach ($active_questions as $q_id) {
    $post_key = "q_" . $q_id; // Score
    $v_key    = "v_" . $q_id; // Checkboxes

    if (!isset($_POST[$post_key])) {
      $_SESSION['msg'] = "Please answer all required evaluation questions.";
      header("Location: admin-evaluate.php");
      exit();
    }

    $score = intval($_POST[$post_key]);
    $selected_verifications = $_POST[$v_key] ?? [];

    $questions_data[$post_key] = $score;
    $questions_data[$v_key] = $selected_verifications;

    $total_score += $score;
  }

  $computed_rating = round(($total_score / $max_possible_score) * 100, 2);

  // ✅ Snapshot the math data so future scale changes don't break this record
  $questions_data['metadata'] = [
    'max_scale' => $max_rating_value,
    'max_score' => $max_possible_score
  ];

  $form_data_json = json_encode($questions_data);

  try {
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

    $insert_query = "INSERT INTO admin_evaluation 
            (evaluator_id, evaluatee_id, evaluator_position, academic_year, semester, total_score, computed_rating, comments, college, answers) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param(
      "sssssidsss",
      $evaluator_id,
      $evaluatee_id,
      $evaluator_position,
      $academic_year,
      $semester,
      $total_score,
      $computed_rating,
      $comments,
      $faculty_college,
      $form_data_json
    );
    $stmt_insert->execute();
    $stmt_insert->close();

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

    $faculty_full_name = trim($faculty_fname . ' ' . (!empty($faculty_mname) ? substr($faculty_mname, 0, 1) . '. ' : '') . $faculty_lname);
    if (empty($faculty_full_name)) $faculty_full_name = $evaluatee_id;

    $activity_message = "Evaluated Faculty: $faculty_full_name for $academic_year $semester";
    logActivity($conn, $evaluator_id, $_SESSION['role'], $activity_message);

    $_SESSION['admin_eval_success'] = true;
    $_SESSION['last_evaluated_faculty_id'] = $evaluatee_id;
    $_SESSION['admin_print_data'] = [
      'evaluator_id'       => $evaluator_id,
      'evaluatee_id'       => $evaluatee_id,
      'academic_year'      => $academic_year,
      'semester'           => $semester,
      'comment'            => $comments,
      'evaluator_position' => $evaluator_position,
      'college'            => $faculty_college,
      'faculty_rank'       => $faculty_rank,
      'answers'            => $questions_data,
      'total_score'        => $total_score,
      'computed_rating'    => $computed_rating
    ];
  } catch (Exception $e) {
    error_log("SEF Evaluation Submission Error: " . $e->getMessage());
    $_SESSION['msg'] = "Error submitting evaluation. Please try again.";
  }
} else {
  $_SESSION['msg'] = "Invalid request method.";
}

header("Location: admin-evaluate.php");
exit();

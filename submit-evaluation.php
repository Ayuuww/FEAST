<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if student is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
  header("Location: pages-login.php");
  exit();
}

// Get current semester and year from settings
$setting_stmt = $conn->prepare("SELECT semester, academic_year FROM evaluation_settings WHERE id = 1 LIMIT 1");
$setting_stmt->execute();
$setting_result = $setting_stmt->get_result();
$setting_row = $setting_result->fetch_assoc();
$current_semester = $setting_row['semester'];
$current_year = $setting_row['academic_year'];
$setting_stmt->close();

// Sanitize inputs
$student_id     = $_SESSION['idnumber'];
$academic_year  = $_POST['academic_year'] ?? $current_year;
$semester       = $_POST['semester'] ?? $current_semester;
$college        = $_POST['college'] ?? '';
$comment        = $_POST['comment'] ?? '';
$is_anonymous   = $_POST['is_anonymous'] ?? 'no';

// Split subject and faculty
$subject_parts = explode('|', $_POST['subject_code'] ?? '');
if (count($subject_parts) < 2) {
  $_SESSION['error_message'] = "Invalid subject selection. Please try again.";
  header("Location: student-evaluate.php");
  exit();
}
$subject_code   = $subject_parts[0];
$faculty_id     = $subject_parts[1];

// Get subject title
$subject_title = '';
$sub_stmt = $conn->prepare("SELECT title FROM subject WHERE code = ?");
$sub_stmt->bind_param("s", $subject_code);
$sub_stmt->execute();
$sub_stmt->bind_result($subject_title);
$sub_stmt->fetch();
$sub_stmt->close();

// Get student section
$student_section = '';
$sec_stmt = $conn->prepare("SELECT section FROM student WHERE idnumber = ?");
$sec_stmt->bind_param("s", $student_id);
$sec_stmt->execute();
$sec_stmt->bind_result($student_section);
$sec_stmt->fetch();
$sec_stmt->close();


// ==========================================
// DYNAMIC SCORING LOGIC
// ==========================================

// ✅ Get highest rating scale dynamically
$scale_res = $conn->query("SELECT MAX(scale_value) as max_val FROM evaluation_rating_scales");
$max_rating_value = $scale_res->fetch_assoc()['max_val'] ?? 5;

// Fetch all currently active questions from the database
$active_questions = [];
$q_res = $conn->query("SELECT id FROM evaluation_questions WHERE status = 'active'");
while ($row = $q_res->fetch_assoc()) {
  $active_questions[] = $row['id'];
}

$total_questions = count($active_questions);

if ($total_questions === 0) {
  $_SESSION['error_message'] = "Evaluation failed: The administrator has not set up any evaluation questions yet.";
  header("Location: student-evaluate.php");
  exit();
}

// ✅ Compute max possible score based on dynamic rating
$max_possible_score = $total_questions * $max_rating_value;
$answers = [];
$total_score = 0;

// Loop strictly through active question IDs to extract POST data
foreach ($active_questions as $q_id) {
  $post_key = "q_" . $q_id; // e.g., $_POST['q_1']

  if (!isset($_POST[$post_key])) {
    $_SESSION['error_message'] = "Please answer all required evaluation questions.";
    header("Location: student-evaluate.php");
    exit();
  }

  $val = intval($_POST[$post_key]);
  $answers[$post_key] = $val;
  $total_score += $val;
}

// Compute dynamic rating percentage
$computed_rating = ($total_score / $max_possible_score) * 100;

// Convert the dynamic associative array to JSON for database storage
$answers_json = json_encode($answers);


// Function to log activity
function logActivity($conn, $user_id, $role, $action)
{
  $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, role, activity) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $user_id, $role, $action);
  $stmt->execute();
  $stmt->close();
}

try {
  // Check for duplicate submission
  $check_query = "SELECT 1 FROM evaluation 
                  WHERE student_id = ? AND faculty_id = ? AND subject_code = ? AND academic_year = ? AND semester = ?";
  $stmt_check = $conn->prepare($check_query);
  $stmt_check->bind_param("sssss", $student_id, $faculty_id, $subject_code, $academic_year, $semester);
  $stmt_check->execute();
  $result_check = $stmt_check->get_result();

  if ($result_check->num_rows > 0) {
    $_SESSION['error_message'] = "You have already evaluated this subject and faculty for this academic year and semester.";
    header("Location: student-evaluate.php");
    exit();
  }
  $stmt_check->close();

  // Insert into main evaluation table
  $stmt = $conn->prepare("INSERT INTO evaluation (
            student_id, faculty_id, subject_code, subject_title,
            college, academic_year, semester,
            total_score, computed_rating, comment, student_section, is_anonymous
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $stmt->bind_param(
    "sssssssddsss",
    $student_id,
    $faculty_id,
    $subject_code,
    $subject_title,
    $college,
    $academic_year,
    $semester,
    $total_score,
    $computed_rating,
    $comment,
    $student_section,
    $is_anonymous
  );
  $stmt->execute();
  $stmt->close();

  // Fetch faculty name for logging
  $faculty_name = '';
  $fac_stmt = $conn->prepare("SELECT first_name, mid_name, last_name, faculty_rank FROM faculty WHERE idnumber = ?");
  $fac_stmt->bind_param("s", $faculty_id);
  $fac_stmt->execute();
  $fac_stmt->bind_result($fname, $mname, $lname, $rank);
  if ($fac_stmt->fetch()) {
    $faculty_name = trim("$fname $mname $lname");
  }
  $fac_stmt->close();

  // Log the activity
  $rounded_rating = round($computed_rating, 2);
  logActivity($conn, $student_id, 'student', "Rated {$rounded_rating}% for {$subject_code} handled by {$faculty_name}");

  // Insert into archive table (student_evaluation_submissions)
  $archive_stmt = $conn->prepare("INSERT INTO student_evaluation_submissions (
            student_id, subject_code, faculty_id, college,
            academic_year, semester, answers,
            total_score, computed_rating, comment, is_anonymous
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $archive_stmt->bind_param(
    "sssssssidss",
    $student_id,
    $subject_code,
    $faculty_id,
    $college,
    $academic_year,
    $semester,
    $answers_json,
    $total_score,
    $computed_rating,
    $comment,
    $is_anonymous
  );
  $archive_stmt->execute();
  $archive_stmt->close();

  // Set session variables for SweetAlert and printing
  $_SESSION['evaluation_success'] = true;

  // Save all evaluation info for printing
  $_SESSION['print_data'] = [
    'student_id' => $student_id,
    'faculty_id' => $faculty_id,
    'subject_code' => $subject_code,
    'academic_year' => $academic_year,
    'college' => $college,
    'is_anonymous' => $is_anonymous,
    'answers' => $answers,
    'total_score' => $total_score,
    'computed_rating' => $computed_rating,
    'comment' => $comment,
  ];

  header("Location: student-evaluate.php");
  exit();
} catch (mysqli_sql_exception $e) {
  error_log("Student Evaluation Error: " . $e->getMessage());
  $_SESSION['error_message'] = "An error occurred during evaluation submission. Please try again later.";
  header("Location: student-evaluate.php");
  exit();
} finally {
  if (isset($conn)) {
    $conn->close();
  }
}

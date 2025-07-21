<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if student is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
    header("Location: pages-login.php");
    exit();
}

// Get current semester and year
$setting = mysqli_query($conn, "SELECT semester, academic_year FROM evaluation_settings WHERE id = 1");
$row = mysqli_fetch_assoc($setting);
$current_semester = $row['semester'];
$current_year = $row['academic_year'];

// Sanitize inputs
$student_id    = mysqli_real_escape_string($conn, $_SESSION['idnumber']);
$academic_year = mysqli_real_escape_string($conn, $_POST['academic_year']);
$semester      = mysqli_real_escape_string($conn, $_POST['semester'] ?? '');
$department    = mysqli_real_escape_string($conn, $_POST['department']);
$comment       = mysqli_real_escape_string($conn, $_POST['comment'] ?? '');

// Split subject and faculty from dropdown
$subject_parts = explode('|', $_POST['subject_code']);
$subject_code  = mysqli_real_escape_string($conn, $subject_parts[0]);
$faculty_id    = mysqli_real_escape_string($conn, $subject_parts[1]);

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

// Collect answers and calculate score
$answers = [];
$total_score = 0;
for ($i = 0; $i < 15; $i++) {
    $qkey = "q$i";
    $val = intval($_POST[$qkey] ?? 0);
    $answers[$qkey] = $val;
    $total_score += $val;
}

if (count($answers) !== 15) {
    $_SESSION['msg'] = "Please answer all 15 questions.";
    header("Location: student-evaluate.php");
    exit();
}

$computed_rating = ($total_score / 75) * 100;
$answers_json = json_encode($answers);

// Define the function FIRST
function logActivity($conn, $user_id, $role, $action)
{
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, role, activity) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_id, $role, $action);
    $stmt->execute();
}

try {
    // Insert into main evaluation table
    $stmt = $conn->prepare("INSERT INTO evaluation (
        student_id, faculty_id, subject_code, subject_title,
        department, academic_year, semester,
        total_score, computed_rating, comment, student_section
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssssssddss",
        $student_id,
        $faculty_id,
        $subject_code,
        $subject_title,
        $department,
        $academic_year,
        $semester,
        $total_score,
        $computed_rating,
        $comment,
        $student_section
    );
    $stmt->execute();

    // Fetch faculty name
    $faculty_name = '';
    $fac_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
    $fac_stmt->bind_param("s", $faculty_id);
    $fac_stmt->execute();
    $fac_stmt->bind_result($fname, $mname, $lname);
    if ($fac_stmt->fetch()) {
        $faculty_name = trim("$fname $mname $lname");
    }
    $fac_stmt->close();

    // Then log the activity
    $rounded_rating = round($computed_rating, 2);
    logActivity($conn, $student_id, 'student', "Rated {$rounded_rating}% for {$subject_code} handled by {$faculty_name}");

    // Insert full answer data into archive table
    $archive_stmt = $conn->prepare("INSERT INTO student_evaluation_submissions (
        student_id, subject_code, faculty_id, department,
        academic_year, semester, answers,
        total_score, computed_rating, comment
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $archive_stmt->bind_param(
        "sssssssids",
        $student_id,
        $subject_code,
        $faculty_id,
        $department,
        $academic_year,
        $semester,
        $answers_json,
        $total_score,
        $computed_rating,
        $comment
    );

    $archive_stmt->execute();

    // Update evaluated status in student_subject table
    $update_stmt = $conn->prepare("UPDATE student_subject SET evaluated = 'yes' WHERE student_id = ? AND subject_code = ? AND faculty_id = ?");
    $update_stmt->bind_param("sss", $student_id, $subject_code, $faculty_id);
    $update_stmt->execute();

    // Store data for reprint
    $_SESSION['print_data'] = [
        'student_id'      => $student_id,
        'faculty_id'      => $faculty_id,
        'subject_code'    => $subject_code,
        'subject_title'   => $subject_title,
        'department'      => $department,
        'academic_year'   => $academic_year,
        'semester'        => $semester,
        'total_score'     => $total_score,
        'computed_rating' => $computed_rating,
        'comment'         => $comment,
        'answers'         => $answers
    ];

    $_SESSION['evaluation_success'] = true;
    header("Location: student-evaluate.php");
    exit();
    
} catch (mysqli_sql_exception $e) {
    if (str_contains($e->getMessage(), 'Duplicate entry')) {
        $_SESSION['error_message'] = "You've already submitted an evaluation for this subject and semester.";
    } else {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    header("Location: student-evaluate.php");
    exit();
}

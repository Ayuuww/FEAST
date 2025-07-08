<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if student is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
    header("Location: pages-login.php");
    exit();
}

// Sanitize inputs
$student_id    = mysqli_real_escape_string($conn, $_POST['student_id']);
$academic_year = mysqli_real_escape_string($conn, $_POST['academic_year']);
$semester      = mysqli_real_escape_string($conn, $_POST['semester'] ?? '');
$department    = mysqli_real_escape_string($conn, $_POST['department']);
$comment       = mysqli_real_escape_string($conn, $_POST['comment'] ?? '');

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

// Compute score
$total_score = 0;
$question_count = 0;

for ($i = 0; $i < 15; $i++) {
    $key = 'q' . $i;
    if (isset($_POST[$key])) {
        $total_score += (int)$_POST[$key];
        $question_count++;
    }
}

if ($question_count !== 15) {
    $_SESSION['msg'] = "Please answer all 15 questions.";
    header("Location: student-evaluate.php");
    exit();
}

$percentage = ($total_score / 75) * 100;

try {
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
        $percentage,
        $comment,
        $student_section
    );

    $stmt->execute();

    // Store data for print
    $_SESSION['print_data'] = [
        'student_id'      => $student_id,
        'faculty_id'      => $faculty_id,
        'subject_code'    => $subject_code,
        'subject_title'   => $subject_title,
        'department'      => $department,
        'academic_year'   => $academic_year,
        'semester'        => $semester,
        'total_score'     => $total_score,
        'computed_rating' => $percentage,
        'comment'         => $comment,
        'answers'         => []
    ];

    for ($i = 0; $i < 15; $i++) {
        $_SESSION['print_data']['answers']["q$i"] = $_POST["q$i"] ?? null;
    }

    header("Location: evaluation-print.php");
    exit();

} catch (mysqli_sql_exception $e) {
    if (str_contains($e->getMessage(), 'Duplicate entry')) {
        $_SESSION['error_message'] = "You've already submitted an evaluation for this subject and semester.";
    } else {
        $_SESSION['error_message'] = "An unexpected error occurred: " . $e->getMessage();
    }
    header("Location: error-page.php");
    exit();
}
?>

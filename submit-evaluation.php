<?php
session_start();
include 'conn/conn.php';

list($subject_code, $faculty_id) = explode('|', $_POST['subject_code']); // Correct values from dropdown
$student_id    = $_POST['student_id'];
$comment       = $_POST['comment'] ?? '';
$semester      = $_POST['semester'];
$school_year   = $_POST['school_year'];
$total_rating  = 0;
$question_count = 6;

// Calculate total score
for ($i = 0; $i < $question_count; $i++) {
    if (!isset($_POST["q$i"])) {
        die("Please answer all questions.");
    }
    $total_rating += intval($_POST["q$i"]);
}

$average_rating = round($total_rating / $question_count, 1);

// ✅ Get subject title by subject_code AND faculty_id
$subjectQuery = "SELECT title FROM subject WHERE code = ? AND faculty_id = ?";
$stmt = $conn->prepare($subjectQuery);
$stmt->bind_param("ss", $subject_code, $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Subject with this instructor not found.");
}

$subject_row = $result->fetch_assoc();
$subject_title = $subject_row['title'];

// Insert into evaluation table
$insert = "INSERT INTO evaluation (
    student_id, subject_code, subject_title, school_year, semester, faculty_id, rating, comment
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($insert);
$stmt->bind_param("ssssssds",
    $student_id,
    $subject_code,
    $subject_title,
    $school_year,
    $semester,
    $faculty_id,
    $average_rating,
    $comment
);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($stmt->execute()) {
        $_SESSION['print_data'] = [
            'student_id'    => $student_id,
            'faculty_id'    => $faculty_id,
            'subject_title' => $subject_title,
            'subject_code'  => $subject_code,
            'school_year'   => $school_year,
            'semester'      => $semester,
            'comment'       => $comment,
            'average'       => $average_rating,
            'answers'       => [],
        ];

        for ($i = 0; $i < $question_count; $i++) {
            $_SESSION['print_data']['answers']["q$i"] = $_POST["q$i"];
        }

        header('Location: evaluation-print.php');
        exit;
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        $_SESSION['msg'] = 'You have already submitted an evaluation for this subject.';
    } else {
        $_SESSION['msg'] = 'Error submitting evaluation: ' . $e->getMessage();
    }
    header('Location: student-evaluate.php');
    exit;
}
?>

<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if student is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
    header("Location: pages-login.php");
    exit();
}

require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// After successfully saving the evaluation responses to your `evaluation` table...

$student_id = $_SESSION['idnumber'];
$subject_code = $_POST['subject_code'];
$faculty_id = $_POST['faculty_id'];
$academic_year = $_POST['academic_year'];
$semester = $_POST['semester'];

// 1. Generate HTML for the PDF
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

// Example data you should retrieve from DB or POST:
$faculty_name = "John A. Doe";
$department = "Computer Science";
$subject_code = "CS101";
$subject_title = "Intro to Computing";
$academic_year = "2024-2025";
$answers = $_POST['answers'] ?? []; // associative like: ['q0' => 4, 'q1' => 5, ...]
$total_score = $_POST['total_score'] ?? 65;
$computed_rating = $_POST['computed_rating'] ?? 86.67;
$comment = $_POST['comment'] ?? '';
$student_name = $_SESSION['full_name'] ?? 'Anonymous';

$html = "
<div style='text-align: center; margin-bottom: 20px;'>
  <h2 style='color: #0d6efd;'>Faculty Evaluation Summary</h2>
</div>

<p><strong>Name of Faculty being Evaluated:</strong> " . htmlspecialchars($faculty_name) . "</p>
<p><strong>Department/College:</strong> " . htmlspecialchars($department) . "</p>
<p><strong>Subject Code/Title:</strong> " . htmlspecialchars("$subject_code / $subject_title") . "</p>
<p><strong>Academic Year:</strong> " . htmlspecialchars($academic_year) . "</p>

<table width='100%' border='1' cellspacing='0' cellpadding='5'>
  <thead>
    <tr style='background-color:#f8f9fa;'>
      <th align='left'>Benchmark Statement</th>
      <th>Rating (1-5)</th>
    </tr>
  </thead>
  <tbody>";
foreach ($questions as $index => $question) {
  $rating = isset($answers["q$index"]) ? htmlspecialchars($answers["q$index"]) : '-';
  $html .= "
    <tr>
      <td>" . ($index + 1) . ". " . htmlspecialchars($question) . "</td>
      <td align='center'>$rating</td>
    </tr>";
}
$html .= "
  </tbody>
</table>

<p><strong>Total Score:</strong> $total_score / 75</p>
<p><strong>Computed Rating:</strong> " . number_format($computed_rating, 2) . "%</p>";

if (!empty($comment)) {
  $html .= "
    <p><strong>Additional Comment:</strong></p>
    <p style='border:1px solid #ccc; padding:10px;'>" . nl2br(htmlspecialchars($comment)) . "</p>";
}

$html .= "
<p><strong>Signature of Evaluator:</strong> ________________________________</p>
<p><strong>Name of Evaluator/ID Number:</strong> " . htmlspecialchars("$student_name / $student_id") . "</p>
<p><strong>Date of Evaluation:</strong> " . date('F j, Y') . "</p>";

// 2. Generate PDF from HTML
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfOutput = $dompdf->output(); // get PDF as string

// 3. Store PDF as BLOB in DB
include 'conn/conn.php';
$stmt = $conn->prepare("INSERT INTO student_evaluation_submissions 
    (student_id, subject_code, faculty_id, academic_year, semester, pdf_blob)
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssb", $student_id, $subject_code, $faculty_id, $academic_year, $semester, $null);
$null = null;
$stmt->send_long_data(5, $pdfOutput);
$stmt->execute();
$stmt->close();

// Superadmin Set the default semester and academic year
$setting = mysqli_query($conn, "SELECT semester, academic_year FROM evaluation_settings WHERE id = 1");
$row = mysqli_fetch_assoc($setting);
$current_semester = $row['semester'];
$current_year = $row['academic_year'];


// Sanitize inputs
$student_id    = mysqli_real_escape_string($conn, $_POST['student_id']);
$academic_year = mysqli_real_escape_string($conn, $_POST['academic_year']);
$semester      = mysqli_real_escape_string($conn, $_POST['semester'] ?? '');
$department    = mysqli_real_escape_string($conn, $_POST['department']);
$comment       = mysqli_real_escape_string($conn, $_POST['comment'] ?? '');

// 
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

    // Update the student-subject table BEFORE redirect
    $update_eval_status = $conn->prepare("UPDATE student_subject SET evaluated = 'yes' WHERE student_id = ? AND subject_code = ? AND faculty_id = ?");
    $update_eval_status->bind_param("sss", $student_id, $subject_code, $faculty_id);
    $update_eval_status->execute();
    $update_eval_status->close();

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

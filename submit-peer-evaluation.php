<?php
session_start();
include 'conn/conn.php';

// Check if evaluator is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
    header("Location: pages-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $evaluator_id = $_POST['evaluator_id'];
    $evaluatee_id = $_POST['evaluatee_id'];
    $school_year  = $_POST['school_year'] ?? '';
    $semester     = $_POST['semester'] ?? '';
    $comment      = $_POST['comment'] ?? '';

    // Collect ratings
    $total_score   = 0;
    $num_questions = 0;
    $ratings       = [];

    for ($i = 0; isset($_POST["q$i"]); $i++) {
        $score         = intval($_POST["q$i"]);
        $ratings[]     = $score;
        $total_score  += $score;
        $num_questions++;
    }

    // Calculate average rating
    if ($num_questions > 0) {
        $average_rating = round($total_score / $num_questions, 1);
    } else {
        $_SESSION['msg'] = "Evaluation failed: No questions were answered.";
        header("Location: faculty-peer-evaluate.php");
        exit();
    }

    // Check for duplicate evaluations
    $check_query = "SELECT * FROM faculty_peer_evaluation 
                    WHERE evaluator_id = ? 
                      AND evaluated_faculty_id = ? 
                      AND school_year = ? 
                      AND semester = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ssss", $evaluator_id, $evaluatee_id, $school_year, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['msg'] = "You have already evaluated this faculty member for this school year and semester.";
        header("Location: faculty-peer-evaluate.php");
        exit();
    }

    // Insert evaluation
    $insert_query = "INSERT INTO faculty_peer_evaluation 
                     (evaluator_id, evaluated_faculty_id, school_year, semester, rating, comment)
                     VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssds", $evaluator_id, $evaluatee_id, $school_year, $semester, $average_rating, $comment);

    if ($stmt->execute()) {
    $_SESSION['peer_print_data'] = [
        'evaluator_id'    => $evaluator_id,
        'evaluatee_id'    => $evaluatee_id,
        'school_year'     => $school_year,
        'semester'        => $semester,
        'average_rating'  => $average_rating,
        'comment'         => $comment,
        'answers'         => $_POST  // includes q0, q1, ...
    ];
    header("Location: faculty-peer-evaluation-print.php");
    exit();
    } else {
        $_SESSION['msg'] = "Error submitting evaluation. Please try again.";
        header("Location: faculty-peer-evaluate.php");
        exit();
    }


} else {
    $_SESSION['msg'] = "Invalid request method.";
    header("Location: faculty-peer-evaluate.php");
    exit();
}
?>

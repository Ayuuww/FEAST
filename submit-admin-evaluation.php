<?php
session_start();
include 'conn/conn.php';

// Check if evaluator is logged in
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    header("Location: pages-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $evaluator_id = $_SESSION['idnumber']; // use session for security
    $evaluatee_id = $_POST['evaluatee_id'];
    $school_year  = $_POST['school_year'] ?? '';
    $semester     = $_POST['semester'] ?? '';
    $comment      = $_POST['comment'] ?? '';

    // Collect ratings
    $total_score   = 0;
    $num_questions = 0;

    for ($i = 1; isset($_POST["q$i"]); $i++) {
        $score = intval($_POST["q$i"]);
        $total_score += $score;
        $num_questions++;
    }

    // Compute rating
    $max_score = $num_questions * 5;
    if ($num_questions > 0) {
        $computed_rating = round(($total_score / $max_score) * 100, 2); // percentage
    } else {
        $_SESSION['msg'] = "Evaluation failed: No questions were answered.";
        header("Location: admin-evaluate.php");
        exit();
    }

    // Check for duplicate evaluations
    $check_query = "SELECT 1 FROM admin_evaluation 
                    WHERE evaluator_id = ? 
                      AND evaluatee_id = ? 
                      AND school_year = ? 
                      AND semester = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ssss", $evaluator_id, $evaluatee_id, $school_year, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['msg'] = "You have already evaluated this faculty member for this school year and semester.";
        header("Location: admin-evaluate.php");
        exit();
    }

    // Insert into admin_evaluation
    $insert_query = "INSERT INTO admin_evaluation 
                 (evaluator_id, evaluatee_id, school_year, semester, total_score, computed_rating, comments, department) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssidss", $evaluator_id, $evaluatee_id, $school_year, $semester, $total_score, $computed_rating, $comment, $evaluatee_department);


    // After successful insert...
    if ($stmt->execute()) {
        // Fetch faculty info
        $faculty_query = "SELECT faculty_rank, department FROM faculty WHERE idnumber = ?";
        $stmt_info = $conn->prepare($faculty_query);
        $stmt_info->bind_param("s", $evaluatee_id);
        $stmt_info->execute();
        $info_result = $stmt_info->get_result();
        $faculty_data = $info_result->fetch_assoc();

        // Average rating = computed_rating converted back to scale of 5
        $average_rating = round(($computed_rating / 100) * 5, 2);

        $_SESSION['admin_print_data'] = [
            'evaluator_id'    => $evaluator_id,
            'evaluatee_id'    => $evaluatee_id,
            'school_year'     => $school_year,
            'semester'        => $semester,
            'total_score'     => $total_score,
            'computed_rating' => $computed_rating,
            'average_rating'  => $average_rating,
            'faculty_rank'    => $faculty_data['faculty_rank'] ?? '',
            'department'      => $faculty_data['department'] ?? '',
            'comment'         => $comment,
            'answers'         => $_POST
        ];
        header("Location: admin-evaluation-print.php");
        exit();
    }


} else {
    $_SESSION['msg'] = "Invalid request method.";
    header("Location: admin-evaluate.php");
    exit();
}
?>

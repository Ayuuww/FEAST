<?php
session_start();
include 'conn/conn.php';

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
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $evaluator_id   = $_SESSION['idnumber'];
    $evaluatee_id   = $_POST['evaluatee_id'];
    $academic_year  = $_POST['academic_year'] ?? '';
    $semester       = $_POST['semester'] ?? '';
    $comment        = $_POST['comment'] ?? '';

    // Collect scores
    $total_score = 0;
    $num_questions = 0;
    for ($i = 0; isset($_POST["q$i"]); $i++) {
        $score = intval($_POST["q$i"]);
        $total_score += $score;
        $num_questions++;
    }


    if ($num_questions === 0) {
        $_SESSION['msg'] = "Evaluation failed: No questions were answered.";
        header("Location: admin-evaluate.php");
        exit();
    }

    $computed_rating = round(($total_score / ($num_questions * 5)) * 100, 2);

    // Check for duplicates
    $check_query = "SELECT 1 FROM admin_evaluation 
                    WHERE evaluator_id = ? AND evaluatee_id = ? AND academic_year = ? AND semester = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ssss", $evaluator_id, $evaluatee_id, $academic_year, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['msg'] = "You have already evaluated this faculty member for this school year and semester.";
        header("Location: admin-evaluate.php");
        exit();
    }

    // ✅ Fetch faculty's department and rank first
    $faculty_query = "SELECT faculty_rank, department FROM faculty WHERE idnumber = ?";
    $stmt_info = $conn->prepare($faculty_query);
    $stmt_info->bind_param("s", $evaluatee_id);
    $stmt_info->execute();
    $info_result = $stmt_info->get_result();
    $faculty_data = $info_result->fetch_assoc();

    $evaluatee_department = $faculty_data['department'] ?? '';

    $position_query = "SELECT position FROM admin WHERE idnumber = ?";
    $stmt_position = $conn->prepare($position_query);
    $stmt_position->bind_param("s", $evaluator_id);
    $stmt_position->execute();
    $position_result = $stmt_position->get_result();
    $position_data = $position_result->fetch_assoc();
    $evaluator_position = $position_data['position'] ?? '';

    // ✅ Now insert using that department
    $insert_query = "INSERT INTO admin_evaluation 
    (evaluator_id, evaluatee_id, academic_year, semester, total_score, computed_rating, comments, department, evaluator_position) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param(
        "ssssidsss",
        $evaluator_id,
        $evaluatee_id,
        $academic_year,
        $semester,
        $total_score,
        $computed_rating,
        $comment,
        $evaluatee_department,
        $evaluator_position
    );


    if ($stmt->execute()) {
        // Prepare values for secondary insert
        $questions = [];
        foreach ($_POST as $key => $value) {
            if (preg_match('/^q\d+$/', $key)) {
                $questions[$key] = intval($value);
            }
        }

        $form_data = json_encode($questions);
        $rating_percent = round(($computed_rating), 2);

        $insert = $conn->prepare("INSERT INTO admin_evaluation_submissions 
            (evaluator_id, evaluatee_id, semester, academic_year, total_score, rating_percent, comment, form_data) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $insert->bind_param(
            "sssssdss",
            $evaluator_id,
            $evaluatee_id,
            $semester,
            $academic_year,
            $total_score,
            $rating_percent,
            $comment,
            $form_data
        );
        if ($insert->execute()) {
            // ✅ Log activity
            $faculty_name_stmt = $conn->prepare("SELECT first_name, mid_name, last_name FROM faculty WHERE idnumber = ?");
            $faculty_name_stmt->bind_param("s", $evaluatee_id);
            $faculty_name_stmt->execute();
            $faculty_name_result = $faculty_name_stmt->get_result();
            $faculty_name_row = $faculty_name_result->fetch_assoc();
            $faculty_fullname = $faculty_name_row
                ? trim($faculty_name_row['first_name'] . ' ' . (isset($faculty_name_row['mid_name']) ? substr($faculty_name_row['mid_name'], 0, 1) . '. ' : '') . $faculty_name_row['last_name'])
                : $evaluatee_id;


            $activity_message = "Evaluated Faculty: $faculty_fullname with a rating of $computed_rating%";
            logActivity($conn, $evaluator_id, 'admin', $activity_message);

            // Store session for printing
            $average_rating = round(($computed_rating / 100) * 5, 2);

            $_SESSION['admin_print_data'] = [
                'evaluator_id'          => $evaluator_id,
                'evaluatee_id'          => $evaluatee_id,
                'evaluator_position'    => $evaluator_position,
                'academic_year'         => $academic_year,
                'semester'              => $semester,
                'total_score'           => $total_score,
                'computed_rating'       => $computed_rating,
                'average_rating'        => $average_rating,
                'faculty_rank'          => $faculty_data['faculty_rank'] ?? '',
                'department'            => $evaluatee_department,
                'comment'               => $comment,
                'answers'               => $_POST
            ];

            $_SESSION['admin_eval_success'] = true;
            header("Location: admin-evaluate.php");
            exit();
        }
    } else {
        $_SESSION['msg'] = "Failed to save evaluation.";
        header("Location: admin-evaluate.php");
        exit();
    }
} else {
    $_SESSION['msg'] = "Invalid request method.";
    header("Location: admin-evaluate.php");
    exit();
}

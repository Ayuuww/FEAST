<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

if (!isset($_GET['faculty_id'])) {
    echo "No faculty selected.";
    exit();
}

$faculty_id = $_GET['faculty_id'];

// Fetch faculty info
$stmt = $conn->prepare("SELECT last_name, first_name, mid_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($lname, $fname, $mname, $department, $faculty_rank);
$stmt->fetch();
$stmt->close();
$faculty_name = "$fname $mname $lname";

// Semester & year
$semester = "N/A";
$academic_year = "N/A";

$eval_q = mysqli_query($conn, "SELECT semester, academic_year FROM admin_evaluation WHERE evaluatee_id = '$faculty_id' ORDER BY evaluation_date DESC LIMIT 1");

if ($eval_q && mysqli_num_rows($eval_q) > 0) {
    $row = mysqli_fetch_assoc($eval_q);
    $semester = $row['semester'] ?? "N/A";
    $academic_year = $row['academic_year'] ?? "N/A";
} else {
    // Try getting from student evaluation if supervisor evaluation is missing
    $eval_fallback = mysqli_query($conn, "SELECT semester, academic_year FROM evaluation WHERE faculty_id = '$faculty_id' ORDER BY id DESC LIMIT 1");
    if ($eval_fallback && mysqli_num_rows($eval_fallback) > 0) {
        $row = mysqli_fetch_assoc($eval_fallback);
        $semester = $row['semester'] ?? "N/A";
        $academic_year = $row['academic_year'] ?? "N/A";
    }
}


// SET Summary
$result = mysqli_query($conn, "
    SELECT subject_code, subject_title, student_section, COUNT(*) as num_students,
        AVG(computed_rating) as avg_rating,
        SUM(computed_rating) as total_weight
    FROM evaluation
    WHERE faculty_id = '$faculty_id'
    GROUP BY subject_code, student_section
");

$total_students = 0;
$total_weighted_value = 0;
$table_rows = '';
while ($row = mysqli_fetch_assoc($result)) {
    $weighted = $row['num_students'] * $row['avg_rating'];
    $total_students += $row['num_students'];
    $total_weighted_value += $weighted;
    $table_rows .= "<tr>
        <td>{$row['subject_code']}</td>
        <td>{$row['student_section']}</td>
        <td>{$row['num_students']}</td>
        <td>" . number_format($row['avg_rating'], 2) . "</td>
        <td>" . number_format($weighted, 2) . "</td>
    </tr>";
}
$overall_set = $total_students ? number_format($total_weighted_value / $total_students, 2) : '0.00';

// SEF
$sef_result = mysqli_query($conn, "SELECT AVG(computed_rating) as sef_rating FROM admin_evaluation WHERE evaluatee_id = '$faculty_id'");
$sef_rating = mysqli_fetch_assoc($sef_result)['sef_rating'] ?? 0;
$sef_rating = number_format($sef_rating, 2);

// Comments
$comments_q = mysqli_query($conn, "SELECT comment FROM evaluation WHERE faculty_id = '$faculty_id' AND comment IS NOT NULL AND comment <> '' LIMIT 5");

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Faculty Evaluation Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h3, h5 { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; }
        @media print {
            button { display: none; }
           .no-print { display: none !important; }
            
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left; /* Make content left-aligned */
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
        }
        .signature-cell {
            height: 60px;
            min-width: 150px;
        }
        .wide-cell {
            width: 200px;
        } 
    </style>
</head>
<body onload="window.print()">

    <button onclick="window.print()" class="btn btn-secondary w-20 no-print">
        <i class="bi bi-printer"></i> Print Again
    </button>

        <h3>INDIVIDUAL FACULTY EVALUATION REPORT</h3>

        <h5>A. Faculty Information</h5>
        <table>
            <tr><th>Name of Faculty Evaluated:</th><td><?= htmlspecialchars($faculty_name) ?></td></tr>
            <tr><th>Department/College:</th><td><?= htmlspecialchars($department) ?></td></tr>
            <tr><th>Current Faculty Rank:</th><td><?= htmlspecialchars($faculty_rank) ?></td></tr>
            <tr><th>Semester/Term & Academic Year:</th><td><?= htmlspecialchars($semester . ' / ' . $academic_year) ?></td></tr>
        </table>

        <h5>B. Summary of Average SET Rating</h5>
        <table>
            <thead>
                <tr><th>Course Code</th><th>Section</th><th>No. of Students</th><th>Avg. SET Rating</th><th>Weighted Value</th></tr>
            </thead>
            <tbody>
                <?= $table_rows ?>
                <tr><th colspan="2">TOTAL</th><td><?= $total_students ?></td><td></td><td><?= number_format($total_weighted_value, 2) ?></td></tr>
            </tbody>
        </table>

        <h5>C. SET and SEF Ratings</h5>
        <table>
            <tr><th>OVERALL SET Rating</th><td><?= $overall_set ?></td></tr>
            <tr><th>Supervisor (SEF) Rating</th><td><?= $sef_rating ?></td></tr>
        </table>

        <h5>D. Summary of Qualitative Comments and Suggestions</h5>
        <table>
            <tr><th>#</th><th>Comments</th></tr>
            <?php
            $count = 1;
            while ($row = mysqli_fetch_assoc($comments_q)) {
                echo "<tr><td>{$count}</td><td>" . htmlspecialchars($row['comment']) . "</td></tr>";
                $count++;
            }
            if ($count == 1) echo "<tr><td colspan='2'>No comments available.</td></tr>";
            ?>
        </table>

        <h5>E. Development Plan</h5>
        <table>
            <tr><th>Areas for Improvement</th></tr>
            <tr><td style="height:60px;"></td></tr>
            <tr><th>Proposed Learning and Development Activities</th></tr>
            <tr><td style="height:60px;"></td></tr>
            <tr><th>Action Plan</th></tr>
            <tr><td style="height:60px;"></td></tr>
        </table>

        <table class="table table-bordered">
        <tr>
            <th class="wide-cell">Prepared by (Staff Signature)</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Name:</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Date:</th>
            <td class="signature-cell"></td>
        </tr>
        <tr>
            <th class="wide-cell">Reviewed by (Authorized Official)</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Name:</th>
            <td class="signature-cell"></td>
            <th class="wide-cell">Date:</th>
            <td class="signature-cell"></td>
        </tr>
        </table>

</body>
</html>

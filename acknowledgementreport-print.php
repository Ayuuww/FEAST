<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

if (!isset($_GET['faculty_id'])) {
    die("Invalid access.");
}

$faculty_id = $_GET['faculty_id'];

// Get faculty info
$stmt = $conn->prepare("SELECT first_name, mid_name, last_name, department, faculty_rank FROM faculty WHERE idnumber = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$stmt->bind_result($fname, $mname, $lname, $dept, $rank);
$stmt->fetch();
$stmt->close();
$full_name = strtoupper("$fname $mname $lname");
$dept = strtoupper($dept);
$rank = ucwords($rank);

// Get semester/year
$sem = "N/A";
$sy = "N/A";
$q = mysqli_query($conn, "SELECT semester, academic_year FROM admin_evaluation WHERE evaluatee_id = '$faculty_id' ORDER BY evaluation_date DESC LIMIT 1");
if ($q && mysqli_num_rows($q) > 0) {
    $row = mysqli_fetch_assoc($q);
    $sem = $row['semester'];
    $sy = $row['academic_year'];
}

// SET Rating
$set_q = mysqli_query($conn, "SELECT COUNT(*) as count, AVG(computed_rating) as avg FROM evaluation WHERE faculty_id = '$faculty_id'");
$set_avg = ($row = mysqli_fetch_assoc($set_q)) ? number_format($row['avg'], 2) : '0.00';

// SEF Rating
$sef_q = mysqli_query($conn, "SELECT AVG(computed_rating) as avg FROM admin_evaluation WHERE evaluatee_id = '$faculty_id'");
$sef_avg = ($row = mysqli_fetch_assoc($sef_q)) ? number_format($row['avg'], 2) : '0.00';

// Supervisor Name
$evaluator_name = '';
$eval_result = mysqli_query($conn, "SELECT evaluator_id FROM admin_evaluation WHERE evaluatee_id = '$faculty_id' ORDER BY evaluation_date DESC LIMIT 1");
if ($eval_result && mysqli_num_rows($eval_result) > 0) {
    $admin_row = mysqli_fetch_assoc($eval_result);
    $admin_id = $admin_row['evaluator_id'];

    $admin_info = mysqli_query($conn, "SELECT first_name, mid_name, last_name FROM admin WHERE idnumber = '$admin_id'");
    if ($admin_info && mysqli_num_rows($admin_info) > 0) {
        $admin = mysqli_fetch_assoc($admin_info);
        $evaluator_name = strtoupper($admin['first_name'] . ' ' . $admin['mid_name'] . ' ' . $admin['last_name']);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Acknowledgement Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table th, .table td {
            border: 1px solid #444;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        .signature-box {
            height: 60px;
            min-width: 250px;
            border-bottom: 1px solid #000;
        }
        h5, h6 {
            margin: 10px 0;
        }
        p {
            margin-top: 15px;
        }
        .text-center {
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <h5 class="text-center"><strong>FACULTY EVALUATION ACKNOWLEDGEMENT FORM</strong></h5>

    <h6><strong>FACULTY MEMBER INFORMATION</strong></h6>
    <table class="table">
        <tr><th>Name of Faculty</th><td><?= $full_name ?></td></tr>
        <tr><th>Department/College</th><td><?= $dept ?></td></tr>
        <tr><th>Current Faculty Rank</th><td><?= $rank ?></td></tr>
        <tr><th>Semester/Term & Academic Year</th><td><?= $sem ?> / <?= $sy ?></td></tr>
    </table>

    <h6><strong>FACULTY EVALUATION SUMMARY</strong></h6>
    <table class="table text-center">
        <thead>
            <tr>
                <th>Student Evaluation of Teachers (SET)</th>
                <th>Supervisor's Evaluation of Faculty (SEF)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong><?= $set_avg ?></strong></td>
                <td><strong><?= $sef_avg ?></strong></td>
            </tr>
        </tbody>
    </table>

    <p>
        I acknowledge that I have received and reviewed the faculty evaluation conducted for the period mentioned above.
        I understand that my signature below does not necessarily indicate agreement with the evaluation but confirms that I have been given the opportunity to discuss it with my supervisor.
    </p>

    <h6><strong>SUPERVISOR</strong></h6>
    <table class="table">
        <tr>
            <th>Signature</th><td class="signature-box"></td>
            <th>Name</th><td class="signature-box"><?= $evaluator_name ?></td>
            <th>Date Signed</th><td class="signature-box"></td>
        </tr>
    </table>

    <h6><strong>FACULTY</strong></h6>
    <table class="table">
        <tr>
            <th>Signature</th><td class="signature-box"></td>
            <th>Name</th><td><?= $full_name ?></td>
            <th>Date Signed</th><td class="signature-box"></td>
        </tr>
    </table>

    <div class="text-center no-print">
        <button onclick="window.print()">Print Acknowledgement</button>
    </div>

</body>
</html>

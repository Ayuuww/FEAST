<?php
session_start();
include 'conn/conn.php';

$errorMessage = '';
if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$evaluationSuccess = false;
if (isset($_SESSION['evaluation_success']) && $_SESSION['evaluation_success'] === true) {
    $evaluationSuccess = true;
    // We will keep evaluation_print_data in session for the SweetAlert to use
    // unset($_SESSION['evaluation_success']); // Keep this set until after the SweetAlert is displayed
}


// Check evaluation switch status
$evalRes = mysqli_query($conn, "SELECT status FROM evaluation_switch LIMIT 1");
$evalStatus = mysqli_fetch_assoc($evalRes)['status'] ?? 'off';
$evaluation_closed = $evalStatus === 'off';


// Check if the user is logged in and is a student
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
    header("Location: pages-login.php");
    exit();
}

// Setting the academic year and semester by superadmin
$setting_query = "SELECT semester, academic_year FROM evaluation_settings WHERE id = 1 LIMIT 1";
$setting_result = $conn->query($setting_query);
$default_semester = '';
$default_year = '';

if ($setting_result && $setting_result->num_rows > 0) {
    $setting_row = $setting_result->fetch_assoc();
    $default_semester = $setting_row['semester'];
    $default_year = $setting_row['academic_year'];
}

// Fetching subjects and their respective faculty that have NOT been evaluated yet by the student
$student_id = $_SESSION['idnumber'];
$academic_year = $default_year; // Use default year from settings
$semester = $default_semester; // Use default semester from settings

// MODIFIED QUERY: This query selects subjects from student_subject
// and LEFT JOINs with faculty/admin. It then uses NOT EXISTS
// to ensure that no corresponding entry for the current student,
// subject, faculty, academic year, and semester exists in the 'evaluation' table.
$query = "
    SELECT
        ss.subject_code,
        s.title AS subject_title,
        COALESCE(f.idnumber, a.idnumber) AS faculty_id,
        COALESCE(f.first_name, a.first_name) AS first_name,
        COALESCE(f.mid_name, a.mid_name) AS mid_name,
        COALESCE(f.last_name, a.last_name) AS last_name,
        COALESCE(f.status, a.status) AS status,
        COALESCE(f.department, a.department) AS department,
        CASE
            WHEN f.idnumber IS NOT NULL THEN 'faculty'
            WHEN a.idnumber IS NOT NULL THEN 'admin'
            ELSE 'unknown'
        END AS role
    FROM
        student_subject ss
    JOIN
        subject s ON ss.subject_code = s.code
    LEFT JOIN
        faculty f ON ss.faculty_id = f.idnumber AND f.status = 'active'
    LEFT JOIN
        admin a ON ss.admin_id = a.idnumber AND a.status = 'active'
    WHERE
        ss.student_id = ?
        AND ss.academic_year = ? -- Added academic_year filter for student_subject
        AND ss.semester = ?     -- Added semester filter for student_subject
        AND NOT EXISTS (
            SELECT 1
            FROM evaluation e
            WHERE
                e.student_id = ss.student_id
                AND e.subject_code = ss.subject_code
                AND e.faculty_id = COALESCE(ss.faculty_id, ss.admin_id)
                AND e.academic_year = ?
                AND e.semester = ?
        )
";

$stmt = $conn->prepare($query);
// Bind academic_year and semester twice for the outer query and the subquery
$stmt->bind_param("sssss", $student_id, $academic_year, $semester, $academic_year, $semester);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

// Fetching faculty department (used for dropdown, but not directly tied to the subjects for evaluation)
$dept_query = "SELECT DISTINCT department FROM faculty WHERE department IS NOT NULL AND department != '' ORDER BY department ASC";
$dept_result = mysqli_query($conn, $dept_query);
$department = [];

if ($dept_result && mysqli_num_rows($dept_result) > 0) {
    while ($row = mysqli_fetch_assoc($dept_result)) {
        $department[] = $row;
    }
}

// No need for explicit student_section variable from database lookup here
// as it can be fetched directly in submit-evaluation.php or passed via session
// if strictly needed on this page for display. For now, it's fine as a hidden input.
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>FEAST / Student Evaluate </title>

    <?php include 'header.php' ?>

    <style>
        @media print {

            aside,
            header,
            .btn,
            .back-to-top,
            nav.breadcrumb,
            .sidebar {
                display: none !important;
            }

            main {
                margin: 0;
                padding: 0;
                width: 100%;
            }

            table {
                page-break-inside: avoid;
            }
        }
    </style>

</head>

<body>

    <?php include 'student-header.php' ?>

    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link collapsed" href="student-dashboard.php">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapse" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="charts-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="student-evaluate.php" class="active">
                            <i class="bi bi-circle"></i><span>Form</span>
                        </a>
                    </li>
                    <li>
                        <a href="student-evaluatedsubject.php">
                            <i class="bi bi-circle"></i><span>Evaluated Subject</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-heading">Pages</li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="student-user-profile.php">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                </a>
            </li>
        </ul>

    </aside>
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Student Evaluation Form</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
                    <li class="breadcrumb-item ">Evaluate</li>
                    <li class="breadcrumb-item active">Form</li>
                </ol>
            </nav>
        </div>
        <section class="section dashboard">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-10 col-sm-12">
                        <div class="card shadow-lg">
                            <div class="card-body">

                                <?php if (isset($_SESSION['msg'])): ?>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Evaluation Issue',
                                                text: <?= json_encode($_SESSION['msg']) ?>,
                                                confirmButtonText: 'OK'
                                            });
                                        });
                                    </script>
                                    <?php unset($_SESSION['msg']); ?>
                                <?php endif; ?>

                                <h5 class="card-title text-center">Student Evaluation of Teachers (SET)</h5>


                                <?php if ($evalStatus === 'off'): ?>
                                    <div class="alert alert-warning text-center fs-5 my-5">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        Evaluation period is currently <strong>CLOSED</strong>.
                                    </div>
                                <?php else: ?>

                                    <style>
                                        .overlay-block {
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            background: rgba(255, 255, 255, 0.6);
                                            z-index: 10;
                                        }

                                        .form-disabled {
                                            pointer-events: none;
                                            opacity: 0.6;
                                        }

                                        .disabled-button {
                                            pointer-events: none;
                                            opacity: 0.5;
                                        }
                                    </style>

                                    <form action="submit-evaluation.php" method="POST">

                                        <div class="<?= $evaluation_closed ? 'position-relative form-disabled' : '' ?>">
                                            <?php if ($evaluation_closed): ?>
                                                <div class="overlay-block rounded"></div>
                                            <?php endif; ?>

                                            <div class="row">
                                                <h5 class="mb-3"><strong>A. Faculty Information</strong></h5>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-floating">
                                                        <select name="subject_code" id="subject_code" class="form-select text-capitalize" required>
                                                            <option value="" disabled selected>-- Select a Subject --</option>
                                                            <?php if (empty($subjects)): ?>
                                                                <option value="" disabled>No subjects available for evaluation.</option>
                                                            <?php else: ?>
                                                                <?php foreach ($subjects as $row):
                                                                    $facultyName = htmlspecialchars($row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name']);
                                                                    $subjectTitle = htmlspecialchars($row['subject_title']);
                                                                    $subjectCode = htmlspecialchars($row['subject_code']);
                                                                    $facultyId = htmlspecialchars($row['faculty_id']);
                                                                    $tag = $row['role'] === 'admin' ? ' (Admin)' : '';
                                                                ?>
                                                                    <option value="<?= $subjectCode . '|' . $facultyId ?>"
                                                                        data-department="<?= htmlspecialchars($row['department']) ?>">
                                                                        <?= $subjectTitle ?> (<?= $subjectCode ?>) - <?= $facultyName . $tag ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                        <label for="subject_code" class="form-label">Subject</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <div class="form-floating">
                                                        <select class="form-select" disabled>
                                                            <option value="<?= $default_year ?>" selected><?= $default_year ?></option>
                                                        </select>
                                                        <label for="academic_year" class="form-label">Academic Year</label>
                                                        <input type="hidden" name="academic_year" value="<?= $default_year ?>">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <div class="form-floating">
                                                        <select class="form-select" disabled>
                                                            <option value="<?= $default_semester ?>" selected><?= $default_semester ?></option>
                                                        </select>
                                                        <label for="semester" class="form-label">Semester</label>
                                                        <input type="hidden" name="semester" value="<?= $default_semester ?>">
                                                    </div>
                                                </div>

                                            </div>

                                            <h5 class="mb-3"><strong>B. Rating Scale</strong></h5>
                                            <div class="table-responsive mb-4">
                                                <table class="table table-bordered text-center align-middle small">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Scale</th>
                                                            <th>Qualitative Description</th>
                                                            <th>Operational Definition</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>5</strong></td>
                                                            <td>Always manifested</td>
                                                            <td class="text-start text-danger">The behavior, characteristic, or condition is
                                                                consistently and unfailling demostrated in all relevant situation or instances.
                                                                There is no observed deviation from this pattern. Operationally, this could mean
                                                                occurring in 95-100% of observed opportunities or instances.</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>4</strong></td>
                                                            <td>Often manifested</td>
                                                            <td class="text-start text-danger">The behavior, characteristic, or condition is
                                                                demostrated frequently, though occasional instances of non-manifestation may occur.
                                                                Operationally, this could mean occurring in 60-94% of
                                                                observed opportunities or instances.</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>3</strong></td>
                                                            <td>Sometimes manifested</td>
                                                            <td class="text-start text-danger">The behavior, characteristic, or condition is
                                                                demostrated intermittenly or irregulary, with an approximately equal likelihood
                                                                occurrence and non-occurence. Operationally, this could mean occurring in 40-60%
                                                                of observed opportunities or instances.</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>2</strong></td>
                                                            <td>Seldom manifested</td>
                                                            <td class="text-start text-danger">The behavior, characteristic, or condition is
                                                                demostrated infrequently and is generally absent in most relevant situation.
                                                                Operationally, this could mean occurring in 25-40% of
                                                                observed opportunities or instances.</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>1</strong></td>
                                                            <td>Rarely manifested</td>
                                                            <td class="text-start text-danger">The behavior, characteristic, or condition is
                                                                almost never demostrated, with only isolated or exceptional instances of occurrence.
                                                                Operationally, this could mean occurring in 0-24% of
                                                                observed opportunities or instances.</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <h5 class="mb-3"><strong>c. Instruction: </strong>Read the benchmark statements carefully.
                                                Please rate the faculty on each of the following
                                                statements below using the above-listed rating scale</h5>
                                            <div class="table-responsive ">
                                                <table class="table table-bordered text-center align-middle">
                                                    <tbody>
                                                        <thead class="table-light">
                                                            <tr class="text-start">
                                                                <th>Benchmark Statement for Faculty Teaching Effectiveness</th>
                                                            </tr>
                                                        </thead>
                                                    </tbody>
                                                </table>
                                                <table class="table table-bordered text-center align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-start">A. Manage of Teacking and Learning</th>
                                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                                <th><?= $i ?></th>
                                                            <?php endfor; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $questionIndex = 0;
                                                        $questions_a = [
                                                            "Comes to class on time regularly.",
                                                            "Explains learning outcomes, expectations, grading system, and various requirements of the subject/course.",
                                                            "Maximizes the allocated time/learning hours effectively.",
                                                            "Facilitates students to think critically and creatively by providing appropriate learning activities.",
                                                            "Guides students to learn on their own, reflect on new ideas and experiences, and make decisions in accomplishing given tasks.",
                                                            "Communicates constructive feedback to students for their academic growth."
                                                        ];
                                                        foreach ($questions_a as $question):
                                                        ?>
                                                            <tr>
                                                                <td class="text-start"><?= $questionIndex + 1 ?>. <?= $question ?></td>
                                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                                    <td>
                                                                        <input type="radio" name="q<?= $questionIndex ?>" value="<?= $i ?>" required>
                                                                    </td>
                                                                <?php endfor; ?>
                                                            </tr>
                                                        <?php
                                                            $questionIndex++;
                                                        endforeach;
                                                        ?>
                                                    </tbody>
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-start">B. Content Knowledge, Pedagogy and Technology</th>
                                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                                <th><?= $i ?></th>
                                                            <?php endfor; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $questions_b = [
                                                            "Demonstrates extensive and broad knowledge of the subject/course.",
                                                            "Simplifies complex ideas in the lesson for ease of understanding.",
                                                            "Relates the subject matter to contemporary issues and developments in the discipline and/or daily life activities.",
                                                            "Promotes active learning and student engagement by using appropriate teaching and learning resources including ICT Tools and platforms.",
                                                            "Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes"
                                                        ];
                                                        foreach ($questions_b as $question):
                                                        ?>
                                                            <tr>
                                                                <td class="text-start"><?= $questionIndex + 1 ?>. <?= $question ?></td>
                                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                                    <td>
                                                                        <input type="radio" name="q<?= $questionIndex ?>" value="<?= $i ?>" required>
                                                                    </td>
                                                                <?php endfor; ?>
                                                            </tr>
                                                        <?php
                                                            $questionIndex++;
                                                        endforeach;
                                                        ?>
                                                    </tbody>
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-start">C. Commitment and Transparency</th>
                                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                                <th><?= $i ?></th>
                                                            <?php endfor; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $questions_c = [
                                                            "Recognizes and values the unique diversity and individuality difference among students.",
                                                            "Assist students with their learning challenges during consultation hours.",
                                                            "Provide immediate feedback on student outputs and performance.",
                                                            "Provides transparent and clear criteria in rating student's performance."
                                                        ];
                                                        foreach ($questions_c as $question):
                                                        ?>
                                                            <tr>
                                                                <td class="text-start"><?= $questionIndex + 1 ?>. <?= $question ?></td>
                                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                                    <td>
                                                                        <input type="radio" name="q<?= $questionIndex ?>" value="<?= $i ?>" required>
                                                                    </td>
                                                                <?php endfor; ?>
                                                            </tr>
                                                        <?php
                                                            $questionIndex++;
                                                        endforeach;
                                                        ?>
                                                    </tbody>
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-start">Total Score</th>
                                                            <th colspan="5" id="totalScore" class="text-center text-danger fs-5">0</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>

                                            <div class="mb-3">
                                                <label for="comment" class="form-label">Other comments and suggestions (optional)</label>
                                                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Write your feedback here..."></textarea>
                                            </div>

                                            <input type="hidden" name="student_id" value="<?= $_SESSION['idnumber'] ?>">

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Computed Rating (%)</label>
                                                    <input type="text" class="form-control text-danger fw-bold" id="computedRating" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Date of Evaluation</label>
                                                    <input type="text" class="form-control" value="<?= date('F j, Y') ?>" readonly>
                                                </div>
                                            </div>

                                            <input type="hidden" name="student_section" value="<?= htmlspecialchars($student_section ?? '') ?>">
                                            <input type="hidden" name="department" id="department_hidden">

                                            <div class="col-md-4 offset-md-4 mb-3">
                                                <button type="submit" class="btn btn-success btn-block w-100 <?= $evaluation_closed || empty($subjects) ? 'disabled-button' : '' ?>"
                                                    <?= empty($subjects) ? 'disabled' : '' ?>>
                                                    Submit Evaluation
                                                </button>
                                            </div>
                                            <?php if (empty($subjects) && $evalStatus !== 'off'): ?>
                                                <div class="alert alert-info text-center mt-3">
                                                    All your subjects for the current Academic Year (<?= $default_year ?>) and Semester (<?= $default_semester ?>) have been evaluated.
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    </form>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </main><?php include 'footer.php' ?>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <script src="vendors/apexcharts/apexcharts.min.js"></script>
    <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendors/chart.js/chart.umd.js"></script>
    <script src="vendors/echarts/echarts.min.js"></script>
    <script src="vendors/quill/quill.js"></script>
    <script src="vendors/simple-datatables/simple-datatables.js"></script>
    <script src="vendors/tinymce/tinymce.min.js"></script>
    <script src="vendors/php-email-form/validate.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script src="assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('input[type="radio"]');
            const totalScoreDisplay = document.getElementById('totalScore');
            const computedRatingDisplay = document.getElementById('computedRating');
            const submitButton = document.querySelector('button[type="submit"]');
            const subjectSelect = document.getElementById('subject_code');

            function calculateScore() {
                let total = 0;
                const questionCount = new Set();

                inputs.forEach(input => {
                    if (input.checked) {
                        total += parseInt(input.value);
                        questionCount.add(input.name);
                    }
                });

                totalScoreDisplay.textContent = total;

                // Rating formula: (total / 75) * 100
                const rating = ((total / 75) * 100).toFixed(2);
                computedRatingDisplay.value = `${rating}%`;

                // Enable/disable submit button based on whether a subject is selected and evaluation is open
                // The PHP logic for $evaluation_closed already handles disabling if closed.
                // We add a check for selected subject here.
                if (subjectSelect.value !== "" && !<?= json_encode($evaluation_closed) ?>) {
                    submitButton.removeAttribute('disabled');
                    submitButton.classList.remove('disabled-button');
                } else {
                    submitButton.setAttribute('disabled', 'disabled');
                    submitButton.classList.add('disabled-button');
                }
            }

            inputs.forEach(input => {
                input.addEventListener('change', calculateScore);
            });

            const deptHiddenInput = document.getElementById('department_hidden');

            subjectSelect.addEventListener('change', function() {
                const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
                const department = selectedOption.getAttribute('data-department') || '';
                deptHiddenInput.value = department;
                calculateScore(); // Recalculate score and re-check button state when subject changes
            });

            calculateScore(); // Initial calc on load
        });
    </script>

    <?php if (!empty($errorMessage)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: <?= json_encode($errorMessage) ?>,
                    confirmButtonText: 'OK'
                });
            });
        </script>
    <?php endif; ?>

    <?php if ($evaluationSuccess): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Evaluation Submitted!',
                    text: "Would you like to print your evaluation now?",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Print Now',
                    cancelButtonText: 'Print Later',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open('student-evaluation-print-fpdf.php', '_blank'); // Open in new tab
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'You can print your evaluation later. Check on Evaluated Subject',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        </script>
        <?php unset($_SESSION['evaluation_success']); // Unset after SweetAlert is shown ?>
    <?php endif; ?>

</body>

</html>
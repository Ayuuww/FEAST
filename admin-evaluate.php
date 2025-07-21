<?php
session_start();
include 'conn/conn.php';

// Check evaluation switch status
$evalRes = mysqli_query($conn, "SELECT status FROM evaluation_switch LIMIT 1");
$evalStatus = mysqli_fetch_assoc($evalRes)['status'] ?? 'off';
$evaluation_closed = $evalStatus === 'off';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    header("Location: pages-login.php");
    exit();
}

$evaluator_id = $_SESSION['idnumber'];

// Get current admin's department and position
$admin_info_stmt = $conn->prepare("SELECT department, position FROM admin WHERE idnumber = ? LIMIT 1");
$admin_info_stmt->bind_param("s", $evaluator_id);
$admin_info_stmt->execute();
$admin_result = $admin_info_stmt->get_result();
$admin_data = $admin_result->fetch_assoc();
$department = $admin_data['department'] ?? '';
$evaluator_position = $admin_data['position'] ?? 'Not Set';
$admin_info_stmt->close();


// super admin set academic year and semester to default
$setting_query = "SELECT semester, academic_year FROM evaluation_settings WHERE id = 1 LIMIT 1";
$setting_result = $conn->query($setting_query);
$default_semester = '';
$default_year = '';

if ($setting_result && $setting_result->num_rows > 0) {
    $setting_row = $setting_result->fetch_assoc();
    $default_semester = $setting_row['semester'];
    $default_year = $setting_row['academic_year'];
}

// --- IMPORTANT MODIFICATION HERE ---
// Fetching faculty members from the same department as the admin
// AND who have NOT been evaluated by this specific admin for the current academic year and semester
$query = "
    SELECT
        f.idnumber,
        f.first_name,
        f.mid_name,
        f.last_name,
        f.faculty_rank,
        f.department
    FROM
        faculty f
    WHERE
        f.department = ? -- Filter by admin's department
        AND f.status = 'active' -- Only active faculty
        AND NOT EXISTS ( -- Subquery to exclude already evaluated faculty
            SELECT 1
            FROM admin_evaluation ae
            WHERE
                ae.evaluatee_id = f.idnumber
                AND ae.evaluator_id = ?
                AND ae.academic_year = ?
                AND ae.semester = ?
        )
    ORDER BY
        f.last_name, f.first_name";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $department, $evaluator_id, $default_year, $default_semester);
$stmt->execute();
$result = $stmt->get_result();

$faculty_list = [];
while ($row = $result->fetch_assoc()) {
    $faculty_list[] = $row;
}
$stmt->close();


// Display message if set
$errorMessage = '';
if (isset($_SESSION['msg'])) {
    $errorMessage = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$evaluationSuccess = false;
if (isset($_SESSION['admin_eval_success']) && $_SESSION['admin_eval_success'] === true) {
    $evaluationSuccess = true;
    unset($_SESSION['admin_eval_success']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>FEAST / Admin Faculty Evaluation</title>

    <?php include 'header.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

</head>

<body>

    <?php include 'admin-header.php' ?>

    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link collapsed" href="admin-dashboard.php">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><li class="nav-item">
                <a class="nav-link collapse " data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="charts-nav" class="nav-content collapse show " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="admin-evaluate.php" class="active">
                            <i class="bi bi-circle"></i><span>Form</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin-evaluatedfaculty.php">
                            <i class="bi bi-circle"></i><span>Evaluated Faculty</span>
                        </a>
                    </li>
                </ul>
            </li><li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#subject" data-bs-toggle="collapse" href="#">
                    <i class="ri-book-line"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="subject" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="admin-subjectlist.php">
                            <i class="bi bi-circle"></i><span>List</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin-subjectadding.php">
                            <i class="bi bi-circle"></i><span>Add Subject</span>
                        </a>
                    </li>
                </ul>
            </li><li class="nav-item">
                <a class="nav-link collapsed" href="admin-studentsubject.php">
                    <i class="ri-book-fill"></i>
                    <span>Assign Subject</span>
                </a>
            </li><li class="nav-item">
                <a class="nav-link collapsed" href="admin-evaluatedsubject.php">
                    <i class="bi bi-book-fill"></i>
                    <span>Subject Evaluated</span>
                </a>
            </li><li class="nav-heading">Pages</li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="admin-user-profile.php">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li><li class="nav-item">
                <a class="nav-link collapsed" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                </a>
            </li></ul>

    </aside><main id="main" class="main">
        <div class="pagetitle">
            <h1>Faculty Evaluation Form</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li>
                    <li class="breadcrumb-item">Evaluate</li>
                    <li class="breadcrumb-item active">Faculty Evaluation</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-10 col-sm-12">
                        <div class="card shadow-lg">
                            <div class="card-body table-responsive">
                                <h5 class="card-title text-center">Supervisor's Evaluation of Faculty (SEF)</h5>

                                <?php if ($evalStatus === 'off'): ?>
                                    <div class="alert alert-warning text-center fs-5 my-5">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        Evaluation period is currently <strong>CLOSED</strong>.
                                    </div>
                                <?php else: ?>

                                    <form action="submit-admin-evaluation.php" method="POST">

                                        <div class="<?= $evaluation_closed ? 'position-relative form-disabled' : '' ?>">
                                            <?php if ($evaluation_closed): ?>
                                                <div class="overlay-block rounded"></div>
                                            <?php endif; ?>

                                            <div class="row mb-3">
                                                <h5 class="mb-3"><strong>A. Faculty Information</strong></h5>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <select name="evaluatee_id" id="evaluatee_id" class="form-select text-capitalize" required>
                                                            <option value="" disabled selected>-- Select Faculty --</option>
                                                            <?php
                                                            if (empty($faculty_list)) {
                                                                echo '<option value="" disabled>No faculty to evaluate in your department or all have been evaluated.</option>';
                                                            } else {
                                                                foreach ($faculty_list as $faculty):
                                                                    $fullName = htmlspecialchars($faculty['first_name'] . ' ' . $faculty['mid_name'] . ' ' . $faculty['last_name']);
                                                                    $rank = htmlspecialchars($faculty['faculty_rank']);
                                                                    ?>
                                                                    <option value="<?= htmlspecialchars($faculty['idnumber']) ?>">
                                                                        <?= $fullName ?> (<?= $rank ?>)
                                                                    </option>
                                                                <?php endforeach;
                                                            }
                                                            ?>
                                                        </select>
                                                        <label for="evaluatee_id">Faculty to Evaluate</label>
                                                    </div>
                                                </div>


                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <select name="academic_year" id="academic_year" class="form-select" required disabled>
                                                            <option value="<?= $default_year ?>" selected><?= $default_year ?></option>
                                                        </select>
                                                        <label for="academic_year">Academic Year</label>
                                                        <input type="hidden" name="academic_year" value="<?= $default_year ?>">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <select name="semester" id="semester" class="form-select" required disabled>
                                                            <option value="<?= $default_semester ?>" selected><?= $default_semester ?></option>
                                                        </select>
                                                        <label for="semester">Semester</label>
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
                                                        <tr><td><strong>5</strong></td><td>Always manifested</td><td class="text-start text-danger">The behavior, characteristic, or condition is consistently and unfailling demostrated in all relevant situation or instances. There is no observed deviation from this pattern. Operationally, this could mean occurring in 95-100% of observed opportunities or instances.</td></tr>
                                                        <tr><td><strong>4</strong></td><td>Often manifested</td><td class="text-start text-danger">The behavior, characteristic, or condition is demostrated frequently, though occasional instances of non-manifestation may occur. Operationally, this could mean occurring in 60-94% of observed opportunities or instances.</td></tr>
                                                        <tr><td><strong>3</strong></td><td>Sometimes manifested</td><td class="text-start text-danger">The behavior, characteristic, or condition is demostrated intermittenly or irregulary, with an approximately equal likelihood occurrence and non-occurence. Operationally, this could mean occurring in 40-60% of observed opportunities or instances.</td></tr>
                                                        <tr><td><strong>2</strong></td><td>Seldom manifested</td><td class="text-start text-danger">The behavior, characteristic, or condition is demostrated infrequently and is generally absent in most relevant situation. Operationally, this could mean occurring in 25-40% of observed opportunities or instances.</td></tr>
                                                        <tr><td><strong>1</strong></td><td>Rarely manifested</td><td class="text-start text-danger">The behavior, characteristic, or condition is almost never demostrated, with only isolated or exceptional instances of occurrence. Operationally, this could mean occurring in 0-24% of observed opportunities or instances.</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>


                                            <h5 class="mb-3"><strong>C. Instruction: </strong>Read the benchmark statement carefully and rate the faculty on each
                                                statement using the above-listed rating scale by shading your rating. The Suggested Means of Verification
                                                column can be used by the supervisor to assist the faculty objectively </h5>
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
                                                            <th class="text-start">A. Manage of Teaching and Learning</th>
                                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                                <th><?= $i ?></th>
                                                            <?php endfor; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $questionIndex = 0;
                                                        $questions_A = [
                                                            "Comes to class on time regularly.",
                                                            "Submits updated syllabus, grade sheets, and other required reports on time.",
                                                            "Maximizes the allocated time/learning hours effectively.",
                                                            "Provide appropriate learning activities that facilitate critical thinking and creativity of students.",
                                                            "Guides students to learn on their own, reflect on new ideas and experiences, and make decisions in accomplishing given tasks.",
                                                            "Communicates constructive feedback to students for their academic growth."
                                                        ];
                                                        foreach ($questions_A as $question):
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
                                                        $questions_B = [
                                                            "Demonstrates extensive and broad knowledge of the subject/course.",
                                                            "Simplifies complex ideas in the lesson for ease of understanding.",
                                                            "Integrates contemporary issues and developments in the discipline and/or daily life activities in the syllabus.",
                                                            "Promotes active learning and student engagement by using appropriate teaching and learning resources including ICT Tools and platforms.",
                                                            "Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes"
                                                        ];
                                                        foreach ($questions_B as $question):
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
                                                        $questions_C = [
                                                            "Recognizes and values the unique diversity and individual differences among students.",
                                                            "Assist students with their learning challenges during consultation hours.",
                                                            "Provide immediate feedback on student outputs and performance.",
                                                            "Provides transparent and clear criteria in rating student's performance."
                                                        ];
                                                        foreach ($questions_C as $question):
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
                                                <textarea name="comments" id="comment" class="form-control" rows="3" placeholder="Write your feedback here..."></textarea>
                                            </div>

                                            <input type="hidden" name="evaluator_id" value="<?= $_SESSION['idnumber'] ?>">
                                            <input type="hidden" name="evaluator_position" value="<?= htmlspecialchars($evaluator_position) ?>">
                                            <input type="hidden" name="department" value="<?= htmlspecialchars($department) ?>">


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

                                            <div class="col-md-4 offset-md-4 mb-3">
                                                <button type="submit" class="btn btn-success btn-block w-100 <?= empty($faculty_list) || $evaluation_closed ? 'disabled-button' : '' ?>">
                                                    Submit Evaluation
                                                </button>
                                            </div>
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

    <script src="assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('input[type="radio"]');
            const totalScoreDisplay = document.getElementById('totalScore');
            const computedRatingDisplay = document.getElementById('computedRating');
            const submitButton = document.querySelector('button[type="submit"]');
            const facultySelect = document.getElementById('evaluatee_id'); // Corrected ID

            const totalQuestions = 15; // Total number of radio button questions

            function calculateScore() {
                let total = 0;
                let answeredQuestions = 0;

                for (let i = 0; i < totalQuestions; i++) {
                    const radioGroupName = `q${i}`;
                    const checkedRadio = document.querySelector(`input[name="${radioGroupName}"]:checked`);
                    if (checkedRadio) {
                        total += parseInt(checkedRadio.value);
                        answeredQuestions++;
                    }
                }

                totalScoreDisplay.textContent = total;

                // Calculate rating only if all questions are answered
                let rating = 0;
                if (answeredQuestions === totalQuestions) {
                    rating = ((total / (totalQuestions * 5)) * 100).toFixed(2);
                }
                computedRatingDisplay.value = `${rating}%`;
            }

            inputs.forEach(input => {
                input.addEventListener('change', calculateScore);
            });

            calculateScore(); // Initial calculation on page load

            // Disable submit button if no faculty to evaluate or if evaluation is closed
            if (facultySelect && facultySelect.options.length <= 1) { // Only the "-- Select Faculty --" option exists
                submitButton.classList.add('disabled-button');
                submitButton.disabled = true;
            }
        });
    </script>

    <?php if (!empty($errorMessage)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
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
                    text: 'Do you want to print the evaluation now?',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Print Now',
                    cancelButtonText: 'Print Later'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Assuming you pass the evaluatee_id to print page
                        // You might need to adjust this based on how you handle printing
                        window.location.href = "admin-evaluation-print.php?evaluatee_id=<?= $_SESSION['last_evaluated_faculty_id'] ?? '' ?>&academic_year=<?= $default_year ?>&semester=<?= $default_semester ?>";
                    } else {
                        Swal.fire({
                            title: 'Saved!',
                            text: 'You may print it later from your evaluated faculty list.',
                            icon: 'info',
                            timer: 3000
                        }).then(() => {
                             window.location.reload(); // Reload to refresh dropdown
                        });
                    }
                });
            });
        </script>
        <?php unset($_SESSION['last_evaluated_faculty_id']); ?> <?php endif; ?>

</body>

</html>
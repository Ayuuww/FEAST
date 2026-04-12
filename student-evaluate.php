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

$student_id = $_SESSION['idnumber'];
$academic_year = $default_year;
$semester = $default_semester;

// Fetch subjects
$query = "SELECT
              ss.subject_code,
              s.title AS subject_title,
              COALESCE(f.idnumber, a.idnumber) AS faculty_id,
              COALESCE(f.first_name, a.first_name) AS first_name,
              COALESCE(f.mid_name, a.mid_name) AS mid_name,
              COALESCE(f.last_name, a.last_name) AS last_name,
              COALESCE(f.status, a.status) AS status,
              COALESCE(f.college, ad.college_name, s.college) AS college,
              CASE
                  WHEN f.idnumber IS NOT NULL THEN 'faculty'
                  WHEN a.idnumber IS NOT NULL THEN 'admin'
                  ELSE 'unknown'
              END AS role
          FROM student_subject ss
          JOIN subject s ON ss.subject_code = s.code
          LEFT JOIN faculty f ON ss.faculty_id = f.idnumber AND f.status = 'active'
          LEFT JOIN admin a ON ss.admin_id = a.idnumber AND a.status = 'active'
          LEFT JOIN admin_college ad ON ad.admin_idnumber = a.idnumber
          WHERE ss.student_id = ?
            AND ss.academic_year = ?
            AND ss.semester = ?
            AND NOT EXISTS (
                SELECT 1 
                FROM evaluation e
                WHERE e.student_id   = ss.student_id
                  AND e.subject_code = ss.subject_code
                  AND e.faculty_id   = COALESCE(ss.faculty_id, ss.admin_id)
                  AND e.academic_year = ss.academic_year
                  AND e.semester      = ss.semester
            )";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $student_id, $academic_year, $semester);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
  $subjects[] = $row;
}

// ==========================================
// DYNAMIC FETCHING LOGIC
// ==========================================
$categories = [];
$total_active_questions = 0;

$cat_res = $conn->query("SELECT * FROM evaluation_categories ORDER BY order_by ASC");
if ($cat_res) {
  while ($cat = $cat_res->fetch_assoc()) {
    $cat_id = $cat['id'];
    $q_stmt = $conn->prepare("SELECT id, question_text FROM evaluation_questions WHERE category_id = ? AND status = 'active' ORDER BY order_by ASC");
    $q_stmt->bind_param("i", $cat_id);
    $q_stmt->execute();
    $q_result = $q_stmt->get_result();

    $questions = [];
    while ($q = $q_result->fetch_assoc()) {
      $questions[] = $q;
      $total_active_questions++;
    }
    $q_stmt->close();

    if (!empty($questions)) {
      $cat['questions'] = $questions;
      $categories[] = $cat;
    }
  }
}

// ✅ Fetch Rating Scales dynamically
$rating_scales = [];
$scale_res = $conn->query("SELECT * FROM evaluation_rating_scales ORDER BY scale_value DESC");
if ($scale_res) {
  while ($row = $scale_res->fetch_assoc()) {
    $rating_scales[] = $row;
  }
}
$max_rating_value = !empty($rating_scales) ? $rating_scales[0]['scale_value'] : 5;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
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

    .table-dark-header {
      background-color: #000 !important;
      color: #fff !important;
    }
  </style>
</head>

<body>

  <?php include 'student-header.php' ?>
  <?php include 'student-sidebar.php' ?>

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
                                  <option value="<?= $subjectCode . '|' . $facultyId ?>" data-college="<?= htmlspecialchars($row['college']) ?>">
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
                            <?php if (empty($rating_scales)): ?>
                              <tr>
                                <td colspan="3" class="text-danger">Rating scales have not been configured in the database.</td>
                              </tr>
                            <?php else: ?>
                              <?php foreach ($rating_scales as $scale): ?>
                                <tr>
                                  <td><strong><?= htmlspecialchars($scale['scale_value']) ?></strong></td>
                                  <td><?= htmlspecialchars($scale['qualitative_description']) ?></td>
                                  <td class="text-start text-danger"><?= htmlspecialchars($scale['operational_definition']) ?></td>
                                </tr>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>

                      <h5 class="mb-3"><strong>C. Instruction: </strong>Read the benchmark statements carefully. Please rate the faculty on each of the following statements below.</h5>
                      <div class="table-responsive">

                        <?php if (empty($categories) || empty($rating_scales)): ?>
                          <div class="alert alert-danger text-center">Missing evaluation questions or rating scales. Please contact the administrator.</div>
                        <?php else: ?>

                          <table class="table table-bordered text-center align-middle">
                            <thead class="table-secondary">
                              <tr>
                                <th rowspan="2" class="text-start align-middle" width="70%">Benchmark Statements for Faculty Teaching Effectiveness</th>
                                <th colspan="<?= count($rating_scales) ?>" class="text-center align-middle">Rating</th>
                              </tr>
                              <tr>
                                <?php foreach ($rating_scales as $scale): ?>
                                  <th width="6%"><?= htmlspecialchars($scale['scale_value']) ?></th>
                                <?php endforeach; ?>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $question_counter = 1;
                              $category_index = 0;
                              $category_colspan = count($rating_scales) + 1;

                              foreach ($categories as $category):
                                $letter = chr(65 + $category_index);
                              ?>
                                <tr>
                                  <td colspan="<?= $category_colspan ?>" class="text-start fw-bold table-dark-header">
                                    <?= $letter ?>. <?= htmlspecialchars($category['category_name']) ?>
                                  </td>
                                </tr>

                                <?php foreach ($category['questions'] as $q): ?>
                                  <tr>
                                    <td class="text-start"><?= $question_counter ?>. <?= htmlspecialchars($q['question_text']) ?></td>
                                    <?php foreach ($rating_scales as $scale): ?>
                                      <td><input type="radio" name="q_<?= $q['id'] ?>" value="<?= $scale['scale_value'] ?>" required></td>
                                    <?php endforeach; ?>
                                  </tr>
                                <?php
                                  $question_counter++;
                                endforeach;
                                ?>
                              <?php
                                $category_index++;
                              endforeach;
                              ?>
                              <tr class="table-light">
                                <th class="text-end pe-3">Total Score</th>
                                <th colspan="<?= count($rating_scales) ?>" id="totalScore" class="text-center text-danger fs-5">0</th>
                              </tr>
                            </tbody>
                          </table>
                        <?php endif; ?>

                      </div>

                      <div class="mb-3 mt-4">
                        <label for="comment" class="form-label">Other comments and suggestions (optional)</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Write your feedback here..."></textarea>
                      </div>

                      <input type="hidden" name="student_id" value="<?= $_SESSION['idnumber'] ?>">
                      <input type="hidden" name="is_anonymous" id="is_anonymous" value="no">

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
                      <input type="hidden" name="college" id="college_hidden">

                      <div class="col-md-4 offset-md-4 mb-3">
                        <button type="submit" class="btn btn-success btn-block w-100 <?= $evaluation_closed || empty($subjects) || empty($categories) ? 'disabled-button' : '' ?>"
                          <?= empty($subjects) || empty($categories) ? 'disabled' : '' ?>>
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

  </main>

  <?php include 'footer.php' ?>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const inputs = document.querySelectorAll('input[type="radio"]');
      const totalScoreDisplay = document.getElementById('totalScore');
      const computedRatingDisplay = document.getElementById('computedRating');
      const submitButton = document.querySelector('button[type="submit"]');
      const subjectSelect = document.getElementById('subject_code');

      // Dynamic variables passed from PHP
      const totalActiveQuestions = <?= $total_active_questions ?>;
      const maxPossibleScore = totalActiveQuestions * <?= $max_rating_value ?>;

      function calculateScore() {
        let total = 0;
        inputs.forEach(input => {
          if (input.checked) {
            total += parseInt(input.value);
          }
        });

        totalScoreDisplay.textContent = total;

        if (maxPossibleScore > 0) {
          const rating = ((total / maxPossibleScore) * 100).toFixed(2);
          computedRatingDisplay.value = `${rating}%`;
        } else {
          computedRatingDisplay.value = `0.00%`;
        }

        if (subjectSelect && subjectSelect.value !== "" && !<?= json_encode($evaluation_closed) ?> && totalActiveQuestions > 0) {
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

      const deptHiddenInput = document.getElementById('college_hidden');
      if (subjectSelect) {
        subjectSelect.addEventListener('change', function() {
          const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
          const college = selectedOption.getAttribute('data-college') || '';
          deptHiddenInput.value = college;
          calculateScore();
        });
      }

      calculateScore();
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
          text: "Thank you for your feedback.",
          icon: 'success',
          showCancelButton: false,
          confirmButtonText: 'Print',
        }).then((result) => {
          if (result.isConfirmed) {
            window.open('student-evaluation-print-fpdf.php', '_blank');
          }
          window.location.reload();
        });
      });
    </script>
    <?php unset($_SESSION['evaluation_success']); ?>
  <?php endif; ?>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('form[action="submit-evaluation.php"]');
      const anonInput = document.getElementById('is_anonymous');

      if (form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          Swal.fire({
            title: 'Submit Anonymously?',
            text: 'If you choose Yes, your name and ID will be hidden in the evaluation printout.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Be Anonymous',
            cancelButtonText: 'No, Show My Name',
            reverseButtons: true
          }).then((result) => {
            if (result.isConfirmed) {
              anonInput.value = 'yes';
            } else {
              anonInput.value = 'no';
            }
            form.submit();
          });
        });
      }
    });
  </script>
</body>

</html>
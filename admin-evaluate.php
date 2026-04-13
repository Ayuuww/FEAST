<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$evalRes = mysqli_query($conn, "SELECT status FROM evaluation_switch LIMIT 1");
$evalStatus = mysqli_fetch_assoc($evalRes)['status'] ?? 'off';
$evaluation_closed = $evalStatus === 'off';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$evaluator_id = $_SESSION['idnumber'];

$admin_info_stmt = $conn->prepare("SELECT position FROM admin WHERE idnumber = ? LIMIT 1");
$admin_info_stmt->bind_param("s", $evaluator_id);
$admin_info_stmt->execute();
$admin_result = $admin_info_stmt->get_result();
$admin_data = $admin_result->fetch_assoc();
$evaluator_position = $admin_data['position'] ?? 'Not Set';
$admin_info_stmt->close();

$allowed_positions = ['Dean', 'Chair Person', 'Program Chair', 'Director'];
if (!in_array($evaluator_position, $allowed_positions)) {
  $_SESSION['access_denied'] = "Access denied. Your position ($evaluator_position) is not allowed to perform faculty evaluations.";
  header("Location: admin-dashboard.php");
  exit();
}

$dept_stmt = $conn->prepare("SELECT college_name, program_name FROM admin_college WHERE admin_idnumber = ?");
$dept_stmt->bind_param("s", $evaluator_id);
$dept_stmt->execute();
$dept_result = $dept_stmt->get_result();

$assignments = [];
while ($row = $dept_result->fetch_assoc()) {
  $assignments[] = $row;
}
$dept_stmt->close();

if (empty($assignments)) {
  $_SESSION['msg'] = "No colleges or programs assigned to your account. Cannot find faculty to evaluate.";
  $_SESSION['msg_type'] = "error";
  header("Location: admin-dashboard.php");
  exit();
}

$setting_query = "SELECT semester, academic_year FROM evaluation_settings ORDER BY id DESC LIMIT 1";
$setting_result = $conn->query($setting_query);
$default_semester = '';
$default_year = '';

if ($setting_result && $setting_result->num_rows > 0) {
  $setting_row = $setting_result->fetch_assoc();
  $default_semester = $setting_row['semester'];
  $default_year = $setting_row['academic_year'];
}

$faculty_list = [];
if (!empty($assignments) && !empty($default_year) && !empty($default_semester)) {
  $params = [];
  $types = "";

  $subject_where_clauses = [];
  foreach ($assignments as $assign) {
    $subject_where_clauses[] = "(s.college = ? AND s.program = ?)";
    $params[] = $assign['college_name'];
    $params[] = $assign['program_name'];
    $types .= "ss";
  }
  $subject_where_sql = implode(' OR ', $subject_where_clauses);
  $query_part1 = "SELECT f.idnumber FROM faculty f JOIN subject s ON f.idnumber = s.faculty_id WHERE f.status = 'active' AND ($subject_where_sql)";

  $home_where_clauses = [];
  foreach ($assignments as $assign) {
    $home_where_clauses[] = "(f.college = ? AND f.program = ?)";
    $params[] = $assign['college_name'];
    $params[] = $assign['program_name'];
    $types .= "ss";
  }
  $home_where_sql = implode(' OR ', $home_where_clauses);
  $query_part2 = "SELECT f.idnumber FROM faculty f WHERE f.status = 'active' AND ($home_where_sql)";

  $combined_query = "
    SELECT f.idnumber, f.first_name, f.mid_name, f.last_name, f.faculty_rank, f.college AS home_college
    FROM faculty f
    WHERE f.idnumber IN ($query_part1 UNION $query_part2)
    AND NOT EXISTS (
        SELECT 1 FROM admin_evaluation ae
        WHERE ae.evaluatee_id = f.idnumber AND ae.evaluator_id = ? AND ae.academic_year = ? AND ae.semester = ?
    )
    ORDER BY f.last_name, f.first_name";

  $params[] = $evaluator_id;
  $params[] = $default_year;
  $params[] = $default_semester;
  $types .= "sss";

  $stmt = $conn->prepare($combined_query);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      $faculty_list[] = $row;
    }
  }
  if (isset($stmt)) $stmt->close();
}


// ==========================================
// DYNAMIC FETCHING: QUESTIONS WITH TEMPLATE/LOADOUT SYSTEM
// ==========================================
$categories = [];
$total_active_questions = 0;

// 1. Find the currently EQUIPPED SEF Template
$equipped_template_id = 1; // Default fallback
$template_query = $conn->query("SELECT id FROM sef_templates WHERE is_equipped = 1 LIMIT 1");
if ($template_query && $template_query->num_rows > 0) {
  $equipped_template_id = $template_query->fetch_assoc()['id'];
}

// 2. Fetch Categories ONLY for the equipped template
$cat_stmt = $conn->prepare("SELECT * FROM admin_evaluation_categories WHERE template_id = ? ORDER BY order_by ASC");
$cat_stmt->bind_param("i", $equipped_template_id);
$cat_stmt->execute();
$cat_res = $cat_stmt->get_result();

if ($cat_res) {
  while ($cat = $cat_res->fetch_assoc()) {
    $cat_id = $cat['id'];

    $q_stmt = $conn->prepare("SELECT id, question_text, verifications FROM admin_evaluation_questions WHERE category_id = ? AND status = 'active' ORDER BY order_by ASC");
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
$cat_stmt->close();

// ✅ Fetch Rating Scales dynamically (Ordered highest to lowest)
$rating_scales = [];
$scale_res = $conn->query("SELECT * FROM evaluation_rating_scales ORDER BY scale_value DESC");
if ($scale_res) {
  while ($row = $scale_res->fetch_assoc()) {
    $rating_scales[] = $row;
  }
}
// Get the highest possible rating score for math calculations
$max_rating_value = !empty($rating_scales) ? $rating_scales[0]['scale_value'] : 5;


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
  </style>
</head>

<body>
  <?php include 'admin-header.php' ?>
  <?php include 'admin-sidebar.php' ?>

  <main id="main" class="main">
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
                                echo '<option value="" disabled>No faculty to evaluate in your college or all have been evaluated.</option>';
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

                      <h5 class="mb-3"><strong>C. Instruction: </strong>Read the benchmark statement carefully and rate the faculty on each statement using the rating scale. Please check the applicable Means of Verification.</h5>
                      <div class="table-responsive">

                        <?php if (empty($categories) || empty($rating_scales)): ?>
                          <div class="alert alert-danger text-center">Missing evaluation questions or rating scales. Please contact the administrator.</div>
                        <?php else: ?>
                          <table class="table table-bordered text-center align-middle">
                            <thead class="table-dark">
                              <tr>
                                <th class="text-start" width="35%">Benchmark Statement</th>
                                <th class="text-start" width="35%">Suggested Means of Verification</th>
                                <?php foreach ($rating_scales as $scale): ?>
                                  <th width="6%"><?= htmlspecialchars($scale['scale_value']) ?></th>
                                <?php endforeach; ?>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $question_counter = 1;
                              $category_colspan = count($rating_scales) + 2;

                              foreach ($categories as $category):
                              ?>
                                <tr class="table-secondary">
                                  <td colspan="<?= $category_colspan ?>" class="text-start fw-bold text-uppercase">
                                    <?= htmlspecialchars($category['category_name']) ?>
                                  </td>
                                </tr>
                                <?php foreach ($category['questions'] as $q): ?>
                                  <tr>
                                    <td class="text-start small">
                                      <?= $question_counter ?>. <?= htmlspecialchars($q['question_text']) ?>
                                    </td>
                                    <td class="text-start">
                                      <?php
                                      $verifs = array_filter(array_map('trim', explode(',', $q['verifications'] ?? '')));
                                      if (empty($verifs)) {
                                        echo '<span class="text-muted small">No verifications set.</span>';
                                      } else {
                                        foreach ($verifs as $v_idx => $v_text):
                                      ?>
                                          <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="v_<?= $q['id'] ?>[]" value="<?= htmlspecialchars($v_text) ?>" id="v_<?= $q['id'] ?>_<?= $v_idx ?>">
                                            <label class="form-check-label small text-muted" for="v_<?= $q['id'] ?>_<?= $v_idx ?>">
                                              <?= htmlspecialchars($v_text) ?>
                                            </label>
                                          </div>
                                      <?php
                                        endforeach;
                                      }
                                      ?>
                                    </td>
                                    <?php foreach ($rating_scales as $scale): ?>
                                      <td><input type="radio" name="q_<?= $q['id'] ?>" value="<?= $scale['scale_value'] ?>" required></td>
                                    <?php endforeach; ?>
                                  </tr>
                                <?php
                                  $question_counter++;
                                endforeach;
                                ?>
                              <?php endforeach; ?>
                              <tr class="table-light">
                                <td colspan="2" class="text-end fw-bold">Total Score</td>
                                <td colspan="<?= count($rating_scales) ?>" id="totalScore" class="text-center text-danger fw-bold fs-5">0</td>
                              </tr>
                            </tbody>
                          </table>
                        <?php endif; ?>

                      </div>

                      <div class="mb-3 mt-4">
                        <label for="comment" class="form-label">Other comments and suggestions (optional)</label>
                        <textarea name="comments" id="comment" class="form-control" rows="3" placeholder="Write your feedback here..."></textarea>
                      </div>

                      <input type="hidden" name="evaluator_id" value="<?= $_SESSION['idnumber'] ?>">
                      <input type="hidden" name="evaluator_position" value="<?= htmlspecialchars($evaluator_position) ?>">
                      <input type="hidden" name="college" value="<?= htmlspecialchars($college ?? '') ?>">

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
                        <button type="submit" class="btn btn-success btn-block w-100 <?= empty($faculty_list) || $evaluation_closed || empty($categories) ? 'disabled-button' : '' ?>" <?= empty($faculty_list) || empty($categories) ? 'disabled' : '' ?>>
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
  </main>

  <?php include 'footer.php' ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const inputs = document.querySelectorAll('input[type="radio"]');
      const totalScoreDisplay = document.getElementById('totalScore');
      const computedRatingDisplay = document.getElementById('computedRating');
      const submitButton = document.querySelector('button[type="submit"]');
      const facultySelect = document.getElementById('evaluatee_id');

      const totalActiveQuestions = <?= $total_active_questions ?>;
      const maxPossibleScore = totalActiveQuestions * <?= $max_rating_value ?>;

      function calculateScore() {
        let total = 0;
        let answeredQuestions = 0;

        inputs.forEach(input => {
          if (input.checked) {
            total += parseInt(input.value);
            answeredQuestions++;
          }
        });

        totalScoreDisplay.textContent = total;

        let rating = 0;
        if (answeredQuestions === totalActiveQuestions && totalActiveQuestions > 0) {
          rating = ((total / maxPossibleScore) * 100).toFixed(2);
        }
        computedRatingDisplay.value = `${rating}%`;
      }

      inputs.forEach(input => {
        input.addEventListener('change', calculateScore);
      });

      calculateScore();

      if (facultySelect && facultySelect.options.length <= 1) {
        if (submitButton) {
          submitButton.classList.add('disabled-button');
          submitButton.disabled = true;
        }
      }
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
          icon: 'success',
          confirmButtonText: 'Print',
          allowOutsideClick: false,
          allowEscapeKey: false
        }).then(() => {
          window.open(
            "admin-evaluation-print-fpdf.php?evaluatee_id=<?= $_SESSION['last_evaluated_faculty_id'] ?? '' ?>&academic_year=<?= $default_year ?>&semester=<?= $default_semester ?>",
            "_blank"
          );
          window.location.reload();
        });
      });
    </script>
    <?php unset($_SESSION['last_evaluated_faculty_id']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['access_denied'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: 'Access Denied',
          text: <?= json_encode($_SESSION['access_denied']) ?>,
          confirmButtonText: 'OK',
          confirmButtonColor: '#d33',
          footer: 'Need help? Contact Superadmin'
        }).then(() => {
          window.location.href = "admin-dashboard.php";
        });
      });
    </script>
    <?php unset($_SESSION['access_denied']); ?>
  <?php endif; ?>

</body>

</html>
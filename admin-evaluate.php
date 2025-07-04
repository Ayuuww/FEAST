<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a faculty
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    header("Location: pages-login.php");
    exit();
}

$evaluator_id = $_SESSION['idnumber'];

// Get current faculty's department
$dept_query = "SELECT department FROM admin WHERE idnumber = ?";
$stmt = $conn->prepare($dept_query);
$stmt->bind_param("s", $evaluator_id);
$stmt->execute();
$dept_result = $stmt->get_result();
$dept_row = $dept_result->fetch_assoc();
$department = $dept_row['department'] ?? '';

// Fetch other faculty members from the same department
$query = "SELECT idnumber, first_name, mid_name, last_name, faculty_rank, department 
          FROM faculty 
          WHERE role = 'faculty' AND department = ? AND idnumber != ?";


$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $department, $evaluator_id);
$stmt->execute();
$result = $stmt->get_result();

$faculty_list = [];
while ($row = $result->fetch_assoc()) {
    $faculty_list[] = $row;
}


// Display message if set
if (isset($_SESSION['msg'])) {
    echo "<script>alert('" . addslashes($_SESSION['msg']) . "');</script>";
    unset($_SESSION['msg']);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Faculty Evaluate </title>

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

    <?php include 'faculty-header.php'?>

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-dashboard.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li><!-- End Dashboard Nav -->

        <!-- Evaluate Nav -->
        <li class="nav-item">
          <a class="nav-link collapse" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
            <li>
              <a href="admin-evaluate.php" class="active">
                <i class="bi bi-circle"></i><span>Form</span>
              </a>
            </li>
            <li>
              <a href="admin-faculty-evaluated.php">
                <i class="bi bi-circle"></i><span>Evaluated Peer</span>
              </a>
            </li>
          </ul>
        </li><!-- End Evaluate Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-evaluatedsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Subject</span>
          </a>
        </li><!-- End Profile Nav -->

        <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-user-profile.php">
            <i class="bi bi-person"></i>
            <span>Profile</span>
          </a>
        </li><!-- End Profile Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
          </a>
        </li><!-- End Sign out Nav -->

      </ul>

    </aside><!-- End Sidebar-->
    
    <!-- Start Main Content -->
    <main id="main" class="main">
      <div class="pagetitle">
        <h1>Peer Evaluation Form</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li>
            <li class="breadcrumb-item">Evaluate</li>
            <li class="breadcrumb-item active">Peer Evaluation</li>
          </ol>
        </nav>
      </div>

      <section class="section dashboard">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">
              <div class="card shadow-lg">
                <div class="card-body">
                  <h5 class="card-title text-center">Supervisor-to-Faculty Evaluation</h5>

                  <form action="submit-admin-evaluation.php" method="POST">

                    <!-- Faculty Dropdown -->
                    <div class="row mb-3">
                      <h5 class="mb-3"><strong>A. Faculty Information</strong></h5>
                    <!-- Evaluatee (Faculty) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                        <select name="evaluatee_id" class="form-select text-capitalize" required>
                            <option value="" disabled selected>-- Select Faculty --</option>
                            <?php foreach ($faculty_list as $faculty): 
                                  $fullName = htmlspecialchars($faculty['first_name'] . ' ' . $faculty['mid_name'] . ' ' . $faculty['last_name']);
                                  $rank = htmlspecialchars($faculty['faculty_rank']);
                                  $dept = htmlspecialchars($faculty['department']);
                              ?>
                                  <option value="<?= htmlspecialchars($faculty['idnumber']) ?>">
                                      <?= $fullName ?> (<?= $rank ?>) (<?= $dept ?>)
                                  </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="evaluatee_id">Faculty to Evaluate</label>
                        </div>
                    </div>

                    <!-- School Year -->
                    <div class="col-md-3">
                        <div class="form-floating">
                        <select name="school_year" id="school_year" class="form-select" required>
                            <option value="" disabled selected>-- Academic Year --</option>
                            <?php
                            $currentYear = date("Y");
                            for ($i = 0; $i < 5; $i++) {
                                $sy = ($currentYear - $i) . '-' . ($currentYear - $i + 1);
                                echo "<option value='$sy'>$sy</option>";
                            }
                            ?>
                        </select>
                        <label for="school_year">Academic Year</label>
                        </div>
                    </div>

                    <!-- Semester -->
                    <div class="col-md-3">
                        <div class="form-floating">
                        <select name="semester" id="semester" class="form-select" required>
                            <option value="" disabled selected>-- Semester --</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                        </select>
                        <label for="semester">Semester</label>
                        </div>
                    </div>
                    </div>

                    <!-- Rating Scale -->
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
                              occurring in 95-100% of observed opportunities or instances.
                            </td>
                          </tr>
                          <tr>
                            <td><strong>4</strong></td>
                            <td>Often manifested</td>
                            <td class="text-start text-danger">The behavior, characteristic, or condition is 
                              demostrated frequently, though occasional instances of non-manifestation may occur. 
                              Operationally, this could mean occurring in 60-94% of 
                              observed opportunities or instances.
                            </td>
                          </tr>
                          <tr>
                            <td><strong>3</strong></td>
                            <td>Sometimes manifested</td>
                            <td class="text-start text-danger">The behavior, characteristic, or condition is 
                              demostrated intermittenly or irregulary, with an approximately equal likelihood 
                              occurrence and non-occurence. Operationally, this could mean occurring in 40-60% 
                              of observed opportunities or instances.
                            </td>
                          </tr>
                          <tr>
                            <td><strong>2</strong></td>
                            <td>Seldom manifested</td>
                            <td class="text-start text-danger">The behavior, characteristic, or condition is 
                              demostrated infrequently and is generally absend in most relevant situation. 
                              Operationally, this could mean occurring in 25-40% of 
                              observed opportunities or instances.
                            </td>
                          </tr>
                          <tr>
                            <td><strong>1</strong></td>
                            <td>Rarely manifested</td>
                            <td class="text-start text-danger">The behavior, characteristic, or condition is 
                              almost never demostrated, with only isolated or exceptional instances of 
                              occurrence. Operationally, this could mean occurring in 0-24% of observed 
                              opportunities or instances.
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>


                    <!-- Evaluation Questions (Benchmark Style) -->
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
                              <th class="text-start">A. Manage of Teacking and Learning</th>
                              <?php for ($i = 5; $i >= 1; $i--): ?>
                                <th><?= $i ?></th>
                              <?php endfor; ?>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $questionIndex = 0;
                              $questions = [
                                "Comes to class on time regularly.",
                                "Submits updated syllabus, grade sheets, and other required reports on time.",
                                "Maximizes the allocated time/learning hours effectively.",
                                "Provide appropriate learning activities that facilitate critical thinking and creativity of students.",
                                "Guides students to learn on their own, reflect on new ideas and experiences, and make decisions in accomplishing given tasks.",
                                "Communicates constructive feedback to students for their academic growth."
                              ];
                              foreach ($questions as $question):
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
                              $questions = [
                                "Demonstrates extensive and broad knowledge of the subject/course.",
                                "Simplifies complex ideas in the lesson for ease of understanding.",
                                "Integrates contemporary issues and developments in the discipline and/or daily life activities in the syllabus.",
                                "Promotes active learning and student engagement by using appropriate teaching and learning resources including ICT Tools and platforms.",
                                "Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes"
                              ];
                              foreach ($questions as $question):
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
                              $questions = [
                                "Recognizes and values the unique diversity and individual differences among students.",
                                "Assist students with their learning challenges during consultation hours.",
                                "Provide immediate feedback on student outputs and performance.",
                                "Provides transparent and clear criteria in rating student's performance."
                              ];
                              foreach ($questions as $question):
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
                              </thead>
                              <thead class="table-light">
                                <tr>
                                  <th class="text-start">Total Score</th>
                                  <th colspan="5" id="totalScore" class="text-center text-danger fs-5">0</th>
                                </tr>
                              </thead>
                          </tbody>
                        </table>
                      </div>


                    <!-- Comment Box -->
                      <div class="mb-3">
                        <label for="comment" class="form-label">Other comments and suggestions (optional)</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Write your feedback here..."></textarea>
                      </div>

                      <input type="hidden" name="evaluator_id" value="<?= $_SESSION['idnumber'] ?>">

                      <!-- Computed Rating and Date -->
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

                      <div class="col-md-4 offset-md-4 mb3">
                        <button type="submit" class="btn btn-success btn-block w-100">Submit Evaluation</button>
                      </div>

                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <?php include'footer.php'?>
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="vendors/apexcharts/apexcharts.min.js"></script>
    <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendors/chart.js/chart.umd.js"></script>
    <script src="vendors/echarts/echarts.min.js"></script>
    <script src="vendors/quill/quill.js"></script>
    <script src="vendors/simple-datatables/simple-datatables.js"></script>
    <script src="vendors/tinymce/tinymce.min.js"></script>
    <script src="vendors/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll('input[type="radio"]');
        const totalScoreDisplay = document.getElementById('totalScore');
        const computedRatingDisplay = document.getElementById('computedRating');

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
        }

        inputs.forEach(input => {
          input.addEventListener('change', calculateScore);
        });

        calculateScore(); // Initial calc on load
      });

    </script>



  </body>

</html>

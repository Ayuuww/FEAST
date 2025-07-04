<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a student
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
    header("Location: pages-login.php");
    exit();
}


// Fetching subjects and their respective faculty

$student_id   = $_SESSION['idnumber'];
$academic_year  = $_GET['sy'] ?? '';
$semester     = $_GET['sem'] ?? '';

$query = "  SELECT 
            s.code AS subject_code,
            s.title AS subject_title,
            COALESCE(f.idnumber, a.idnumber) AS faculty_id,
            COALESCE(f.first_name, a.first_name) AS first_name,
            COALESCE(f.mid_name, a.mid_name) AS mid_name,
            COALESCE(f.last_name, a.last_name) AS last_name,
            CASE 
              WHEN f.idnumber IS NOT NULL THEN 'faculty'
              WHEN a.idnumber IS NOT NULL THEN 'admin'
              ELSE 'unknown'
            END AS role
          FROM student_subject ss
          JOIN subject s 
            ON ss.subject_code = s.code 
          LEFT JOIN faculty f ON ss.faculty_id = f.idnumber
          LEFT JOIN admin a ON ss.admin_id = a.idnumber AND a.faculty = 'yes'
          WHERE ss.student_id = ?
            AND NOT EXISTS (
              SELECT 1 FROM evaluation e 
              WHERE e.student_id    = ss.student_id 
                AND e.subject_code  = ss.subject_code
                AND e.faculty_id    = COALESCE(ss.faculty_id, ss.admin_id)
                AND e.academic_year = ?
                AND e.semester      = ?
            )";





$stmt   = $conn->prepare($query);
$stmt->bind_param("sss", $student_id, $academic_year, $semester);
$stmt->execute();
$result = $stmt->get_result();

$subjects       = [];
while ($row     = $result->fetch_assoc()) {
    $subjects[] = $row;
}

$dept_query = "SELECT DISTINCT department FROM faculty WHERE department IS NOT NULL AND department != '' ORDER BY department ASC";
$dept_result = mysqli_query($conn, $dept_query);
$department = [];

if ($dept_result && mysqli_num_rows($dept_result) > 0) {
    while ($row = mysqli_fetch_assoc($dept_result)) {
        $department[] = $row;
    }
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

    <?php include 'student-header.php'?>

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link collapsed" href="student-dashboard.php">
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
        </li><!-- End Evaluate Nav -->

        <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="student-user-profile.php">
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
      </div><!-- End Page Title -->

      <section class="section dashboard">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">
              <div class="card shadow-lg">
                <div class="card-body">
                  <h5 class="card-title text-center">Faculty Evaluation Form</h5>

                  <form action="submit-evaluation.php" method="POST">

                    <div class="row">
                      <h5 class="mb-3"><strong>A. Faculty Information</strong></h5>
                        <!-- Subject Dropdown -->
                        <div class="col-md-12 mb-3">
                          <div class="form-floating">
                            <select name="subject_code" id="subject_code" class="form-select text-capitalize" required>
                              <option value="" disabled selected>-- Select a Subject --</option>
                              <?php foreach ($subjects as $row): 
                                $facultyName  = htmlspecialchars($row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name']);
                                $subjectTitle = htmlspecialchars($row['subject_title']);
                                $subjectCode  = htmlspecialchars($row['subject_code']);
                                $facultyId    = htmlspecialchars($row['faculty_id']);
                                $tag          = $row['role'] === 'admin' ? ' (Admin)' : '';

                              ?>
                                <?php
                                $isAdminFaculty = empty($row['first_name']) && !empty($row['last_name']); // crude check
                                $tag = isset($row['role']) && $row['role'] === 'admin' ? ' (Admin)' : '';
                              ?>
                              <option value="<?= $subjectCode . '|' . $facultyId ?>">
                                <?= $subjectTitle ?> (<?= $subjectCode ?>) - <?= $facultyName . $tag ?>
                              </option>
                              <?php endforeach; ?>
                            </select>
                            <label for="subject_code" class="form-label">Subject</label>
                          </div>
                        </div>
                      
                        <!-- School Year Dropdown -->
                        <div class="col-md-4 mb-3">
                          <div class="form-floating">
                            <select name="academic_year" id="academic_year" class="form-select" required>
                              <option value="" disabled selected>-- Select Academic Year --</option>
                              <?php
                                $currentYear = date("Y");
                                for ($i = 0; $i < 5; $i++) {
                                  $sy = ($currentYear - $i) . '-' . ($currentYear - $i + 1);
                                  echo "<option value='$sy'>$sy</option>";
                                }
                              ?>
                            </select>
                            <label for="academic_year" class="form-label">Academic Year</label>
                          </div>
                        </div>

                        <!-- Department Dropdown -->
                        <div class="col-md-4 mb-3">
                          <div class="form-floating">
                            <select name="department" id="department" class="form-select" required>
                              <option value="" disabled selected>-- Select Instructor College --</option>
                              <?php foreach ($department as $row): 
                                $dept = htmlspecialchars($row['department']);
                              ?>
                                <option value="<?= $dept ?>"><?= $dept ?></option>
                              <?php endforeach; ?>
                            </select>
                            <label for="department" class="form-label">Instructor College</label>
                          </div>
                        </div>

                        <div class="col-md-4 mb-3">
                          <div class="form-floating">
                            <select name="semester" id="semester" class="form-select" required>
                              <option value="" disabled selected>-- Select Semester --</option>
                              <option value="1st Semester">1st Semester</option>
                              <option value="2nd Semester">2nd Semester</option>
                            </select>
                            <label for="department" class="form-label">Semester</label>
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
                              <td class="text-start text-danger">The behavior, characteristic, or condition is consistently and unfailling demostrated in all relevant situation or instances. There is no observed deviation from this pattern. Operationally, this could mean occurring in 95-100% of observed opportunities or instances.</td>
                            </tr>
                            <tr>
                              <td><strong>4</strong></td>
                              <td>Often manifested</td>
                              <td class="text-start text-danger">The behavior, characteristic, or condition is demostrated frequently, though occasional instances of non-manifestation may occur. Operationally, this could mean occurring in 60-94% of observed opportunities or instances.</td>
                            </tr>
                            <tr>
                              <td><strong>3</strong></td>
                              <td>Sometimes manifested</td>
                              <td class="text-start text-danger">The behavior, characteristic, or condition is demostrated intermittenly or irregulary, with an approximately equal likelihood occurrence and non-occurence. Operationally, this could mean occurring in 40-60% of observed opportunities or instances.</td>
                            </tr>
                            <tr>
                              <td><strong>2</strong></td>
                              <td>Seldom manifested</td>
                              <td class="text-start text-danger">The behavior, characteristic, or condition is demostrated infrequently and is generally absend in most relevant situation. Operationally, this could mean occurring in 25-40% of observed opportunities or instances.</td>
                            </tr>
                            <tr>
                              <td><strong>1</strong></td>
                              <td>Rarely manifested</td>
                              <td class="text-start text-danger">The behavior, characteristic, or condition is almost never demostrated, with only isolated or exceptional instances of occurrence. Operationally, this could mean occurring in 0-24% of observed opportunities or instances.</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      
                      <h5 class="mb-3"><strong>c. Instruction: </strong>Read the benchmark statements carefully. Please rate the faculty on each of the following statements below using the above-listed rating scale</h5>
                      <!-- Evaluation Questions -->
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
                                "Explains learning outcomes, expectations, grading system, and various requirements of the subject/course.",
                                "Maximizes the allocated time/learning hours effectively.",
                                "Facilitates students to think critically and creatively by providing appropriate learning activities.",
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
                                "Relates the subject matter to contemporary issues and developments in the discipline and/or daily life activities.",
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
                                "Recognizes and values the unique diversity and individuality difference among students.",
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

                      <input type="hidden" name="student_id" value="<?= $_SESSION['idnumber'] ?>">

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

                      <input type="hidden" name="student_section" value="<?= htmlspecialchars($student_section) ?>">



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
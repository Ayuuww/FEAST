<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a student
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
    header("Location: pages-login.php");
    exit();
}


// Fetching subjects and their respective faculty

$student_id = $_SESSION['idnumber'];
$school_year = $_GET['sy'] ?? '';
$semester = $_GET['sem'] ?? '';

$query = "SELECT 
            s.code AS subject_code,
            s.title AS subject_title,
            s.faculty_id,
            r.first_name, r.mid_name, r.last_name
          FROM student_subject ss
          JOIN subject s ON ss.subject_code = s.code AND ss.faculty_id = s.faculty_id
          JOIN register r ON ss.faculty_id = r.idnumber
          WHERE ss.student_id = ?
            AND NOT EXISTS (
              SELECT 1 FROM evaluation e 
              WHERE e.student_id = ss.student_id 
                AND e.subject_code = ss.subject_code
                AND e.faculty_id = ss.faculty_id
                AND e.school_year = ?
                AND e.semester = ?
            )";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $student_id, $school_year, $semester);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
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
        <h1>Form</h1>
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
                    <!-- Subject Dropdown -->
                    <div class="col-md-6 mb-3">
                      <div class="form-floating">
                        <select name="subject_code" id="subject_code" class="form-select text-capitalize" required>
                          <option value="" disabled selected>-- Select a Subject --</option>
                          <?php foreach ($subjects as $row): 
                            $facultyName = htmlspecialchars($row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name']);
                            $subjectTitle = htmlspecialchars($row['subject_title']);
                            $subjectCode = htmlspecialchars($row['subject_code']);
                            $facultyId = htmlspecialchars($row['faculty_id']);
                          ?>
                            <option value="<?= $subjectCode . '|' . $facultyId ?>">
                              <?= $subjectTitle ?> (<?= $subjectCode ?>) - <?= $facultyName ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <label for="subject_code" class="form-label">Subject</label>
                      </div>
                    </div>
                    
                      <!-- School Year Dropdown -->
                      <div class="col-md-3 mb-3">
                        <div class="form-floating">
                          <select name="school_year" id="school_year" class="form-select" required>
                            <option value="" disabled selected>-- Select School Year --</option>
                            <?php
                              $currentYear = date("Y");
                              for ($i = 0; $i < 5; $i++) {
                                $sy = ($currentYear - $i) . '-' . ($currentYear - $i + 1);
                                echo "<option value='$sy'>$sy</option>";
                              }
                            ?>
                          </select>
                          <label for="school_year" class="form-label">School Year</label>
                        </div>
                      </div>

                      <!-- Semester Dropdown -->
                      <div class="col-md-3 mb-3">
                        <div class="form-floating">
                          <select name="semester" id="semester" class="form-select" required>
                            <option value="" disabled selected>-- Select Semester --</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                          </select>
                          <label for="semester" class="form-label">Semester</label>
                        </div>
                      </div>

                    </div>

                    <!-- Evaluation Questions -->
                    <div class="table-responsive mb-4">
                      <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                          <tr>
                            <th class="text-start">Questions</th>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                              <th><?= $i ?></th>
                            <?php endfor; ?>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $questions = [
                            "The instructor demonstrates mastery of the subject.",
                            "The instructor encourages student participation.",
                            "The instructor communicates clearly.",
                            "The instructor is fair in grading.",
                            "The instructor is punctual and prepared.",
                            "The instructor provides timely feedback on assignments."
                          ];
                          foreach ($questions as $index => $question):
                          ?>
                            <tr>
                              <td class="text-start"><?= $index + 1 ?>. <?= $question ?></td>
                              <?php for ($i = 1; $i <= 5; $i++): ?>
                                <td>
                                  <input type="radio" name="q<?= $index ?>" id="q<?= $index ?>_<?= $i ?>" value="<?= $i ?>" required>
                                </td>
                              <?php endfor; ?>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>

                    <!-- Comment Box -->
                    <div class="mb-3">
                      <label for="comment" class="form-label">Additional Comments (optional)</label>
                      <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Write your feedback here..."></textarea>
                    </div>

                    <input type="hidden" name="student_id" value="<?= $_SESSION['idnumber'] ?>">

                    <div class="col-md-4 offset-md-4 mb3">
                      <button type="submit" class="btn btn-primary btn-block w-100">Submit Evaluation</button>
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

  </body>

</html>

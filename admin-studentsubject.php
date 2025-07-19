<?php
session_start();
include 'conn/conn.php';

$max_subjects = 9;

$query = "SELECT s.idnumber, s.first_name, s.mid_name, s.last_name, s.department, s.section, s.role, COUNT(ss.subject_code) AS subject_count
          FROM student s
          LEFT JOIN student_subject ss ON s.idnumber = ss.student_id
          WHERE s.role = 'student'
          GROUP BY s.idnumber
          HAVING subject_count < $max_subjects
          ORDER BY s.department, s.section";

$result = mysqli_query($conn, $query);

$subject_query = "SELECT ss.code, ss.title, ss.faculty_id, ss.admin_id,
                  COALESCE(f.first_name, a.first_name) AS first_name,
                  COALESCE(f.last_name, a.last_name) AS last_name
                  FROM subject ss
                  LEFT JOIN faculty f ON ss.faculty_id = f.idnumber
                  LEFT JOIN admin a ON ss.admin_id = a.idnumber";
$subject_result = mysqli_query($conn, $subject_query);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['assign'])) {
  $student_ids = $_POST['student_id'];
  $subject_codes = $_POST['subject_code']; // This is now an array

  $success = 0;
  $errors = [];

  foreach ($student_ids as $student_id) {
    foreach ($subject_codes as $subject_code) {
      // Get faculty_id or admin_id for this subject
      $query = "SELECT faculty_id, admin_id FROM subject WHERE code = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $subject_code);
      $stmt->execute();
      $result = $stmt->get_result();
      $subject_data = $result->fetch_assoc();

      if ($subject_data) {
        $faculty_id = $subject_data['faculty_id'] ?? null;
        $admin_id = $subject_data['admin_id'] ?? null;

        // Check if already assigned to avoid duplicates
        $check_stmt = $conn->prepare("SELECT * FROM student_subject WHERE student_id = ? AND subject_code = ?");
        $check_stmt->bind_param("ss", $student_id, $subject_code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
          // Insert
          $insert_stmt = $conn->prepare("INSERT INTO student_subject (student_id, subject_code, faculty_id, admin_id) VALUES (?, ?, ?, ?)");
          $insert_stmt->bind_param("ssss", $student_id, $subject_code, $faculty_id, $admin_id);
          if ($insert_stmt->execute()) {
            $success++;
          } else {
            $errors[] = "Failed to assign $subject_code.";
          }
        } else {
          $errors[] = "$subject_code already assigned.";
        }
      } else {
        $errors[] = "$subject_code not found.";
      }
    }
  }

  if ($success > 0) {
    $_SESSION['msg'] = "$success subject(s) successfully assigned.";
    $_SESSION['msg_type'] = 'success';
  } else {
    $_SESSION['msg'] = "No subjects were assigned. " . implode(" ", $errors);
    $_SESSION['msg_type'] = 'danger';
  }

  header("Location: admin-studentsubject.php");
  exit();
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Student Subject</title>

  <?php include 'header.php' ?>

  <style>
    div.dataTables_length {
      display: none;
    }

    select[multiple] {
      height: 400px;
    }

    .select2-results__options {
      max-height: 300px;
    }

    select[multiple] {
      height: auto;
      min-height: 300px;
      overflow-y: auto;
    }

    .mobiscroll-input {
      min-height: 50px;
    }

    .mobiscroll-select {
      max-height: 400px !important;
    }

    select.form-select {
      padding-top: 0.5rem;
    }

    .select2-container--default .select2-selection--multiple {
      min-height: 120px;
      padding: 8px;
      border: 1px solid #ced4da;
      border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
      background-color: #198754;
      border: none;
      color: white;
      padding: 3px 10px;
      margin-top: 4px;
      border-radius: 20px;
    }
  </style>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Mobiscroll CSS -->
  <!-- <link rel="stylesheet" href="https://cdn.mobiscroll.com/5.27.1/css/mobiscroll.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->


</head>

<body>

  <?php include 'admin-header.php' ?>

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
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="admin-evaluate.php">
              <i class="bi bi-circle"></i><span>Form</span>
            </a>
          </li>
          <li>
            <a href="admin-evaluatedfaculty.php">
              <i class="bi bi-circle"></i><span>Evaluated Faculty</span>
            </a>
          </li>
        </ul>
      </li><!-- End Evaluate Nav -->

      <!-- Subject Nav -->
      <li class="nav-item">
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
      </li><!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" href="admin-studentsubject.php">
          <i class="ri-book-fill"></i>
          <span>Assign Subject</span>
        </a>
      </li><!-- End Student Subject Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin-evaluatedsubject.php">
          <i class="bi bi-book-fill"></i>
          <span>Subject Evaluated</span>
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

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Assign Subject</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Student Subject</li>
          <li class="breadcrumb-item active">Assign Subject</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <?php if (isset($_SESSION['msg'])): ?>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          const type = <?= json_encode($_SESSION['msg_type'] ?? 'info') ?>;
          const msg = <?= json_encode($_SESSION['msg']) ?>;
          const errors = <?= json_encode($_SESSION['detailed_errors'] ?? []) ?>;

          if (errors.length > 0) {
            const htmlTable = `
          <table class="table table-bordered" style="text-align:left;">
            <thead>
              <tr><th>Skipped Details</th></tr>
            </thead>
            <tbody>
              ${errors.map(err => `<tr><td>${err}</td></tr>`).join('')}
            </tbody>
          </table>
        `;

            Swal.fire({
              icon: type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'error'),
              title: msg,
              html: htmlTable,
              width: 600,
              confirmButtonText: 'OK',
            });
          } else {
            Swal.fire({
              icon: type,
              title: msg,
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
          }
        });
      </script>
      <?php
      unset($_SESSION['msg'], $_SESSION['msg_type'], $_SESSION['detailed_errors']);
      ?>
    <?php endif; ?>




    <!-- Inserting Subject to Student Section -->
    <!-- Subject & Student Selection Form Section -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card shadow-sm p-4">
            <div class="card-body">
              <h5 class="card-title mb-4">Assign Subject to Students</h5>

              <form method="POST" action="assignsubject.php" class="row g-4">

                <!-- Department Filter -->
                <div class="col-md-2">
                  <label for="departmentFilter" class="form-label">Filter by Department</label>
                  <select id="departmentFilter" class="form-select">
                    <option value="">All Departments</option>
                    <?php
                    $students_by_dept = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                      $students_by_dept[$row['department']][] = $row;
                    }
                    ksort($students_by_dept);
                    foreach (array_keys($students_by_dept) as $dept): ?>
                      <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Section Filter -->
                <div class="col-md-2">
                  <label for="sectionFilter" class="form-label">Filter by Section</label>
                  <select id="sectionFilter" class="form-select">
                    <option value="">All Sections</option>
                    <?php
                    $sections = [];
                    foreach ($students_by_dept as $students) {
                      foreach ($students as $stu) {
                        $sections[$stu['section']] = true;
                      }
                    }
                    ksort($sections);
                    foreach (array_keys($sections) as $section): ?>
                      <option value="<?= htmlspecialchars($section) ?>"><?= htmlspecialchars($section) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Student Select -->
                <div class="col-md-4">
                  <label for="student_id" class="form-label">Students (Hold Ctrl for Multiple Selection)</label>
                  <select id="student_id" name="student_id[]" class="form-select" multiple>
                    <?php foreach ($students_by_dept as $department => $students): ?>
                      <optgroup label="<?= htmlspecialchars($department) ?>">
                        <?php foreach ($students as $student): ?>
                          <option value="<?= $student['idnumber'] ?>" data-section="<?= $student['section'] ?>" data-department="<?= $student['department'] ?>">
                            <?= $student['first_name'] . ' ' . $student['mid_name'] . ' ' . $student['last_name'] ?>
                          </option>
                        <?php endforeach; ?>
                      </optgroup>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Subject Select -->
                <div class="col-md-4">
                  <label for="subject_code" class="form-label">Subjects</label>
                  <select id="subject_code" name="subject_code[]" class="form-select" multiple>
                    <?php
                    $subjects_by_faculty = [];
                    while ($subject = mysqli_fetch_assoc($subject_result)) {
                      $instructor = trim($subject['first_name'] . ' ' . $subject['last_name']);
                      $subjects_by_faculty[$instructor][] = $subject;
                    }
                    foreach ($subjects_by_faculty as $faculty => $subjects): ?>
                      <optgroup label="<?= htmlspecialchars("Instructor: $faculty") ?>">
                        <?php foreach ($subjects as $sub): ?>
                          <option value="<?= $sub['code'] ?>"
                            data-faculty-id="<?= $sub['faculty_id'] ?? '' ?>"
                            data-admin-id="<?= $sub['admin_id'] ?? '' ?>">
                            <?= $sub['code'] . ": " . $sub['title'] ?>
                          </option>
                        <?php endforeach; ?>
                      </optgroup>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Submit Button -->
                <div class="col-12 d-flex justify-content-center">
                  <button type="submit" name="assign" class="btn btn-success px-5">Assign Selected Subjects</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- End Inserting Subject to Student Section -->

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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

  <!-- Mobiscroll JS -->
  <!-- Select2 CSS & JS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    $(document).ready(function() {
      // Store original student options for filtering
      const originalOptions = $('#student_id option').clone();

      // Initialize Select2 for both dropdowns
      $('#student_id').select2({
        placeholder: "Select Students",
        width: '100%',
        allowClear: true
      });

      $('#subject_code').select2({
        placeholder: "Select Subjects",
        width: '100%',
        allowClear: true
      });

      function filterStudents() {
        const selectedDept = $('#departmentFilter').val();
        const selectedSection = $('#sectionFilter').val();

        // Clear existing student options
        $('#student_id').empty();

        // Loop through original options
        originalOptions.each(function() {
          const dept = $(this).data('department');
          const section = $(this).data('section');

          const matchDept = !selectedDept || selectedDept === dept;
          const matchSection = !selectedSection || selectedSection === section;

          if (matchDept && matchSection) {
            $('#student_id').append($(this).clone());
          }
        });

        // Refresh Select2 with updated options
        $('#student_id').trigger('change.select2');
      }

      $('#departmentFilter').on('change', filterStudents);
      $('#sectionFilter').on('change', filterStudents);
    });
  </script>




</body>

</html>
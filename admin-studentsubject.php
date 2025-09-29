<?php
session_start();
include 'conn/conn.php';

// ✅ Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// ✅ Get admin’s department + position
$dept_stmt = $conn->prepare("SELECT department, position FROM admin WHERE idnumber = ? LIMIT 1");
$dept_stmt->bind_param("s", $admin_id);
$dept_stmt->execute();
$dept_res = $dept_stmt->get_result();
$admin_data = $dept_res->fetch_assoc() ?? [];
$dept_stmt->close();

$admin_dept = $admin_data['department'] ?? '';
$admin_position = $admin_data['position'] ?? '';

// ✅ Restrict allowed positions
$allowed_positions = ['Dean', 'Chair Person', 'Program Chair'];
if (!in_array($admin_position, $allowed_positions)) {
  $_SESSION['access_denied'] = "Access denied. Your position ($admin_position) is not allowed to assign student subjects.";
  header("Location: admin-dashboard.php");
  exit();
}

// ✅ Continue with fetching current academic year/semester
$current_period_query = "SELECT academic_year, semester FROM evaluation_settings ORDER BY updated_at DESC LIMIT 1";
$current_period_result = mysqli_query($conn, $current_period_query);

$current_academic_year = null;
$current_semester = null;

if ($current_period_result && mysqli_num_rows($current_period_result) > 0) {
  $current_period = mysqli_fetch_assoc($current_period_result);
  $current_academic_year = $current_period['academic_year'];
  $current_semester = $current_period['semester'];
}

// Check if current period is found. If not, set an error message and prevent assignment.
if (!$current_academic_year || !$current_semester) {
  $_SESSION['msg'] = "Error: Current academic year and semester are not set in evaluation settings. Please inform the superadmin to set them first.";
  $_SESSION['msg_type'] = 'danger';
  // If you want to stop execution completely and redirect immediately:
  // header("Location: admin-dashboard.php"); // Or a specific error page
  // exit();
  // Otherwise, the page will load, but the assignment logic below will be skipped
  // or insert NULLs if your student_subject columns allow it.
}

$max_subjects = 9; // This variable seems unused in the POST logic, but kept for context.

// Query to get students for the dropdown, limited by subject count.
// You might want to also filter this by current academic year/semester if students
// can be assigned subjects for different periods. For now, it remains global.
$query = "SELECT s.idnumber, s.first_name, s.mid_name, s.last_name, s.department, s.section, s.role, COUNT(ss.subject_code) AS subject_count
          FROM student s
          LEFT JOIN student_subject ss ON s.idnumber = ss.student_id
          WHERE s.role = 'student'
          GROUP BY s.idnumber
          ORDER BY s.department, s.section";


$result = mysqli_query($conn, $query);

// Query to get subjects for the dropdown
$subject_query = "SELECT ss.code, ss.title, ss.faculty_id, ss.admin_id,
                  COALESCE(f.first_name, a.first_name) AS first_name,
                  COALESCE(f.last_name, a.last_name) AS last_name
                  FROM subject ss
                  LEFT JOIN faculty f ON ss.faculty_id = f.idnumber
                  LEFT JOIN admin a ON ss.admin_id = a.idnumber";
$subject_result = mysqli_query($conn, $subject_query);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['assign'])) {
  // Ensure current academic period is set before proceeding with assignment
  if (!$current_academic_year || !$current_semester) {
    // Error message already set above, just redirect
    header("Location: admin-studentsubject.php");
    exit();
  }

  $student_ids = $_POST['student_id'];
  $subject_codes = $_POST['subject_code'];

  $success = 0;
  $_SESSION['detailed_errors'] = []; // Initialize detailed errors array

  foreach ($student_ids as $student_id) {
    foreach ($subject_codes as $subject_code) {
      // Get faculty_id or admin_id for this subject
      $stmt = $conn->prepare("SELECT faculty_id, admin_id FROM subject WHERE code = ?");
      $stmt->bind_param("s", $subject_code);
      $stmt->execute();
      $result_subject_data = $stmt->get_result(); // Renamed to avoid conflict with $result
      $subject_data = $result_subject_data->fetch_assoc();
      $stmt->close();

      if ($subject_data) {
        $faculty_id = $subject_data['faculty_id'] ?? null;
        $admin_id = $subject_data['admin_id'] ?? null;

        // Check if already assigned for the CURRENT academic year and semester
        $check_stmt = $conn->prepare("SELECT 1 FROM student_subject WHERE student_id = ? AND subject_code = ? AND academic_year = ? AND semester = ?");
        $check_stmt->bind_param("ssss", $student_id, $subject_code, $current_academic_year, $current_semester);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $check_stmt->close();

        if ($check_result->num_rows === 0) {
          // Insert with academic_year and semester
          if ($faculty_id) {
            $insert_stmt = $conn->prepare("INSERT INTO student_subject (student_id, subject_code, faculty_id, academic_year, semester) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssss", $student_id, $subject_code, $faculty_id, $current_academic_year, $current_semester);
          } else if ($admin_id) { // Use admin_id if faculty_id is not set
            $insert_stmt = $conn->prepare("INSERT INTO student_subject (student_id, subject_code, admin_id, academic_year, semester) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssss", $student_id, $subject_code, $admin_id, $current_academic_year, $current_semester);
          } else {
            $_SESSION['detailed_errors'][] = "❌ Subject **$subject_code** does not have an assigned faculty or admin instructor.";
            continue; // Skip this subject assignment
          }

          if ($insert_stmt->execute()) {
            $success++;
          } else {
            $_SESSION['detailed_errors'][] = "❌ Failed to assign **$subject_code** to student **$student_id**. DB Error: " . $conn->error;
          }
          $insert_stmt->close();
        } else {
          $_SESSION['detailed_errors'][] = "⚠️ Subject **$subject_code** is already assigned to student **$student_id** for the **$current_academic_year ($current_semester)**.";
        }
      } else {
        $_SESSION['detailed_errors'][] = "❌ Subject **$subject_code** not found in the subject list.";
      }
    }
  }

  if ($success > 0 && !empty($_SESSION['detailed_errors'])) {
    $_SESSION['msg'] = "Assigned **$success** subject(s). Some assignments were skipped or had issues.";
    $_SESSION['msg_type'] = 'warning';
  } elseif ($success > 0) {
    $_SESSION['msg'] = "**$success** subject(s) successfully assigned.";
    $_SESSION['msg_type'] = 'success';
  } else {
    $_SESSION['msg'] = "No subjects were assigned.";
    $_SESSION['msg_type'] = 'danger';
  }

  header("Location: admin-studentsubject.php");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->


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

</head>

<body>

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Assign Subject</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Student Subject</li>
          <li class="breadcrumb-item active">Assign Subject</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($_SESSION['msg'])) : ?>
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

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card shadow-sm p-4">
            <div class="card-body">
              <h5 class="card-title mb-4">Assign Subject to Students</h5>

              <form method="POST" action="admin-studentsubject.php" class="row g-4">
                <?php if ($current_academic_year && $current_semester) : ?>
                  <div class="col-12 mb-3">
                    <div class="alert alert-info" role="alert">
                      Current Academic Period: **<?= htmlspecialchars($current_academic_year) ?> - <?= htmlspecialchars($current_semester) ?>**
                    </div>
                  </div>
                <?php else : ?>
                  <div class="col-12 mb-3">
                    <div class="alert alert-warning" role="alert">
                      **Warning:** Current academic year and semester are not set. Subject assignments might not be recorded correctly. Please contact the superadmin.
                    </div>
                  </div>
                <?php endif; ?>

                <div class="col-md-2">
                  <label for="departmentFilter" class="form-label">Filter by Department</label>
                  <select id="departmentFilter" class="form-select">
                    <option value="">All Departments</option>
                    <?php
                    // Reset result pointer to reuse for display
                    mysqli_data_seek($result, 0);
                    $students_by_dept = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                      $students_by_dept[$row['department']][] = $row;
                    }
                    ksort($students_by_dept);
                    foreach (array_keys($students_by_dept) as $dept) : ?>
                      <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

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
                    foreach (array_keys($sections) as $section) : ?>
                      <option value="<?= htmlspecialchars($section) ?>"><?= htmlspecialchars($section) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-4">
                  <label for="student_id" class="form-label">Students (Hold Ctrl for Multiple Selection)</label>
                  <select id="student_id" name="student_id[]" class="form-select" multiple>
                    <?php foreach ($students_by_dept as $department => $students) : ?>
                      <optgroup label="<?= htmlspecialchars($department) ?>">
                        <?php foreach ($students as $student) : ?>
                          <option value="<?= $student['idnumber'] ?>" data-section="<?= $student['section'] ?>" data-department="<?= $student['department'] ?>">
                            <?= $student['first_name'] . ' ' . $student['mid_name'] . ' ' . $student['last_name'] ?> (<?= $student['subject_count'] ?> assigned)
                          </option>
                        <?php endforeach; ?>
                      </optgroup>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-4">
                  <label for="subject_code" class="form-label">Subjects (Hold Ctrl for Multiple Selection)</label>
                  <select id="subject_code" name="subject_code[]" class="form-select" multiple>
                    <?php
                    // Reset subject_result pointer if needed, or re-run query if the page needs fresh data after form submission
                    // For this scenario, if the page is loaded once, this is fine.
                    mysqli_data_seek($subject_result, 0); // Rewind result pointer
                    $subjects_by_faculty = [];
                    while ($subject = mysqli_fetch_assoc($subject_result)) {
                      $instructor = trim($subject['first_name'] . ' ' . $subject['last_name']);
                      // Handle cases where instructor name might be empty (e.g., if faculty/admin not linked)
                      if (empty($instructor)) {
                        $instructor = "Unassigned Instructor";
                      }
                      $subjects_by_faculty[$instructor][] = $subject;
                    }
                    ksort($subjects_by_faculty); // Sort by instructor name
                    foreach ($subjects_by_faculty as $faculty => $subjects) : ?>
                      <optgroup label="<?= htmlspecialchars("Instructor: $faculty") ?>">
                        <?php foreach ($subjects as $sub) : ?>
                          <option value="<?= $sub['code'] ?>"
                            data-faculty="<?= $faculty ?>"
                            data-faculty-id="<?= $sub['faculty_id'] ?? '' ?>"
                            data-admin-id="<?= $sub['admin_id'] ?? '' ?>">
                            <?= $sub['code'] . ": " . $sub['title'] ?>
                          </option>
                        <?php endforeach; ?>
                      </optgroup>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-12 d-flex justify-content-center">
                  <button type="submit" name="assign" class="btn btn-success px-5">Assign Selected Subjects</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>
  <?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>

  <script src="assets/js/main.js"></script>

  <link href="assets/css/select2.min.css" rel="stylesheet" />
  <script src="assets/js/select2.min.js"></script>

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
        allowClear: true,
        matcher: function(params, data) {
          if ($.trim(params.term) === '') {
            return data;
          }
          if (typeof data.text === 'undefined') {
            return null;
          }

          // search in subject text + faculty name
          const term = params.term.toLowerCase();
          const text = data.text.toLowerCase();
          const faculty = $(data.element).data('faculty')?.toLowerCase() || '';

          if (text.indexOf(term) > -1 || faculty.indexOf(term) > -1) {
            return data;
          }
          return null;
        }
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
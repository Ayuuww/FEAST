<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get admin’s department + position for access control
$pos_stmt = $conn->prepare("SELECT position FROM admin WHERE idnumber = ? LIMIT 1");
$pos_stmt->bind_param("s", $admin_id);
$pos_stmt->execute();
$admin_position = $pos_stmt->get_result()->fetch_assoc()['position'] ?? '';
$pos_stmt->close();

$allowed_positions = ['Dean', 'Chair Person', 'Program Chair', 'Director'];
if (!in_array($admin_position, $allowed_positions)) {
  $_SESSION['access_denied'] = "Access denied. Your position ($admin_position) is not allowed to assign student subjects.";
  header("Location: admin-dashboard.php");
  exit();
}

// Get current evaluation period
$current_period_query = "SELECT academic_year, semester FROM evaluation_settings ORDER BY updated_at DESC LIMIT 1";
$current_period_result = $conn->query($current_period_query);
$current_period = $current_period_result->fetch_assoc();
$current_academic_year = $current_period['academic_year'] ?? null;
$current_semester = $current_period['semester'] ?? null;

$success = 0;
$_SESSION['detailed_errors'] = [];

// --- Handle Manual Form Submission ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['assign'])) {
  if (!$current_academic_year || !$current_semester) {
    $_SESSION['msg'] = "Cannot assign subjects because the current academic period is not set.";
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-studentsubject.php");
    exit();
  }

  $student_ids = $_POST['student_id'] ?? [];
  $subject_codes = $_POST['subject_code'] ?? [];

  if (empty($student_ids) || empty($subject_codes)) {
    $_SESSION['msg'] = "You must select at least one student and one subject.";
    $_SESSION['msg_type'] = 'warning';
    header("Location: admin-studentsubject.php");
    exit();
  }

  foreach ($student_ids as $student_id) {
    foreach ($subject_codes as $subject_code) {
      // (Your existing, correct assignment logic is here)
      // This is a reusable function now
      assignSubject($conn, $student_id, $subject_code, $current_academic_year, $current_semester, $success);
    }
  }

  if ($success > 0 && !empty($_SESSION['detailed_errors'])) {
    $_SESSION['msg'] = "Assigned **$success** subject(s) manually, but some had issues.";
    $_SESSION['msg_type'] = 'warning';
  } elseif ($success > 0) {
    $_SESSION['msg'] = "**$success** subject(s) assigned successfully.";
    $_SESSION['msg_type'] = 'success';
  } else {
    $_SESSION['msg'] = "No new subjects were assigned. They may have already been assigned.";
    $_SESSION['msg_type'] = 'info';
  }

  header("Location: admin-studentsubject.php");
  exit();
}


// --- ✅ NEW: Handle Bulk CSV Upload ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['upload_bulk_assign'])) {
  if (!$current_academic_year || !$current_semester) {
    $_SESSION['msg'] = "Cannot assign subjects because the current academic period is not set.";
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-studentsubject.php");
    exit();
  }

  if (isset($_FILES['bulk_file']) && $_FILES['bulk_file']['error'] == UPLOAD_ERR_OK) {
    $file_tmp_path = $_FILES['bulk_file']['tmp_name'];
    $file_name = $_FILES['bulk_file']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if ($file_ext != 'csv') {
      $_SESSION['msg'] = "Upload failed. Only .csv files are allowed.";
      $_SESSION['msg_type'] = 'danger';
      header("Location: admin-studentsubject.php");
      exit();
    }

    if (($handle = fopen($file_tmp_path, "r")) !== FALSE) {
      // Skip the header row (student_id, subject_code)
      fgetcsv($handle);

      $line = 1;
      while (($row = fgetcsv($handle)) !== FALSE) {
        $line++;
        if (count($row) < 2) {
          $_SESSION['detailed_errors'][] = "⚠️ Line $line: Skipping row, expected 2 columns but found " . count($row);
          continue;
        }

        $student_id = trim($row[0]);
        $subject_code = trim($row[1]);

        if (empty($student_id) || empty($subject_code)) {
          $_SESSION['detailed_errors'][] = "⚠️ Line $line: Skipping row, student_id or subject_code is empty.";
          continue;
        }

        // Use the same assignment logic as the manual form
        assignSubject($conn, $student_id, $subject_code, $current_academic_year, $current_semester, $success);
      }
      fclose($handle);

      // Set final session messages
      if ($success > 0 && !empty($_SESSION['detailed_errors'])) {
        $_SESSION['msg'] = "Uploaded file processed. Assigned **$success** subject(s), but some rows had issues.";
        $_SESSION['msg_type'] = 'warning';
      } elseif ($success > 0) {
        $_SESSION['msg'] = "File upload successful. **$success** subject(s) assigned.";
        $_SESSION['msg_type'] = 'success';
      } else {
        $_SESSION['msg'] = "No new subjects were assigned from the file. They may have already been assigned.";
        $_SESSION['msg_type'] = 'info';
      }
    } else {
      $_SESSION['msg'] = "Error reading the uploaded CSV file.";
      $_SESSION['msg_type'] = 'danger';
    }
  } else {
    $_SESSION['msg'] = "File upload failed. Error code: " . $_FILES['bulk_file']['error'];
    $_SESSION['msg_type'] = 'danger';
  }
  header("Location: admin-studentsubject.php");
  exit();
}


/**
 * ✅ NEW: Reusable function to assign a subject to a student.
 * This function is used by BOTH the manual and bulk assign logic.
 */
function assignSubject($conn, $student_id, $subject_code, $ay, $sem, &$success_counter)
{
  // 1. Get subject/instructor details
  $stmt_subj = $conn->prepare("SELECT faculty_id, admin_id FROM subject WHERE code = ?");
  $stmt_subj->bind_param("s", $subject_code);
  $stmt_subj->execute();
  $subject_data = $stmt_subj->get_result()->fetch_assoc();
  $stmt_subj->close();

  if ($subject_data) {
    $faculty_id = $subject_data['faculty_id'] ?? null;
    $admin_id = $subject_data['admin_id'] ?? null;
    $instructor_id = $faculty_id ?: $admin_id;

    if (!$instructor_id) {
      $_SESSION['detailed_errors'][] = "❌ Subject **$subject_code** has no assigned instructor.";
      return;
    }

    // 2. Check if it already exists
    $check_stmt = $conn->prepare("SELECT 1 FROM student_subject WHERE student_id = ? AND subject_code = ? AND academic_year = ? AND semester = ?");
    $check_stmt->bind_param("ssss", $student_id, $subject_code, $ay, $sem);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows === 0) {
      // 3. Insert if it doesn't exist
      $insert_stmt = $conn->prepare("INSERT INTO student_subject (student_id, subject_code, faculty_id, admin_id, academic_year, semester) VALUES (?, ?, ?, ?, ?, ?)");
      $insert_stmt->bind_param("ssssss", $student_id, $subject_code, $faculty_id, $admin_id, $ay, $sem);
      if ($insert_stmt->execute()) {
        $success_counter++;
      } else {
        $_SESSION['detailed_errors'][] = "❌ DB Error assigning **$subject_code** to **$student_id**.";
      }
      $insert_stmt->close();
    } else {
      $_SESSION['detailed_errors'][] = "⚠️ Subject **$subject_code** already assigned to **$student_id** for this period.";
    }
    $check_stmt->close();
  } else {
    $_SESSION['detailed_errors'][] = "❌ Subject **$subject_code** (for student **$student_id**) does not exist in the database.";
  }
}


// --- Fetch data for the form (existing code) ---
$student_query = "SELECT s.idnumber, s.first_name, s.mid_name, s.last_name, s.department, s.section FROM student s WHERE s.role = 'student' ORDER BY s.department, s.last_name ASC";
$student_result = $conn->query($student_query);
$students_by_dept = [];
while ($row = $student_result->fetch_assoc()) {
  $students_by_dept[$row['department']][] = $row;
}

$subject_query = "
    SELECT ss.code, ss.title, ss.faculty_id, ss.admin_id,
           COALESCE(f.first_name, a.first_name) AS first_name,
           COALESCE(f.last_name, a.last_name) AS last_name
    FROM subject ss
    LEFT JOIN faculty f ON ss.faculty_id = f.idnumber
    LEFT JOIN admin a ON ss.admin_id = a.idnumber
    WHERE ss.admin_id = ?
    ORDER BY last_name, first_name, ss.title";

$subject_stmt = $conn->prepare($subject_query);
$subject_stmt->bind_param("s", $admin_id);
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();
$subjects_by_faculty = [];
while ($subject = $subject_result->fetch_assoc()) {
  $instructor = trim($subject['first_name'] . ' ' . $subject['last_name']);
  $instructor = empty($instructor) ? "Unassigned Instructor" : $instructor;
  $subjects_by_faculty[$instructor][] = $subject;
}
ksort($subjects_by_faculty);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
</head>

<body>
  <?php include 'admin-header.php' ?>
  <?php include 'admin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Assign Subject to Students</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Assign Subject</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          const type = <?= json_encode($_SESSION['msg_type'] ?? 'info') ?>;
          const msg = <?= json_encode($_SESSION['msg']) ?>;
          const errors = <?= json_encode($_SESSION['detailed_errors'] ?? []) ?>;

          if (errors.length > 0) {
            const htmlContent = `<div style="text-align: left; max-height: 200px; overflow-y: auto;"><ul>${errors.map(e => `<li>${e}</li>`).join('')}</ul></div>`;
            Swal.fire({
              icon: type,
              title: msg,
              html: htmlContent,
              confirmButtonText: 'OK'
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
      <?php unset($_SESSION['msg'], $_SESSION['msg_type'], $_SESSION['detailed_errors']); ?>
    <?php endif; ?>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card shadow-sm p-4 mb-4">
            <div class="card-body">
              <h5 class="card-title mb-4">Bulk Assign via CSV Upload</h5>
              <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
                <?php if (!$current_academic_year || !$current_semester): ?>
                  <div class="col-12">
                    <div class="alert alert-danger"><b>Warning:</b> Current academic period is not set. Please contact the superadmin.</div>
                  </div>
                <?php endif; ?>
                <div class="col-md-9">
                  <label for="bulk_file" class="form-label">Upload CSV File</label>
                  <input type="file" name="bulk_file" class="form-control" id="bulk_file" accept=".csv" required <?= (!$current_academic_year || !$current_semester) ? 'disabled' : '' ?>>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                  <button type="submit" name="upload_bulk_assign" class="btn btn-primary w-100" <?= (!$current_academic_year || !$current_semester) ? 'disabled' : '' ?>>
                    <i class="bi bi-upload"></i> Upload and Assign
                  </button>
                </div>
                <div class="col-12">
                  <p class="small text-muted mb-0">
                    File must be a .csv with two columns (in this order): <strong>student_id</strong> and <strong>subject_code</strong>.
                    <a href="templates/student_subject_template.csv" download>Download Template</a>
                  </p>
                </div>
              </form>
            </div>
          </div>

          <div class="card shadow-sm p-4">
            <div class="card-body">
              <h5 class="card-title mb-4">Manual Assignment for A.Y. <?= htmlspecialchars($current_academic_year ?? 'N/A') ?> / <?= htmlspecialchars($current_semester ?? 'N/A') ?></h5>

              <form method="POST" action="" class="row g-4">
                <div class="col-md-3">
                  <label for="departmentFilter" class="form-label">Filter Students by Department</label>
                  <select id="departmentFilter" class="form-select">
                    <option value="">All Departments</option>
                    <?php ksort($students_by_dept);
                    foreach (array_keys($students_by_dept) as $dept): ?>
                      <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="sectionFilter" class="form-label">Filter Students by Section</label>
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

                <div class="col-12">
                  <label for="student_id" class="form-label fw-bold">Select Students</label>
                  <select id="student_id" name="student_id[]" multiple>
                    <?php foreach ($students_by_dept as $department => $students): ?>
                      <optgroup label="<?= htmlspecialchars($department) ?>">
                        <?php foreach ($students as $student): ?>
                          <option value="<?= $student['idnumber'] ?>" data-section="<?= $student['section'] ?>" data-department="<?= $student['department'] ?>">
                            <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?> (<?= htmlspecialchars($student['section']) ?>)
                          </option>
                        <?php endforeach; ?>
                      </optgroup>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-12">
                  <label for="subject_code" class="form-label fw-bold">Select Subjects</label>
                  <select id="subject_code" name="subject_code[]" multiple>
                    <?php foreach ($subjects_by_faculty as $faculty => $subjects): ?>
                      <optgroup label="<?= htmlspecialchars($faculty) ?>">
                        <?php foreach ($subjects as $sub): ?>
                          <?php
                          $displayText = $sub['code'] . ': ' . $sub['title'] . ' — (' . $faculty . ')';
                          ?>
                          <option
                            value="<?= htmlspecialchars($sub['code']) ?>"
                            data-faculty="<?= htmlspecialchars($faculty) ?>">
                            <?= htmlspecialchars($displayText) ?>
                          </option>
                        <?php endforeach; ?>
                      </optgroup>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-12 d-flex justify-content-center">
                  <button type="submit" name="assign" class="btn btn-success px-5" <?= (!$current_academic_year || !$current_semester) ? 'disabled' : '' ?>>
                    Assign Selected Subjects
                  </button>
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

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/choices.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // --- Store Original Student Data ---
      const studentSelectElement = document.getElementById('student_id');
      const originalStudentData = Array.from(studentSelectElement.options).map(opt => ({
        value: opt.value,
        label: opt.textContent,
        department: opt.dataset.department,
        section: opt.dataset.section,
        groupLabel: opt.parentElement.label
      }));

      // --- Initialize Choices.js Dropdowns ---
      const studentChoices = new Choices(studentSelectElement, {
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Click to select students...',
        searchPlaceholderValue: 'Search for a student...'
      });
      const subjectSelect = document.getElementById('subject_code');
      const subjectChoices = new Choices(subjectSelect, {
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Click to select subjects...',
        searchPlaceholderValue: 'Search for a subject or instructor...',
        searchResultLimit: 100,
        shouldSort: false,
        fuseOptions: {
          includeScore: true,
          threshold: 0.4,
          keys: ['label', 'customProperties.faculty']
        }
      });

      // --- Filter Elements ---
      const deptFilter = document.getElementById('departmentFilter');
      const sectionFilter = document.getElementById('sectionFilter');

      function filterStudents() {
        const selectedDept = deptFilter.value;
        const selectedSection = sectionFilter.value;

        // Filter the JS array, not the DOM
        const filteredStudents = originalStudentData.filter(student => {
          const matchesDept = !selectedDept || student.department === selectedDept;
          const matchesSection = !selectedSection || student.section === selectedSection;
          return matchesDept && matchesSection;
        });

        // Group the filtered students by their original optgroup label
        const groupedStudents = filteredStudents.reduce((acc, student) => {
          if (!acc[student.groupLabel]) {
            acc[student.groupLabel] = [];
          }
          acc[student.groupLabel].push({
            value: student.value,
            label: student.label
          });
          return acc;
        }, {});

        // Format for Choices.js setChoices API
        const choicesData = Object.keys(groupedStudents).map(groupLabel => ({
          label: groupLabel,
          choices: groupedStudents[groupLabel]
        }));

        // Update the dropdown with the filtered and grouped data
        studentChoices.clearStore();
        studentChoices.setChoices(choicesData, 'value', 'label', true);
      }

      // --- Attach Event Listeners ---
      deptFilter.addEventListener('change', filterStudents);
      sectionFilter.addEventListener('change', filterStudents);
    });
  </script>
</body>

</html>
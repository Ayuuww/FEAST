<?php
require __DIR__ . '/vendor/autoload.php';

use Smalot\PdfParser\Parser;

session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// Get admin’s position
$pos_stmt = $conn->prepare("SELECT position FROM admin WHERE idnumber = ? LIMIT 1");
$pos_stmt->bind_param("s", $admin_id);
$pos_stmt->execute();
$admin_position = $pos_stmt->get_result()->fetch_assoc()['position'] ?? '';
$pos_stmt->close();

// Get admin's college and program
$college_stmt = $conn->prepare("
    SELECT college_name, program_name
    FROM admin_college
    WHERE admin_idnumber = ?
    LIMIT 1
");
$college_stmt->bind_param("s", $admin_id);
$college_stmt->execute();
$collegeRow = $college_stmt->get_result()->fetch_assoc();
$college_stmt->close();

$admin_college = $collegeRow['college_name'] ?? null;
$admin_program = $collegeRow['program_name'] ?? null;

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

/* -------------------------
   MANUAL FORM SUBMISSION
-------------------------- */
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
      assignSubject(
        $conn,
        $student_id,
        $subject_code,
        $current_academic_year,
        $current_semester,
        $success
      );
    }
  }

  if ($success > 0 && !empty($_SESSION['detailed_errors'])) {
    $_SESSION['msg'] = "Processed request. Assigned $success subjects, but some students had issues.";
    $_SESSION['msg_type'] = 'warning';
  } elseif ($success > 0) {
    $_SESSION['msg'] = "Assignment successful. Assigned $success subjects.";
    $_SESSION['msg_type'] = 'success';
  } elseif (!empty($_SESSION['detailed_errors'])) {
    $_SESSION['msg'] = "No subjects assigned. Please check the errors shown.";
    $_SESSION['msg_type'] = 'danger';
  } else {
    $_SESSION['msg'] = "No subjects assigned.";
    $_SESSION['msg_type'] = 'info';
  }

  header("Location: admin-studentsubject.php");
  exit();
}

/* -------------------------
   BULK PDF UPLOAD ONLY
-------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['upload_bulk_assign'])) {

  if (!$current_academic_year || !$current_semester) {
    $_SESSION['msg'] = "Cannot assign subjects because the current academic period is not set.";
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-studentsubject.php");
    exit();
  }

  if (!isset($_FILES['bulk_file']) || $_FILES['bulk_file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['msg'] = "File upload failed. Error code: " . ($_FILES['bulk_file']['error'] ?? 'Unknown');
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-studentsubject.php");
    exit();
  }

  $file_tmp_path = $_FILES['bulk_file']['tmp_name'];
  $file_name = $_FILES['bulk_file']['name'];
  $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

  // ONLY PDF
  if ($file_ext !== 'pdf') {
    $_SESSION['msg'] = "Upload failed. Only .pdf files are allowed.";
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-studentsubject.php");
    exit();
  }

  $dataRows = [];

  // ---------- PDF PARSER ----------
  $parser = new Parser();
  $pdf = $parser->parseFile($file_tmp_path);
  $text = $pdf->getText();

  // 1. SUBJECT CODE (e.g., "Subject Code: ISAE Units: 3 \n 106")
  $subject_code_detected = '';
  if (preg_match('/Subject\s*Code\s*:\s*([a-zA-Z]+).*?(?<!\d)(\d{2,5}[a-zA-Z]?)(?!\d)/is', $text, $m_code)) {
    $subject_code_detected = strtoupper(trim($m_code[1] . ' ' . $m_code[2]));
  } else {
    preg_match('/Subject Code\s*:\s*([A-Z0-9\-]+)/i', $text, $m_code_alt);
    $subject_code_detected = strtoupper(trim($m_code_alt[1] ?? ''));
  }

  // 2. SUBJECT TITLE (Descriptive)
  $subject_title_detected = '';
  if (preg_match('/Descriptive\s*:\s*(.*?)(?=Instructor\s*:)/is', $text, $m_title)) {
    $subject_title_detected = trim(preg_replace('/\s+/', ' ', $m_title[1]));
  } else {
    // Fallback if Instructor tag is missing
    preg_match('/Descriptive\s*:\s*([^\n\r]+)/i', $text, $m_title_fallback);
    $subject_title_detected = trim($m_title_fallback[1] ?? '');
  }

  // 3. INSTRUCTOR NAME PARSING
  preg_match('/Instructor\s*:\s*([^\n\r]+)/i', $text, $m_inst);
  $instructor_name_raw = trim($m_inst[1] ?? '');

  // 4. MATCH FACULTY BY NAME AND ASSIGN IDNUMBER
  $faculty_id = null;
  if ($instructor_name_raw !== '') {
    $instructor_name_raw = preg_replace('/\s+/', ' ', $instructor_name_raw);
    $last_name = '';
    $first_name_clean = '';

    if (strpos($instructor_name_raw, ',') !== false) {
      $parts = explode(',', $instructor_name_raw);
      $last_name = trim($parts[0]);
      $first_name_str = trim($parts[1] ?? '');
      $first_name_clean = explode(' ', $first_name_str)[0];
    } else {
      $parts = explode(' ', $instructor_name_raw);
      $last_name = array_pop($parts);
      $first_name_clean = $parts[0] ?? '';
    }

    $stmt = $conn->prepare("
        SELECT idnumber 
        FROM faculty 
        WHERE LOWER(last_name) = LOWER(?) 
          AND LOWER(first_name) LIKE LOWER(?)
        LIMIT 1
    ");
    $like_first = '%' . $first_name_clean . '%';
    $stmt->bind_param("ss", $last_name, $like_first);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$res && $last_name !== '') {
      $stmt2 = $conn->prepare("SELECT idnumber FROM faculty WHERE LOWER(last_name) = LOWER(?) LIMIT 1");
      $stmt2->bind_param("s", $last_name);
      $stmt2->execute();
      $res = $stmt2->get_result()->fetch_assoc();
      $stmt2->close();
    }

    if (!empty($res['idnumber'])) {
      $faculty_id = $res['idnumber'];
    }
  }

  // 5. STUDENT IDS
  preg_match_all('/\b\d{3}-\d{4}-\d\b/', $text, $matches);
  $student_ids = $matches[0];

  // ✅ NEW MAJORITY LOGIC FOR COLLEGE AND PROGRAM
  $majority_college = $admin_college; // Default fallback
  $majority_program = $admin_program; // Default fallback

  if (!empty($student_ids)) {
    // Create a string of question marks for the IN clause
    $in_placeholders = implode(',', array_fill(0, count($student_ids), '?'));
    $types = str_repeat('s', count($student_ids));

    $stmt_maj = $conn->prepare("SELECT college, program FROM student WHERE idnumber IN ($in_placeholders)");
    $stmt_maj->bind_param($types, ...$student_ids);
    $stmt_maj->execute();
    $res_maj = $stmt_maj->get_result();

    $freq = [];
    while ($sRow = $res_maj->fetch_assoc()) {
      $c = trim($sRow['college']);
      $p = trim($sRow['program']);
      if (!empty($c) && !empty($p)) {
        $key = $c . '|||' . $p; // Create a combined string key
        if (!isset($freq[$key])) $freq[$key] = 0;
        $freq[$key]++;
      }
    }
    $stmt_maj->close();

    // Find the combination with the highest frequency
    if (!empty($freq)) {
      arsort($freq); // Sorts highest to lowest
      $top_key = array_key_first($freq);
      list($majority_college, $majority_program) = explode('|||', $top_key);
    }
  }

  // 6. BUILD DATAROWS
  foreach ($student_ids as $sid) {
    $dataRows[] = [
      trim($sid),
      $subject_code_detected,
      $subject_title_detected,
      $faculty_id,
      $admin_id,
      $majority_college,  // ✅ Now dynamically set based on majority
      $majority_program   // ✅ Now dynamically set based on majority
    ];
  }

  $_SESSION['msg'] = "PDF roster processed: " . count($dataRows) . " students found.";
  $_SESSION['msg_type'] = 'success';

  // PROCESS DATA ROWS
  $line = 1;
  $created_subjects_in_loop = []; // Tracker array to prevent duplicate subject creation

  foreach ($dataRows as $row) {
    $line++;

    if (count($row) < 2) continue;

    $student_id   = trim($row[0]);
    $subject_code = isset($row[1]) ? trim($row[1]) : '';
    $subject_title = isset($row[2]) && trim($row[2]) !== ''
      ? trim($row[2])
      : ($subject_title_detected ?: 'Auto-created subject from OCR');
    $faculty_id   = isset($row[3]) ? trim($row[3]) : null;
    $admin_csv_id = isset($row[4]) ? trim($row[4]) : $admin_id;
    $college      = isset($row[5]) ? trim($row[5]) : "";
    $program      = isset($row[6]) ? trim($row[6]) : "";

    if (empty($student_id) || empty($subject_code)) {
      $_SESSION['detailed_errors'][] = "Row $line: Missing student ID or subject code in the PDF roster.";
      continue;
    }

    // AUTO-CREATE SUBJECT SAFELY (NO DUPLICATES)
    $cache_key = $subject_code . '|' . $college . '|' . $program;

    $subject_check = $conn->prepare("
      SELECT idnumber 
      FROM subject 
      WHERE code = ? 
        AND (college = ? OR ? = '') 
        AND (program = ? OR ? = '') 
      LIMIT 1
    ");
    $subject_check->bind_param("sssss", $subject_code, $college, $college, $program, $program);
    $subject_check->execute();
    $subRow = $subject_check->get_result()->fetch_assoc();
    $subject_check->close();

    if (!$subRow && !in_array($cache_key, $created_subjects_in_loop)) {

      $insert_subj = $conn->prepare("
        INSERT INTO subject (code, title, faculty_id, admin_id, college, program) 
        VALUES (?, ?, ?, ?, ?, ?)
      ");
      $insert_subj->bind_param("ssssss", $subject_code, $subject_title, $faculty_id, $admin_csv_id, $college, $program);

      if ($insert_subj->execute()) {
        $created_subjects_in_loop[] = $cache_key;
      } else {
        $_SESSION['detailed_errors'][] = "Row $line: Failed to auto-create missing subject <b>$subject_code</b>. Error: " . $insert_subj->error;
        continue;
      }
      $insert_subj->close();
    }

    $checkStudent = $conn->prepare("SELECT idnumber FROM student WHERE idnumber = ? LIMIT 1");
    $checkStudent->bind_param("s", $student_id);
    $checkStudent->execute();
    $studentExists = $checkStudent->get_result()->num_rows > 0;
    $checkStudent->close();

    if (!$studentExists) {
      $_SESSION['detailed_errors'][] = "Row $line: Student ID <b>$student_id</b> does not exist in the Student table.";
      continue;
    }

    assignSubject(
      $conn,
      $student_id,
      $subject_code,
      $current_academic_year,
      $current_semester,
      $success,
      $faculty_id,
      $admin_csv_id
    );
  }

  if ($success > 0 && !empty($_SESSION['detailed_errors'])) {
    $_SESSION['msg'] = "Some rows were assigned successfully, but others had errors. See details below.";
    $_SESSION['msg_type'] = 'warning';
  } elseif ($success > 0) {
    $_SESSION['msg'] = "Upload successful. Assigned $success subjects.";
    $_SESSION['msg_type'] = 'success';
  } elseif (!empty($_SESSION['detailed_errors'])) {
    $_SESSION['msg'] = "No subjects were assigned. Please review the errors below and correct them.";
    $_SESSION['msg_type'] = 'danger';
  } else {
    $_SESSION['msg'] = "No subjects assigned. The PDF did not contain any valid rows.";
    $_SESSION['msg_type'] = 'info';
  }

  header("Location: admin-studentsubject.php");
  exit();
}

/**
 * Reusable function to assign a subject to a student.
 */
function assignSubject($conn, $student_id, $subject_code, $ay, $sem, &$success_counter, $csv_faculty_id = null, $csv_admin_id = null)
{
  $stmt_subj = $conn->prepare("SELECT faculty_id, admin_id FROM subject WHERE code = ? LIMIT 1");
  $stmt_subj->bind_param("s", $subject_code);
  $stmt_subj->execute();
  $subject_data = $stmt_subj->get_result()->fetch_assoc();
  $stmt_subj->close();

  if (!$subject_data) {
    $_SESSION['detailed_errors'][] = "❌ Subject <b>$subject_code</b> not found in subject table.";
    return;
  }

  $faculty_id = $subject_data['faculty_id'];
  $admin_id   = $subject_data['admin_id'];

  $check_stmt = $conn->prepare("
      SELECT 1
      FROM student_subject 
      WHERE student_id = ? AND subject_code = ? AND academic_year = ? AND semester = ?
  ");
  $check_stmt->bind_param("ssss", $student_id, $subject_code, $ay, $sem);
  $check_stmt->execute();
  $exists = $check_stmt->get_result()->num_rows > 0;
  $check_stmt->close();

  if ($exists) {
    $_SESSION['detailed_errors'][] =
      "Student <b>$student_id</b> is already assigned to subject <b>$subject_code</b> for $ay / $sem.";
    return;
  }

  $insert = $conn->prepare("
      INSERT INTO student_subject
      (student_id, subject_code, faculty_id, admin_id, academic_year, semester)
      VALUES (?, ?, ?, ?, ?, ?)
  ");
  $insert->bind_param(
    "ssssss",
    $student_id,
    $subject_code,
    $faculty_id,
    $admin_id,
    $ay,
    $sem
  );
  $insert->execute();
  $insert->close();

  $success_counter++;
}

/* -------------------------
   FETCH DATA FOR FORMS
-------------------------- */
$student_query = "SELECT s.idnumber, s.first_name, s.mid_name, s.last_name, s.college, s.section
                  FROM student s
                  WHERE s.role = 'student'
                  ORDER BY s.college, s.last_name ASC";
$student_result = $conn->query($student_query);
$students_by_dept = [];
while ($row = $student_result->fetch_assoc()) {
  $students_by_dept[$row['college']][] = $row;
}

$college_stmt = $conn->prepare("
    SELECT college_name
    FROM admin_college
    WHERE admin_idnumber = ?
    LIMIT 1
");
$college_stmt->bind_param("s", $admin_id);
$college_stmt->execute();
$collegeRow = $college_stmt->get_result()->fetch_assoc();
$college_stmt->close();

$admin_college = $collegeRow['college_name'] ?? null;

$adminIds = [];
if ($admin_college) {
  $adm_stmt = $conn->prepare("
        SELECT admin_idnumber
        FROM admin_college
        WHERE college_name = ?
    ");
  $adm_stmt->bind_param("s", $admin_college);
  $adm_stmt->execute();
  $adm_res = $adm_stmt->get_result();
  while ($r = $adm_res->fetch_assoc()) {
    $adminIds[] = $r['admin_idnumber'];
  }
  $adm_stmt->close();
}

if (!empty($adminIds)) {
  $placeholders = implode(',', array_fill(0, count($adminIds), '?'));
  $types = str_repeat('s', count($adminIds));

  $subject_query = "
        SELECT ss.code, ss.title, ss.faculty_id, ss.admin_id,
               COALESCE(f.first_name, a.first_name) AS first_name,
               COALESCE(f.last_name, a.last_name) AS last_name
        FROM subject ss
        LEFT JOIN faculty f ON ss.faculty_id = f.idnumber
        LEFT JOIN admin   a ON ss.admin_id   = a.idnumber
        WHERE ss.admin_id IN ($placeholders)
        ORDER BY last_name, first_name, ss.title
    ";
  $subject_stmt = $conn->prepare($subject_query);
  $subject_stmt->bind_param($types, ...$adminIds);
} else {
  $subject_query = "
        SELECT ss.code, ss.title, ss.faculty_id, ss.admin_id,
               COALESCE(f.first_name, a.first_name) AS first_name,
               COALESCE(f.last_name, a.last_name) AS last_name
        FROM subject ss
        LEFT JOIN faculty f ON ss.faculty_id = f.idnumber
        LEFT JOIN admin   a ON ss.admin_id   = a.idnumber
        WHERE 1=0
    ";
  $subject_stmt = $conn->prepare($subject_query);
}

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

          const iconMap = {
            success: 'success',
            warning: 'warning',
            danger: 'error',
            info: 'info'
          };
          const icon = iconMap[type] || 'info';

          if (errors.length > 0) {
            const htmlContent =
              `<div style="text-align:left;max-height:250px;overflow-y:auto;">
                 <ul>${errors.map(e => `<li>${e}</li>`).join('')}</ul>
               </div>`;

            Swal.fire({
              icon: icon,
              title: msg,
              html: htmlContent,
              confirmButtonText: 'OK',
              confirmButtonColor: '#198754'
            });
          } else {
            Swal.fire({
              icon: icon,
              title: msg,
              showConfirmButton: false,
              timer: 2200,
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
              <h5 class="card-title mb-4">Bulk Assign via PDF Class Roster / OCR</h5>

              <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
                <?php if (!$current_academic_year || !$current_semester): ?>
                  <div class="col-12">
                    <div class="alert alert-danger"><b>Warning:</b> Current academic period is not set. Please contact the superadmin.</div>
                  </div>
                <?php endif; ?>

                <div class="col-md-9">
                  <label for="bulk_file" class="form-label fw-bold">Select PDF File</label>
                  <input type="file" name="bulk_file" id="bulk_file" class="form-control" accept=".pdf" required>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                  <button type="submit" name="upload_bulk_assign" class="btn btn-success w-100">
                    <i class="bi bi-upload"></i> Upload and Assign
                  </button>
                </div>

                <div class="col-12">
                  <p class="small text-muted mb-0">
                    Only <strong>PDF class rosters</strong> are supported. Students will be auto-assigned based on detected subject code and student IDs. <strong>
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
                  <label for="collegeFilter" class="form-label">Filter Students by College</label>
                  <select id="collegeFilter" class="form-select">
                    <option value="">All Colleges</option>
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

                <div class="col-md-3 d-flex align-items-end">
                  <button type="button" id="selectAllStudents" class="btn btn-success w-100">
                    Select All Filtered Students
                  </button>
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

                <div class="col-12">
                  <label for="student_id" class="form-label fw-bold">Select Students</label>
                  <select id="student_id" name="student_id[]" multiple>
                    <?php foreach ($students_by_dept as $college => $students): ?>
                      <optgroup label="<?= htmlspecialchars($college) ?>">
                        <?php foreach ($students as $student): ?>
                          <option value="<?= $student['idnumber'] ?>" data-section="<?= $student['section'] ?>" data-college="<?= $student['college'] ?>">
                            <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?> (<?= htmlspecialchars($student['section']) ?>)
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
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/choices.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const studentSelectElement = document.getElementById('student_id');
      const originalStudentData = Array.from(studentSelectElement.options).map(opt => ({
        value: opt.value,
        label: opt.textContent,
        college: opt.dataset.college,
        section: opt.dataset.section,
        groupLabel: opt.parentElement.label
      }));

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

      subjectSelect.addEventListener("change", function() {
        const selectedSubjects = subjectChoices.getValue(true);
        if (!selectedSubjects || selectedSubjects.length === 0) {
          const groups = {};
          originalStudentData.forEach(s => {
            if (!groups[s.groupLabel]) groups[s.groupLabel] = [];
            groups[s.groupLabel].push({
              value: s.value,
              label: s.label
            });
          });
          const formatted = Object.keys(groups).map(g => ({
            label: g,
            choices: groups[g]
          }));
          studentChoices.clearStore();
          studentChoices.setChoices(formatted, 'value', 'label', true);
          return;
        }

        const body = selectedSubjects.map(code => 'subject_codes[]=' + encodeURIComponent(code)).join('&');

        fetch('admin-get-assigned-students.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: body
          })
          .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
          })
          .then(assignedStudents => {
            const filtered = originalStudentData.filter(s => !assignedStudents.includes(s.value));
            const groups = {};
            filtered.forEach(s => {
              if (!groups[s.groupLabel]) groups[s.groupLabel] = [];
              groups[s.groupLabel].push({
                value: s.value,
                label: s.label
              });
            });
            const formatted = Object.keys(groups).map(g => ({
              label: g,
              choices: groups[g]
            }));
            studentChoices.clearStore();
            studentChoices.setChoices(formatted, 'value', 'label', true);
          })
          .catch(err => {
            console.error('Error fetching assigned students:', err);
          });
      });

      const deptFilter = document.getElementById('collegeFilter');
      const sectionFilter = document.getElementById('sectionFilter');

      function filterStudents() {
        const selectedDept = deptFilter.value;
        const selectedSection = sectionFilter.value;
        const filteredStudents = originalStudentData.filter(student => {
          const matchesDept = !selectedDept || student.college === selectedDept;
          const matchesSection = !selectedSection || student.section === selectedSection;
          return matchesDept && matchesSection;
        });
        const groupedStudents = filteredStudents.reduce((acc, student) => {
          if (!acc[student.groupLabel]) acc[student.groupLabel] = [];
          acc[student.groupLabel].push({
            value: student.value,
            label: student.label
          });
          return acc;
        }, {});

        const choicesData = Object.keys(groupedStudents).map(groupLabel => ({
          label: groupLabel,
          choices: groupedStudents[groupLabel]
        }));
        studentChoices.clearStore();
        studentChoices.setChoices(choicesData, 'value', 'label', true);
      }

      deptFilter.addEventListener('change', filterStudents);
      sectionFilter.addEventListener('change', filterStudents);

      const selectAllBtn = document.getElementById("selectAllStudents");
      selectAllBtn.addEventListener("click", function() {
        const displayedStudentIDs = Array.from(studentSelectElement.options)
          .filter(opt => !opt.disabled && opt.style.display !== "none")
          .map(opt => opt.value);

        if (displayedStudentIDs.length === 0) {
          Swal.fire({
            icon: "warning",
            title: "No Students Available",
            text: "There are no students in the current filter."
          });
          return;
        }

        displayedStudentIDs.forEach(id => {
          studentChoices.setChoiceByValue(id);
        });

        Swal.fire({
          icon: "success",
          title: "All Filtered Students Selected",
          text: `${displayedStudentIDs.length} students selected.`,
          timer: 1500,
          showConfirmButton: false
        });
      });
    });
  </script>
</body>

</html>
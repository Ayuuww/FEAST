<?php
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
  }

  // Summary
  if ($success > 0 && !empty($_SESSION['detailed_errors'])) {
    $_SESSION['msg'] = "Processed file. Assigned $success subjects, but some rows had issues.";
    $_SESSION['msg_type'] = 'warning';
  } elseif ($success > 0) {
    $_SESSION['msg'] = "Upload successful. Assigned $success subjects.";
    $_SESSION['msg_type'] = 'success';
  } elseif (!empty($_SESSION['detailed_errors'])) {
    // Check if all errors are warnings about already assigned
    $onlyAlreadyAssigned = true;
    foreach ($_SESSION['detailed_errors'] as $err) {
      if (stripos($err, 'already assigned') === false) {
        $onlyAlreadyAssigned = false;
        break;
      }
    }
    if ($onlyAlreadyAssigned) {
      $_SESSION['msg'] = "No new subjects assigned because they were already assigned to the students for this period.";
      $_SESSION['msg_type'] = 'info';
    } else {
      $_SESSION['msg'] = "No subjects assigned. Please check your file format or correct the errors.";
      $_SESSION['msg_type'] = 'danger';
    }
  } else {
    $_SESSION['msg'] = "No subjects assigned.";
    $_SESSION['msg_type'] = 'info';
  }

  header("Location: admin-studentsubject.php");
  exit();
}


// --- UPDATED: Handle Bulk CSV Upload ---
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

  if ($file_ext !== 'csv') {
    $_SESSION['msg'] = "Upload failed. Only .csv files are allowed.";
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin-studentsubject.php");
    exit();
  }

  $dataRows = [];

  // ========================
  // OPTION A: CSV FILE
  // ========================
  if ($file_ext === 'csv') {
    if (($fp = fopen($file_tmp_path, "r")) !== FALSE) {
      $bom = fread($fp, 3);
      if ($bom !== "\xEF\xBB\xBF") rewind($fp);

      fgetcsv($fp); // skip header
      while (($row = fgetcsv($fp)) !== FALSE) {
        if (array_filter($row)) {
          $dataRows[] = $row;
        }
      }
      fclose($fp);
    }
  }


  // ========================
  // PROCESS DATA ROWS
  // ========================
  $line = 1; // Start at 1 (header was 1, so first data row is line 2)

  $missing_subjects = [];

  foreach ($dataRows as $row) {
    $line++;

    // Expecting 7 columns: student_id, subject_code, subject_title, faculty_id, admin_id, college, program
    if (count($row) < 2) continue; // You may want to use count($row) < 7 for strict checking

    $student_id    = trim($row[0]);
    $subject_code  = trim($row[1]);
    $subject_title = isset($row[2]) ? trim($row[2]) : "Untitled Subject";
    $faculty_id    = isset($row[3]) ? trim($row[3]) : null;
    $admin_csv_id  = isset($row[4]) ? trim($row[4]) : $admin_id;
    $college    = isset($row[5]) ? trim($row[5]) : "";
    $program       = isset($row[6]) ? trim($row[6]) : "";

    if (empty($student_id) || empty($subject_code)) {
      $_SESSION['detailed_errors'][] = "⚠️ Row $line: Missing student_id or subject_code.";
      continue;
    }

    // --- Check if subject exists and get current values ---
    // --- Check if this specific offering (code + college + program + faculty) exists ---
    $subject_check = $conn->prepare("
    SELECT idnumber, code, title, faculty_id, admin_id, college, program
    FROM subject
    WHERE code = ?
      AND ( (college = ? OR (? = '')) )
      AND ( (program = ? OR (? = '')) )
      AND ( (faculty_id = ? OR (? = '')) )
    LIMIT 1
");
    $colParam  = $college;   // from CSV
    $progParam = $program;   // from CSV
    $facParam  = $faculty_id; // from CSV

    $subject_check->bind_param(
      "sssssss",
      $subject_code,
      $colParam,
      $colParam,
      $progParam,
      $progParam,
      $facParam,
      $facParam
    );
    $subject_check->execute();
    $subject_row = $subject_check->get_result()->fetch_assoc();
    $subject_check->close();

    if (!$subject_row) {
      // --- CREATE NEW SUBJECT (same code allowed, different faculty/college/program) ---
      $insert_subject = $conn->prepare("
        INSERT INTO subject
        (code, title, faculty_id, admin_id, college, program)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
      $insert_subject->bind_param(
        "ssssss",
        $subject_code,
        $subject_title,
        $faculty_id,     // may be null
        $admin_csv_id,
        $college,        // where to assign
        $program
      );
      $insert_subject->execute();
      $insert_subject->close();

      $_SESSION['detailed_errors'][] =
        "ℹ️ NEW subject <b>{$subject_code}</b> created for faculty <b>{$faculty_id}</b> in <b>{$college}</b>/<b>{$program}</b>.";
    } else {
      // --- UPDATE ONLY THIS SPECIFIC OFFERING ---
      $new_title   = !empty($subject_title) ? $subject_title   : $subject_row['title'];
      $new_admin   = !empty($admin_csv_id) ? $admin_csv_id     : $subject_row['admin_id'];
      $new_college = !empty($college)      ? $college          : $subject_row['college'];
      $new_program = !empty($program)      ? $program          : $subject_row['program'];
      $new_faculty = !empty($faculty_id)   ? $faculty_id       : $subject_row['faculty_id'];

      $update_subject = $conn->prepare("
        UPDATE subject
        SET title = ?, faculty_id = ?, admin_id = ?, college = ?, program = ?
        WHERE idnumber = ?
    ");
      $update_subject->bind_param(
        "sssssi",
        $new_title,
        $new_faculty,
        $new_admin,
        $new_college,
        $new_program,
        $subject_row['idnumber']
      );
      $update_subject->execute();
      $update_subject->close();

      $_SESSION['detailed_errors'][] =
        "🔄 Updated existing subject <b>{$subject_code}</b> for faculty <b>{$new_faculty}</b> in <b>{$new_college}</b>/<b>{$new_program}</b>.";
    }

    // --- Check student exists ---
    $checkStudent = $conn->prepare("SELECT idnumber FROM student WHERE idnumber = ? LIMIT 1");
    $checkStudent->bind_param("s", $student_id);
    $checkStudent->execute();
    $studentExists = $checkStudent->get_result()->num_rows > 0;
    $checkStudent->close();

    if (!$studentExists) {
      $_SESSION['detailed_errors'][] = "❌ Row $line: Student ID $student_id does not exist.";
      continue;
    }

    // --- Assign Subject ---
    assignSubject(
      $conn,
      $student_id,
      $subject_code,
      $current_academic_year,
      $current_semester,
      $success,
      $faculty_id,      // <-- from CSV
      $admin_csv_id     // <-- from CSV or logged admin
    );
  }
  $_SESSION['missing_subjects'] = array_unique($missing_subjects);

  // Summary
  if ($success > 0 && !empty($_SESSION['detailed_errors'])) {
    $_SESSION['msg'] = "Processed file. Assigned $success subjects, but some rows had issues.";
    $_SESSION['msg_type'] = 'warning';
  } elseif ($success > 0) {
    $_SESSION['msg'] = "Upload successful. Assigned $success subjects.";
    $_SESSION['msg_type'] = 'success';
  } elseif (!empty($_SESSION['detailed_errors'])) {
    // Check if ALL errors are "already assigned"
    $onlyAlreadyAssigned = true;
    foreach ($_SESSION['detailed_errors'] as $err) {
      if (stripos($err, 'already assigned') === false) { // Not just "already assigned"
        $onlyAlreadyAssigned = false;
        break;
      }
    }
    if ($onlyAlreadyAssigned) {
      $_SESSION['msg'] = "No new subjects assigned because all subjects were already assigned to these students for this academic period.";
      $_SESSION['msg_type'] = 'info';
    } else {
      $_SESSION['msg'] = "No subjects assigned. Please check your file format or correct the errors.";
      $_SESSION['msg_type'] = 'danger';
    }
  } else {
    $_SESSION['msg'] = "No subjects assigned.";
    $_SESSION['msg_type'] = 'info';
  }


  header("Location: admin-studentsubject.php");
  exit();
}

/**
 * ✅ NEW: Reusable function to assign a subject to a student.
 * This function is used by BOTH the manual and bulk assign logic.
 */
function assignSubject($conn, $student_id, $subject_code, $ay, $sem, &$success_counter, $csv_faculty_id = null, $csv_admin_id = null)
{
  // 1. Decide instructor from CSV directly
  $faculty_id = $csv_faculty_id;
  $admin_id   = $csv_admin_id;

  // Optional safety: if both are empty, try to read from subject table once
  if (empty($faculty_id) && empty($admin_id)) {
    $stmt_subj = $conn->prepare("SELECT faculty_id, admin_id FROM subject WHERE code = ? LIMIT 1");
    $stmt_subj->bind_param("s", $subject_code);
    $stmt_subj->execute();
    $subject_data = $stmt_subj->get_result()->fetch_assoc();
    $stmt_subj->close();

    if ($subject_data) {
      if (empty($faculty_id)) $faculty_id = $subject_data['faculty_id'];
      if (empty($admin_id))   $admin_id   = $subject_data['admin_id'];
    }
  }

  // Ensure at least one of them is present
  if (empty($faculty_id) && empty($admin_id)) {
    $_SESSION['detailed_errors'][] = "❌ Subject <b>$subject_code</b> has no faculty/admin in CSV or subject table.";
    return;
  }

  // 2. Check if already assigned
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
    // Already assigned; no insert
    return;
  }

  // 3. Insert assignment with the CSV faculty/admin
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

// --- Fetch data for the form (existing code) ---
$student_query = "SELECT s.idnumber, s.first_name, s.mid_name, s.last_name, s.college, s.section FROM student s WHERE s.role = 'student' ORDER BY s.college, s.last_name ASC";
$student_result = $conn->query($student_query);
$students_by_dept = [];
while ($row = $student_result->fetch_assoc()) {
  $students_by_dept[$row['college']][] = $row;
}

// 1) Get this admin's college
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

// 2) Get all admin IDs in the same college
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

// 3) Build subject query
if (!empty($adminIds)) {
  // build an IN (...) with placeholders
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
  // fallback: no college info, show nothing or all
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

    <?php if (!empty($_SESSION['missing_subjects'])): ?>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          const missingSubjects = <?= json_encode($_SESSION['missing_subjects']) ?>;
          const errors = <?= json_encode($_SESSION['detailed_errors'] ?? []) ?>;
          let htmlContent = "<strong>The following subject codes are NOT listed in your subject table:</strong><br><ul>";
          missingSubjects.forEach((sub) => htmlContent += "<li><b>" + sub + "</b></li>");
          htmlContent += "</ul>";

          if (errors.length > 0) {
            htmlContent += `<div style="margin-top:10px;text-align:left;"><strong>Other Errors:</strong><ul>${errors.map(e => `<li>${e}</li>`).join('')}</ul></div>`;
          }

          Swal.fire({
            icon: "warning",
            title: "Missing Subjects Detected",
            html: htmlContent,
            confirmButtonText: "OK"
          });
        });
      </script>
      <?php unset($_SESSION['missing_subjects'], $_SESSION['detailed_errors']); ?>
    <?php endif; ?>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card shadow-sm p-4 mb-4">
            <div class="card-body">
              <h5 class="card-title mb-4">Bulk Assign via CSV Upload</h5>
              <div class="alert alert-info border-info mt-2" style="font-size: 14px;">
                <strong>📌 Reminder:</strong><br>
                <ul class="mb-0">
                  <li><strong>Only <code>student_id</code> and <code>subject_code</code></strong> need to be filled in for every row.</li>
                  <li>Other fields (<code>subject_title</code>, <code>faculty_id</code>, <code>admin_id</code>, <code>college</code>, <code>program</code>)
                    can be entered <strong>once</strong> and reused automatically for new subjects.</li>
                  <li>If the subject already exists in the database, you may leave the extra fields <strong>blank</strong>.</li>
                  <li>If the subject does <strong>not</strong> exist, the system will create it using the additional fields from your CSV.</li>
                </ul>
              </div>

              <!-- CSV Template Download Button -->
              <div class="mb-3">
                <a href="downloads/sample_template.csv" class="btn btn-success" download>
                  <i class="bi bi-download"></i> Download CSV Template
                </a>
                <span class="small text-muted ms-2">
                  Use this file as your starting template.
                </span>
              </div>
              <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
                <?php if (!$current_academic_year || !$current_semester): ?>
                  <div class="col-12">
                    <div class="alert alert-danger"><b>Warning:</b> Current academic period is not set. Please contact the superadmin.</div>
                  </div>
                <?php endif; ?>
                <div class="mt-2 p-3 border rounded bg-light">
                  <strong>✔ Correct Sample CSV Format:</strong>
                  <pre class="border p-2 bg-white" style="white-space: pre-wrap; font-size: 90%;">
student_id|subject_code|subject_title     |faculty_id|admin_id|college                       |program                                   |
202-3110-1|IT101       |Introduction to IT|10001     |00001   |COLLEGE OF INFORMATION SYSTEMS|Bachelor of Science in Information Systems|
202-3110-2|IT101       |
202-3121-2|IT101       |
        </pre>
                  <p class="small text-muted mb-0">
                    Save your file as <b>.csv</b> (comma separated values) with these columns in this order. For existing subjects, you may leave other subject fields blank.
                  </p>
                </div>
                <div class="col-md-9">
                  <label for="bulk_file" class="form-label fw-bold">Select CSV File</label>
                  <input type="file" name="bulk_file" id="bulk_file" class="form-control" accept=".csv" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                  <button type="submit" name="upload_bulk_assign" class="btn btn-success w-100">
                    <i class="bi bi-upload"></i> Upload and Assign
                  </button>
                </div>
                <div class="col-12">
                  <p class="small text-muted mb-0">
                    Data is validated on upload. Any missing or incorrect IDs/codes will be shown in the feedback popup.
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
        college: opt.dataset.college,
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

      // --- When subject(s) change, filter students that ALREADY have those subjects ---
      subjectSelect.addEventListener("change", function() {
        // Get selected subject codes as array of strings
        const selectedSubjects = subjectChoices.getValue(true);
        // If nothing selected, restore original full student list
        if (!selectedSubjects || selectedSubjects.length === 0) {
          // rebuild grouped choices from originalStudentData
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

        // Prepare body for application/x-www-form-urlencoded: subject_codes[]=A&subject_codes[]=B...
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
            // assignedStudents should be array of student_id strings
            const filtered = originalStudentData.filter(s => !assignedStudents.includes(s.value));
            // Group the remaining students
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
            // fallback: restore full list so admin can still proceed
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
            Swal.fire({
              icon: 'error',
              title: 'Could not check assigned students',
              text: 'There was a problem checking which students already have the selected subject(s). Please try again or contact IT.'
            });
          });
      });

      // --- rest of your code (filters) remains unchanged ---
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
      // --- SELECT ALL FILTERED STUDENTS BUTTON ---
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

  <script>
    document.addEventListener("DOMContentLoaded", function() {

      const bulkFileInput = document.getElementById("bulk_file");

      bulkFileInput.addEventListener("change", function() {
        const file = this.files[0];
        if (!file) return;

        const fileName = file.name.toLowerCase();
        const isCSV = fileName.endsWith(".csv");

        if (!isCSV) {
          Swal.fire({
            icon: "error",
            title: "Invalid File",
            text: "Please upload a valid .csv file."
          });
          this.value = "";
          return;
        }

        const reader = new FileReader();

        // ==========================================
        // HANDLER FOR CSV FILES (Text Mode)
        // ==========================================
        if (isCSV) {
          reader.onload = function(e) {
            const text = e.target.result;
            // Split by new line, then split by comma
            const rows = text.trim().split("\n").map(r => r.split(","));
            showPreview(rows, "CSV");
          };
          reader.readAsText(file);
        }
      });

      // ==========================================
      // REUSABLE PREVIEW FUNCTION
      // ==========================================
      function showPreview(rows, type) {
        if (!rows || rows.length === 0) {
          Swal.fire({
            icon: "warning",
            title: "Empty File",
            text: `The ${type} file has no data.`
          });
          return;
        }

        // Extract header (Row 0)
        const header = rows[0];

        // Generate Table HTML
        let tableHTML = `
        <div style="text-align:left; margin-bottom:10px;">
            <strong>Format Detected:</strong> ${type}<br>
            <strong>Total Rows:</strong> ${rows.length - 1} (excluding header)
        </div>
        <strong>Preview (First 5 rows):</strong><br>
        <table border="1" cellpadding="5" style="width:100%; border-collapse: collapse; text-align:left; font-size: 12px;">
          <tr style="background:#f0f0f0; font-weight:bold;">
            ${header.map(h => `<th>${h}</th>`).join("")}
          </tr>
      `;

        // Show max 5 data rows
        const previewLimit = Math.min(6, rows.length); // 6 because index 0 is header
        for (let i = 1; i < previewLimit; i++) {
          const cols = rows[i].map(c => `<td>${c}</td>`).join("");
          tableHTML += `<tr>${cols}</tr>`;
        }
        tableHTML += "</table>";

        // Fire SweetAlert
        Swal.fire({
          title: "File Preview",
          html: tableHTML,
          width: 700,
          confirmButtonText: "Looks Good",
          showCancelButton: true,
          cancelButtonText: "Cancel Upload"
        }).then((result) => {
          if (result.dismiss === Swal.DismissReason.cancel) {
            document.getElementById("bulk_file").value = ""; // Clear input if canceled
          }
        });
      }

    });
  </script>

</body>

</html>
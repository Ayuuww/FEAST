<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a registrar
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// --- NEW PHP BLOCK: Handle CSV Template Download Request ---
if (isset($_GET['download_template'])) {
  // Define the required headers for a CSV download
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="student_template.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');

  // Define the CSV header row
  $output = fopen('php://output', 'w');
  fputcsv($output, ['idnumber', 'first_name', 'mid_name', 'last_name', 'college', 'program', 'section']);

  fclose($output);
  exit;
}
// -----------------------------------------------------------


// --- ✅ FIX 1: This query is NOW ONLY for college and Programs ---
$query_dept_prog = "SELECT DISTINCT college_name, program_name 
               FROM adds 
               WHERE college_name IS NOT NULL AND college_name != '' 
                 AND program_name IS NOT NULL AND program_name != ''
               ORDER BY college_name, program_name";

$result_dept_prog = $conn->query($query_dept_prog);
if (!$result_dept_prog) {
  die("Query Failed: " . $conn->error);
}

// This array is now simpler: $data['college'] = ['Program 1', 'Program 2']
$data = [];
while ($row = $result_dept_prog->fetch_assoc()) {
  $dept = $row['college_name'];
  $prog = $row['program_name'];

  if (!isset($data[$dept])) {
    $data[$dept] = [];
  }
  if (!in_array($prog, $data[$dept])) {
    $data[$dept][] = $prog;
  }
}
// Check if colleges/programs data is empty
$has_college_data = !empty($data);


// --- ✅ FIX 2: Add a NEW, SEPARATE query just for sections ---
$sections_result = $conn->query("SELECT DISTINCT section_name 
                                 FROM adds 
                                 WHERE section_name IS NOT NULL AND section_name != '' 
                                 ORDER BY section_name ASC");
if (!$sections_result) {
  die("Query Failed: " . $conn->error);
}
// Get sections array for iteration
$sections = $sections_result->fetch_all(MYSQLI_ASSOC);
$has_section_data = !empty($sections);

// Reset pointer for use in HTML dropdown
if ($has_section_data) {
  $sections_result->data_seek(0);
}

// --- End data fetch ---

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
</head>

<body>

  <?php include 'register-header.php' ?>
  <?php include 'register-sidebar.php' ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Student</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item ">Student</li>
          <li class="breadcrumb-item active">Add New Student</li>
        </ol>
      </nav>
    </div>
    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title text-center">Create New Student</h5>
              <form class="row g-3 needs-validation" novalidate method="post" action="studentcreation_csv.php" enctype="multipart/form-data" id="student_form">

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="idnumber" class="form-control" id="idnumber" placeholder="ID Number" pattern="^[0-9\-]+$" required>
                    <label for="idnumber" class="form-label">ID Number</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                    <label class="form-label">First Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="mid_name" class="form-control" placeholder="Middle Name" required>
                    <label class="form-label">Middle Name</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    <label class="form-label">Last Name</label>
                  </div>
                </div>

                <input type="hidden" name="password" value="ILOVEDMMMSU">

                <div class="col-md-6">
                  <div class="form-floating">
                    <select name="section" id="section" class="form-select" required>
                      <option value="" disabled selected>Select Section</option>
                      <?php foreach ($sections as $row): ?>
                        <option value="<?= htmlspecialchars($row['section_name']) ?>"><?= htmlspecialchars($row['section_name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <label for="section">Section</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <select name="college" id="college" class="form-select" required>
                      <option value="" disabled selected>Select College</option>
                    </select>
                    <label for="college">College</label>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-floating">
                    <select name="program" id="program" class="form-select" required disabled>
                      <option value="" disabled selected>Select Program</option>
                    </select>
                    <label for="program">Program</label>
                  </div>
                </div>

                <div class="col-4 offset-4">
                  <button class="btn btn-success w-100" name="submit" id="create" type="submit">Create Account</button>
                </div>

                <div class="col-12 my-4 d-flex align-items-center justify-content-center">
                  <hr class="w-100 me-3">
                  <h5 class="text-center text-muted fw-bold mb-0" style="white-space: nowrap;">OR Bulk Upload</h5>
                  <hr class="w-100 ms-3">
                </div>

                <div class="col-md-12">
                  <div class="alert alert-info py-2" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Required Format:</strong> <code>idnumber, first_name, mid_name, last_name, college, program, section</code> 
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-floating">
                    <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv">
                    <label for="csv_file">Select CSV File to Upload</label>
                  </div>
                </div>

                <div class="col-md-12 d-grid gap-2 d-md-flex justify-content-md-between">
                  <a href="?download_template=1" class="btn btn-outline-success me-md-2 w-100" type="button">
                    <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Download Template
                  </a>
                  <button class="btn btn-success w-100" name="bulk_upload" value="1" type="submit">
                    <i class="bi bi-upload me-1"></i> Upload Students
                  </button>
                </div>

                <input type="hidden" name="bulk_upload_trigger" value="0">
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
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    const allData = <?php echo json_encode($data); ?>;
    const form = document.getElementById('student_form');

    // Flag to bypass SweetAlert on final submission
    let isConfirmedSubmission = false;

    // --- FUNCTION 1: Intercept Single Student Submission to Show Preview ---
    form.addEventListener('submit', function(event) {
      // Check if the submission is for single student creation (not bulk upload)
      if (event.submitter && event.submitter.name === 'submit' && !isConfirmedSubmission) {
        event.preventDefault(); // Stop the default submission

        // Check form validity before proceeding
        if (!form.checkValidity()) {
          form.classList.add('was-validated');
          return;
        }

        // Gather data
        const idnumber = document.getElementById('idnumber').value;
        const firstName = document.querySelector('[name="first_name"]').value;
        const midName = document.querySelector('[name="mid_name"]').value;
        const lastName = document.querySelector('[name="last_name"]').value;
        const section = document.getElementById('section').value;
        const college = document.getElementById('college').value;
        const program = document.getElementById('program').value;

        // Generate HTML for the preview
        const htmlContent = `
            <div class="text-start">
              <p><strong>ID Number:</strong> ${idnumber}</p>
              <p><strong>Name:</strong> ${firstName} ${midName} ${lastName}</p>
              <p><strong>College:</strong> ${college}</p>
              <p><strong>Program:</strong> ${program}</p>
              <p><strong>Section:</strong> ${section}</p>
            </div>
            <hr>
            <small class="text-muted">Default Password: ILOVEDMMMSU</small>
          `;

        Swal.fire({
          title: 'Confirm Student Creation',
          html: htmlContent,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes, Create Student!',
          cancelButtonText: 'Review Data',
          customClass: {
            container: 'confirmation-alert',
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            // Set flag and re-submit the form programmatically
            isConfirmedSubmission = true;
            form.submit();
          }
        });
      }
    });


    // --- FUNCTION 2: Dropdown Population and Empty Data Alert ---
    document.addEventListener('DOMContentLoaded', function() {
      const deptSelect = document.getElementById('college');
      const progSelect = document.getElementById('program');

      // 1. Populate college
      const college = Object.keys(allData);

      // Initial check for empty data
      const hasCollegeData = <?= json_encode($has_college_data) ?>;
      const hasSectionData = <?= json_encode($has_section_data) ?>;

      if (!hasCollegeData || !hasSectionData) {
        let missing = [];
        if (!hasCollegeData) missing.push('Colleges and Programs');
        if (!hasSectionData) missing.push('Sections');

        Swal.fire({
          icon: 'warning',
          title: 'Missing Reference Data',
          html: `The following reference data is not found in the <strong>adds</strong> table: <ul><li>${missing.join('</li><li>')}</li></ul><p>Please ensure College/Program/Section data is added to the database before creating students.</p>`,
          confirmButtonText: 'I Understand'
        });
      }

      // Populate college options
      college.forEach(dept => {
        const option = new Option(dept, dept);
        deptSelect.add(option);
      });

      // 2. college Change Event
      deptSelect.addEventListener('change', function() {
        // Clear and disable program dropdown
        progSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';
        progSelect.disabled = true;

        const selectedDept = this.value;
        if (!selectedDept) return;

        const programs = allData[selectedDept] || [];

        if (programs.length > 0) {
          programs.forEach(prog => {
            const option = new Option(prog, prog);
            progSelect.add(option);
          });
          progSelect.disabled = false;
        }
      });
    });

    function showFailureDetails(event) {
      event.preventDefault();

      // Check if the session variable is available (passed via PHP)
      const failureDetails = <?php echo json_encode($_SESSION['failure_details'] ?? []); ?>;

      if (failureDetails.length === 0) {
        Swal.fire('No error details found', 'The bulk upload encountered no errors requiring detailed logging.', 'info');
        return;
      }

      // Convert the array of error strings into an HTML list
      const htmlList = `<ul class="text-start" style="max-height: 300px; overflow-y: auto; padding-left: 20px; list-style-type: none;">
        ${failureDetails.map(detail => `<li class="mb-2"><i class="bi bi-x-octagon-fill text-danger me-2"></i>${detail}</li>`).join('')}
      </ul>`;

      Swal.fire({
        title: '⚠️ Bulk Upload Errors Found',
        html: `
          <p class="text-start mb-3">Some rows were skipped during the upload. Please review the errors below and correct your CSV file before re-uploading.</p>
          <div class="alert alert-danger p-2 text-start">
            **Common Cause:** If the error mentions **"Required data is missing..."**, it means the College, Program, or Section you entered in the CSV is misspelled or has not yet been added to the system's reference list in Manage Page.
          </div>
          <h6 class="text-start fw-bold mt-4 mb-2">Detailed Error Log:</h6>
          ${htmlList}
        `,
        icon: 'error',
        confirmButtonText: 'Understood',
        width: 600
      });
      // The PHP unset is now handled outside the function.
    }
    // --- CLEAR FAILURE DETAILS AFTER DISPLAY ---
    // This cleans up the $_SESSION variable after the JavaScript has consumed it.
    <?php unset($_SESSION['failure_details']); ?>
    // -------------------------------------------
  </script>

  <?php if (isset($_SESSION['msg'])): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const msg = '<?= addslashes($_SESSION['msg']) ?>';
        const type = '<?= $_SESSION['msg_type'] ?>';
        let options = {
          icon: type === 'success' ? 'success' : (type === 'danger' ? 'error' : (type === 'warning' ? 'warning' : 'info')),
          title: msg,
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        };

        // If the message is a warning (bulk upload failure), keep it open and allow click to trigger details
        if (type === 'warning' && msg.includes('Click here for details.')) {
          options.showConfirmButton = true;
          options.confirmButtonText = 'OK';
          options.timer = false;
          options.allowOutsideClick = true; // Important for clickable links within the message

          // Render the message and look for the specific link to hook the JS function
          Swal.fire(options).then(() => {
            // If the user clicks OK, and we need to show the details, we can't reliably
            // trigger the link from here. A better way is to ensure the link 
            // embedded in the PHP session message calls the JS function directly:
            // e.g., <a href='#' onclick='showFailureDetails(event);'>
          });
          // The link within the HTML message will call showFailureDetails(event) directly.
        } else {
          Swal.fire(options);
        }
      });
    </script>
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
  <?php endif; ?>

</body>

</html>
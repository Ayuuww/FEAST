<?php
session_start();
include 'conn/conn.php';

// Only registrar allowed
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// -----------------------------
// CSV Template Download
// -----------------------------
if (isset($_GET['download_template'])) {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="faculty_template.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');

  $output = fopen('php://output', 'w');
  // Column order expected by facultycreation_csv.php
  fputcsv($output, ['idnumber', 'first_name', 'mid_name', 'last_name', 'faculty_rank', 'college', 'program']);
  fclose($output);
  exit;
}

// -----------------------------
// Fetch College & Programs
// -----------------------------
$query_dept_prog = "
    SELECT DISTINCT college_name, program_name
    FROM adds
    WHERE college_name IS NOT NULL AND college_name != ''
      AND program_name IS NOT NULL AND program_name != ''
    ORDER BY college_name, program_name
";

$result_dept_prog = $conn->query($query_dept_prog);
if (!$result_dept_prog) {
  die("Query Failed: " . $conn->error);
}

$college_program_data = [];
while ($row = $result_dept_prog->fetch_assoc()) {
  $dept = $row['college_name'];
  $prog = $row['program_name'];

  if (!isset($college_program_data[$dept])) {
    $college_program_data[$dept] = [];
  }
  if (!in_array($prog, $college_program_data[$dept])) {
    $college_program_data[$dept][] = $prog;
  }
}
$has_college_program_data = !empty($college_program_data);

// -----------------------------
// Fetch Faculty Ranks
// -----------------------------
$ranks_result = $conn->query("
    SELECT DISTINCT rank_name
    FROM adds
    WHERE rank_name IS NOT NULL AND rank_name != ''
    ORDER BY rank_name ASC
");
if (!$ranks_result) {
  die("Query Failed: " . $conn->error);
}

$ranks = $ranks_result->fetch_all(MYSQLI_ASSOC);
$has_rank_data = !empty($ranks);

if ($has_rank_data) {
  // rewind if you need the result resource elsewhere (not strictly necessary now)
  $ranks_result->data_seek(0);
}

// Prepare optional stored failure details for JS (bulk upload)
$failure_details = $_SESSION['failure_details'] ?? [];
// We will unset at end of page after passing it to JS.

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
      <h1>Faculty</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Faculty</li>
          <li class="breadcrumb-item active">Add New Faculty</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">

              <?php if (isset($_SESSION['msg'])): ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    const msg = <?= json_encode($_SESSION['msg']) ?>;
                    const type = <?= json_encode($_SESSION['msg_type'] ?? 'info') ?>;
                    const options = {
                      icon: (type === 'success') ? 'success' : ((type === 'danger') ? 'error' : ((type === 'warning') ? 'warning' : 'info')),
                      title: msg.includes('<br>') ? 'Faculty Creation Result' : msg,
                      html: msg.includes('<br>') ? msg : null,
                      showConfirmButton: type !== 'warning',
                      timer: type !== 'warning' ? 3000 : false,
                      timerProgressBar: type !== 'warning' ? true : false
                    };
                    Swal.fire(options);
                  });
                </script>
              <?php endif; ?>
              <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>

              <h5 class="card-title text-center">Create New Faculty</h5>

              <!-- Form: posts to facultycreation_csv.php which handles single + bulk -->
              <form id="faculty_form" class="row g-3 needs-validation" novalidate
                method="post" action="facultycreation_csv.php" enctype="multipart/form-data">

                <!-- ID -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="idnumber" id="idnumber" class="form-control" placeholder="ID Number" pattern="^[0-9\-]+$" required>
                    <label for="idnumber">ID Number</label>
                    <div class="invalid-feedback">Please enter a valid ID number.</div>
                  </div>
                </div>

                <!-- First Name -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                    <label>First Name</label>
                  </div>
                </div>

                <!-- Middle Name -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="mid_name" class="form-control" placeholder="Middle Name (Optional)">
                    <label>Middle Name (Optional)</label>
                  </div>
                </div>

                <!-- Last Name -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    <label>Last Name</label>
                  </div>
                </div>

                <!-- Hidden Default Password -->
                <input type="hidden" name="password" value="ILOVEDMMMSU">

                <!-- Faculty Rank -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <select name="faculty_rank" id="faculty_rank" class="form-select" required>
                      <option value="" disabled selected>Select Academic Rank</option>
                      <?php foreach ($ranks as $r): ?>
                        <option value="<?= htmlspecialchars($r['rank_name']) ?>"><?= htmlspecialchars($r['rank_name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <label for="faculty_rank">Academic Rank</label>
                  </div>
                </div>

                <!-- College -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <select name="college" id="college" class="form-select" required>
                      <option value="" disabled selected>Select College</option>
                      <!-- populated by JS -->
                    </select>
                    <label for="college">College</label>
                  </div>
                </div>

                <!-- Program -->
                <div class="col-md-12">
                  <div class="form-floating">
                    <select name="program" id="program" class="form-select" required disabled>
                      <option value="" disabled selected>Select Program</option>
                    </select>
                    <label for="program">Program</label>
                  </div>
                </div>

                <!-- Single create button -->
                <div class="col-4 offset-4">
                  <button class="btn btn-success w-100" name="submit" id="create" type="submit">Create Account</button>
                </div>

                <!-- Divider -->
                <div class="col-12 my-4 d-flex align-items-center justify-content-center">
                  <hr class="w-100 me-3">
                  <h5 class="text-center text-muted fw-bold mb-0" style="white-space: nowrap;">OR Bulk Upload (CSV)</h5>
                  <hr class="w-100 ms-3">
                </div>

                <div class="col-md-12">
                  <div class="alert alert-info py-2" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Required Format</strong>: <code>idnumber, first_name, mid_name, last_name, faculty_rank, college, program</code>
                  </div>
                </div>

                <!-- CSV Input -->
                <div class="col-md-12">
                  <div class="form-floating">
                    <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv">
                    <label for="csv_file">Select CSV File to Upload</label>
                  </div>
                </div>

                <div class="col-md-12 d-grid gap-2 d-md-flex justify-content-md-between">
                  <a href="?download_template=1" class="btn btn-outline-success me-md-2 w-100">
                    <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Download Template
                  </a>
                  <button class="btn btn-success w-100" name="bulk_upload" value="1" type="submit">
                    <i class="bi bi-upload me-1"></i> Upload Faculty
                  </button>
                </div>
              </form>

            </div> <!-- card-body -->
          </div> <!-- card -->
        </div> <!-- col -->
      </div> <!-- row -->
    </section>
  </main>

  <?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // Data passed from PHP
    const allData = <?= json_encode($college_program_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const hasCollegeProgramData = <?= json_encode($has_college_program_data) ?>;
    const hasRankData = <?= json_encode($has_rank_data) ?>;
    const failureDetails = <?= json_encode($failure_details) ?>;

    // Grab form elements
    const form = document.getElementById('faculty_form');
    const deptSelect = document.getElementById('college');
    const progSelect = document.getElementById('program');

    // Populate college dropdown
    Object.keys(allData).forEach(dept => {
      const opt = new Option(dept, dept);
      deptSelect.add(opt);
    });

    // College change -> populate programs
    deptSelect.addEventListener('change', function() {
      progSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';
      progSelect.disabled = true;

      const selected = this.value;
      if (!selected) return;

      const progs = allData[selected] || [];
      if (progs.length > 0) {
        progs.forEach(p => progSelect.add(new Option(p, p)));
        progSelect.disabled = false;
      }
    });

    // On page load, alert missing reference data if any
    document.addEventListener('DOMContentLoaded', function() {
      if (!hasCollegeProgramData || !hasRankData) {
        const missing = [];
        if (!hasCollegeProgramData) missing.push('Colleges and Programs');
        if (!hasRankData) missing.push('Academic Ranks');

        Swal.fire({
          icon: 'warning',
          title: 'Missing Reference Data',
          html: `The following reference data is not found in the <strong>adds</strong> table: <ul><li>${missing.join('</li><li>')}</li></ul><p>Please ensure this data is added to the database before creating faculty.</p>`,
          confirmButtonText: 'I Understand'
        });
      }

      // If bulk upload produced failure details, show a button/alert to view them
      if (Array.isArray(failureDetails) && failureDetails.length > 0) {
        Swal.fire({
          icon: 'error',
          title: 'Bulk Upload Completed with Errors',
          html: `<p>${failureDetails.length} row(s) were skipped. Click <strong>Show Details</strong> to view the log.</p>`,
          showCancelButton: true,
          confirmButtonText: 'Show Details',
          cancelButtonText: 'Close'
        }).then(result => {
          if (result.isConfirmed) {
            const htmlList = `<ul class="text-start" style="max-height:300px; overflow:auto; padding-left: 20px;">${failureDetails.map(d => `<li>${d}</li>`).join('')}</ul>`;
            Swal.fire({
              icon: 'info',
              title: 'Upload Error Details',
              html: htmlList,
              width: 700,
              confirmButtonText: 'OK'
            });
          }
        });
      }
    });

    // Intercept single-create submission to show confirm preview
    // Note: event.submitter is widely supported in modern browsers.
    let isConfirmedSubmission = false;
    form.addEventListener('submit', function(event) {
      // If bulk upload submit (csv_file present or bulk_upload button clicked), let server handle it
      const isBulk = !!document.getElementById('csv_file').files.length || (event.submitter && event.submitter.name === 'bulk_upload');
      if (isBulk) {
        // optional: validate that csv file exists when bulk_upload button clicked
        if (event.submitter && event.submitter.name === 'bulk_upload') {
          if (!document.getElementById('csv_file').files.length) {
            event.preventDefault();
            Swal.fire({
              icon: 'warning',
              title: 'No file selected',
              text: 'Please select a CSV file before uploading.'
            });
            return;
          }
        }
        return; // allow bulk submission to proceed
      }

      // Single create path
      // Prevent double show if already confirmed
      if (isConfirmedSubmission) return;

      event.preventDefault();

      // Basic HTML5 validity check
      if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
      }

      // Gather values for preview
      const idnumber = document.getElementById('idnumber').value.trim();
      const firstName = (document.querySelector('[name="first_name"]') || {}).value || '';
      const midName = (document.querySelector('[name="mid_name"]') || {}).value || '';
      const lastName = (document.querySelector('[name="last_name"]') || {}).value || '';
      const rank = (document.getElementById('faculty_rank') || {}).value || '';
      const college = (document.getElementById('college') || {}).value || '';
      const program = (document.getElementById('program') || {}).value || '';

      const htmlContent = `
                <div class="text-start">
                    <p><strong>ID Number:</strong> ${escapeHtml(idnumber)}</p>
                    <p><strong>Name:</strong> ${escapeHtml(firstName)} ${escapeHtml(midName)} ${escapeHtml(lastName)}</p>
                    <p><strong>Academic Rank:</strong> ${escapeHtml(rank)}</p>
                    <p><strong>College:</strong> ${escapeHtml(college)}</p>
                    <p><strong>Program:</strong> ${escapeHtml(program)}</p>
                </div>
                <hr>
                <small class="text-muted">Default Password: ILOVEDMMMSU</small>
            `;

      Swal.fire({
        title: 'Confirm Faculty Creation',
        html: htmlContent,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Create Faculty!',
        cancelButtonText: 'Review Data',
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-secondary'
        },
        didOpen: () => {
          // style fix so SweetAlert buttons look like bootstrap buttons
          const confirmBtn = document.querySelector('.swal2-confirm');
          const cancelBtn = document.querySelector('.swal2-cancel');
          if (confirmBtn) confirmBtn.classList.add('me-2');
        }
      }).then(result => {
        if (result.isConfirmed) {
          isConfirmedSubmission = true;
          form.submit();
        }
      });
    });

    // Simple HTML-escape helper for preview
    function escapeHtml(text) {
      if (!text) return '';
      return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }
  </script>

  <?php
  // cleanup session failure details after passing to JS
  unset($_SESSION['failure_details']);
  ?>
</body>

</html>
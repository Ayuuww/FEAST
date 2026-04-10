<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if the user is logged in and is a registrar
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: page-login.php");
  exit();
}

// --- ✅ 1. FETCH DYNAMIC DROPDOWN DATA FROM `adds` TABLE ---
$collegePrograms = [];
$dept_res = $conn->query("SELECT DISTINCT college_name FROM adds WHERE college_name IS NOT NULL AND college_name != '' ORDER BY college_name ASC");
while ($r = $dept_res->fetch_assoc()) {
  $collegePrograms[$r['college_name']] = [];
}

$prog_res = $conn->query("SELECT college_name, program_name FROM adds WHERE college_name IS NOT NULL AND program_name IS NOT NULL AND program_name != '' ORDER BY college_name, program_name ASC");
while ($r = $prog_res->fetch_assoc()) {
  if (isset($collegePrograms[$r['college_name']])) {
    $collegePrograms[$r['college_name']][] = $r['program_name'];
  }
}

// Fetch sections for dropdown
$sections = [];
$sections_result = $conn->query("SELECT DISTINCT section_name FROM adds WHERE section_name IS NOT NULL AND section_name != '' ORDER BY section_name ASC");
while ($sec_row = $sections_result->fetch_assoc()) {
  $sections[] = $sec_row['section_name'];
}

// --- ✅ 2. HANDLE FORM SUBMISSION (UPDATE STUDENT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
  $old_id = $_POST['old_idnumber'];
  $new_id = trim($_POST['idnumber']);
  $new_college = !empty($_POST['college']) ? trim($_POST['college']) : null;
  $new_program = !empty($_POST['program']) ? trim($_POST['program']) : '';
  $new_section = !empty($_POST['section']) ? trim($_POST['section']) : null;
  $new_password = trim($_POST['new_password']);

  // Hash the new password if the registrar provided one
  $final_password = null;
  if (!empty($new_password)) {
    $final_password = password_hash($new_password, PASSWORD_BCRYPT);
  }

  try {
    $conn->begin_transaction();

    if ($final_password) {
      // Update with new hashed password
      $update_student = $conn->prepare("UPDATE student SET idnumber = ?, college = ?, program = ?, section = ?, password = ? WHERE idnumber = ?");
      $update_student->bind_param("ssssss", $new_id, $new_college, $new_program, $new_section, $final_password, $old_id);
    } else {
      // Update without touching the password
      $update_student = $conn->prepare("UPDATE student SET idnumber = ?, college = ?, program = ?, section = ? WHERE idnumber = ?");
      $update_student->bind_param("sssss", $new_id, $new_college, $new_program, $new_section, $old_id);
    }

    $update_student->execute();
    $conn->commit();

    $_SESSION['msg'] = 'Student information updated successfully!';
    $_SESSION['msg_type'] = 'success';
  } catch (Exception $e) {
    $conn->rollback();
    // Catch duplicate ID entries gracefully
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
      $_SESSION['msg'] = 'Error: That ID Number already exists.';
    } else {
      $_SESSION['msg'] = 'Failed to update student: ' . $e->getMessage();
    }
    $_SESSION['msg_type'] = 'error';
  }

  header("Location: admin-studentlist.php");
  exit();
}

// --- ✅ 3. FETCH STUDENT DATA FOR LISTING BASED ON ADMIN'S COLLEGE ---
$admin_id = $_SESSION['idnumber'];
$query = "
    SELECT s.* FROM student s
    INNER JOIN (
        SELECT DISTINCT college_name 
        FROM admin_college 
        WHERE admin_idnumber = ?
    ) ac ON s.college = ac.college_name
    WHERE s.role = 'student' 
    ORDER BY s.last_name ASC
";
$stmt_stud = $conn->prepare($query);
$stmt_stud->bind_param("s", $admin_id);
$stmt_stud->execute();
$result = $stmt_stud->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

  <?php include 'admin-header.php' ?>
  <?php include 'admin-sidebar.php' ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>List of Students</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Student</li>
          <li class="breadcrumb-item active">List</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          Swal.fire({
            icon: '<?= $_SESSION['msg_type'] ?>',
            title: '<?= $_SESSION['msg_type'] === 'success' ? 'Success!' : 'Error!' ?>',
            text: '<?= addslashes($_SESSION['msg']) ?>',
            timer: 2000,
            showConfirmButton: false,
          });
        });
      </script>
      <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">

              <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                <div class="d-flex align-items-center gap-2">
                  <label for="pageSize" class="mb-0 fw-semibold text-muted">Show</label>
                  <select id="pageSize" class="form-select form-select-sm w-auto">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="999999">All</option>
                  </select>
                  <span class="mb-0 fw-semibold text-muted">entries</span>
                </div>
                <div class="search-box position-relative" style="width: 300px;">
                  <input type="text" id="customStudentSearch" class="form-control" placeholder="Search ID, Name, Program...">
                  <i class="bi bi-search position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); color: #6c757d;"></i>
                </div>
              </div>

              <table class="table table-hover align-middle" id="studentTable">
                <thead class="table-light text-center">
                  <tr>
                    <th>ID Number</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>College</th>
                    <th>Program</th>
                    <th>Section</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody class="text-center">
                  <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                      <td><?php echo htmlspecialchars($row['idnumber']); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['first_name']); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['mid_name']); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['last_name']); ?></td>
                      <td class="text-uppercase"><?php echo htmlspecialchars($row['college'] ?? '—'); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['program'] ?? '—'); ?></td>
                      <td class="text-uppercase"><?php echo htmlspecialchars($row['section'] ?? '—'); ?></td>
                      <td>
                        <button type="button" class="btn btn-warning btn-sm edit-btn"
                          data-bs-toggle="modal"
                          data-bs-target="#editStudentModal"
                          data-id="<?php echo htmlspecialchars($row['idnumber']); ?>"
                          data-fname="<?php echo htmlspecialchars($row['first_name']); ?>"
                          data-mname="<?php echo htmlspecialchars($row['mid_name']); ?>"
                          data-lname="<?php echo htmlspecialchars($row['last_name']); ?>"
                          data-college="<?php echo htmlspecialchars($row['college']); ?>"
                          data-program="<?php echo htmlspecialchars($row['program']); ?>"
                          data-section="<?php echo htmlspecialchars($row['section']); ?>">
                          <i class="bi bi-pencil-square"></i> Edit
                        </button>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>

              <div class="d-flex justify-content-end mt-3">
                <nav aria-label="Table navigation">
                  <ul class="pagination pagination-sm" id="paginationControls"></ul>
                </nav>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editStudentModalLabel">Edit Student Information</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" id="editStudentForm" class="row g-3 px-3 py-2">

          <input type="hidden" name="old_idnumber" id="modal_old_idnumber">
          <input type="hidden" name="update_student" value="1">

          <div class="col-md-6 mt-3">
            <div class="form-floating">
              <input type="text" id="modal_fullname" class="form-control" disabled>
              <label>Full Name</label>
            </div>
          </div>

          <div class="col-md-6 mt-3">
            <div class="form-floating">
              <input type="text" name="idnumber" id="modal_idnumber" class="form-control" required pattern="^[0-9\-]+$">
              <label>ID Number</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <select name="college" class="form-select" id="modal_college" required>
                <option value="" disabled selected>-- Select College --</option>
              </select>
              <label>College</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select" name="program" id="modal_program">
                <option value="" selected>-- Select Program --</option>
              </select>
              <label>Program</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select" name="section" id="modal_section">
                <option value="" selected>-- Select Section --</option>
                <?php foreach ($sections as $sec): ?>
                  <option value="<?= htmlspecialchars($sec) ?>"><?= htmlspecialchars($sec) ?></option>
                <?php endforeach; ?>
              </select>
              <label>Section</label>
            </div>
          </div>

          <div class="col-md-12 mt-2">
            <div class="form-floating position-relative">
              <input type="password" name="new_password" id="modal_password" class="form-control" placeholder="New Password">
              <label>New Password (Leave blank to keep current)</label>
              <span class="toggle-password" onclick="toggleModalPassword()" style="position:absolute; top:50%; right:15px; transform:translateY(-50%); cursor:pointer; font-size: 1.1rem; color: #6c757d;">
                <i class="bi bi-eye-fill" id="toggleModalPasswordIcon"></i>
              </span>
            </div>
          </div>

          <div class="modal-footer border-0 p-0 mt-3 pb-3 w-100 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success px-4">Update Student</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    const collegePrograms = <?= json_encode($collegePrograms, JSON_UNESCAPED_UNICODE) ?>;

    document.addEventListener('DOMContentLoaded', function() {
      const collegeSelect = document.getElementById('modal_college');
      const programSelect = document.getElementById('modal_program');

      // 1. Populate Colleges
      const colleges = Object.keys(collegePrograms);
      colleges.forEach(dept => {
        collegeSelect.add(new Option(dept, dept));
      });

      // 2. Cascade Programs based on selected College
      function populatePrograms(selectedDept, selectedProg = null) {
        programSelect.innerHTML = '<option value="" selected>-- Select Program (if any) --</option>';

        if (selectedDept && collegePrograms[selectedDept]) {
          const programs = collegePrograms[selectedDept];
          programs.forEach(prog => {
            programSelect.add(new Option(prog, prog));
          });
        }

        if (selectedProg) {
          programSelect.value = selectedProg;
        }
      }

      // Listen for manual college changes
      collegeSelect.addEventListener('change', function() {
        populatePrograms(this.value);
      });

      // 3. Handle Edit Button Click
      const editButtons = document.querySelectorAll('.edit-btn');
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const fname = this.getAttribute('data-fname');
          const mname = this.getAttribute('data-mname');
          const lname = this.getAttribute('data-lname');
          const college = this.getAttribute('data-college');
          const program = this.getAttribute('data-program');
          const section = this.getAttribute('data-section');

          const fullName = `${fname} ${mname} ${lname}`.replace(/\s+/g, ' ').trim();

          document.getElementById('modal_old_idnumber').value = id;
          document.getElementById('modal_idnumber').value = id;
          document.getElementById('modal_fullname').value = fullName;
          document.getElementById('modal_section').value = section || '';
          document.getElementById('modal_password').value = '';

          // Set college and trigger program population
          if (colleges.includes(college)) {
            collegeSelect.value = college;
          } else {
            collegeSelect.value = "";
          }

          populatePrograms(collegeSelect.value, program);
        });
      });

      // 4. Form Submit Confirmation
      document.getElementById("editStudentForm").addEventListener("submit", function(e) {
        e.preventDefault();
        Swal.fire({
          title: "Confirm Update",
          text: "Are you sure you want to save these changes?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Yes, Update",
          cancelButtonText: "Cancel",
          confirmButtonColor: "#198754"
        }).then((result) => {
          if (result.isConfirmed) {
            this.submit();
          }
        });
      });

      // --- SEARCH & PAGINATION LOGIC ---
      let currentPage = 1;
      let rowsPerPage = 10;

      function updateTable() {
        let filterValue = document.getElementById('customStudentSearch').value.toLowerCase();
        let tableRows = Array.from(document.querySelectorAll('#studentTable tbody tr'));

        let filteredRows = tableRows.filter(row => {
          return row.textContent.toLowerCase().includes(filterValue);
        });

        tableRows.forEach(row => row.style.display = 'none');

        let totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (currentPage > totalPages) currentPage = totalPages || 1;

        let start = (currentPage - 1) * rowsPerPage;
        let end = start + rowsPerPage;

        filteredRows.slice(start, end).forEach(row => {
          row.style.display = '';
        });

        renderPaginationControls(totalPages);
      }

      function renderPaginationControls(totalPages) {
        const paginationContainer = document.getElementById('paginationControls');
        paginationContainer.innerHTML = '';

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
          let li = document.createElement('li');
          li.className = `page-item ${i === currentPage ? 'active' : ''}`;
          let a = document.createElement('a');
          a.className = 'page-link';
          a.href = 'javascript:void(0);';
          a.innerText = i;
          a.addEventListener('click', () => {
            currentPage = i;
            updateTable();
          });
          li.appendChild(a);
          paginationContainer.appendChild(li);
        }
      }

      document.getElementById('customStudentSearch').addEventListener('keyup', () => {
        currentPage = 1;
        updateTable();
      });

      document.getElementById('pageSize').addEventListener('change', function() {
        rowsPerPage = parseInt(this.value);
        currentPage = 1;
        updateTable();
      });

      updateTable();
    });

    // 5. Toggle Password Visibility in Modal
    function toggleModalPassword() {
      const passwordInput = document.getElementById("modal_password");
      const icon = document.getElementById("toggleModalPasswordIcon");
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("bi-eye-fill");
        icon.classList.add("bi-eye-slash-fill");
      } else {
        passwordInput.type = "password";
        icon.classList.remove("bi-eye-slash-fill");
        icon.classList.add("bi-eye-fill");
      }
    }
  </script>

</body>

</html>
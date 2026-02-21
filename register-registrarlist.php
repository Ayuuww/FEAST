<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ✅ Check if the user is logged in and is a registrar
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// --- ✅ 1. HANDLE MODAL FORM SUBMISSION (UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_registrar'])) {
  $original_id = trim($_POST['old_idnumber']);
  $new_id = trim($_POST['idnumber']);
  $status = trim($_POST['status']);
  $employment_role = trim($_POST['employment_role']);
  $new_password = trim($_POST['new_password']);

  $main_college = !empty($_POST['main_college']) ? trim($_POST['main_college']) : null;
  $main_program = !empty($_POST['main_program']) ? trim($_POST['main_program']) : null;

  if ($new_id === '') {
    $_SESSION['msg'] = "ID number is required.";
    $_SESSION['msg_type'] = "danger";
    header("Location: register-registrarlist.php");
    exit();
  }

  // Check for duplicate ID against OTHER registrars
  $chk = $conn->prepare("SELECT idnumber FROM registrar WHERE idnumber = ? AND idnumber != ?");
  $chk->bind_param("ss", $new_id, $original_id);
  $chk->execute();
  if ($chk->get_result()->num_rows > 0) {
    $_SESSION['msg'] = "ID number already exists for another registrar.";
    $_SESSION['msg_type'] = "danger";
    header("Location: register-registrarlist.php");
    exit();
  }
  $chk->close();

  // Hash the new password if provided
  $final_password = null;
  if (!empty($new_password)) {
    $final_password = password_hash($new_password, PASSWORD_BCRYPT);
  }

  try {
    if ($final_password) {
      $update = $conn->prepare("UPDATE registrar SET idnumber = ?, employment_role = ?, college = ?, program = ?, status = ?, password = ? WHERE idnumber = ?");
      $update->bind_param("sssssss", $new_id, $employment_role, $main_college, $main_program, $status, $final_password, $original_id);
    } else {
      $update = $conn->prepare("UPDATE registrar SET idnumber = ?, employment_role = ?, college = ?, program = ?, status = ? WHERE idnumber = ?");
      $update->bind_param("ssssss", $new_id, $employment_role, $main_college, $main_program, $status, $original_id);
    }

    $update->execute();
    $_SESSION['update_success'] = "Registrar record updated successfully!";
  } catch (Exception $e) {
    $_SESSION['msg'] = "Update failed: " . $e->getMessage();
    $_SESSION['msg_type'] = "danger";
  }
  header("Location: register-registrarlist.php");
  exit();
}

// --- ✅ 2. FETCH DATA FOR LIST & MODAL ---

// A. Fetch all registrar accounts
$query = "
  SELECT 
    idnumber,
    first_name,
    mid_name,
    last_name,
    employment_role,
    college,
    program,
    status
  FROM registrar 
  WHERE role = 'registrar'
  ORDER BY last_name ASC
";
$result = mysqli_query($conn, $query);

// B. Fetch all available college & programs for dropdowns
$collegePrograms = [];
$dept_res = $conn->query("SELECT DISTINCT college_name FROM adds WHERE college_name IS NOT NULL AND college_name != '' ORDER BY college_name ASC");
while ($r = $dept_res->fetch_assoc()) {
  $collegePrograms[$r['college_name']] = [];
}
$prog_res = $conn->query("SELECT college_name, program_name FROM adds WHERE college_name IS NOT NULL AND program_name IS NOT NULL AND program_name != '' ORDER BY college_name, program_name");
while ($r = $prog_res->fetch_assoc()) {
  if (isset($collegePrograms[$r['college_name']])) {
    $collegePrograms[$r['college_name']][] = $r['program_name'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="assets/css/choices.min.css" />
</head>

<body>

  <?php include 'register-header.php'; ?>
  <?php include 'register-sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>List of Registrars</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Registrar</li>
          <li class="breadcrumb-item active">List</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">

              <?php if (isset($_SESSION['update_success'])): ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                      icon: 'success',
                      title: 'Updated!',
                      text: <?= json_encode($_SESSION['update_success']) ?>,
                      timer: 2000,
                      showConfirmButton: false
                    });
                  });
                </script>
              <?php unset($_SESSION['update_success']);
              endif; ?>

              <?php if (isset($_SESSION['msg'])): ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                      icon: '<?= $_SESSION['msg_type'] === 'danger' ? 'error' : 'info' ?>',
                      title: 'Notice',
                      text: <?= json_encode($_SESSION['msg']) ?>
                    });
                  });
                </script>
              <?php unset($_SESSION['msg'], $_SESSION['msg_type']);
              endif; ?>

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
                  <input type="text" id="customRegistrarSearch" class="form-control" placeholder="Search ID, Name, College...">
                  <i class="bi bi-search position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); color: #6c757d;"></i>
                </div>
              </div>

              <table class="table table-hover align-middle" id="registrarTable">
                <thead class="table-light text-center">
                  <tr>
                    <th>ID Number</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Employment Role</th>
                    <th>College</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th width="120px">Action</th>
                  </tr>
                </thead>
                <tbody class="text-center">
                  <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['idnumber']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['first_name']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['mid_name']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['last_name']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['employment_role']); ?></td>
                      <td class="text-uppercase"><?= htmlspecialchars($row['college'] ?? '—'); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['program'] ?? '—'); ?></td>
                      <td>
                        <?php if ($row['status'] === 'active'): ?>
                          <span class="badge bg-success">Active</span>
                        <?php else: ?>
                          <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-warning btn-sm edit-btn"
                          data-bs-toggle="modal"
                          data-bs-target="#editRegistrarModal"
                          data-id="<?= htmlspecialchars($row['idnumber']); ?>"
                          data-fname="<?= htmlspecialchars($row['first_name']); ?>"
                          data-mname="<?= htmlspecialchars($row['mid_name']); ?>"
                          data-lname="<?= htmlspecialchars($row['last_name']); ?>"
                          data-employ="<?= htmlspecialchars($row['employment_role']); ?>"
                          data-college="<?= htmlspecialchars($row['college'] ?? ''); ?>"
                          data-program="<?= htmlspecialchars($row['program'] ?? ''); ?>"
                          data-status="<?= htmlspecialchars($row['status']); ?>">
                          <i class="bi bi-pencil-square"></i> Edit
                        </button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
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

  <div class="modal fade" id="editRegistrarModal" tabindex="-1" aria-labelledby="editRegistrarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editRegistrarModalLabel">Edit Registrar Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" id="editRegistrarForm" class="row g-3 px-3 py-2">

          <input type="hidden" name="old_idnumber" id="modal_old_idnumber">
          <input type="hidden" name="update_registrar" value="1">

          <div class="col-md-6 mt-3">
            <div class="form-floating">
              <input type="text" class="form-control" name="idnumber" id="modal_idnumber" required>
              <label>ID Number</label>
            </div>
          </div>

          <div class="col-md-6 mt-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="modal_fullname" disabled>
              <label>Full Name</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select name="employment_role" id="modal_employment_role" class="form-select" required>
                <option value="Teaching">Teaching</option>
                <option value="Non-Teaching">Non-Teaching</option>
              </select>
              <label>Employment Role</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select name="status" id="modal_status" class="form-select" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <label>Status</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select class="form-select" name="main_college" id="modal_college">
                <option value="" selected>-- Select College --</option>
              </select>
              <label>College (Optional)</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <select class="form-select" name="main_program" id="modal_program">
                <option value="" selected>-- Select Program --</option>
              </select>
              <label>Program (Optional)</label>
            </div>
          </div>

          <div class="col-md-12 mb-3 mt-2">
            <div class="form-floating position-relative">
              <input type="password" name="new_password" id="modal_password" class="form-control" placeholder="New Password">
              <label>New Password (Leave blank to keep current)</label>
              <span class="toggle-password" onclick="toggleModalPassword()"
                style="position:absolute; top:50%; right:15px; transform:translateY(-50%); cursor:pointer; font-size: 1.1rem; color: #6c757d;">
                <i class="bi bi-eye-fill" id="toggleModalPasswordIcon"></i>
              </span>
            </div>
          </div>

          <div class="modal-footer border-0 p-0 mt-2 pb-3 w-100 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success px-4">Save Changes</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    const collegePrograms = <?= json_encode($collegePrograms, JSON_UNESCAPED_UNICODE) ?>;
    const allColleges = Object.keys(collegePrograms);

    document.addEventListener('DOMContentLoaded', function() {

      const mainDeptSelect = document.getElementById('modal_college');
      const mainProgramSelect = document.getElementById('modal_program');
      let prefillProgram = null;

      // 1. Populate College Dropdown
      allColleges.forEach(dept => {
        mainDeptSelect.add(new Option(dept, dept));
      });

      // 2. Cascade Program Dropdown
      function updateProgramDropdown() {
        const dept = mainDeptSelect.value;
        mainProgramSelect.innerHTML = '<option value="" selected>-- Select Program --</option>';

        if (dept && collegePrograms[dept]) {
          collegePrograms[dept].forEach(prog => {
            const opt = new Option(prog, prog);
            if (prog === prefillProgram) {
              opt.selected = true;
            }
            mainProgramSelect.appendChild(opt);
          });
        }
        prefillProgram = null;
      }

      mainDeptSelect.addEventListener('change', updateProgramDropdown);

      // --- 3. EDIT BUTTON CLICK POPULATION ---
      const editButtons = document.querySelectorAll('.edit-btn');
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const fname = this.getAttribute('data-fname');
          const mname = this.getAttribute('data-mname');
          const lname = this.getAttribute('data-lname');
          const employ = this.getAttribute('data-employ');
          const college = this.getAttribute('data-college');
          const program = this.getAttribute('data-program');
          const status = this.getAttribute('data-status');

          const fullName = `${fname} ${mname} ${lname}`.replace(/\s+/g, ' ').trim();

          document.getElementById('modal_old_idnumber').value = id;
          document.getElementById('modal_idnumber').value = id;
          document.getElementById('modal_fullname').value = fullName;
          document.getElementById('modal_employment_role').value = employ || 'Teaching';
          document.getElementById('modal_status').value = status || 'active';
          document.getElementById('modal_password').value = '';

          // Pre-fill College and cascade Program
          if (allColleges.includes(college)) {
            mainDeptSelect.value = college;
            prefillProgram = program;
          } else {
            mainDeptSelect.value = "";
          }

          updateProgramDropdown();
        });
      });

      // Submit Confirmation
      document.getElementById("editRegistrarForm").addEventListener("submit", function(e) {
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
        let filterValue = document.getElementById('customRegistrarSearch').value.toLowerCase();
        let tableRows = Array.from(document.querySelectorAll('#registrarTable tbody tr'));

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

      document.getElementById('customRegistrarSearch').addEventListener('keyup', () => {
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

    // Toggle Password Visibility in Modal
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
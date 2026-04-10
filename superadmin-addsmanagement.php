<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// ----------------------------------------------------
// 1. HANDLE ADDING NEW ENTRIES
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_entry'])) {
  $type = $_POST['type'] ?? '';
  $value = trim($_POST['value'] ?? '');

  if ($type && $value) {
    switch ($type) {
      case 'Rank':
        $column = 'rank_name';
        break;
      case 'Position':
        $column = 'position_name';
        break;
      case 'Section':
        $column = 'section_name';
        break;
      case 'College':
        $column = 'college_name';
        break;
      case 'Program':
        $column = 'program_name';
        break;
      default:
        $column = '';
    }

    if ($column) {
      // Force uppercase for both College and Program
      if ($type === 'College' || $type === 'Program') {
        $value = strtoupper($value);
      }

      if ($type === 'Program') {
        $college_name = trim($_POST['college_name'] ?? '');
        if (empty($college_name)) {
          $_SESSION['msg'] = "Please select a College for the program.";
          $_SESSION['msg_type'] = "warning";
          header("Location: superadmin-addsmanagement.php");
          exit();
        }

        $check = $conn->prepare("SELECT COUNT(*) FROM adds WHERE LOWER(program_name)=LOWER(?) AND LOWER(college_name)=LOWER(?)");
        $check->bind_param("ss", $value, $college_name);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
          $_SESSION['msg'] = "Program already exists in this College.";
          $_SESSION['msg_type'] = "warning";
        } else {
          $stmt = $conn->prepare("INSERT INTO adds (college_name, program_name) VALUES (?, ?)");
          $stmt->bind_param("ss", $college_name, $value);
          $stmt->execute();
          $_SESSION['msg'] = "Program added successfully!";
          $_SESSION['msg_type'] = "success";
          $stmt->close();
        }
      } else {
        $check = $conn->prepare("SELECT COUNT(*) FROM adds WHERE LOWER($column)=LOWER(?)");
        $check->bind_param("s", $value);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
          $_SESSION['msg'] = "$type already exists.";
          $_SESSION['msg_type'] = "warning";
        } else {
          $stmt = $conn->prepare("INSERT INTO adds ($column) VALUES (?)");
          $stmt->bind_param("s", $value);
          $stmt->execute();
          $_SESSION['msg'] = "$type added successfully!";
          $_SESSION['msg_type'] = "success";
          $stmt->close();
        }
      }
      header("Location: superadmin-addsmanagement.php");
      exit();
    }
  }
}

// ----------------------------------------------------
// 2. HANDLE EDITING EXISTING ENTRIES (MODAL SUBMIT)
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_entry'])) {
  $id = $_POST['edit_id'];
  $type = $_POST['edit_type'];
  $value = trim($_POST['edit_value']);

  if ($id && $type && $value) {
    if ($type === 'College') {
      $value = strtoupper($value);

      // Fetch the old college name first so we can cascade the update to its programs
      $old_stmt = $conn->prepare("SELECT college_name FROM adds WHERE id = ?");
      $old_stmt->bind_param("i", $id);
      $old_stmt->execute();
      $old_res = $old_stmt->get_result()->fetch_assoc();
      $old_college = $old_res['college_name'];
      $old_stmt->close();

      // Update all rows (Colleges and Programs) that have this old college name
      $stmt = $conn->prepare("UPDATE adds SET college_name = ? WHERE college_name = ?");
      $stmt->bind_param("ss", $value, $old_college);
      $stmt->execute();
    } elseif ($type === 'Program') {
      $value = strtoupper($value); // Force uppercase for Program edits
      $college_name = trim($_POST['edit_college_name']);
      $stmt = $conn->prepare("UPDATE adds SET program_name = ?, college_name = ? WHERE id = ?");
      $stmt->bind_param("ssi", $value, $college_name, $id);
      $stmt->execute();
    } else {
      $column = '';
      if ($type === 'Rank') $column = 'rank_name';
      if ($type === 'Position') $column = 'position_name';
      if ($type === 'Section') $column = 'section_name';

      $stmt = $conn->prepare("UPDATE adds SET $column = ? WHERE id = ?");
      $stmt->bind_param("si", $value, $id);
      $stmt->execute();
    }

    $_SESSION['msg'] = "$type updated successfully!";
    $_SESSION['msg_type'] = "success";
  } else {
    $_SESSION['msg'] = "Failed to update. Missing information.";
    $_SESSION['msg_type'] = "error";
  }
  header("Location: superadmin-addsmanagement.php");
  exit();
}

// Pre-fetch Colleges to use in both Add and Edit Dropdowns
$colleges_list = [];
$college_query = $conn->query("SELECT DISTINCT college_name FROM adds WHERE college_name IS NOT NULL AND college_name != '' AND (program_name IS NULL OR program_name = '') ORDER BY college_name ASC");
while ($r = $college_query->fetch_assoc()) {
  $colleges_list[] = $r['college_name'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <style>
    .college-card {
      border: 1px solid #dee2e6;
      border-radius: 10px;
      margin-bottom: 1rem;
      background: #f8f9fa;
      padding: 1rem;
    }

    .college-title {
      font-weight: 600;
      font-size: 1rem;
      color: #0d6efd;
    }

    .program-list {
      list-style-type: none;
      margin: 0.5rem 0 0 1rem;
      padding: 0;
    }

    .program-list li {
      padding: 3px 0;
    }

    .program-list li::before {
      content: "• ";
      color: #198754;
    }

    .box-card {
      background: #fff;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
    }
  </style>
</head>

<body>
  <?php include 'superadmin-header.php'; ?>
  <?php include 'superadmin-sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Manage Colleges, Programs, Ranks, Positions, Sections</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Manage</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
      <script>
        Swal.fire({
          icon: '<?= $_SESSION['msg_type'] === 'success' ? 'success' : ($_SESSION['msg_type'] === 'warning' ? 'warning' : 'error') ?>',
          title: "<?= $_SESSION['msg_type'] === 'success' ? 'Success!' : 'Notice' ?>",
          text: "<?= htmlspecialchars($_SESSION['msg']) ?>",
          confirmButtonColor: "#198754"
        });
      </script>
      <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <section class="section">

      <div class="card p-4 mb-4">
        <h5 class="card-title">Add New Entry</h5>
        <form method="POST" class="row g-3">
          <input type="hidden" name="add_entry" value="1">
          <div class="col-md-4">
            <label class="form-label">Type</label>
            <select class="form-select" name="type" id="type" onchange="togglecollegeDropdown()" required>
              <option value="">-- Select Type --</option>
              <option value="Rank">Rank</option>
              <option value="Position">Position</option>
              <option value="Section">Section</option>
              <option value="College">College</option>
              <option value="Program">Program</option>
            </select>
          </div>

          <div class="col-md-4 d-none" id="collegeField">
            <label class="form-label">Select College</label>
            <select class="form-select" name="college_name" id="collegeSelect">
              <option value="">-- Choose College --</option>
              <?php foreach ($colleges_list as $c_name): ?>
                <option value="<?= htmlspecialchars($c_name) ?>"><?= htmlspecialchars($c_name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-4" id="nameContainer">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="value" id="entryValue" required>
          </div>

          <div class="col-12 text-end mt-3">
            <button type="button" id="confirmAdd" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Entry</button>
          </div>
        </form>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <h5 class="mb-3"><i class="bi bi-building"></i> Colleges & Programs</h5>
          <?php
          $colleges = $conn->query("
            SELECT id, college_name
            FROM adds
            WHERE college_name IS NOT NULL 
              AND college_name != '' 
              AND (program_name IS NULL OR program_name = '')
            ORDER BY college_name ASC
          ");
          while ($col = $colleges->fetch_assoc()):
            $dept = $col['college_name'];
            $programs = $conn->query("SELECT id, program_name FROM adds WHERE college_name='$dept' AND program_name IS NOT NULL ORDER BY program_name ASC");
          ?>
            <div class="college-card">
              <div class="d-flex justify-content-between align-items-center">
                <div class="college-title text-success"><?= htmlspecialchars($dept) ?></div>
                <button type="button" class="btn btn-sm btn-warning edit-btn"
                  data-bs-toggle="modal"
                  data-bs-target="#editModal"
                  data-id="<?= $col['id'] ?>"
                  data-type="College"
                  data-value="<?= htmlspecialchars($dept) ?>">Edit</button>
              </div>

              <?php if ($programs->num_rows > 0): ?>
                <ul class="program-list">
                  <?php while ($p = $programs->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($p['program_name']) ?>
                      <button type="button" class="btn btn-link text-warning p-0 small ms-2 edit-btn" style="text-decoration:none;"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-id="<?= $p['id'] ?>"
                        data-type="Program"
                        data-value="<?= htmlspecialchars($p['program_name']) ?>"
                        data-college="<?= htmlspecialchars($dept) ?>">(Edit)</button>
                    </li>
                  <?php endwhile; ?>
                </ul>
              <?php else: ?>
                <p class="text-muted mt-1 ms-3 mb-0">No programs listed.</p>
              <?php endif; ?>
            </div>
          <?php endwhile; ?>
        </div>

        <div class="col-lg-6">
          <h5 class="mb-3"><i class="bi bi-list-check"></i> Ranks, Positions & Sections</h5>

          <?php
          $categories = [
            "Ranks" => ["column" => "rank_name", "type" => "Rank"],
            "Positions" => ["column" => "position_name", "type" => "Position"],
            "Sections" => ["column" => "section_name", "type" => "Section"]
          ];

          foreach ($categories as $title => $info):
            $query = $conn->query("SELECT id, {$info['column']} AS name FROM adds WHERE {$info['column']} IS NOT NULL ORDER BY name ASC");
          ?>
            <div class="college-card">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="college-title text-success"><?= htmlspecialchars($title) ?></div>
              </div>

              <?php if ($query->num_rows > 0): ?>
                <ul class="program-list">
                  <?php while ($row = $query->fetch_assoc()): ?>
                    <li class="d-flex justify-content-between align-items-center">
                      <span><?= htmlspecialchars($row['name']) ?></span>
                      <button type="button" class="btn btn-sm btn-warning ms-2 edit-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-id="<?= $row['id'] ?>"
                        data-type="<?= htmlspecialchars($info['type']) ?>"
                        data-value="<?= htmlspecialchars($row['name']) ?>">Edit</button>
                    </li>
                  <?php endwhile; ?>
                </ul>
              <?php else: ?>
                <p class="text-muted mt-1 ms-3 mb-0">No <?= strtolower($title) ?> added.</p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>

      </div>
    </section>
  </main>

  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="editModalLabel">Edit Entry</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">

            <input type="hidden" name="update_entry" value="1">
            <input type="hidden" name="edit_id" id="edit_id">
            <input type="hidden" name="edit_type" id="edit_type">

            <div class="mb-3 d-none" id="editCollegeContainer">
              <label class="form-label text-muted fw-semibold">Belongs to College</label>
              <select class="form-select" name="edit_college_name" id="edit_college_name">
                <?php foreach ($colleges_list as $c_name): ?>
                  <option value="<?= htmlspecialchars($c_name) ?>"><?= htmlspecialchars($c_name) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label text-muted fw-semibold">Name</label>
              <input type="text" class="form-control" name="edit_value" id="edit_value" required>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // Sweet Alert for Form Submission Confirmation (Add)
    document.getElementById("confirmAdd").addEventListener("click", function(e) {
      const type = document.getElementById("type").value;
      const name = document.querySelector("input[name='value']").value.trim();
      if (!type || !name) {
        Swal.fire({
          icon: "warning",
          title: "Missing Fields",
          text: "Please complete all fields."
        });
        return;
      }
      Swal.fire({
        title: `Add "${name}" as ${type}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, add it",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#198754",
        cancelButtonColor: "#dc3545"
      }).then((result) => {
        if (result.isConfirmed) {
          // Submit the form containing this button
          e.target.closest("form").submit();
        }
      });
    });

    // Populate Add Form dynamic fields
    function togglecollegeDropdown() {
      const type = document.getElementById("type").value;
      const collegeField = document.getElementById("collegeField");
      const nameContainer = document.getElementById("nameContainer");
      const inputField = document.getElementById("entryValue");
      const collegeSelect = document.getElementById("collegeSelect");

      if (type === "Program") {
        collegeField.classList.remove("d-none");
        collegeSelect.setAttribute("required", "required");
        nameContainer.className = "col-md-4";
        inputField.style.textTransform = "uppercase";
        inputField.addEventListener('input', forceUppercase);
        inputField.placeholder = "ENTER PROGRAM NAME (CAPS ONLY)";
      } else if (type === "College") {
        collegeField.classList.add("d-none");
        collegeSelect.removeAttribute("required");
        nameContainer.className = "col-md-8";
        inputField.style.textTransform = "uppercase";
        inputField.addEventListener('input', forceUppercase);
        inputField.placeholder = "ENTER COLLEGE NAME (CAPS ONLY)";
      } else {
        collegeField.classList.add("d-none");
        collegeSelect.removeAttribute("required");
        nameContainer.className = "col-md-8";
        inputField.style.textTransform = "none";
        inputField.removeEventListener('input', forceUppercase);
        inputField.placeholder = "";
      }
    }

    function forceUppercase(e) {
      e.target.value = e.target.value.toUpperCase();
    }

    // Modal Trigger Logic
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const type = this.getAttribute('data-type');
        const value = this.getAttribute('data-value');
        const college = this.getAttribute('data-college'); // Only present for Programs

        // Set hidden input values
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_type').value = type;

        // Set visible input values
        const editValueInput = document.getElementById('edit_value');
        editValueInput.value = value;
        document.getElementById('editModalLabel').innerText = 'Edit ' + type;

        // Display College Dropdown if editing a Program
        const collegeContainer = document.getElementById('editCollegeContainer');
        const collegeSelect = document.getElementById('edit_college_name');

        if (type === 'Program') {
          collegeContainer.classList.remove('d-none');
          collegeSelect.setAttribute('required', 'required');
          collegeSelect.value = college;
        } else {
          collegeContainer.classList.add('d-none');
          collegeSelect.removeAttribute('required');
        }

        // Apply College & Program auto-uppercase rule in the edit modal
        if (type === 'College' || type === 'Program') {
          editValueInput.style.textTransform = 'uppercase';
          editValueInput.oninput = function() {
            this.value = this.value.toUpperCase();
          };
        } else {
          editValueInput.style.textTransform = 'none';
          editValueInput.oninput = null;
        }
      });
    });
  </script>
</body>

</html>
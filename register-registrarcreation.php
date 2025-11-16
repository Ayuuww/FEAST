<?php
session_start();
include 'conn/conn.php';

// Restrict to Registrar only
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
</head>

<body>
  <?php include 'register-header.php'; ?>
  <?php include 'register-sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Account Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Account Creator</li>
          <li class="breadcrumb-item active">Add New Account Creator</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center align-items-center">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <?php if (isset($_SESSION['msg'])): ?>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                      icon: '<?= $_SESSION['msg_type'] === 'success' ? 'success' : 'info' ?>',
                      title: '<?= htmlspecialchars($_SESSION['msg']) ?>',
                      showConfirmButton: false,
                      timer: 1500,
                      timerProgressBar: true
                    });
                  });
                </script>
                <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
              <?php endif; ?>

              <h5 class="card-title text-center">Create New Account</h5>

              <form class="row g-3 needs-validation" novalidate method="post" action="registrarcreation.php">

                <!-- ID Number -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="idnumber" class="form-control" placeholder="ID Number" pattern="^[0-9\-]+$" required>
                    <label>ID Number</label>
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
                    <input type="text" name="mid_name" class="form-control" placeholder="Middle Name" required>
                    <label>Middle Name</label>
                  </div>
                </div>

                <!-- Last Name -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    <label>Last Name</label>
                  </div>
                </div>

                <!-- Employment Role -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" name="employment_role" id="employment_role" required>
                      <option value="" disabled selected>Select Employment Role</option>
                      <option value="Teaching">Teaching</option>
                      <option value="Non-Teaching">Non-Teaching</option>
                    </select>
                    <label>Employment Role</label>
                  </div>
                </div>

                <!-- Faculty Rank (only for Teaching) -->
                <div class="col-md-6 teaching-only" id="facultyRankDiv">
                  <div class="form-floating">
                    <select class="form-select" name="faculty_rank" id="faculty_rank" required>
                      <option value="" selected disabled>-- Select Faculty Rank --</option>
                      <?php
                      $rankQuery = $conn->query("SELECT DISTINCT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != '' ORDER BY rank_name ASC");
                      while ($row = $rankQuery->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['rank_name']) . '">' . htmlspecialchars($row['rank_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label>Faculty Rank</label>
                  </div>
                </div>


                <!-- Department (only for Teaching) -->
                <div class="col-md-6 teaching-only" id="departmentDiv">
                  <div class="form-floating">
                    <select class="form-select" name="department" id="department" required>
                      <option value="" selected disabled>-- Select Department --</option>
                      <?php
                      $deptQuery = $conn->query("SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != '' ORDER BY department_name ASC");
                      while ($row = $deptQuery->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['department_name']) . '">' . htmlspecialchars($row['department_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label>Department</label>
                  </div>
                </div>

                <!-- Program (only for Teaching) -->
                <div class="col-md-6 teaching-only" id="programDiv">
                  <div class="form-floating">
                    <select class="form-select" name="program" id="program" required>
                      <option value="" selected disabled>-- Select Program --</option>
                    </select>
                    <label>Program</label>
                  </div>
                </div>

                <!-- Hidden defaults -->
                <input type="hidden" name="password" value="ILOVEDMMMSU">
                <input type="hidden" name="status" value="active">

                <!-- Submit -->
                <div class="col-4 offset-4">
                  <button class="btn btn-success w-100" type="submit">Create Account</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <script src="sweetalert2/sweetalert2@11.js"></script>

  <script>
    // Dynamic Program loading
    document.getElementById('department').addEventListener('change', function() {
      let dept = this.value;
      let programSelect = document.getElementById('program');
      programSelect.innerHTML = '<option disabled selected>Loading...</option>';

      fetch('get_programs.php?department=' + encodeURIComponent(dept))
        .then(res => res.json())
        .then(data => {
          programSelect.innerHTML = '<option value="">-- Select Program --</option>';
          data.forEach(p => {
            let opt = document.createElement('option');
            opt.value = p;
            opt.textContent = p;
            programSelect.appendChild(opt);
          });
        })
        .catch(() => {
          programSelect.innerHTML = '<option disabled selected>Error loading programs</option>';
        });
    });

    // Show/hide teaching-only fields
    document.getElementById('employment_role').addEventListener('change', function() {
      let teachingFields = document.querySelectorAll('.teaching-only');
      if (this.value === 'Teaching') {
        teachingFields.forEach(el => el.style.display = 'block');
      } else {
        teachingFields.forEach(el => {
          el.style.display = 'none';
          el.querySelectorAll('input, select').forEach(i => i.value = '');
        });
      }
    });

    // Initialize state (hide by default)
    document.querySelectorAll('.teaching-only').forEach(el => el.style.display = 'none');

    document.getElementById('employment_role').addEventListener('change', function() {
      let teachingFields = document.querySelectorAll('.teaching-only');
      let rankSelect = document.getElementById('faculty_rank');

      if (this.value === 'Teaching') {
        teachingFields.forEach(el => el.style.display = 'block');
        rankSelect.required = true;
      } else {
        teachingFields.forEach(el => {
          el.style.display = 'none';
          el.querySelectorAll('input, select').forEach(i => i.value = '');
        });
        rankSelect.required = false;
      }
    });
  </script>
</body>

</html>
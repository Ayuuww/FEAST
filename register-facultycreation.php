<?php
session_start();
include 'conn/conn.php'; // Connection to the database

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

              <h5 class="card-title text-center">Create New Faculty</h5>

              <form class="row g-3 needs-validation" novalidate method="post" action="facultycreation.php">

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

                <!-- Hidden Default Password -->
                <input type="hidden" name="password" value="ILOVEDMMMSU">

                <!-- Academic Rank -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" name="faculty_rank" required>
                      <option value="" disabled selected>Select Rank</option>
                      <?php
                      $rankQuery = $conn->query("SELECT rank_name FROM adds WHERE rank_name IS NOT NULL AND rank_name != ''");
                      while ($row = $rankQuery->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['rank_name']) . '">' . htmlspecialchars($row['rank_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label>Academic Rank</label>
                  </div>
                </div>

                <!-- Department -->
                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" name="department" id="department" required>
                      <option value="" disabled selected>Select Department</option>
                      <?php
                      $deptQuery = $conn->query("SELECT DISTINCT department_name FROM adds WHERE department_name IS NOT NULL AND department_name != ''");
                      while ($row = $deptQuery->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['department_name']) . '">' . htmlspecialchars($row['department_name']) . '</option>';
                      }
                      ?>
                    </select>
                    <label>College</label>
                  </div>
                </div>

                <!-- Program (changes based on department) -->
                <div class="col-md-12">
                  <div class="form-floating">
                    <select class="form-select" name="program" id="program" required>
                      <option value="" disabled selected>Select Program</option>
                    </select>
                    <label>Program</label>
                  </div>
                </div>

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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    // 🟢 Dynamic Program Loading based on Department
    document.getElementById('department').addEventListener('change', function() {
      let dept = this.value;
      let programSelect = document.getElementById('program');
      programSelect.innerHTML = '<option disabled selected>Loading...</option>';

      fetch('get_programs.php?department=' + encodeURIComponent(dept))
        .then(res => res.json())
        .then(data => {
          programSelect.innerHTML = '<option disabled selected>Select Program</option>';
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
  </script>
</body>

</html>
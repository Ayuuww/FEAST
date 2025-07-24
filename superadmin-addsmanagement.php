<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
      case 'Department':
        $column = 'department_name';
        break;
      default:
        $column = '';
    }

    if ($column) {
      $check = $conn->prepare("SELECT COUNT(*) FROM adds WHERE LOWER($column) = LOWER(?)");
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
        if ($stmt->execute()) {
          $_SESSION['msg'] = "$type added successfully!";
          $_SESSION['msg_type'] = "success";
        } else {
          $_SESSION['msg'] = "Failed to add $type.";
          $_SESSION['msg_type'] = "danger";
        }
        $stmt->close();
      }
      header("Location: superadmin-addsmanagement.php");
      exit();
    }
  }
}

// Fetch latest adds
$result = mysqli_query($conn, "SELECT * FROM adds ORDER BY id DESC");

?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Manage Ranks & Positions</title>

  <?php include 'header.php' ?>

  <style>
    .table+.card-title {
      margin-top: 2rem;
    }

    .table th,
    .table td {
      vertical-align: middle;
    }
  </style>

</head>

<body>

  <?php include 'superadmin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="superadmin-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <!-- Subject Nav -->
      <!-- <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="superadmin-subjectlist.php" >
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="superadmin-subjectadding.php">
                <i class="bi bi-circle"></i><span>Add Subject</span>
              </a>
            </li>
          </ul>
        </li> -->
      <!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <!-- <li class="nav-item">
          <a class="nav-link collapsed" href="superadmin-studentsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Assign Subject</span>
          </a>
        </li> -->
      <!-- End Student Subject Nav -->

      <!-- Reports Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="reports" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-individualreport.php">
              <i class="bi bi-circle"></i><span>Invidiual Report</span>
            </a>
          </li>
          <li>
            <a href="superadmin-acknowledgementreport.php">
              <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
            </a>
          </li>
          <li>
            <a href="superadmin-pastrecords.php">
              <i class="bi bi-circle"></i><span>Past Record</span>
            </a>
          </li>
        </ul>
      </li><!-- End Reports Nav -->

      <!-- Evaluation Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#evaluation" data-bs-toggle="collapse" href="#">
          <i class="ri-settings-4-line"></i><span>Evaluation</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="evaluation" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-evaluationsetting.php">
              <i class="bi bi-circle"></i><span>Setting</span>
            </a>
          </li>
          <li>
            <a href="superadmin-evaluationswitch.php">
              <i class="bi bi-circle"></i><span>On/Off</span>
            </a>
          </li>
        </ul>
      </li><!-- End Evalutaion Nav -->

      <li class="nav-heading">Account Management</li>

      <!-- Management Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" href="superadmin-addsmanagement.php">
          <i class="ri-settings-line"></i>
          <span>Manage</span>
        </a>
      </li><!-- End Management Nav -->

      <!-- Faculty Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people-fill"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-facultylist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-facultycreation.php">
              <i class="bi bi-circle"></i><span>Add New Faculty</span>
            </a>
          </li>
        </ul>
      </li><!-- End Faculty Nav -->

      <!-- Student Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-studentlist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-studentcreation.php">
              <i class="bi bi-circle"></i><span>Add New Student</span>
            </a>
          </li>
        </ul>
      </li><!-- End Student Nav -->

      <!-- Admin Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="admin-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-adminlist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-admincreation.php">
              <i class="bi bi-circle"></i><span>Add New Admin</span>
            </a>
          </li>
        </ul>
      </li><!-- End Admin Nav -->

      <!-- Super Admin Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-fill"></i><span>Super Admin</span><i
            class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-superadminlist.php">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-superadmincreation.php">
              <i class="bi bi-circle"></i><span>Add New SuperAdmin</span>
            </a>
          </li>
        </ul>
      </li><!-- End Super Admin Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="superadmin-user-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End Sign Out Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Manage Ranks, Positions, Sections, Departments</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Manage</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <?php if (isset($_SESSION['msg'])): ?>
      <script>
        window.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
            icon: '<?= $_SESSION['msg_type'] === 'success' ? 'success' : ($_SESSION['msg_type'] === 'warning' ? 'warning' : 'error') ?>',
            title: "<?= $_SESSION['msg_type'] === 'success' ? 'Successfully Added!' : 'Notice' ?>",
            text: "<?= htmlspecialchars($_SESSION['msg']) ?>",
            confirmButtonColor: "#198754"
          });
        });
      </script>
      <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>



    <section class="section">
      <div class="row justify-content-center">
        <div class="card col-lg-6">
          <div class="card-body">
            <h5 class="card-title">Add New</h5>


            <form method="POST" class="row g-3">
              <div class="col-md-4">
                <label for="type" class="form-label">Type</label>
                <select class="form-select" name="type" required>
                  <option value="">-- Select --</option>
                  <option value="Rank">Rank</option>
                  <option value="Position">Position</option>
                  <option value="Section">Section</option>
                  <option value="Department">Department</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="value" class="form-label">Name</label>
                <input type="text" class="form-control" name="value" required>
              </div>
              <div class="col-md-2 align-self-end">
                <button type="button" id="confirmAdd" class="btn btn-success w-100">Add</button>
              </div>
            </form>
          </div>
        </div>

        <?php
        // Fetch entries by type
        $ranks = mysqli_query($conn, "SELECT id, rank_name AS name FROM adds WHERE rank_name IS NOT NULL ORDER BY rank_name ASC");
        $positions = mysqli_query($conn, "SELECT id, position_name AS name FROM adds WHERE position_name IS NOT NULL ORDER BY position_name ASC");
        $sections = mysqli_query($conn, "SELECT id, section_name AS name FROM adds WHERE section_name IS NOT NULL ORDER BY section_name ASC");
        $departments = mysqli_query($conn, "SELECT id, department_name AS name FROM adds WHERE department_name IS NOT NULL ORDER BY department_name ASC");
        ?>

        <div class="row justify-content-center">
          <div class="card col-lg-12">
            <div class="card-body">
              <div class="row">
                <!-- Ranks -->
                <div class="col-md-3">
                  <h5 class="card-title">Existing Ranks</h5>
                  <table class="table table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Rank</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = mysqli_fetch_assoc($ranks)): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['name']) ?></td>
                          <td class="text-center">
                            <a href="superadmin-addsedit.php?id=<?= $row['id'] ?>&type=Rank" class="btn btn-warning btn-sm">Edit</a>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>

                <!-- Positions -->
                <div class="col-md-3">
                  <h5 class="card-title">Existing Positions</h5>
                  <table class="table table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Position</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = mysqli_fetch_assoc($positions)): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['name']) ?></td>
                          <td class="text-center">
                            <a href="superadmin-addsedit.php?id=<?= $row['id'] ?>&type=Position" class="btn btn-warning btn-sm">Edit</a>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>

                <!-- Sections -->
                <div class="col-md-3">
                  <h5 class="card-title">Existing Sections</h5>
                  <table class="table table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Section</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = mysqli_fetch_assoc($sections)): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['name']) ?></td>
                          <td class="text-center">
                            <a href="superadmin-addsedit.php?id=<?= $row['id'] ?>&type=Section" class="btn btn-warning btn-sm">Edit</a>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>

                <!-- Departments -->
                <div class="col-md-3">
                  <h5 class="card-title">Existing Departments</h5>
                  <table class="table table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Department</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = mysqli_fetch_assoc($departments)): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['name']) ?></td>
                          <td class="text-center">
                            <a href="superadmin-addsedit.php?id=<?= $row['id'] ?>&type=Department" class="btn btn-warning btn-sm">Edit</a>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div> <!-- End .row -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php' ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
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

  <!-- <script>
    setTimeout(() => {
      const alert = document.querySelector('.alert');
      if (alert) {
        alert.classList.remove('show');
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 500); // optional DOM cleanup
      }
    }, 5000); // Hide after 5 seconds
  </script> -->

  <script>
    document.getElementById("confirmAdd").addEventListener("click", function(e) {
      const typeSelect = document.querySelector("select[name='type']");
      const nameInput = document.querySelector("input[name='value']");
      const type = typeSelect.value;
      const value = nameInput.value.trim();

      if (!type || !value) {
        Swal.fire({
          icon: "warning",
          title: "Missing Fields",
          text: "Please select a type and enter a name.",
        });
        return;
      }

      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-success mx-2",
          cancelButton: "btn btn-danger mx-2"
        },
        buttonsStyling: false
      });

      swalWithBootstrapButtons.fire({
        title: `Add "${value}" as ${type}?`,
        text: "Please double-check if this is correct before saving.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, add it!",
        cancelButtonText: "Cancel",
        reverseButtons: false // Change this to false
      }).then((result) => {
        if (result.isConfirmed) {
          // Submit the form manually if confirmed
          e.target.closest("form").submit();
        }
      });
    });
  </script>

</body>

</html>
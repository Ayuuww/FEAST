<?php 

session_start();
include 'conn/conn.php';// Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: pages-login.php");
    exit();
}

// Display message if set
if (isset($_SESSION['msg'])) {
    echo "<script>alert('" . $_SESSION['msg'] . "');</script>";
    unset($_SESSION['msg']);
  }

$max_subjects = 9; // example limit

// Query to get students with less than the maximum number of subjects
// This query counts the number of subjects each student is enrolled in and filters those with less than the maximum allowed
$query = "  SELECT s.idnumber, s.first_name, s.mid_name, s.last_name, s.department, s.role, COUNT(ss.subject_code) AS subject_count
            FROM student s
            LEFT JOIN student_subject ss ON s.idnumber = ss.student_id
            WHERE s.role = 'student'
            GROUP BY s.idnumber
            HAVING subject_count < $max_subjects 
            ORDER BY s.department";

$result = mysqli_query($conn, $query);

// Query to get subjects and their associated faculty
// This query retrieves all subjects along with the faculty who teaches them
$subject_query = "  SELECT ss.code, ss.title, ss.faculty_id, ss.admin_id,
                    COALESCE(f.first_name, a.first_name) AS first_name,
                    COALESCE(f.last_name, a.last_name) AS last_name
                    FROM subject ss
                    LEFT JOIN faculty f ON ss.faculty_id = f.idnumber
                    LEFT JOIN admin a ON ss.admin_id = a.idnumber";


$subject_result = mysqli_query($conn, $subject_query);


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Student Subject</title>

  <?php include 'header.php'?>

  <style>
  div.dataTables_length {
    display: none;
  }
  </style>

</head>

<body>

  <?php include 'superadmin-header.php'?>

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
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-book"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-subjectlist.php" >
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
          <li>
            <a href="superadmin-subjectadding.php" >
              <i class="bi bi-circle"></i><span>Add Subject</span>
            </a>
          </li>
        </ul>
      </li><!-- End Subject Nav -->

      <!-- Student Subject Nav -->
      <li class="nav-item">
        <a class="nav-link collapse" href="superadmin-studentsubject.php">
          <i class="bi bi-book-fill"></i>
          <span>Assign Subject</span>
        </a>
      </li><!-- End Student Subject Nav -->

      <!-- Reports Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="reports" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="superadmin-individualreport.php" >
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
            <a href="superadmin-evaluationsetting.php" >
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
            <a href="superadmin-adminlist.php" >
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
      <h1>Assign Subject</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Student Subject</li>
          <li class="breadcrumb-item active">Assign Subject</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <!-- Inserting Subject to Student Section -->
      <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Assign Subject</h5>

                  <form method="POST" action="assignsubject.php" class="row g-3">
                    <!--Organize students by department -->
                    <div class="col-md-2">
                      <div class="form-floating mb-3">
                        <select id="departmentFilter" class="form-select">
                          <option value="">All Departments</option>
                          <?php         
                          $students_by_dept = [];
                          while ($row = mysqli_fetch_assoc($result)) {
                              $students_by_dept[$row['department']][] = $row;
                          }
                          ksort($students_by_dept); // Sort departments alphabetically
                          foreach (array_keys($students_by_dept) as $dept): ?>
                            <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <label for="departmentFilter">Filter by Department</label>
                      </div>
                    </div>
                    
                    <!-- Student Selection -->
                    <div class="col-md-5">
                      <div class="form-floating">
                        <select id="student_id" name="student_id" class="form-select text-capitalize" required>
                          <option value="" disabled selected>Select Student Name</option>
                          <?php foreach ($students_by_dept as $department => $students): ?>
                            <optgroup label="<?= htmlspecialchars($department) ?>" data-department="<?= htmlspecialchars($department) ?>">
                              <?php foreach ($students as $student): ?>
                                <option value="<?= $student['idnumber'] ?>" class="text-capitalize">
                                  <?= $student['first_name'] . ' ' . $student['mid_name'] . ' ' . $student['last_name'] ?>
                                </option>
                              <?php endforeach; ?>
                            </optgroup>
                          <?php endforeach; ?>
                        </select>
                        <label for="student_id" class="form-label">Student Name</label>
                      </div>
                    </div>

                    <!-- Subject Selection -->
                    <div class="col-md-5">
                      <div class="form-floating">
                        <select id="subject_code" name="subject_code" class="form-select text-capitalize" required>
                          <option value="" disabled selected>Subject with Instructor Name</option>
                          <?php
                          $subjects_by_faculty = [];
                          while ($subject = mysqli_fetch_assoc($subject_result)) {
                              $instructor_name = trim($subject['first_name'] . ' ' . $subject['last_name']);
                              $subjects_by_faculty[$instructor_name][] = $subject;

                          }
                          foreach ($subjects_by_faculty as $faculty => $subjects): ?>
                            <optgroup label="Instructor: <?= htmlspecialchars($faculty) ?>">
                              <?php foreach ($subjects as $sub): ?>
                                <option value="<?= $sub['code'] ?>" 
                                  data-faculty-id="<?= $sub['faculty_id'] ?? '' ?>" 
                                  data-admin-id="<?= $sub['admin_id'] ?? '' ?>">
                                  <?= $sub['code'] . ": " . $sub['title'] ?>
                                </option>
                              <?php endforeach; ?>
                            </optgroup>
                          <?php endforeach; ?>
                        </select>
                        <label for="subject_code" class="form-label">Select Subject</label>
                      </div>
                    </div>
                    <!-- Hidden Faculty ID -->
                     <input type="hidden" name="faculty_id" id="faculty_id_hidden">
                     <input type="hidden" name="admin_id" id="admin_id_hidden">

                    <!-- End Subject Selection -->


                    <!-- Submit Button -->
                    <div class="col-4 offset-4">
                        <button type="submit" name="assign" class="btn btn-success w-100">Assign Subject</button>
                    </div>
                </form>

              </div>
            </div>
          </div>
        </div>
      </section><!-- End Inserting Subject to Student Section -->

    

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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

 <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Elements
      const departmentFilter = document.getElementById("departmentFilter");
      const studentSelect = document.getElementById("student_id");
      const allGroups = Array.from(studentSelect.querySelectorAll("optgroup"));

      const subjectSelect = document.getElementById("subject_code");
      const hiddenFaculty = document.getElementById("faculty_id_hidden");
      const hiddenAdmin = document.getElementById("admin_id_hidden");

      // Department filtering logic
      departmentFilter.addEventListener("change", function () {
        const selectedDept = this.value;

        allGroups.forEach(group => {
          const groupDept = group.getAttribute("data-department");
          group.style.display = (!selectedDept || selectedDept === groupDept) ? "block" : "none";
        });

        // Reset student selection
        studentSelect.selectedIndex = 0;
      });

      // Subject selection logic
      subjectSelect.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const facultyId = selectedOption.getAttribute("data-faculty-id");
        const adminId = selectedOption.getAttribute("data-admin-id");

        hiddenFaculty.value = facultyId ?? "";
        hiddenAdmin.value = adminId ?? "";

        console.log("Selected subject:", selectedOption.value);
        console.log("Faculty ID:", facultyId);
        console.log("Admin ID:", adminId);
      });
    });
  </script>





</body>

</html>

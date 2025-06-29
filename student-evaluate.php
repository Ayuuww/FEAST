<?php
session_start();
include 'conn/conn.php';

// Fetching subjects and their respective faculty
$query = "  SELECT 
            s.code AS subject_code,
            s.title AS subject_title,
            r.first_name, r.mid_name, r.last_name
            FROM student_subject ss
            JOIN subject s ON ss.subject_code = s.code
            JOIN register r ON s.faculty_id = r.idnumber
            WHERE ss.student_id = '{$_SESSION['idnumber']}' ";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>FEAST / Student Evaluate </title>

  <?php include 'header.php' ?>

  </head>
  <body>

    <?php include 'student-header.php'?>

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link collapsed" href="student-dashboard.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li><!-- End Dashboard Nav -->

        <!-- Evaluate Nav -->
        <li class="nav-item">
          <a class="nav-link collapse" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
            <li>
              <a href="student-evaluate.php" class="active">
                <i class="bi bi-circle"></i><span>Evaluate Subject</span>
              </a>
            </li>
            <li>
              <a href="superadmin-subjectadding.php">
                <i class="bi bi-circle"></i><span>Evaluated Subject</span>
              </a>
            </li>
          </ul>
        </li><!-- End Evaluate Nav -->

      </ul>

    </aside><!-- End Sidebar-->

    <main id="main" class="main">

      <div class="pagetitle">
        <h1>Evaluate Subject</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
            <li class="breadcrumb-item ">Evaluate</li>
            <li class="breadcrumb-item active">Evaluate Subject</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section dashboard">
        <div class="row">
           <div class="card">
            <div class="card-body">
              <h5 class="card-title">Table with stripped rows</h5>

              <!-- Table with stripped rows -->
              <h3>Evaluate Your Faculty</h3>
                <table class="table table-bordered">
                <thead>
                    <tr>
                    <th>Subject Code</th>
                    <th>Title</th>
                    <th>Faculty Name</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                    <td><?= htmlspecialchars($row['subject_code']) ?></td>
                    <td><?= htmlspecialchars($row['subject_title']) ?></td>
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name']) ?></td>
                    <td>
                      <a href="student-evaluate-form.php?subject_code=<?= urlencode($row['subject_code']) ?>" class="btn btn-primary">Evaluate</a>
                    </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                </table><!-- End Table with stripped rows -->

            </div>
          </div> 


        </div>
      </section>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <?php include'footer.php'?>
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

  </body>

</html>

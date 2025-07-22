<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'student') {
  header("Location: pages-login.php");
  exit();
}

$student_id = $_SESSION['idnumber'];

$query = "SELECT 
              e.subject_code,
              s.title AS subject_title,
              e.faculty_id,
              COALESCE(f.first_name, a.first_name) AS first_name,
              COALESCE(f.mid_name, a.mid_name) AS mid_name,
              COALESCE(f.last_name, a.last_name) AS last_name,
              e.total_score,
              e.computed_rating,
              e.academic_year,
              e.comment,
              e.semester,
              e.created_at
          FROM evaluation e
          JOIN subject s ON e.subject_code = s.code
          LEFT JOIN faculty f ON e.faculty_id = f.idnumber
          LEFT JOIN admin a ON e.faculty_id = a.idnumber
          LEFT JOIN student_subject ss 
              ON ss.subject_code = s.code 
            AND ss.student_id = e.student_id
            AND ss.faculty_id = e.faculty_id
          WHERE e.student_id = ?
          ORDER BY e.created_at DESC ";

$stmt = $conn->prepare($query); // FIXED: prepare the statement
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();


if (isset($_SESSION['msg'])) {
  echo "<script>alert('" . addslashes($_SESSION['msg']) . "');</script>";
  unset($_SESSION['msg']);
}
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

  <?php include 'student-header.php' ?>

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
            <a href="student-evaluate.php">
              <i class="bi bi-circle"></i><span>Form</span>
            </a>
          </li>
          <li>
            <a href="student-evaluatedsubject.php" class="active">
              <i class="bi bi-circle"></i><span>Evaluated Subject</span>
            </a>
          </li>
        </ul>
      </li><!-- End Evaluate Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="student-user-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End Sign out Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Evaluated Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="student-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Evaluate</li>
          <li class="breadcrumb-item active">Evaluated Subjects</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card">
        <div class="card-body pt-4">
          <h5 class="card-title">List of Subjects You Have Evaluated</h5>

          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center datatable">
              <thead class="table-light">
                <tr>
                  <th>Subject Code</th>
                  <th>Title</th>
                  <th>Faculty Name</th>
                  <th>Total Score</th>
                  <th>Computed Rating (%)</th>
                  <th>Comment</th>
                  <th>Academic Year</th>
                  <th>Semester</th>
                  <th>Evaluated On</th>
                  <th>Reprint</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['subject_code']) ?></td>
                      <td><?= htmlspecialchars($row['subject_title']) ?></td>
                      <td class="text-capitalize">
                        <?= htmlspecialchars(
                          $row['first_name'] . ' ' .
                            $row['mid_name'] . ' ' .
                            $row['last_name'] .
                            ($row['faculty_id'][0] === 'A' ? ' (Admin)' : '')
                        ) ?>
                      </td>
                      <td><?= htmlspecialchars($row['total_score']) ?></td>
                      <td><?= number_format($row['computed_rating'], 2) ?>%</td>
                      <td><?= htmlspecialchars($row['comment']) ?></td>
                      <td><?= htmlspecialchars($row['academic_year']) ?></td>
                      <td><?= htmlspecialchars($row['semester']) ?></td>
                      <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
                      <td>
                        <a href="student-evaluation-reprint-fpdf.php?faculty_id=<?= urlencode($row['faculty_id']) ?>&subject_code=<?= urlencode($row['subject_code']) ?>&academic_year=<?= urlencode($row['academic_year']) ?>&semester=<?= urlencode($row['semester']) ?>"
                          class="btn btn-sm btn-outline-primary">
                          Reprint
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted">No evaluations submitted yet.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
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

</body>

</html>
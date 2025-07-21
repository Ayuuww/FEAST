<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a faculty
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
    header("Location: pages-login.php");
    exit();
}

$evaluator_id = $_SESSION['idnumber'];

// Fetching peer evaluations done by the current faculty
$query = "SELECT 
            ae.id,
            ae.evaluatee_id,
            f.first_name, f.mid_name, f.last_name,
            ae.total_score,
            ae.computed_rating,
            ae.academic_year,
            ae.semester,
            ae.evaluation_date
          FROM admin_evaluation ae
          JOIN faculty f ON ae.evaluatee_id = f.idnumber
          WHERE ae.evaluator_id = ?
          ORDER BY ae.evaluation_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $evaluator_id);
$stmt->execute();
$result = $stmt->get_result();

// Display message if set
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

    <?php include 'admin-header.php'?>

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-dashboard.php">
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
              <a href="admin-evaluate.php" >
                <i class="bi bi-circle"></i><span>Form</span>
              </a>
            </li>
            <li>
              <a href="admin-evaluatedfaculty.php" class="active">
                <i class="bi bi-circle"></i><span>Evaluated Faculty</span>
              </a>
            </li>
          </ul>
        </li><!-- End Evaluate Nav -->

        <!-- Subject Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#subject" data-bs-toggle="collapse" href="#">
            <i class="ri-book-line"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="subject" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="admin-subjectlist.php" >
                <i class="bi bi-circle"></i><span>List</span>
              </a>
            </li>
            <li>
              <a href="admin-subjectadding.php">
                <i class="bi bi-circle"></i><span>Add Subject</span>
              </a>
            </li>
          </ul>
        </li><!-- End Subject Nav -->

        <!-- Student Subject Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-studentsubject.php">
            <i class="ri-book-fill"></i>
            <span>Assign Subject</span>
          </a>
        </li><!-- End Student Subject Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-evaluatedsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Subject Evaluated</span>
          </a>
        </li><!-- End Profile Nav -->

        <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="admin-user-profile.php">
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
            <h1>Evaluated Faculty</h1>
            <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
                <li class="breadcrumb-item">Evaluate</li>
                <li class="breadcrumb-item active">Evaluated Faculty</li>
            </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Faculty You Have Evaluated</h5>

                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Evaluatee Name</th>
                                    <th>Total Score</th>
                                    <th>Computed Rating</th>
                                    <th>Semester</th>
                                    <th>Academic Year</th>
                                    <th>Evaluated On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-capitalize"><?= htmlspecialchars($row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name']) ?></td>
                                        <td><?= htmlspecialchars($row['total_score']) ?></td>
                                        <td><?= htmlspecialchars($row['computed_rating']) ?></td>
                                        <td><?= htmlspecialchars($row['semester']) ?></td>
                                        <td><?= htmlspecialchars($row['academic_year']) ?></td>
                                        <td><?= htmlspecialchars($row['evaluation_date']) ?></td>
                                        <td>
                                          <a href="admin-evaluation-reprint.php?evaluatee_id=<?= urlencode($row['evaluatee_id']) ?>&academic_year=<?= urlencode($row['academic_year']) ?>&semester=<?= urlencode($row['semester']) ?>" 
                                            class="btn btn-sm btn-outline-primary">
                                            Reprint
                                          </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                    <td colspan="5" class="text-center">You have not evaluated any faculty peers yet.</td>
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
    <?php include 'footer.php'?>
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

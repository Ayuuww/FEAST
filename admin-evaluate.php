<?php
session_start();
include 'conn/conn.php';

// Check if the user is logged in and is a faculty
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
    header("Location: pages-login.php");
    exit();
}

$evaluator_id = $_SESSION['idnumber'];

// Get current faculty's department
$dept_query = "SELECT department FROM register WHERE idnumber = ?";
$stmt = $conn->prepare($dept_query);
$stmt->bind_param("s", $evaluator_id);
$stmt->execute();
$dept_result = $stmt->get_result();
$dept_row = $dept_result->fetch_assoc();
$department = $dept_row['department'] ?? '';

// Fetch other faculty members from the same department
$query = "SELECT idnumber, first_name, mid_name, last_name 
          FROM register 
          WHERE role = 'faculty' AND status = 'approved' 
            AND department = ? AND idnumber != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $department, $evaluator_id);
$stmt->execute();
$result = $stmt->get_result();

$faculty_list = [];
while ($row = $result->fetch_assoc()) {
    $faculty_list[] = $row;
}

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

  <title>FEAST / Faculty Evaluate </title>

  <?php include 'header.php' ?>

  <style>
    @media print {
      aside,
      header,
      .btn,
      .back-to-top,
      nav.breadcrumb,
      .sidebar {
        display: none !important;
      }

      main {
        margin: 0;
        padding: 0;
        width: 100%;
      }

      table {
        page-break-inside: avoid;
      }
    }
  </style>

  </head>
  <body>

    <?php include 'faculty-header.php'?>

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-dashboard.php">
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
              <a href="faculty-peer-evaluate.php" class="active">
                <i class="bi bi-circle"></i><span>Form</span>
              </a>
            </li>
            <li>
              <a href="faculty-peer-evaluatedpeer.php">
                <i class="bi bi-circle"></i><span>Evaluated Peer</span>
              </a>
            </li>
          </ul>
        </li><!-- End Evaluate Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-evaluatedsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Subject</span>
          </a>
        </li><!-- End Profile Nav -->

        <li class="nav-heading">Pages</li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-user-profile.php">
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
    
    <!-- Start Main Content -->
    <main id="main" class="main">
      <div class="pagetitle">
        <h1>Peer Evaluation Form</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="faculty-dashboard.php">Home</a></li>
            <li class="breadcrumb-item">Evaluate</li>
            <li class="breadcrumb-item active">Peer Evaluation</li>
          </ol>
        </nav>
      </div>

      <section class="section dashboard">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">
              <div class="card shadow-lg">
                <div class="card-body">
                  <h5 class="card-title text-center">Faculty-to-Faculty Evaluation</h5>

                  <form action="submit-peer-evaluation.php" method="POST">

                    <!-- Faculty Dropdown -->
                      <div class="row mb-3">
                      <!-- Evaluatee (Faculty) -->
                      <div class="col-md-6">
                          <div class="form-floating">
                          <select name="evaluatee_id" class="form-select text-capitalize" required>
                              <option value="" disabled selected>-- Select Faculty --</option>
                              <?php foreach ($faculty_list as $faculty): 
                              $fullName = htmlspecialchars($faculty['first_name'] . ' ' . $faculty['mid_name'] . ' ' . $faculty['last_name']);
                              ?>
                              <option value="<?= htmlspecialchars($faculty['idnumber']) ?>"><?= $fullName ?></option>
                              <?php endforeach; ?>
                          </select>
                          <label for="evaluatee_id">Faculty to Evaluate</label>
                          </div>
                      </div>

                      <!-- School Year -->
                      <div class="col-md-3">
                          <div class="form-floating">
                          <select name="school_year" id="school_year" class="form-select" required>
                              <option value="" disabled selected>-- School Year --</option>
                              <?php
                              $currentYear = date("Y");
                              for ($i = 0; $i < 5; $i++) {
                                  $sy = ($currentYear - $i) . '-' . ($currentYear - $i + 1);
                                  echo "<option value='$sy'>$sy</option>";
                              }
                              ?>
                          </select>
                          <label for="school_year">School Year</label>
                          </div>
                      </div>

                      <!-- Semester -->
                      <div class="col-md-3">
                          <div class="form-floating">
                          <select name="semester" id="semester" class="form-select" required>
                              <option value="" disabled selected>-- Semester --</option>
                              <option value="1st Semester">1st Semester</option>
                              <option value="2nd Semester">2nd Semester</option>
                          </select>
                          <label for="semester">Semester</label>
                          </div>
                      </div>
                      </div>


                    <!-- Evaluation Questions -->
                    <div class="table-responsive">
                      <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                          <tr>
                            <th class="text-start">Questions</th>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                              <th><?= $i ?></th>
                            <?php endfor; ?>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $questions = [
                            "Demonstrates professional behavior in the workplace.",
                            "Collaborates well with other faculty members.",
                            "Is open to feedback and professional improvement.",
                            "Shares knowledge and resources with colleagues.",
                            "Contributes positively to department goals.",
                            "Is punctual and consistent in work commitments."
                          ];
                          foreach ($questions as $index => $question):
                          ?>
                            <tr>
                              <td class="text-start"><?= $index + 1 ?>. <?= $question ?></td>
                              <?php for ($i = 1; $i <= 5; $i++): ?>
                                <td>
                                  <input type="radio" name="q<?= $index ?>" value="<?= $i ?>" required>
                                </td>
                              <?php endfor; ?>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>

                    <!-- Comment Box -->
                    <div class="mb-3">
                      <label for="comment" class="form-label">Additional Comments (optional)</label>
                      <textarea name="comment" class="form-control" rows="3" placeholder="Write your feedback here..."></textarea>
                    </div>

                    <input type="hidden" name="evaluator_id" value="<?= $evaluator_id ?>">

                    <div class="col-md-4 offset-md-4 mb-3">
                      <button type="submit" class="btn btn-success btn-block w-100">Submit Evaluation</button>
                    </div>

                  </form>

                </div>
              </div>
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

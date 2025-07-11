<?php
session_start();
include 'conn/conn.php';

// Verify login and role
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'faculty') {
    header("Location: pages-login.php");
    exit();
}

$faculty_id = $_SESSION['idnumber'];

// Fetch evaluations from the database
$stmt = $conn->prepare("SELECT * FROM evaluation WHERE faculty_id = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Faculty Evaluation Records</title>
  <?php include 'header.php'; ?>
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
        <!-- <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="faculty-peer-evaluate.php" >
                <i class="bi bi-circle"></i><span>Form</span>
              </a>
            </li>
            <li>
              <a href="faculty-peer-evaluatedpeer.php" >
                <i class="bi bi-circle"></i><span>Evaluated Peer</span>
              </a>
            </li>
          </ul>
        </li>End Evaluate Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="faculty-evaluatedsubject.php">
            <i class="bi bi-book-fill"></i>
            <span>Subject</span>
          </a>
        </li><!-- End Profile Nav -->

        <li class="nav-item">
          <a class="nav-link collapse" href="faculty-records.php">
            <i class="ri-record-circle-fill"></i>
            <span>Records</span>
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

<main id="main" class="main">
  <div class="pagetitle">
    <h1>My Evaluation Records</h1>
  </div>

  <section class="section">
    <div class="card">
      <div class="card-body table-responsive">
        <h5 class="card-title">Evaluation Summary</h5>

        <?php if ($result->num_rows > 0): ?>
          <table class="table table-bordered datatable">
            <thead>
              <tr>
                <th>Date Evaluated</th>
                <th>Subject Code</th>
                <th>Subject Title</th>
                <th>Student Section</th>
                <th>Academic Year</th>
                <th>Semester</th>
                <th>Total Score</th>
                <th>Rating (%)</th>
                <th>Comment</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= date("F j, Y", strtotime($row['created_at'])) ?></td>
                  <td><?= htmlspecialchars($row['subject_code']) ?></td>
                  <td><?= htmlspecialchars($row['subject_title']) ?></td>
                  <td><?= htmlspecialchars($row['student_section']) ?></td>
                  <td><?= htmlspecialchars($row['academic_year']) ?></td>
                  <td><?= htmlspecialchars($row['semester']) ?></td>
                  <td><?= $row['total_score'] !== null ? number_format($row['total_score'], 2) : 'N/A' ?></td>
                  <td><?= number_format($row['computed_rating'], 2) ?>%</td>
                  <td><?= htmlspecialchars($row['comment'] ?? '-') ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-info">No evaluations found for you yet.</div>
        <?php endif; ?>

      </div>
    </div>
  </section>
</main>

</body>
</html>

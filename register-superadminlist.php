<?php
session_start();
include 'conn/conn.php';

// ✅ Check if user is logged in and is registrar
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

// ✅ Fetch all superadmins
$query = "
  SELECT 
    idnumber,
    first_name,
    mid_name,
    last_name,
    faculty_rank,
    department,
    program,
    position,
    status
  FROM superadmin
  ORDER BY last_name ASC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <script src="sweetalert2/sweetalert2@11.js"></script>
</head>

<body>
  <?php include 'register-header.php'; ?>
  <?php include 'register-sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Super Admin List</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">List</li>
          <li class="breadcrumb-item active">Super Admin List</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body table-responsive">
              <h5 class="card-title">List of Super Admin Accounts</h5>

              <table id="superadminTable" class="table table-hover align-middle datatable">
                <thead class="table-light text-center">
                  <tr>
                    <th>ID Number</th>
                    <th>Full Name</th>
                    <th>Faculty Rank</th>
                    <th>Department</th>
                    <th>Program</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['idnumber']); ?></td>
                      <td class="text-capitalize">
                        <?= htmlspecialchars($row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name']); ?>
                      </td>
                      <td><?= htmlspecialchars($row['faculty_rank'] ?? '—'); ?></td>

                      <!-- Department (College) -->
                      <td>
                        <?php if (!empty($row['department'])): ?>
                          <?php foreach (explode(', ', $row['department']) as $dept): ?>
                            <span class="badge bg-primary mb-1"><?= htmlspecialchars($dept); ?></span><br>
                          <?php endforeach; ?>
                        <?php else: ?>
                          —
                        <?php endif; ?>
                      </td>

                      <!-- Program -->
                      <td>
                        <?php if (!empty($row['program'])): ?>
                          <?php foreach (explode(', ', $row['program']) as $prog): ?>
                            <span class="badge bg-info text-dark mb-1"><?= htmlspecialchars($prog); ?></span><br>
                          <?php endforeach; ?>
                        <?php else: ?>
                          —
                        <?php endif; ?>
                      </td>

                      <td><?= htmlspecialchars($row['position'] ?? '—'); ?></td>

                      <td>
                        <?php if ($row['status'] === 'active'): ?>
                          <span class="badge bg-success">Active</span>
                        <?php else: ?>
                          <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                      </td>

                      <td>
                        <a href="register-editsuperadmin.php?id=<?= urlencode($row['idnumber']); ?>"
                          class="btn btn-warning btn-sm">
                          <i class="bi bi-pencil-square"></i> Edit
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>

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

  <!-- Vendor JS Files -->
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <!-- ✅ Ensure DataTable initializes -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const table = document.querySelector('#superadminTable');
      if (table) new simpleDatatables.DataTable(table);
    });
  </script>

</body>

</html>
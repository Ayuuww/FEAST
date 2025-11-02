<?php
session_start();
include 'conn/conn.php';

// ✅ Check if user is logged in and is superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// ✅ Fetch all admins and combine their departments/programs
$query = "
  SELECT 
    a.idnumber,
    a.first_name,
    a.mid_name,
    a.last_name,
    a.faculty_rank,
    a.position,
    a.status,
    GROUP_CONCAT(DISTINCT ad.department_name ORDER BY ad.department_name SEPARATOR ', ') AS departments,
    GROUP_CONCAT(DISTINCT ad.program_name ORDER BY ad.program_name SEPARATOR ', ') AS programs
  FROM admin a
  LEFT JOIN admin_departments ad ON a.idnumber = ad.admin_idnumber
  WHERE a.role = 'admin'
  GROUP BY a.idnumber
  ORDER BY a.last_name ASC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <?php include 'superadmin-header.php'; ?>
  <?php include 'superadmin-sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Admin List</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
            <li class="breadcrumb-item">List</li>
            <li class="breadcrumb-item active">Admin List</li>
          </ol>
        </nav>
      </div>

      <!-- ✅ Optional Add Button -->
      <a href="superadmin-createadmin.php" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add Admin
      </a>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body table-responsive">
              <h5 class="card-title">List of Admin Accounts</h5>

              <table id="adminTable" class="table table-hover align-middle datatable">
                <thead class="table-light text-center">
                  <tr>
                    <th>ID Number</th>
                    <th>Full Name</th>
                    <th>Faculty Rank</th>
                    <th>Departments</th>
                    <th>Programs</th>
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

                      <!-- Departments -->
                      <td>
                        <?php if (!empty($row['departments'])): ?>
                          <?php foreach (explode(', ', $row['departments']) as $dept): ?>
                            <span class="badge bg-primary mb-1"><?= htmlspecialchars($dept); ?></span><br>
                          <?php endforeach; ?>
                        <?php else: ?>
                          —
                        <?php endif; ?>
                      </td>

                      <!-- Programs -->
                      <td>
                        <?php if (!empty($row['programs'])): ?>
                          <?php foreach (explode(', ', $row['programs']) as $prog): ?>
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
                        <a href="superadmin-editadmin.php?id=<?= urlencode($row['idnumber']); ?>"
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

  <!-- JS Files -->
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>

  <!-- ✅ Ensure DataTable initializes -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const table = document.querySelector('#adminTable');
      if (table) new simpleDatatables.DataTable(table);
    });
  </script>

</body>

</html>
<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// ✅ FIX: Modified query to LEFT JOIN admin_departments and GROUP_CONCAT the department names
$query = "
    SELECT 
        a.idnumber, 
        a.first_name, 
        a.mid_name, 
        a.last_name, 
        a.faculty_rank, 
        a.position, 
        a.status,
        GROUP_CONCAT(ad.department_name SEPARATOR ', ') AS departments
    FROM 
        admin a
    LEFT JOIN 
        admin_departments ad ON a.idnumber = ad.admin_idnumber
    GROUP BY
        a.idnumber
    ORDER BY
        a.last_name ASC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
</head>

<body>
  <?php include 'superadmin-header.php' ?>
  <?php include 'superadmin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Admin List</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Admin</li>
          <li class="breadcrumb-item active">List</li>
        </ol>
      </nav>
    </div>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body table-responsive">
              <h5 class="card-title">List of Admins</h5>
              <table class="table datatable">
                <thead>
                  <tr>
                    <th><b>ID Number</b></th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Department(s)</th>
                    <th>Academic Rank</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['idnumber']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['first_name']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['mid_name']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['last_name']); ?></td>

                      <td class="text-uppercase"><?= htmlspecialchars($row['departments'] ?? 'N/A'); ?></td>

                      <td class="text-capitalize"><?= htmlspecialchars($row['faculty_rank'] ?? 'N/A'); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['position']); ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row['status']); ?></td>
                      <td>
                        <a href="superadmin-editadmin.php?id=<?= htmlspecialchars($row['idnumber']); ?>" class="btn btn-warning btn-sm">Edit</a>
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

  <?php include 'footer.php' ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
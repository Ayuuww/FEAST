<?php
session_start();
include 'conn/conn.php'; // Connection to the database

// Check if the user is logged in and is an admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: pages-login.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// ✅ Get admin's position
$pos_stmt = $conn->prepare("SELECT position FROM admin WHERE idnumber = ? LIMIT 1");
$pos_stmt->bind_param("s", $admin_id);
$pos_stmt->execute();
$pos_res = $pos_stmt->get_result();
$admin_position = ($pos_res->fetch_assoc())['position'] ?? '';
$pos_stmt->close();

// ✅ Get all college assigned to this admin
$dept_stmt = $conn->prepare("SELECT college_name FROM admin_college WHERE admin_idnumber = ?");
$dept_stmt->bind_param("s", $admin_id);
$dept_stmt->execute();
$dept_res = $dept_stmt->get_result();
$college = [];
while ($row = $dept_res->fetch_assoc()) {
  $college[] = $row['college_name'];
}
$dept_stmt->close();

// ✅ Restrict allowed positions (only allow Dean/Chair/Program Chair)
$allowed_positions = ['Dean', 'Chair Person', 'Program Chair', 'Director']; // adjust spelling to match DB values
if (!in_array($admin_position, $allowed_positions)) {
  $_SESSION['access_denied'] = "Access denied. Your position ($admin_position) is not allowed to view the subject list.";
  header("Location: admin-dashboard.php");
  exit();
}


// --- ✅ NEW DYNAMIC QUERY ---

$params = []; // Array to hold all parameters
$types = "";  // String for all types

$sub_q = "
    SELECT subject.*,
           COALESCE(f.first_name, a.first_name) AS first_name,
           COALESCE(f.mid_name, a.mid_name) AS mid_name,
           COALESCE(f.last_name, a.last_name) AS last_name,
           CASE
               WHEN subject.faculty_id IS NOT NULL THEN 'Faculty'
               WHEN subject.admin_id IS NOT NULL THEN 'Admin'
               ELSE 'Unknown'
           END AS handler_role
    FROM subject
    LEFT JOIN faculty f ON subject.faculty_id = f.idnumber
    LEFT JOIN admin a ON subject.admin_id = a.idnumber
";

$where_clauses = [];

// 1. Add college clause ONLY if college exist
if (!empty($college)) {
  $placeholders = implode(',', array_fill(0, count($college), '?'));
  $where_clauses[] = "subject.college IN ($placeholders)";
  $params = array_merge($params, $college); // Add all college to params
  $types .= str_repeat('s', count($college)); // Add 's' for each dept
}

// 2. Add the "created by me" clause
$where_clauses[] = "subject.admin_id = ?";
$params[] = $admin_id; // Add admin_id to params
$types .= 's'; // Add 's' for admin_id

// 3. Combine the WHERE clauses with OR
if (!empty($where_clauses)) {
  $sub_q .= " WHERE " . implode(' OR ', $where_clauses);
}

$sub_q .= " ORDER BY subject.code ASC";

// 4. Prepare and execute the statement
$sub_stmt = $conn->prepare($sub_q);

if (!empty($params)) {
  $sub_stmt->bind_param($types, ...$params);
}

$sub_stmt->execute();
$result = $sub_stmt->get_result();

// --- END NEW DYNAMIC QUERY ---
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Head -->
  <?php include 'head.php' ?>
  <!-- End Head -->

</head>

<body>

  <?php include 'admin-header.php' ?>

  <!-- ======= Sidebar ======= -->
  <?php include 'admin-sidebar.php' ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>List of Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Subject</li>
          <li class="breadcrumb-item active">List</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($_SESSION['msg'])) : ?>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          Swal.fire({
            icon: '<?php echo $_SESSION['msg_type'] ?? 'info'; ?>',
            title: '<?php echo addslashes($_SESSION['msg']); ?>',
            showConfirmButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
          });
        });
      </script>
      <?php
      unset($_SESSION['msg'], $_SESSION['msg_type']);
      ?>
    <?php endif; ?>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body table-responsive">
              <h5 class="card-title">Datatables</h5>

              <table class="table datatable">
                <thead>
                  <tr>
                    <th><b>Subject Code</b></th>
                    <th>Descriptive Title</th>
                    <th>Faculty Name</th>
                    <th>Assigned College</th>
                    <th>Assigned Program</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                      <td class="text-uppercase"><?php echo htmlspecialchars($row['code']); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['title']); ?></td>
                      <td class="text-capitalize">
                        <?php echo htmlspecialchars(trim($row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name'])); ?>
                      </td>

                      <td class="text-uppercase"><?php echo htmlspecialchars($row['college']); ?></td>
                      <td class="text-capitalize"><?php echo htmlspecialchars($row['program']); ?></td>
                      <td>
                        <form method="post" class="delete-form" action="deletesubject.php">
                          <input type="hidden" name="idnumber" value="<?php echo $row['idnumber']; ?>">
                          <button type="button" class="btn btn-danger btn-sm delete-btn" data-subject="<?php echo htmlspecialchars($row['title']); ?>">Delete</button>
                        </form>
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

  <script src="vendors/apexcharts/apexcharts.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendors/chart.js/chart.umd.js"></script>
  <script src="vendors/echarts/echarts.min.js"></script>
  <script src="vendors/quill/quill.js"></script>
  <script src="vendors/simple-datatables/simple-datatables.js"></script>
  <script src="vendors/tinymce/tinymce.min.js"></script>
  <script src="vendors/php-email-form/validate.js"></script>

  <script src="assets/js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          const form = this.closest('form');
          const subjectName = this.getAttribute('data-subject');

          Swal.fire({
            title: `Delete "${subjectName}"?`,
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#697077ff',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              form.submit();
            }
          });
        });
      });
    });
  </script>

</body>

</html>
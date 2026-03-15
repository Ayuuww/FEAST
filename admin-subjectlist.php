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


// --- NEW DYNAMIC QUERY ---

$params = [];
$types  = "";

// Base SELECT
$sub_q = "
    SELECT subject.*,
           COALESCE(f.first_name, a.first_name) AS first_name,
           COALESCE(f.mid_name,   a.mid_name)   AS mid_name,
           COALESCE(f.last_name,  a.last_name)  AS last_name,
           CASE
               WHEN subject.faculty_id IS NOT NULL THEN 'Faculty'
               WHEN subject.admin_id   IS NOT NULL THEN 'Admin'
               ELSE 'Unknown'
           END AS handler_role,
           subject.admin_id AS creator_admin_id
    FROM subject
    LEFT JOIN faculty f ON subject.faculty_id = f.idnumber
    LEFT JOIN admin a    ON subject.admin_id  = a.idnumber
    WHERE 1
";

$conditions = [];

// 1. Filter by College Name (Matches the 'college' column in 'subject' table)
if (!empty($college)) {
  $placeholders = implode(',', array_fill(0, count($college), '?'));
  $conditions[] = "subject.college IN ($placeholders)";
  $params = array_merge($params, $college);
  $types .= str_repeat('s', count($college));
}

// 2. Filter by Personal Creation (If they created it, even if it's a different college)
$conditions[] = "subject.admin_id = ?";
$params[] = $admin_id;
$types .= "s";

// Combine conditions with OR so they see subjects in their college AND subjects they created
if (!empty($conditions)) {
  $sub_q .= " AND (" . implode(" OR ", $conditions) . ")";
}

$sub_q .= " ORDER BY subject.code ASC";

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
  <?php include 'head.php' ?>
</head>

<body>

  <?php include 'admin-header.php' ?>

  <?php include 'admin-sidebar.php' ?>
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
            confirmButtonColor: '#198754',
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
              <h5 class="card-title">Subject Management</h5>

              <table class="table datatable align-middle">
                <thead>
                  <tr>
                    <th>Subject Code</th>
                    <th>Descriptive Title</th>
                    <th>Faculty Name</th>
                    <th>Assigned College</th>
                    <th>Assigned Program</th>
                    <th>Creator ID</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                      <td class="text-uppercase fw-bold">
                        <?= htmlspecialchars($row['code']) ?>
                      </td>

                      <td class="text-capitalize">
                        <?= htmlspecialchars($row['title']) ?>
                      </td>

                      <td class="text-capitalize">
                        <?= htmlspecialchars(trim($row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name'])) ?>
                      </td>

                      <td class="text-uppercase">
                        <?= htmlspecialchars($row['college']) ?>
                      </td>

                      <td class="text-capitalize">
                        <?= htmlspecialchars($row['program']) ?>
                      </td>

                      <td class="text-uppercase">
                        <?= $row['creator_admin_id'] ? htmlspecialchars($row['creator_admin_id']) : 'N/A' ?>
                      </td>

                      <td>
                        <div class="d-flex justify-content-center gap-2">
                          <button type="button" class="btn btn-warning btn-sm text-white edit-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editSubjectModal"
                            data-id="<?= $row['idnumber'] ?>"
                            data-code="<?= htmlspecialchars($row['code']) ?>"
                            data-title="<?= htmlspecialchars($row['title']) ?>">
                            <i class="bi bi-pencil-square"></i>
                          </button>

                          <form method="post" class="delete-form m-0" action="deletesubject.php">
                            <input type="hidden" name="idnumber" value="<?= $row['idnumber'] ?>">
                            <button type="button"
                              class="btn btn-danger btn-sm delete-btn"
                              data-subject="<?= htmlspecialchars($row['title']) ?>">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </div>
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

  <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="editSubjectModalLabel">Edit Subject Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="editsubject.php">
          <div class="modal-body">
            <input type="hidden" name="idnumber" id="edit_idnumber">

            <div class="form-floating mb-3">
              <input type="text" class="form-control" name="code" id="edit_code" placeholder="Subject Code" required>
              <label for="edit_code">Subject Code</label>
            </div>

            <div class="form-floating mb-2">
              <input type="text" class="form-control" name="title" id="edit_title" placeholder="Descriptive Title" required>
              <label for="edit_title">Descriptive Title</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="update_subject" class="btn btn-success">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
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
      // 1. Pass data to Edit Modal
      const editButtons = document.querySelectorAll('.edit-btn');
      editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const code = this.getAttribute('data-code');
          const title = this.getAttribute('data-title');

          document.getElementById('edit_idnumber').value = id;
          document.getElementById('edit_code').value = code;
          document.getElementById('edit_title').value = title;
        });
      });

      // 2. SweetAlert2 Delete Confirmation
      document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          const form = this.closest('form');
          const subjectName = this.getAttribute('data-subject');

          Swal.fire({
            title: `Delete "${subjectName}"?`,
            text: "This action cannot be undone. All assigned students to this subject might be affected.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
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
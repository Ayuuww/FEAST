<?php
session_start();
include 'conn/conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Restrict access to Admin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php");
  exit();
}

$admin_id = $_SESSION['idnumber'];

// ✅ --- Handle Update/Insert Requests ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
  $c_name = $_POST['edit_college_name'];
  $website = trim($_POST['edit_website']);
  $phone = trim($_POST['edit_phone']);
  $email = trim($_POST['edit_email']);

  // Security Check: Verify this admin actually owns this college
  $auth_check = $conn->prepare("SELECT 1 FROM admin_college WHERE admin_idnumber = ? AND college_name = ?");
  $auth_check->bind_param("ss", $admin_id, $c_name);
  $auth_check->execute();
  $is_authorized = $auth_check->get_result()->num_rows > 0;
  $auth_check->close();

  if ($is_authorized) {
    // Check if the record already exists in college_info for this college
    $ci_check = $conn->prepare("SELECT id FROM college_info WHERE college_name = ?");
    $ci_check->bind_param("s", $c_name);
    $ci_check->execute();
    $res = $ci_check->get_result();

    if ($res->num_rows > 0) {
      // Record exists -> UPDATE
      $row = $res->fetch_assoc();
      $update = $conn->prepare("UPDATE college_info SET website = ?, phone = ?, email = ? WHERE id = ?");
      $update->bind_param("sssi", $website, $phone, $email, $row['id']);
      $update->execute();
    } else {
      // Record doesn't exist yet -> INSERT
      $insert = $conn->prepare("INSERT INTO college_info (college_name, website, phone, email) VALUES (?, ?, ?, ?)");
      $insert->bind_param("ssss", $c_name, $website, $phone, $email);
      $insert->execute();
    }

    $_SESSION['alert'] = ['type' => 'success', 'title' => 'Saved!', 'text' => 'College contact details updated successfully.'];
  } else {
    // Block unauthorized editing attempts
    $_SESSION['alert'] = ['type' => 'error', 'title' => 'Unauthorized!', 'text' => 'You do not have permission to modify this college.'];
  }

  header("Location: admin-college-info.php");
  exit();
}

// --- ✅ FETCH ONLY THE ADMIN'S ASSIGNED COLLEGES ---
// Using a subquery to get distinct colleges for the admin, then joining info
$query = "
  SELECT 
    ac.college_name, 
    MAX(ci.website) as website, 
    MAX(ci.phone) as phone, 
    MAX(ci.email) as email 
  FROM (SELECT DISTINCT college_name FROM admin_college WHERE admin_idnumber = ?) ac
  LEFT JOIN college_info ci ON ac.college_name = ci.college_name
  GROUP BY ac.college_name
  ORDER BY ac.college_name ASC
";
$stmt_list = $conn->prepare($query);
$stmt_list->bind_param("s", $admin_id);
$stmt_list->execute();
$list_result = $stmt_list->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    /* Professional UI Customizations */
    .program-card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      transition: all 0.3s ease;
      border-top: 4px solid #198754;
      background-color: #ffffff;
    }

    .program-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.12);
    }

    .card-header-custom {
      background-color: transparent;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      padding: 1.8rem 1.5rem 1.2rem 1.5rem;
      text-align: center;
    }

    .icon-box {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background-color: rgba(25, 135, 84, 0.1);
      color: #198754;
      font-size: 1.1rem;
      margin-right: 14px;
      flex-shrink: 0;
    }

    .contact-text {
      word-break: break-word;
      font-size: 0.95rem;
      color: #495057;
      line-height: 1.4;
    }

    .contact-text a {
      color: #0d6efd;
      text-decoration: none;
      font-weight: 500;
    }

    .contact-text a:hover {
      text-decoration: underline;
    }

    .empty-text {
      color: #adb5bd;
      font-style: italic;
      font-size: 0.9rem;
    }

    .edit-btn-custom {
      border-radius: 8px;
      font-weight: 600;
      padding: 0.6rem;
      letter-spacing: 0.3px;
    }
  </style>
</head>

<body>

  <?php include 'admin-header.php' ?>
  <?php include 'admin-sidebar.php' ?>

  <main id="main" class="main">
    <div class="pagetitle mb-4">
      <h1>My College Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Contact Info</li>
        </ol>
      </nav>
      <p class="text-muted mt-2" style="font-size: 0.95rem;">
        Manage the public contact details for your assigned college. This information ensures students and faculty can easily reach the correct department offices.
      </p>
    </div>

    <section class="section">
      <div class="row justify-content-center">

        <?php if ($list_result->num_rows > 0): ?>
          <?php while ($row = $list_result->fetch_assoc()): ?>
            <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch">
              <div class="card program-card w-100">

                <div class="card-header-custom">
                  <div class="mb-2">
                    <i class="bi bi-building text-success" style="font-size: 2rem; opacity: 0.8;"></i>
                  </div>
                  <h5 class="fw-bold text-dark mb-0 lh-base">
                    <?= htmlspecialchars($row['college_name']) ?>
                  </h5>
                </div>

                <div class="card-body p-4">
                  <div class="d-flex align-items-center mb-3">
                    <div class="icon-box"><i class="bi bi-globe2"></i></div>
                    <div class="contact-text">
                      <?php if (!empty($row['website'])): ?>
                        <a href="<?= htmlspecialchars(strpos($row['website'], 'http') !== 0 ? 'https://' . $row['website'] : $row['website']) ?>" target="_blank">
                          <?= htmlspecialchars($row['website']) ?>
                        </a>
                      <?php else: ?>
                        <span class="empty-text">No website provided</span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="d-flex align-items-center mb-3">
                    <div class="icon-box"><i class="bi bi-telephone"></i></div>
                    <div class="contact-text">
                      <?php if (!empty($row['phone'])): ?>
                        <span class="text-dark fw-medium"><?= htmlspecialchars($row['phone']) ?></span>
                      <?php else: ?>
                        <span class="empty-text">No phone provided</span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="d-flex align-items-center">
                    <div class="icon-box"><i class="bi bi-envelope"></i></div>
                    <div class="contact-text">
                      <?php if (!empty($row['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-dark fw-medium">
                          <?= htmlspecialchars($row['email']) ?>
                        </a>
                      <?php else: ?>
                        <span class="empty-text">No email provided</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class="card-footer bg-transparent border-0 p-4 pt-0">
                  <button type="button" class="btn btn-outline-success w-100 edit-btn-custom edit-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#editInfoModal"
                    data-college="<?= htmlspecialchars($row['college_name']) ?>"
                    data-website="<?= htmlspecialchars($row['website'] ?? '') ?>"
                    data-phone="<?= htmlspecialchars($row['phone'] ?? '') ?>"
                    data-email="<?= htmlspecialchars($row['email'] ?? '') ?>">
                    <i class="bi bi-pencil-square me-2"></i> Update Details
                  </button>
                </div>

              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-center p-5">
              <div class="card-body">
                <div class="mb-3">
                  <i class="bi bi-inboxes text-muted opacity-50" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold text-dark">No College Assigned</h4>
                <p class="text-muted mb-0 mx-auto" style="max-width: 600px;">
                  You currently do not have any college assigned to your Admin account. Please contact the Superadmin or Registrar to assign your jurisdiction.
                </p>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </section>
  </main>

  <div class="modal fade" id="editInfoModal" tabindex="-1" aria-labelledby="editInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">

        <div class="modal-header bg-light border-bottom-0 pb-0">
          <h5 class="modal-title fw-bold text-dark" id="editInfoModalLabel">Edit Contact Information</h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form method="POST" action="admin-college-info.php">
          <div class="modal-body pt-3 pb-4 px-4">

            <input type="hidden" name="update_info" value="1">
            <input type="hidden" name="edit_college_name" id="edit_college_name">

            <div class="alert alert-secondary border-0 d-flex align-items-center mb-4 p-3 rounded-3" role="alert">
              <i class="bi bi-info-circle-fill text-success fs-4 me-3"></i>
              <div>
                <strong class="d-block text-dark mb-0" id="display_college" style="font-size: 0.95rem;"></strong>
              </div>
            </div>

            <div class="form-floating mb-3">
              <input type="text" name="edit_website" id="edit_website" class="form-control" placeholder="Website">
              <label for="edit_website"><i class="bi bi-globe me-2 text-muted"></i> Website URL</label>
            </div>

            <div class="form-floating mb-3">
              <input type="text" name="edit_phone" id="edit_phone" class="form-control" placeholder="Phone Number">
              <label for="edit_phone"><i class="bi bi-telephone me-2 text-muted"></i> Contact Number</label>
            </div>

            <div class="form-floating">
              <input type="email" name="edit_email" id="edit_email" class="form-control" placeholder="Email Address">
              <label for="edit_email"><i class="bi bi-envelope me-2 text-muted"></i> Official Email</label>
            </div>

          </div>

          <div class="modal-footer bg-light border-top-0 px-4 py-3">
            <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success px-4 fw-semibold rounded-3 shadow-sm">Save Changes</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <?php include 'footer.php' ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // General session alert (for errors/success)
    <?php if (isset($_SESSION['alert'])): ?>
      Swal.fire({
        icon: '<?= $_SESSION['alert']['type'] ?>',
        title: '<?= $_SESSION['alert']['title'] ?>',
        text: '<?= $_SESSION['alert']['text'] ?>',
        showConfirmButton: false,
        timer: 2500,
        customClass: {
          popup: 'rounded-4 shadow'
        }
      });
      <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>

    // Populate Modal fields on Edit button click
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const college = this.getAttribute('data-college');

        // Hidden Inputs for backend processing
        document.getElementById('edit_college_name').value = college;

        // Visual display tags in the modal
        document.getElementById('display_college').innerText = college;

        // Form Inputs (Pre-fill existing data)
        document.getElementById('edit_website').value = this.getAttribute('data-website');
        document.getElementById('edit_phone').value = this.getAttribute('data-phone');
        document.getElementById('edit_email').value = this.getAttribute('data-email');
      });
    });
  </script>

</body>

</html>
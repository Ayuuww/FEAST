<?php
session_start();
include 'conn/conn.php';

// Check if user is logged in and is a superadmin
if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'superadmin') {
  header("Location: pages-login.php");
  exit();
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Add New Rating Scale
  if (isset($_POST['add_scale'])) {
    $scale_value = (int)$_POST['scale_value'];
    $qualitative = trim($_POST['qualitative_description']);
    $operational = trim($_POST['operational_definition']);

    // Check if the scale value already exists to prevent duplicates
    $check_stmt = $conn->prepare("SELECT id FROM evaluation_rating_scales WHERE scale_value = ?");
    $check_stmt->bind_param("i", $scale_value);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
      $_SESSION['error_message'] = "A rating scale with the value '$scale_value' already exists.";
    } else {
      $stmt = $conn->prepare("INSERT INTO evaluation_rating_scales (scale_value, qualitative_description, operational_definition) VALUES (?, ?, ?)");
      $stmt->bind_param("iss", $scale_value, $qualitative, $operational);

      if ($stmt->execute()) {
        $_SESSION['success_message'] = "Rating scale added successfully!";
      } else {
        $_SESSION['error_message'] = "Failed to add rating scale.";
      }
      $stmt->close();
    }
    $check_stmt->close();

    header("Location: superadmin-rating-scale.php");
    exit();
  }

  // Edit Existing Rating Scale
  if (isset($_POST['edit_scale'])) {
    $scale_id = (int)$_POST['edit_scale_id'];
    $scale_value = (int)$_POST['edit_scale_value'];
    $qualitative = trim($_POST['edit_qualitative']);
    $operational = trim($_POST['edit_operational']);

    $stmt = $conn->prepare("UPDATE evaluation_rating_scales SET scale_value = ?, qualitative_description = ?, operational_definition = ? WHERE id = ?");
    $stmt->bind_param("issi", $scale_value, $qualitative, $operational, $scale_id);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "Rating scale updated successfully!";
    } else {
      $_SESSION['error_message'] = "Failed to update rating scale.";
    }
    $stmt->close();
    header("Location: superadmin-rating-scale.php");
    exit();
  }

  // Delete Rating Scale
  if (isset($_POST['delete_scale'])) {
    $scale_id = (int)$_POST['delete_scale_id'];

    $stmt = $conn->prepare("DELETE FROM evaluation_rating_scales WHERE id = ?");
    $stmt->bind_param("i", $scale_id);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "Rating scale deleted!";
    } else {
      $_SESSION['error_message'] = "Failed to delete rating scale.";
    }
    $stmt->close();
    header("Location: superadmin-rating-scale.php");
    exit();
  }
}

// Fetch Rating Scales
$rating_scales = [];
$scale_query = $conn->query("SELECT * FROM evaluation_rating_scales ORDER BY scale_value DESC");
if ($scale_query) {
  while ($row = $scale_query->fetch_assoc()) {
    $rating_scales[] = $row;
  }
}
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

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Manage Rating Scale</h1>
        <p class="text-muted small">Configure the evaluation grading rubric</p>
      </div>
      <div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addScaleModal">
          <i class="bi bi-plus-circle"></i> Add Scale Level
        </button>
      </div>
    </div>

    <section class="section dashboard">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card shadow-sm border-left-secondary">
            <div class="card-header bg-white">
              <h5 class="card-title m-0 p-0 text-secondary"><strong>Active Grading Rubric</strong></h5>
            </div>
            <div class="card-body mt-3">

              <?php if (empty($rating_scales)): ?>
                <div class="alert alert-warning text-center">No rating scales found. Evaluations cannot proceed without a rubric. Please add scale levels.</div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                      <tr>
                        <th width="10%">Scale Value</th>
                        <th width="25%">Qualitative Description</th>
                        <th width="50%">Operational Definition</th>
                        <th width="15%">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($rating_scales as $scale): ?>
                        <tr>
                          <td class="text-center fs-5 fw-bold text-secondary"><?= htmlspecialchars($scale['scale_value']) ?></td>
                          <td class="fw-semibold"><?= htmlspecialchars($scale['qualitative_description']) ?></td>
                          <td class="small text-muted"><?= htmlspecialchars($scale['operational_definition']) ?></td>
                          <td class="text-center">
                            <button class="btn btn-sm btn-outline-success mb-1" data-bs-toggle="modal" data-bs-target="#editScaleModal"
                              data-id="<?= $scale['id'] ?>"
                              data-val="<?= htmlspecialchars($scale['scale_value']) ?>"
                              data-qual="<?= htmlspecialchars($scale['qualitative_description']) ?>"
                              data-oper="<?= htmlspecialchars($scale['operational_definition']) ?>"
                              title="Edit Scale">
                              <i class="bi bi-pencil"></i>
                            </button>

                            <form method="POST" action="superadmin-rating-scale.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this rating scale level? This may affect how the table is rendered.');">
                              <input type="hidden" name="delete_scale_id" value="<?= $scale['id'] ?>">
                              <button type="submit" name="delete_scale" class="btn btn-sm btn-outline-danger mb-1" title="Delete Scale">
                                <i class="bi bi-trash"></i>
                              </button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <div class="modal fade" id="addScaleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5>Add Rating Scale Level</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Numerical Scale Value</label>
              <input type="number" name="scale_value" class="form-control" required min="1" max="100" placeholder="e.g., 5">
            </div>
            <div class="mb-3">
              <label class="form-label">Qualitative Description</label>
              <input type="text" name="qualitative_description" class="form-control" required placeholder="e.g., Always manifested">
            </div>
            <div class="mb-3">
              <label class="form-label">Operational Definition</label>
              <textarea name="operational_definition" class="form-control" rows="4" required placeholder="Describe what this score means in practice..."></textarea>
            </div>
          </div>
          <div class="modal-footer"><button type="submit" name="add_scale" class="btn btn-secondary">Save Scale Level</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editScaleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5>Edit Rating Scale Level</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="edit_scale_id" id="edit_modal_scale_id">
            <div class="mb-3">
              <label class="form-label">Numerical Scale Value</label>
              <input type="number" name="edit_scale_value" id="edit_modal_scale_value" class="form-control" required min="1" max="100">
            </div>
            <div class="mb-3">
              <label class="form-label">Qualitative Description</label>
              <input type="text" name="edit_qualitative" id="edit_modal_qualitative" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Operational Definition</label>
              <textarea name="edit_operational" id="edit_modal_operational" class="form-control" rows="4" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_scale" class="btn btn-success">Update Scale Level</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'footer.php' ?>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Logic for passing data into the Edit Scale Modal
      var editScaleModal = document.getElementById('editScaleModal');
      if (editScaleModal) {
        editScaleModal.addEventListener('show.bs.modal', function(event) {
          var button = event.relatedTarget;
          document.getElementById('edit_modal_scale_id').value = button.getAttribute('data-id');
          document.getElementById('edit_modal_scale_value').value = button.getAttribute('data-val');
          document.getElementById('edit_modal_qualitative').value = button.getAttribute('data-qual');
          document.getElementById('edit_modal_operational').value = button.getAttribute('data-oper');
        });
      }
    });
  </script>

  <?php if (isset($_SESSION['success_message'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Done',
        text: '<?= $_SESSION['success_message'] ?>',
        timer: 1500,
        showConfirmButton: false
      });
    </script>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_message'])): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '<?= $_SESSION['error_message'] ?>'
      });
    </script>
    <?php unset($_SESSION['error_message']); ?>
  <?php endif; ?>

</body>

</html>
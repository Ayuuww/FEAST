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

  // 1. ADD RUBRIC
  if (isset($_POST['add_template'])) {
    $type = $_POST['template_type']; // 'set' or 'sef'
    $name = trim($_POST['template_name']);
    $desc = trim($_POST['template_description']);
    $table = ($type === 'sef') ? 'sef_templates' : 'set_templates';

    $stmt = $conn->prepare("INSERT INTO $table (template_name, description, is_equipped) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $name, $desc);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = strtoupper($type) . " Rubric created successfully!";
    } else {
      $_SESSION['error_message'] = "Failed to create rubric.";
    }
    $stmt->close();
    header("Location: superadmin-templates.php");
    exit();
  }

  // 2. EDIT RUBRIC
  if (isset($_POST['edit_template'])) {
    $type = $_POST['edit_template_type'];
    $id = (int)$_POST['edit_template_id'];
    $name = trim($_POST['edit_template_name']);
    $desc = trim($_POST['edit_template_description']);
    $table = ($type === 'sef') ? 'sef_templates' : 'set_templates';

    $stmt = $conn->prepare("UPDATE $table SET template_name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $desc, $id);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "Rubric updated successfully!";
    } else {
      $_SESSION['error_message'] = "Failed to update rubric.";
    }
    $stmt->close();
    header("Location: superadmin-templates.php");
    exit();
  }

  // 3. SET ACTIVE RUBRIC (The Swap)
  if (isset($_POST['equip_template'])) {
    $type = $_POST['template_type'];
    $id = (int)$_POST['template_id'];
    $table = ($type === 'sef') ? 'sef_templates' : 'set_templates';

    // Step A: Deactivate all rubrics in this category
    $conn->query("UPDATE $table SET is_equipped = 0");

    // Step B: Activate the selected rubric
    $stmt = $conn->prepare("UPDATE $table SET is_equipped = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = strtoupper($type) . " Rubric successfully set as Active!";
    } else {
      $_SESSION['error_message'] = "Failed to activate rubric.";
    }
    $stmt->close();
    header("Location: superadmin-templates.php");
    exit();
  }

  // 4. DELETE RUBRIC
  if (isset($_POST['delete_template'])) {
    $type = $_POST['template_type'];
    $id = (int)$_POST['template_id'];
    $table = ($type === 'sef') ? 'sef_templates' : 'set_templates';

    // Prevent deleting an active rubric
    $check = $conn->query("SELECT is_equipped FROM $table WHERE id = $id")->fetch_assoc();
    if ($check['is_equipped'] == 1) {
      $_SESSION['error_message'] = "You cannot delete the currently active rubric!";
    } else {
      $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
      $stmt->bind_param("i", $id);
      if ($stmt->execute()) {
        $_SESSION['success_message'] = "Rubric deleted permanently.";
      } else {
        $_SESSION['error_message'] = "Failed to delete rubric.";
      }
      $stmt->close();
    }
    header("Location: superadmin-templates.php");
    exit();
  }
}

// Fetch SET Templates
$set_templates = [];
$set_res = $conn->query("SELECT * FROM set_templates ORDER BY is_equipped DESC, id ASC");
if ($set_res) {
  while ($row = $set_res->fetch_assoc()) {
    $set_templates[] = $row;
  }
}

// Fetch SEF Templates
$sef_templates = [];
$sef_res = $conn->query("SELECT * FROM sef_templates ORDER BY is_equipped DESC, id ASC");
if ($sef_res) {
  while ($row = $sef_res->fetch_assoc()) {
    $sef_templates[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php' ?>
  <style>
    .rubric-active-row {
      border-left: 5px solid #198754 !important;
      background-color: #f8fff9 !important;
    }

    .rubric-standby-row {
      border-left: 5px solid #6c757d !important;
    }

    .nav-tabs-bordered .nav-link.active {
      background-color: #f6f9ff;
      border-bottom: 2px solid #012970;
    }
  </style>
</head>

<body>

  <?php include 'superadmin-header.php' ?>
  <?php include 'superadmin-sidebar.php' ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Evaluation Rubric Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="superadmin-dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Evaluation</li>
          <li class="breadcrumb-item active">Rubric Versions</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-12">

          <div class="card shadow-sm">
            <div class="card-body pt-3">

              <ul class="nav nav-tabs nav-tabs-bordered d-flex" id="rubricTabs" role="tablist">
                <li class="nav-item flex-fill" role="presentation">
                  <button class="nav-link w-100 active fw-bold text-primary" id="set-tab" data-bs-toggle="tab" data-bs-target="#set-rubrics" type="button" role="tab">
                    <i class="bi bi-people-fill me-1"></i> Student Evaluations (SET)
                  </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                  <button class="nav-link w-100 fw-bold text-secondary" id="sef-tab" data-bs-toggle="tab" data-bs-target="#sef-rubrics" type="button" role="tab">
                    <i class="bi bi-person-badge-fill me-1"></i> Supervisor Evaluations (SEF)
                  </button>
                </li>
              </ul>

              <div class="tab-content pt-4" id="rubricTabsContent">

                <div class="tab-pane fade show active" id="set-rubrics" role="tabpanel">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                      <h5 class="m-0 fw-bold text-dark">SET Rubric Versions</h5>
                      <p class="text-muted small m-0">Manage the questionnaire versions used by students.</p>
                    </div>
                    <button class="btn btn-primary btn-sm btn-add-template" data-bs-toggle="modal" data-bs-target="#addTemplateModal" data-type="set">
                      <i class="bi bi-folder-plus me-1"></i> Create New Version
                    </button>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-hover align-middle shadow-sm border">
                      <thead class="table-light">
                        <tr>
                          <th width="25%" class="ps-4">Rubric Name</th>
                          <th width="40%">Academic Description</th>
                          <th width="15%" class="text-center">System Status</th>
                          <th width="20%" class="text-center">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($set_templates as $tpl): ?>
                          <tr class="<?= $tpl['is_equipped'] ? 'rubric-active-row' : 'rubric-standby-row' ?>">
                            <td class="ps-4 fw-bold text-dark">
                              <i class="bi <?= $tpl['is_equipped'] ? 'bi-journal-check text-success' : 'bi-journal-text text-muted' ?> me-2"></i>
                              <?= htmlspecialchars($tpl['template_name']) ?>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars($tpl['description']) ?></td>
                            <td class="text-center">
                              <?php if ($tpl['is_equipped']): ?>
                                <span class="badge bg-success px-3 py-2 shadow-sm"><i class="bi bi-check-circle-fill me-1"></i> ACTIVE RUBRIC</span>
                              <?php else: ?>
                                <span class="badge bg-secondary px-3 py-2 opacity-75">STANDBY</span>
                              <?php endif; ?>
                            </td>
                            <td class="text-center">
                              <?php if (!$tpl['is_equipped']): ?>
                                <form method="POST" class="d-inline swal-equip-form" data-user="students">
                                  <input type="hidden" name="equip_template" value="1"> <input type="hidden" name="template_type" value="set">
                                  <input type="hidden" name="template_id" value="<?= $tpl['id'] ?>">
                                  <button type="submit" class="btn btn-sm btn-success shadow-sm" title="Set as Active Rubric">
                                    <i class="bi bi-toggle-on"></i> Set Active
                                  </button>
                                </form>
                              <?php else: ?>
                                <button class="btn btn-sm btn-outline-success disabled" title="Currently Active"><i class="bi bi-check2-all"></i> Active</button>
                              <?php endif; ?>

                              <button class="btn btn-sm btn-outline-primary btn-edit-template ms-1" data-bs-toggle="modal" data-bs-target="#editTemplateModal"
                                data-id="<?= $tpl['id'] ?>" data-type="set" data-name="<?= htmlspecialchars($tpl['template_name']) ?>" data-desc="<?= htmlspecialchars($tpl['description']) ?>" title="Edit Details">
                                <i class="bi bi-pencil"></i>
                              </button>

                              <?php if (!$tpl['is_equipped']): ?>
                                <form method="POST" class="d-inline ms-1 swal-delete-form">
                                  <input type="hidden" name="delete_template" value="1"> <input type="hidden" name="template_type" value="set">
                                  <input type="hidden" name="template_id" value="<?= $tpl['id'] ?>">
                                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Version"><i class="bi bi-trash"></i></button>
                                </form>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="tab-pane fade" id="sef-rubrics" role="tabpanel">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                      <h5 class="m-0 fw-bold text-dark">SEF Rubric Versions</h5>
                      <p class="text-muted small m-0">Manage the questionnaire versions used by Supervisors/Admins.</p>
                    </div>
                    <button class="btn btn-primary btn-sm btn-add-template" data-bs-toggle="modal" data-bs-target="#addTemplateModal" data-type="sef">
                      <i class="bi bi-folder-plus me-1"></i> Create New Version
                    </button>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-hover align-middle shadow-sm border">
                      <thead class="table-light">
                        <tr>
                          <th width="25%" class="ps-4">Rubric Name</th>
                          <th width="40%">Academic Description</th>
                          <th width="15%" class="text-center">System Status</th>
                          <th width="20%" class="text-center">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($sef_templates as $tpl): ?>
                          <tr class="<?= $tpl['is_equipped'] ? 'rubric-active-row' : 'rubric-standby-row' ?>">
                            <td class="ps-4 fw-bold text-dark">
                              <i class="bi <?= $tpl['is_equipped'] ? 'bi-journal-check text-success' : 'bi-journal-text text-muted' ?> me-2"></i>
                              <?= htmlspecialchars($tpl['template_name']) ?>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars($tpl['description']) ?></td>
                            <td class="text-center">
                              <?php if ($tpl['is_equipped']): ?>
                                <span class="badge bg-success px-3 py-2 shadow-sm"><i class="bi bi-check-circle-fill me-1"></i> ACTIVE RUBRIC</span>
                              <?php else: ?>
                                <span class="badge bg-secondary px-3 py-2 opacity-75">STANDBY</span>
                              <?php endif; ?>
                            </td>
                            <td class="text-center">
                              <?php if (!$tpl['is_equipped']): ?>
                                <form method="POST" class="d-inline swal-equip-form" data-user="supervisors">
                                  <input type="hidden" name="equip_template" value="1">
                                  <input type="hidden" name="template_type" value="sef">
                                  <input type="hidden" name="template_id" value="<?= $tpl['id'] ?>">
                                  <button type="submit" class="btn btn-sm btn-success shadow-sm" title="Set as Active Rubric">
                                    <i class="bi bi-toggle-on"></i> Set Active
                                  </button>
                                </form>
                              <?php else: ?>
                                <button class="btn btn-sm btn-outline-success disabled" title="Currently Active"><i class="bi bi-check2-all"></i> Active</button>
                              <?php endif; ?>

                              <button class="btn btn-sm btn-outline-primary btn-edit-template ms-1" data-bs-toggle="modal" data-bs-target="#editTemplateModal"
                                data-id="<?= $tpl['id'] ?>" data-type="sef" data-name="<?= htmlspecialchars($tpl['template_name']) ?>" data-desc="<?= htmlspecialchars($tpl['description']) ?>" title="Edit Details">
                                <i class="bi bi-pencil"></i>
                              </button>

                              <?php if (!$tpl['is_equipped']): ?>
                                <form method="POST" class="d-inline ms-1 swal-delete-form">
                                  <input type="hidden" name="delete_template" value="1">
                                  <input type="hidden" name="template_type" value="sef">
                                  <input type="hidden" name="template_id" value="<?= $tpl['id'] ?>">
                                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Version"><i class="bi bi-trash"></i></button>
                                </form>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
  </main>

  <div class="modal fade" id="addTemplateModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="bi bi-folder-plus me-2"></i>Create Rubric Version</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-primary small border-0 shadow-sm">
              <i class="bi bi-info-circle-fill me-1"></i> Creating a new Rubric Version allows you to group a fresh set of questions together. You can set it as "Active" later without overwriting historical evaluations.
            </div>
            <input type="hidden" name="template_type" id="add_template_type">
            <div class="mb-3">
              <label class="form-label fw-semibold">Version Name <span class="text-danger">*</span></label>
              <input type="text" name="template_name" class="form-control" placeholder="e.g., 2026 CHED Compliant Rubric" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Academic Notes / Description <span class="text-danger">*</span></label>
              <textarea name="template_description" class="form-control" rows="3" placeholder="Briefly describe the purpose of this rubric version..." required></textarea>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_template" class="btn btn-primary"><i class="bi bi-save me-1"></i> Create Version</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editTemplateModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Rubric Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="edit_template_type" id="edit_template_type">
            <input type="hidden" name="edit_template_id" id="edit_template_id">
            <div class="mb-3">
              <label class="form-label fw-semibold">Version Name <span class="text-danger">*</span></label>
              <input type="text" name="edit_template_name" id="edit_template_name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Academic Notes / Description <span class="text-danger">*</span></label>
              <textarea name="edit_template_description" id="edit_template_description" class="form-control" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_template" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Save Changes</button>
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
      // Pass TYPE to Add Modal
      document.querySelectorAll('.btn-add-template').forEach(btn => {
        btn.addEventListener('click', function() {
          document.getElementById('add_template_type').value = this.getAttribute('data-type');
        });
      });

      // Pass DATA to Edit Modal
      document.querySelectorAll('.btn-edit-template').forEach(btn => {
        btn.addEventListener('click', function() {
          document.getElementById('edit_template_type').value = this.getAttribute('data-type');
          document.getElementById('edit_template_id').value = this.getAttribute('data-id');
          document.getElementById('edit_template_name').value = this.getAttribute('data-name');
          document.getElementById('edit_template_description').value = this.getAttribute('data-desc');
        });
      });

      // --- SWEETALERT2 CONFIRMATIONS ---

      // Equip Rubric Confirmation
      document.querySelectorAll('.swal-equip-form').forEach(form => {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          const userType = this.getAttribute('data-user');
          Swal.fire({
            title: 'Activate this Rubric?',
            text: `WARNING: ${userType.charAt(0).toUpperCase() + userType.slice(1)} will immediately begin using this version for new evaluations.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Set Active'
          }).then((result) => {
            if (result.isConfirmed) {
              this.submit();
            }
          });
        });
      });

      // Delete Rubric Confirmation
      document.querySelectorAll('.swal-delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          Swal.fire({
            title: 'Delete Rubric?',
            text: 'Are you sure you want to permanently delete this rubric version? This action cannot be undone.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete It'
          }).then((result) => {
            if (result.isConfirmed) {
              this.submit();
            }
          });
        });
      });

    });
  </script>

  <?php if (isset($_SESSION['success_message'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?= $_SESSION['success_message'] ?>',
        timer: 2000,
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
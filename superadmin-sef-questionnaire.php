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

  // Add New SEF Category
  if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $order_by = (int)$_POST['category_order'];

    $stmt = $conn->prepare("INSERT INTO admin_evaluation_categories (category_name, order_by) VALUES (?, ?)");
    $stmt->bind_param("si", $category_name, $order_by);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "SEF Category added successfully!";
    } else {
      $_SESSION['error_message'] = "Failed to add category.";
    }
    $stmt->close();
    header("Location: superadmin-sef-questionnaire.php");
    exit();
  }

  // Add New SEF Question (Updated to include Verifications)
  if (isset($_POST['add_question'])) {
    $category_id = (int)$_POST['category_id'];
    $question_text = trim($_POST['question_text']);
    $verifications = trim($_POST['verifications']); // Capture verifications
    $order_by = (int)$_POST['question_order'];

    $stmt = $conn->prepare("INSERT INTO admin_evaluation_questions (category_id, question_text, verifications, status, order_by) VALUES (?, ?, ?, 'active', ?)");
    $stmt->bind_param("issi", $category_id, $question_text, $verifications, $order_by);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "SEF Question added successfully!";
    } else {
      $_SESSION['error_message'] = "Failed to add question.";
    }
    $stmt->close();
    header("Location: superadmin-sef-questionnaire.php");
    exit();
  }

  // Edit Existing SEF Question (Updated to include Verifications)
  if (isset($_POST['edit_question'])) {
    $question_id = (int)$_POST['edit_question_id'];
    $question_text = trim($_POST['edit_question_text']);
    $verifications = trim($_POST['edit_question_verifications']); // Capture verifications
    $order_by = (int)$_POST['edit_question_order'];

    $stmt = $conn->prepare("UPDATE admin_evaluation_questions SET question_text = ?, verifications = ?, order_by = ? WHERE id = ?");
    $stmt->bind_param("ssii", $question_text, $verifications, $order_by, $question_id);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "Question updated successfully!";
    } else {
      $_SESSION['error_message'] = "Failed to update question.";
    }
    $stmt->close();
    header("Location: superadmin-sef-questionnaire.php");
    exit();
  }

  // Toggle SEF Question Status
  if (isset($_POST['toggle_status'])) {
    $question_id = (int)$_POST['question_id'];
    $current_status = $_POST['current_status'];
    $new_status = ($current_status === 'active') ? 'inactive' : 'active';

    $stmt = $conn->prepare("UPDATE admin_evaluation_questions SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $question_id);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "Question visibility updated!";
    } else {
      $_SESSION['error_message'] = "Failed to update status.";
    }
    $stmt->close();
    header("Location: superadmin-sef-questionnaire.php");
    exit();
  }
}

// Fetch SEF Categories
$categories = [];
$cat_query = $conn->query("SELECT * FROM admin_evaluation_categories ORDER BY order_by ASC");
if ($cat_query) {
  while ($row = $cat_query->fetch_assoc()) {
    $categories[] = $row;
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
        <h1>Manage SEF Questionnaire</h1>
        <p class="text-muted small">Supervisor's Evaluation of Faculty</p>
      </div>
      <div>
        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
          <i class="bi bi-plus-circle"></i> Add Category
        </button>
      </div>
    </div>

    <section class="section dashboard">
      <div class="row">

        <?php if (empty($categories)): ?>
          <div class="col-12">
            <div class="alert alert-info">No SEF categories found. Create one to begin adding questions.</div>
          </div>
        <?php else: ?>
          <?php foreach ($categories as $category): ?>
            <div class="col-12 mb-4">
              <div class="card shadow-sm border-left-secondary">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                  <h5 class="card-title m-0 p-0 text-secondary">
                    <strong><?= htmlspecialchars($category['order_by']) ?>. <?= htmlspecialchars($category['category_name']) ?></strong>
                  </h5>
                  <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addQuestionModal" data-category-id="<?= $category['id'] ?>" data-category-name="<?= htmlspecialchars($category['category_name']) ?>">
                    <i class="bi bi-plus"></i> Add Question
                  </button>
                </div>
                <div class="card-body mt-3">
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover align-middle">
                      <thead class="table-light">
                        <tr>
                          <th width="5%" class="text-center">Order</th>
                          <th width="40%">Benchmark Statement</th>
                          <th width="30%">Means of Verification</th>
                          <th width="10%" class="text-center">Status</th>
                          <th width="15%" class="text-center">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $cat_id = $category['id'];
                        $q_stmt = $conn->prepare("SELECT * FROM admin_evaluation_questions WHERE category_id = ? ORDER BY order_by ASC");
                        $q_stmt->bind_param("i", $cat_id);
                        $q_stmt->execute();
                        $questions = $q_stmt->get_result();

                        if ($questions->num_rows > 0):
                          while ($q = $questions->fetch_assoc()):
                            $badgeClass = ($q['status'] === 'active') ? 'bg-success' : 'bg-secondary';
                        ?>
                            <tr>
                              <td class="text-center"><?= htmlspecialchars($q['order_by']) ?></td>
                              <td class="small"><?= htmlspecialchars($q['question_text']) ?></td>
                              <td class="small text-muted fst-italic"><?= htmlspecialchars($q['verifications'] ?? '') ?></td>
                              <td class="text-center">
                                <span class="badge <?= $badgeClass ?>"><?= ucfirst($q['status']) ?></span>
                              </td>
                              <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editQuestionModal"
                                  data-id="<?= $q['id'] ?>"
                                  data-text="<?= htmlspecialchars($q['question_text']) ?>"
                                  data-verifications="<?= htmlspecialchars($q['verifications'] ?? '') ?>"
                                  data-order="<?= $q['order_by'] ?>" title="Edit Question">
                                  <i class="bi bi-pencil"></i>
                                </button>

                                <form method="POST" action="superadmin-sef-questionnaire.php" class="d-inline">
                                  <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                                  <input type="hidden" name="current_status" value="<?= $q['status'] ?>">
                                  <button type="submit" name="toggle_status" class="btn btn-sm <?= $q['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success' ?>" title="<?= $q['status'] === 'active' ? 'Hide' : 'Show' ?>">
                                    <i class="bi <?= $q['status'] === 'active' ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                  </button>
                                </form>
                              </td>
                            </tr>
                          <?php
                          endwhile;
                        else:
                          ?>
                          <tr>
                            <td colspan="5" class="text-center text-muted small">No questions in this category.</td>
                          </tr>
                        <?php endif; ?>
                        <?php $q_stmt->close(); ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </section>

  </main>

  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5>Add SEF Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Category Name</label><input type="text" name="category_name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Order</label><input type="number" name="category_order" class="form-control" required min="1" value="1"></div>
          </div>
          <div class="modal-footer"><button type="submit" name="add_category" class="btn btn-secondary">Save Category</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5>Add SEF Question</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="category_id" id="modal_category_id">
            <div class="mb-3">
              <label class="form-label text-muted small">Category:</label>
              <input type="text" id="modal_category_name" class="form-control" readonly disabled>
            </div>
            <div class="mb-3">
              <label class="form-label">Question Text</label>
              <textarea name="question_text" class="form-control" rows="2" required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Means of Verification <span class="text-muted small">(Comma separated)</span></label>
              <textarea name="verifications" class="form-control" rows="2" placeholder="e.g. Course Syllabus, Learning Plan, LMS Logs"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Order</label>
              <input type="number" name="question_order" class="form-control" required min="1" value="1">
            </div>
          </div>
          <div class="modal-footer"><button type="submit" name="add_question" class="btn btn-success">Save Question</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5>Edit SEF Question</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="edit_question_id" id="edit_modal_question_id">
            <div class="mb-3">
              <label class="form-label">Question Text</label>
              <textarea name="edit_question_text" id="edit_modal_question_text" class="form-control" rows="2" required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Means of Verification <span class="text-muted small">(Comma separated)</span></label>
              <textarea name="edit_question_verifications" id="edit_modal_question_verifications" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Order</label>
              <input type="number" name="edit_question_order" id="edit_modal_question_order" class="form-control" required min="1">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_question" class="btn btn-success">Update Question</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'footer.php' ?>

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
      // Logic for passing data into the Add Question Modal
      var addQuestionModal = document.getElementById('addQuestionModal');
      if (addQuestionModal) {
        addQuestionModal.addEventListener('show.bs.modal', function(event) {
          var button = event.relatedTarget;
          document.getElementById('modal_category_id').value = button.getAttribute('data-category-id');
          document.getElementById('modal_category_name').value = button.getAttribute('data-category-name');
        });
      }

      // Logic for passing data into the Edit Question Modal
      var editQuestionModal = document.getElementById('editQuestionModal');
      if (editQuestionModal) {
        editQuestionModal.addEventListener('show.bs.modal', function(event) {
          var button = event.relatedTarget;
          document.getElementById('edit_modal_question_id').value = button.getAttribute('data-id');
          document.getElementById('edit_modal_question_text').value = button.getAttribute('data-text');
          document.getElementById('edit_modal_question_verifications').value = button.getAttribute('data-verifications');
          document.getElementById('edit_modal_question_order').value = button.getAttribute('data-order');
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
<?php
session_start();
include 'conn/conn.php';

if (!isset($_SESSION['idnumber']) || $_SESSION['role'] !== 'registrar') {
  header("Location: pages-login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $type = $_POST['type'] ?? '';
  $value = trim($_POST['value'] ?? '');

  if ($type && $value) {
    switch ($type) {
      case 'Rank':
        $column = 'rank_name';
        break;
      case 'Position':
        $column = 'position_name';
        break;
      case 'Section':
        $column = 'section_name';
        break;
      case 'college':
        $column = 'college_name';
        break;
      case 'Program':
        $column = 'program_name';
        break;
      default:
        $column = '';
    }

    if ($column) {
      if ($type === 'Program') {
        $college_name = trim($_POST['college_name'] ?? '');
        if (empty($college_name)) {
          $_SESSION['msg'] = "Please select a college for the program.";
          $_SESSION['msg_type'] = "warning";
          header("Location: register-addsmanagement.php");
          exit();
        }

        $check = $conn->prepare("SELECT COUNT(*) FROM adds WHERE LOWER(program_name)=LOWER(?) AND LOWER(college_name)=LOWER(?)");
        $check->bind_param("ss", $value, $college_name);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
          $_SESSION['msg'] = "Program already exists in this college.";
          $_SESSION['msg_type'] = "warning";
        } else {
          $stmt = $conn->prepare("INSERT INTO adds (college_name, program_name) VALUES (?, ?)");
          $stmt->bind_param("ss", $college_name, $value);
          $stmt->execute();
          $_SESSION['msg'] = "Program added successfully!";
          $_SESSION['msg_type'] = "success";
          $stmt->close();
        }
      } else {
        $check = $conn->prepare("SELECT COUNT(*) FROM adds WHERE LOWER($column)=LOWER(?)");
        $check->bind_param("s", $value);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
          $_SESSION['msg'] = "$type already exists.";
          $_SESSION['msg_type'] = "warning";
        } else {
          $stmt = $conn->prepare("INSERT INTO adds ($column) VALUES (?)");
          $stmt->bind_param("s", $value);
          $stmt->execute();
          $_SESSION['msg'] = "$type added successfully!";
          $_SESSION['msg_type'] = "success";
          $stmt->close();
        }
      }
      header("Location: register-addsmanagement.php");
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
  <style>
    .college-card {
      border: 1px solid #dee2e6;
      border-radius: 10px;
      margin-bottom: 1rem;
      background: #f8f9fa;
      padding: 1rem;
    }

    .college-title {
      font-weight: 600;
      font-size: 1rem;
      color: #0d6efd;
    }

    .program-list {
      list-style-type: none;
      margin: 0.5rem 0 0 1rem;
      padding: 0;
    }

    .program-list li {
      padding: 3px 0;
    }

    .program-list li::before {
      content: "• ";
      color: #198754;
    }

    .box-card {
      background: #fff;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
    }
  </style>
</head>

<body>
  <?php include 'register-header.php'; ?>
  <?php include 'register-sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Manage Colleges, Programs, Ranks, Positions, Sections</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="register-dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Manage</li>
        </ol>
      </nav>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
      <script>
        Swal.fire({
          icon: '<?= $_SESSION['msg_type'] === 'success' ? 'success' : ($_SESSION['msg_type'] === 'warning' ? 'warning' : 'error') ?>',
          title: "<?= $_SESSION['msg_type'] === 'success' ? 'Success!' : 'Notice' ?>",
          text: "<?= htmlspecialchars($_SESSION['msg']) ?>",
          confirmButtonColor: "#198754"
        });
      </script>
      <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <section class="section">
      <div class="card p-4 mb-4">
        <h5 class="card-title">Add New Entry</h5>
        <form method="POST" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Type</label>
            <select class="form-select" name="type" id="type" onchange="togglecollegeDropdown()" required>
              <option value="">-- Select Type --</option>
              <option value="Rank">Rank</option>
              <option value="Position">Position</option>
              <option value="Section">Section</option>
              <option value="college">College</option>
              <option value="Program">Program</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="value" required>
          </div>
          <div class="col-md-4 d-none" id="collegeField">
            <label class="form-label">Select College</label>
            <select class="form-select" name="college_name">
              <option value="">-- Choose College --</option>
              <?php
              $college = $conn->query("SELECT DISTINCT college_name FROM adds WHERE college_name IS NOT NULL AND college_name != '' ORDER BY college_name ASC");
              while ($row = $college->fetch_assoc()):
              ?>
                <option value="<?= htmlspecialchars($row['college_name']) ?>"><?= htmlspecialchars($row['college_name']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-12 text-end mt-3">
            <button type="button" id="confirmAdd" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Entry</button>
          </div>
        </form>
      </div>

      <div class="row">
        <!-- LEFT SIDE -->
        <div class="col-lg-6">
          <h5 class="mb-3"><i class="bi bi-building"></i> Colleges & Programs</h5>
          <?php
          $colleges = $conn->query("
  SELECT id, college_name
  FROM adds
  WHERE college_name IS NOT NULL 
    AND college_name != '' 
    AND (program_name IS NULL OR program_name = '')
  ORDER BY college_name ASC
");
          while ($col = $colleges->fetch_assoc()):
            $dept = $col['college_name'];
            $programs = $conn->query("SELECT id, program_name FROM adds WHERE college_name='$dept' AND program_name IS NOT NULL ORDER BY program_name ASC");
          ?>
            <div class="college-card">
              <div class="d-flex justify-content-between align-items-center">
                <div class="college-title text-success"><?= htmlspecialchars($dept) ?></div>
                <a href="register-addsedit.php?id=<?= $col['id'] ?>&type=college" class="btn btn-sm btn-warning">Edit</a>
              </div>
              <?php if ($programs->num_rows > 0): ?>
                <ul class="program-list">
                  <?php while ($p = $programs->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($p['program_name']) ?>
                      <a href="register-addsedit.php?id=<?= $p['id'] ?>&type=Program" class="text-warning small ms-2">(Edit)</a>
                    </li>
                  <?php endwhile; ?>
                </ul>
              <?php else: ?>
                <p class="text-muted mt-1 ms-3 mb-0">No programs listed.</p>
              <?php endif; ?>
            </div>
          <?php endwhile; ?>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-6">
          <h5 class="mb-3"><i class="bi bi-list-check"></i> Ranks, Positions & Sections</h5>

          <?php
          $categories = [
            "Ranks" => ["column" => "rank_name", "type" => "Rank"],
            "Positions" => ["column" => "position_name", "type" => "Position"],
            "Sections" => ["column" => "section_name", "type" => "Section"]
          ];

          foreach ($categories as $title => $info):
            $query = $conn->query("SELECT id, {$info['column']} AS name FROM adds WHERE {$info['column']} IS NOT NULL ORDER BY name ASC");
          ?>
            <div class="college-card">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="college-title text-success"><?= htmlspecialchars($title) ?></div>
              </div>

              <?php if ($query->num_rows > 0): ?>
                <ul class="program-list">
                  <?php while ($row = $query->fetch_assoc()): ?>
                    <li class="d-flex justify-content-between align-items-center">
                      <span><?= htmlspecialchars($row['name']) ?></span>
                      <a href="register-addsedit.php?id=<?= $row['id'] ?>&type=<?= urlencode($info['type']) ?>" class="btn btn-sm btn-warning ms-2">
                        Edit
                      </a>
                    </li>
                  <?php endwhile; ?>
                </ul>
              <?php else: ?>
                <p class="text-muted mt-1 ms-3 mb-0">No <?= strtolower($title) ?> added.</p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>

      </div>
    </section>
  </main>

  <?php include 'footer.php'; ?>

  <script src="vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    document.getElementById("confirmAdd").addEventListener("click", function(e) {
      const type = document.getElementById("type").value;
      const name = document.querySelector("input[name='value']").value.trim();
      if (!type || !name) {
        Swal.fire({
          icon: "warning",
          title: "Missing Fields",
          text: "Please complete all fields."
        });
        return;
      }
      Swal.fire({
        title: `Add "${name}" as ${type}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, add it",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#198754",
        cancelButtonColor: "#dc3545"
      }).then((result) => {
        if (result.isConfirmed) e.target.closest("form").submit();
      });
    });

    function togglecollegeDropdown() {
      const type = document.getElementById("type").value;
      const field = document.getElementById("collegeField");
      field.classList.toggle("d-none", type !== "Program");
    }
  </script>
</body>

</html>
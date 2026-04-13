<?php
// Detect current file name
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'superadmin-dashboard.php' ? '' : 'collapsed' ?>"
        href="superadmin-dashboard.php">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <?php $reportsPages = ['superadmin-individualreport.php', 'superadmin-acknowledgementreport.php', 'superadmin-overallreport-set.php', 'superadmin-overallreport-sef.php', 'superadmin-overallreport.php', 'superadmin-pastrecords.php']; ?>
      <a class="nav-link <?= in_array($currentPage, $reportsPages) ? '' : 'collapsed' ?>"
        data-bs-target="#reports" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="reports" class="nav-content collapse <?= in_array($currentPage, $reportsPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="superadmin-individualreport.php" class="<?= $currentPage == 'superadmin-individualreport.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Individual Report</span>
          </a>
        </li>
        <li>
          <a href="superadmin-acknowledgementreport.php" class="<?= $currentPage == 'superadmin-acknowledgementreport.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
          </a>
        </li>
        <li>
          <a href="superadmin-overallreport-set.php" class="<?= $currentPage == 'superadmin-overallreport-set.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>College SET Report</span>
          </a>
        </li>
        <li>
          <a href="superadmin-overallreport-sef.php" class="<?= $currentPage == 'superadmin-overallreport-sef.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>College SEF Report</span>
          </a>
        </li>
        <li>
          <a href="superadmin-overallreport.php" class="<?= $currentPage == 'superadmin-overallreport.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>College SET/SEF Report</span>
          </a>
        </li>
        <li>
          <a href="superadmin-pastrecords.php" class="<?= $currentPage == 'superadmin-pastrecords.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Past Record</span>
          </a>
        </li>
      </ul>
    </li>

    <?php $evalPages = ['superadmin-evaluationsetting.php', 'superadmin-evaluationswitch.php', 'superadmin-evaluationprogress.php', 'superadmin-questionnaire.php', 'superadmin-sef-questionnaire.php', 'superadmin-templates.php', 'superadmin-rating-scale.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $evalPages) ? '' : 'collapsed' ?>"
        data-bs-target="#evaluation" data-bs-toggle="collapse" href="#">
        <i class="ri-settings-4-line"></i><span>Evaluation</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="evaluation" class="nav-content collapse <?= in_array($currentPage, $evalPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="superadmin-evaluationsetting.php" class="<?= $currentPage == 'superadmin-evaluationsetting.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Academic Year/ Semester</span>
          </a>
        </li>
        <li>
          <a href="superadmin-evaluationswitch.php" class="<?= $currentPage == 'superadmin-evaluationswitch.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Control</span>
          </a>
        </li>
        <li>
          <a href="superadmin-evaluationprogress.php" class="<?= $currentPage == 'superadmin-evaluationprogress.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Progress</span>
          </a>
        </li>
        <li>
          <a href="superadmin-templates.php" class="<?= $currentPage == 'superadmin-templates.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Rubric Templates</span>
          </a>
        </li>
        <li>
          <a href="superadmin-questionnaire.php" class="<?= $currentPage == 'superadmin-questionnaire.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>SET Questionnaire</span>
          </a>
        </li>
        <li>
          <a href="superadmin-sef-questionnaire.php" class="<?= $currentPage == 'superadmin-sef-questionnaire.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>SEF Questionnaire</span>
          </a>
        </li>
        <li>
          <a href="superadmin-rating-scale.php" class="<?= $currentPage == 'superadmin-rating-scale.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Rating Scale</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'superadmin-evaluatedsubject.php' ? '' : 'collapsed' ?>"
        href="superadmin-evaluatedsubject.php">
        <i class="bi bi-book-fill"></i>
        <span>Subject Evaluated</span>
      </a>
    </li>

    <li class="nav-heading">Management</li>

    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'superadmin-addsmanagement.php' ? '' : 'collapsed' ?>"
        href="superadmin-addsmanagement.php">
        <i class="ri-settings-line"></i>
        <span>Manage</span>
      </a>
    </li>

    <?php $adminPages = ['superadmin-adminlist.php', 'superadmin-admincreation.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $adminPages) ? '' : 'collapsed' ?>"
        data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="admin-nav" class="nav-content collapse <?= in_array($currentPage, $adminPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="superadmin-adminlist.php" class="<?= $currentPage == 'superadmin-adminlist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="superadmin-admincreation.php" class="<?= $currentPage == 'superadmin-admincreation.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add New Admin</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-heading">Pages</li>

    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'superadmin-user-profile.php' ? '' : 'collapsed' ?>"
        href="superadmin-user-profile.php">
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="logout.php">
        <i class="bi bi-box-arrow-right"></i>
        <span>Sign Out</span>
      </a>
    </li>

  </ul>
</aside>
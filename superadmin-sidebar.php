<?php
// Detect current file name
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'superadmin-dashboard.php' ? '' : 'collapsed' ?>"
        href="superadmin-dashboard.php">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- Reports -->
    <li class="nav-item">
      <?php $reportsPages = ['superadmin-individualreport.php', 'superadmin-acknowledgementreport.php','superadmin-overallreport-set.php','superadmin-overallreport-sef.php','superadmin-overallreport.php', 'superadmin-pastrecords.php']; ?>
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

    <!-- Evaluation -->
    <?php $evalPages = ['superadmin-evaluationsetting.php', 'superadmin-evaluationswitch.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $evalPages) ? '' : 'collapsed' ?>"
        data-bs-target="#evaluation" data-bs-toggle="collapse" href="#">
        <i class="ri-settings-4-line"></i><span>Evaluation</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="evaluation" class="nav-content collapse <?= in_array($currentPage, $evalPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="superadmin-evaluationsetting.php" class="<?= $currentPage == 'superadmin-evaluationsetting.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Setting</span>
          </a>
        </li>
        <li>
          <a href="superadmin-evaluationswitch.php" class="<?= $currentPage == 'superadmin-evaluationswitch.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>On/Off</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Subject -->
    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'superadmin-evaluatedsubject.php' ? 'active' : 'collapsed' ?>"
        href="superadmin-evaluatedsubject.php">
        <i class="bi bi-book-fill"></i>
        <span>Subject</span>
      </a>
    </li>

    <li class="nav-heading">Account Management</li>

    <!-- Manage -->
    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'superadmin-addsmanagement.php' ? 'active' : 'collapsed' ?>"
        href="superadmin-addsmanagement.php">
        <i class="ri-settings-line"></i>
        <span>Manage</span>
      </a>
    </li>

    <!-- Student -->
    <?php $studentPages = ['superadmin-studentlist.php', 'superadmin-studentcreation.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $studentPages) ? '' : 'collapsed' ?>"
        data-bs-target="#student-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="student-nav" class="nav-content collapse <?= in_array($currentPage, $studentPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="superadmin-studentlist.php" class="<?= $currentPage == 'superadmin-studentlist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="superadmin-studentcreation.php" class="<?= $currentPage == 'superadmin-studentcreation.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add New Student</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Faculty -->
    <?php $facultyPages = ['superadmin-facultylist.php', 'superadmin-facultycreation.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $facultyPages) ? '' : 'collapsed' ?>"
        data-bs-target="#faculty-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people-fill"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="faculty-nav" class="nav-content collapse <?= in_array($currentPage, $facultyPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="superadmin-facultylist.php" class="<?= $currentPage == 'superadmin-facultylist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="superadmin-facultycreation.php" class="<?= $currentPage == 'superadmin-facultycreation.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add New Faculty</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Admin -->
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

    <!-- Super Admin -->
    <?php $superPages = ['superadmin-superadminlist.php', 'superadmin-superadmincreation.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $superPages) ? '' : 'collapsed' ?>"
        data-bs-target="#super-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person-fill"></i><span>Super Admin</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="super-nav" class="nav-content collapse <?= in_array($currentPage, $superPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="superadmin-superadminlist.php" class="<?= $currentPage == 'superadmin-superadminlist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="superadmin-superadmincreation.php" class="<?= $currentPage == 'superadmin-superadmincreation.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add New SuperAdmin</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-heading">Pages</li>

    <!-- Profile -->
    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'superadmin-user-profile.php' ? 'active' : 'collapsed' ?>"
        href="superadmin-user-profile.php">
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </li>

    <!-- Logout -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="logout.php">
        <i class="bi bi-box-arrow-right"></i>
        <span>Sign Out</span>
      </a>
    </li>

  </ul>
</aside>
<!-- End Sidebar-->
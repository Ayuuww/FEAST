<?php
// Detect current file name
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'register-dashboard.php' ? '' : 'collapsed' ?>"
        href="register-dashboard.php">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- Subject -->
    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'register-evaluatedsubject.php' ? 'active' : 'collapsed' ?>"
        href="register-evaluatedsubject.php">
        <i class="bi bi-book-fill"></i>
        <span>Subject Evaluated</span>
      </a>
    </li>

    <!-- Records -->
    <li class="nav-item">
      <a class="nav-link <?= ($currentPage == 'register-pastrecords.php') ? 'active' : 'collapsed' ?>" href="register-pastrecords.php">
        <i class="bi bi-archive"></i>
        <span>Records</span>
      </a>
    </li><!-- End Records Nav -->

    <li class="nav-heading">Management</li>

    <!-- Manage -->
    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'register-addsmanagement.php' ? 'active' : 'collapsed' ?>"
        href="register-addsmanagement.php">
        <i class="ri-settings-line"></i>
        <span>Manage</span>
      </a>
    </li>

    <!-- Department Information -->
    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'register-department-info.php' ? 'active' : 'collapsed' ?>"
        href="register-department-info.php">
        <i class="bi bi-building"></i>
        <span>Department Information</span>
      </a>
    </li>

    <!-- Student -->
    <?php $studentPages = ['register-studentlist.php', 'register-studentcreation.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $studentPages) ? '' : 'collapsed' ?>"
        data-bs-target="#student-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="student-nav" class="nav-content collapse <?= in_array($currentPage, $studentPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="register-studentlist.php" class="<?= $currentPage == 'register-studentlist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="register-studentcreation.php" class="<?= $currentPage == 'register-studentcreation.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add New Student</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Faculty -->
    <?php $facultyPages = ['register-facultylist.php', 'register-facultycreation.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $facultyPages) ? '' : 'collapsed' ?>"
        data-bs-target="#faculty-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people-fill"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="faculty-nav" class="nav-content collapse <?= in_array($currentPage, $facultyPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="register-facultylist.php" class="<?= $currentPage == 'register-facultylist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="register-facultycreation.php" class="<?= $currentPage == 'register-facultycreation.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add New Faculty</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Admin -->
    <?php $adminPages = ['register-adminlist.php', 'register-admincreation.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $adminPages) ? '' : 'collapsed' ?>"
        data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="admin-nav" class="nav-content collapse <?= in_array($currentPage, $adminPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="register-adminlist.php" class="<?= $currentPage == 'register-adminlist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="register-admincreation.php" class="<?= $currentPage == 'register-admincreation.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add New Admin</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Super Admin -->
    <?php $superPages = ['register-superadminlist.php', 'register-superadmincreation.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $superPages) ? '' : 'collapsed' ?>"
        data-bs-target="#super-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person-fill"></i><span>Super Admin</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="super-nav" class="nav-content collapse <?= in_array($currentPage, $superPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="register-superadminlist.php" class="<?= $currentPage == 'register-superadminlist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="register-superadmincreation.php" class="<?= $currentPage == 'register-superadmincreation.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add New SuperAdmin</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Registrar -->
    <?php $regisPages = ['register-registrarlist.php', 'register-registrarcreation.php']; ?>
    <li class="nav-item">
      <a class="nav-link <?= in_array($currentPage, $regisPages) ? '' : 'collapsed' ?>"
        data-bs-target="#regis-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person-fill"></i><span>Account Creator</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="regis-nav" class="nav-content collapse <?= in_array($currentPage, $regisPages) ? 'show' : '' ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="register-registrarlist.php" class="<?= $currentPage == 'register-registrarlist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="register-registrarcreation.php" class="<?= $currentPage == 'register-registrarcreation.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add New Account Creator</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-heading">Pages</li>

    <!-- Profile -->
    <li class="nav-item">
      <a class="nav-link <?= $currentPage == 'register-user-profile.php' ? 'active' : 'collapsed' ?>"
        href="register-user-profile.php">
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
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link <?= ($current_page == 'student-dashboard.php') ? '' : 'collapsed' ?>" href="student-dashboard.php">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <!-- Evaluate -->
    <li class="nav-item">
      <a class="nav-link <?= ($current_page == 'student-evaluate.php' || $current_page == 'student-evaluatedsubject.php') ? '' : 'collapsed' ?>"
         data-bs-target="#evaluate-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="evaluate-nav" class="nav-content collapse <?= ($current_page == 'student-evaluate.php' || $current_page == 'student-evaluatedsubject.php') ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="student-evaluate.php" class="<?= ($current_page == 'student-evaluate.php') ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Form</span>
          </a>
        </li>
        <li>
          <a href="student-evaluatedsubject.php" class="<?= ($current_page == 'student-evaluatedsubject.php') ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Evaluated Subject</span>
          </a>
        </li>
      </ul>
    </li><!-- End Evaluate Nav -->

    <!-- Pages Heading -->
    <li class="nav-heading">Pages</li>

    <!-- Profile -->
    <li class="nav-item">
      <a class="nav-link <?= ($current_page == 'student-user-profile.php') ? '' : 'collapsed' ?>" href="student-user-profile.php">
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </li><!-- End Profile Nav -->

    <!-- Sign Out -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="logout.php">
        <i class="bi bi-box-arrow-right"></i>
        <span>Sign Out</span>
      </a>
    </li><!-- End Sign Out Nav -->

  </ul>

</aside><!-- End Sidebar -->

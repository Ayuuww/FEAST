<!-- ======= Sidebar ======= -->
<?php
  // Get current page name
  $current_page = basename($_SERVER['PHP_SELF']);
?>

<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link <?= ($current_page == 'faculty-dashboard.php') ? 'active' : 'collapsed' ?>" href="faculty-dashboard.php">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <!-- Subjects -->
    <li class="nav-item">
      <a class="nav-link <?= ($current_page == 'faculty-evaluatedsubject.php') ? 'active' : 'collapsed' ?>" href="faculty-evaluatedsubject.php">
        <i class="bi bi-book"></i>
        <span>Subjects</span>
      </a>
    </li><!-- End Subjects Nav -->

    <!-- Records -->
    <li class="nav-item">
      <a class="nav-link <?= ($current_page == 'faculty-pastrecords.php') ? 'active' : 'collapsed' ?>" href="faculty-pastrecords.php">
        <i class="bi bi-archive"></i>
        <span>Records</span>
      </a>
    </li><!-- End Records Nav -->

    <li class="nav-heading">Pages</li>

    <!-- Profile -->
    <li class="nav-item">
      <a class="nav-link <?= ($current_page == 'faculty-user-profile.php') ? 'active' : 'collapsed' ?>" href="faculty-user-profile.php">
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

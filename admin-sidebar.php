<?php
  $current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link <?= $current_page == 'admin-dashboard.php' ? '' : 'collapsed' ?>" href="admin-dashboard.php">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <!-- Evaluate -->
    <li class="nav-item">
      <?php 
        $evaluate_pages = ['admin-evaluate.php','admin-evaluatedfaculty.php'];
        $is_evaluate_active = in_array($current_page, $evaluate_pages);
      ?>
      <a class="nav-link <?= $is_evaluate_active ? '' : 'collapsed' ?>" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-book"></i><span>Evaluate</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="charts-nav" class="nav-content collapse <?= $is_evaluate_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="admin-evaluate.php" class="<?= $current_page == 'admin-evaluate.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Form</span>
          </a>
        </li>
        <li>
          <a href="admin-evaluatedfaculty.php" class="<?= $current_page == 'admin-evaluatedfaculty.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Evaluated Faculty</span>
          </a>
        </li>
      </ul>
    </li><!-- End Evaluate Nav -->

    <!-- Subject -->
    <li class="nav-item">
      <?php 
        $subject_pages = ['admin-subjectlist.php','admin-subjectadding.php'];
        $is_subject_active = in_array($current_page, $subject_pages);
      ?>
      <a class="nav-link <?= $is_subject_active ? '' : 'collapsed' ?>" data-bs-target="#subject" data-bs-toggle="collapse" href="#">
        <i class="ri-book-line"></i><span>Subject</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="subject" class="nav-content collapse <?= $is_subject_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="admin-subjectlist.php" class="<?= $current_page == 'admin-subjectlist.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>List</span>
          </a>
        </li>
        <li>
          <a href="admin-subjectadding.php" class="<?= $current_page == 'admin-subjectadding.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Add Subject</span>
          </a>
        </li>
      </ul>
    </li><!-- End Subject Nav -->

    <!-- Student Subject -->
    <li class="nav-item">
      <a class="nav-link <?= $current_page == 'admin-studentsubject.php' ? '' : 'collapsed' ?>" href="admin-studentsubject.php">
        <i class="ri-book-fill"></i>
        <span>Assign Subject</span>
      </a>
    </li><!-- End Student Subject Nav -->

    <!-- Evaluated Subject -->
    <li class="nav-item">
      <a class="nav-link <?= $current_page == 'admin-evaluatedsubject.php' ? '' : 'collapsed' ?>" href="admin-evaluatedsubject.php">
        <i class="bi bi-book-fill"></i>
        <span>Subject Evaluated</span>
      </a>
    </li><!-- End Evaluated Subject Nav -->

    <!-- Student Subject -->
    <li class="nav-item">
      <a class="nav-link <?= $current_page == 'admin-evaluationprogress.php' ? '' : 'collapsed' ?>" href="admin-evaluationprogress.php">
        <i class="ri-book-fill"></i>
        <span>Evaluation Progress</span>
      </a>
    </li><!-- End Student Subject Nav -->

    <!-- Reports -->
    <li class="nav-item">
      <?php 
        $report_pages = [
          'admin-individualreport.php',
          'admin-acknowledgementreport.php',
          'admin-overallreport-set.php',
          'admin-overallreport-sef.php',
          'admin-overallreport.php',
          'admin-pastrecords.php'
        ];
        $is_report_active = in_array($current_page, $report_pages);
      ?>
      <a class="nav-link <?= $is_report_active ? '' : 'collapsed' ?>" data-bs-target="#reports" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="reports" class="nav-content collapse <?= $is_report_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="admin-individualreport.php" class="<?= $current_page == 'admin-individualreport.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Individual Report</span>
          </a>
        </li>
        <li>
          <a href="admin-acknowledgementreport.php" class="<?= $current_page == 'admin-acknowledgementreport.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Acknowledgement Report</span>
          </a>
        </li>
        <li>
          <a href="admin-overallreport-set.php" class="<?= $current_page == 'admin-overallreport-set.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>College Report SET</span>
          </a>
        </li>
        <li>
          <a href="admin-overallreport-sef.php" class="<?= $current_page == 'admin-overallreport-sef.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>College Report SEF</span>
          </a>
        </li>
        <li>
          <a href="admin-overallreport.php" class="<?= $current_page == 'admin-overallreport.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>College Report (SET & SEF)</span>
          </a>
        </li>
        <li>
          <a href="admin-pastrecords.php" class="<?= $current_page == 'admin-pastrecords.php' ? 'active' : '' ?>">
            <i class="bi bi-circle"></i><span>Past Record</span>
          </a>
        </li>
      </ul>
    </li><!-- End Reports Nav -->

    <li class="nav-heading">Pages</li>

    <!-- Profile -->
    <li class="nav-item">
      <a class="nav-link <?= $current_page == 'admin-user-profile.php' ? '' : 'collapsed' ?>" href="admin-user-profile.php">
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </li><!-- End Profile Nav -->

    <!-- Logout -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="logout.php">
        <i class="bi bi-box-arrow-right"></i>
        <span>Sign Out</span>
      </a>
    </li><!-- End Sign out Nav -->

  </ul>

</aside><!-- End Sidebar-->

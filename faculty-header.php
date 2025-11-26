<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="faculty-dashboard.php" class="logo d-flex align-items-center">
      <img src="pics/DMMMSUlogo.png" alt="">
      <span class="d-none d-lg-block">FEASTa</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo --><!-- End Logo -->

  <div class="search-bar position-relative">
    <form class="search-form d-flex align-items-center" onsubmit="return false;">
      <input type="text" id="navSearch" placeholder="Search navigation..." autocomplete="off">
      <button type="submit" title="Search"><i class="bi bi-search"></i></button>
    </form>
    <div id="searchSuggestions" class="list-group position-absolute w-100" style="z-index:1000; max-height:200px; overflow-y:auto;"></div>
  </div><!-- End Search Bar -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <li class="nav-item d-block d-lg-none">
        <a class="nav-link nav-icon search-bar-toggle " href="#">
          <i class="bi bi-search"></i>
        </a>
      </li><!-- End Search Icon-->

      <li class="nav-item dropdown pe-3">

        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <span class="d-none d-md-block dropdown-toggle ps-2 text-capitalize"><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></span>
        </a><!-- End Profile Iamge Icon -->

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

          <li>
            <a class="dropdown-item d-flex align-items-center" href="faculty-user-profile.php">
              <i class="bi bi-person"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="logout.php">
              <i class="bi bi-box-arrow-right"></i>
              <span>Sign Out</span>
            </a>
          </li>

        </ul><!-- End Profile Dropdown Items -->
      </li><!-- End Profile Nav -->

    </ul>
  </nav><!-- End Icons Navigation -->

  <script>
    document.getElementById('navSearch').addEventListener('keyup', function() {
      let query = this.value.trim();
      let suggestionBox = document.getElementById('searchSuggestions');

      if (query.length < 1) {
        suggestionBox.innerHTML = '';
        return;
      }

      fetch('search-nav.php?query=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
          suggestionBox.innerHTML = '';

          if (data.length > 0) {
            data.forEach(item => {
              let link = document.createElement('a');
              link.href = item.link;
              link.className = 'list-group-item list-group-item-action';
              link.textContent = item.name;
              suggestionBox.appendChild(link);
            });
          } else {
            let noResult = document.createElement('div');
            noResult.className = 'list-group-item disabled';
            noResult.textContent = 'No matches found';
            suggestionBox.appendChild(noResult);
          }
        });
    });
  </script>
  
</header><!-- End Header -->
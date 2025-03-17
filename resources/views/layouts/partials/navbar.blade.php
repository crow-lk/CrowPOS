<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light dark:bg-gray-800 dark:text-white">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="{{ route('home') }}" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>

  <!-- SEARCH FORM -->
  <form class="form-inline ml-3">
    <div class="input-group input-group-sm">
      <input class="form-control form-control-navbar dark:bg-gray-700" type="search" placeholder="Search" aria-label="Search">
      <div class="input-group-append">
        <button class="btn btn-navbar" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>
  </form>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fas fa-bell"></i>
        <span class="badge badge-warning navbar-badge">15</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">{{ __('common.Notifications', ['total' => 15]) }}</span>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-envelope mr-2"></i> {{ __('common.new_messages', ['total_msg' => 4]) }}
          <span class="float-right text-muted text-sm">{{ __('common.no_mins', ['mins' => 3]) }}</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-users mr-2"></i> {{ __('common.total_friend_request', ['total' => 8]) }}
          <span class="float-right text-muted text-sm">{{ __('common.no_hours', ['hours' => 12]) }}</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-file mr-2"></i> {{ __('common.total_new_reports', ['total' => 3]) }}
          <span class="float-right text-muted text-sm">{{ __('common.no_days', ['days' => 2]) }}</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">{{ __('common.see_all') }}</a>
      </div>
    </li>

    <!-- User Avatar -->
    <li class="nav-item">
      <div class="user-panel mt-1 pb-1 d-flex">
        <div class="image">
          <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ auth()->user()->getFullname() }}</a>
        </div>
      </div>
    </li>

    <!-- Dark Mode Toggle -->
    <li class="nav-item">
      <a class="nav-link" href="#" id="theme-toggle">
        <i class="fas fa-sun" id="light-icon"></i>  <!-- Sun icon for light mode -->
        <i class="fas fa-moon" id="dark-icon" style="display: none;"></i>  <!-- Moon icon for dark mode -->
      </a>
    </li>
  </ul>
</nav>
<!-- /.navbar -->

<!-- Dark Mode Script -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
      const themeToggle = document.getElementById("theme-toggle");
      const body = document.body;
      const lightIcon = document.getElementById("light-icon");
      const darkIcon = document.getElementById("dark-icon");

      // Check for saved theme preference
      const savedTheme = localStorage.getItem("theme");
      if (savedTheme === "dark") {
          body.classList.add("dark-mode");
          lightIcon.style.display = "none";
          darkIcon.style.display = "inline";
      }

      // Toggle theme on button click
      themeToggle.addEventListener("click", function (event) {
          event.preventDefault();
          if (body.classList.contains("dark-mode")) {
              body.classList.remove("dark-mode");
              localStorage.setItem("theme", "light");
              lightIcon.style.display = "inline";
              darkIcon.style.display = "none";
          } else {
              body.classList.add("dark-mode");
              localStorage.setItem("theme", "dark");
              lightIcon.style.display = "none";
              darkIcon.style.display = "inline";
          }
      });
  });
</script>

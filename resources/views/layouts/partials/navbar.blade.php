<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light ">
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
      <input class="form-control form-control-navbar " type="search" placeholder="Search" aria-label="Search">
      <div class="input-group-append">
        <button class="btn btn-navbar" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>
  </form>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto d-flex align-items-center">
    <!-- Dark Mode Toggle Button -->
    <li class="nav-item d-flex align-items-center">
      <button id="theme-toggle" class="nav-link btn btn-sm btn-outline-secondary theme-toggle-btn" aria-label="Toggle Dark Mode">
        <i class="fas fa-moon"></i> <!-- Default icon for light mode -->
      </button>
    </li>

    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
        <div class="icon-with-badge">
            <i class="fa-regular fa-bell fa-shake fa-2xl"></i>
            <span class="badge badge-warning navbar-badge">15</span>
        </div>
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

    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <div class="user-panel pb-2 d-flex">
                <div class="image">
                    <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    {{ auth()->user()->getFullname() }}
                </div>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="p-5">PROFILE</span>
          <div class="dropdown-divider"></div>
          <div class="user-panel pb-2 pt-2 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-1" alt="User Image">
            </div>
            <div class="info">
                {{ auth()->user()->getFullname() }}
            </div>
          </div>
          <div class="dropdown-divider"></div>
          <div class="user-panel pb-2 d-flex">
            <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
                <div class="info">
                    <p><i class="nav-icon fas fa-sign-out-alt"></i>{{ __('common.Logout') }}</p>
                    <form action="{{route('logout')}}" method="POST" id="logout-form">
                        @csrf
                    </form>
                </div>
            </a>
          </div>
        </div>
      </li>
  </ul>
</nav>
<!-- /.navbar -->

<!-- Dark Mode Toggle Script -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const sidebar = document.querySelector('.main-sidebar');

    // Check localStorage for saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
      body.classList.add('dark-mode');
      themeToggle.innerHTML = '<i class="fas fa-sun"></i>'; // Sun icon for dark mode
      sidebar?.classList.replace('sidebar-light-primary', 'sidebar-dark-primary'); // Update sidebar class
    } else {
      themeToggle.innerHTML = '<i class="fas fa-moon"></i>'; // Moon icon for light mode
      sidebar?.classList.replace('sidebar-dark-primary', 'sidebar-light-primary'); // Update sidebar class
    }

    // Toggle theme on button click
    themeToggle.addEventListener('click', () => {
      body.classList.toggle('dark-mode');

      // Toggle sidebar class
      if (body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        sidebar?.classList.replace('sidebar-light-primary', 'sidebar-dark-primary');
      } else {
        localStorage.setItem('theme', 'light');
        themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        sidebar?.classList.replace('sidebar-dark-primary', 'sidebar-light-primary');
      }
    });
  });
</script>

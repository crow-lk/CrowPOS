<!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="{{route('home')}}" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- SEARCH FORM -->

    {{-- <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form> --}}


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->

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

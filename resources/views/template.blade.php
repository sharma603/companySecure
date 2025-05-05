<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ScriptQube</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('admin_template_assets/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_template_assets/vendors/css/vendor.bundle.base.css')}}">
    
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('admin_template_assets/vendors/jvectormap/jquery-jvectormap.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_template_assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_template_assets/vendors/owl-carousel-2/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_template_assets/vendors/owl-carousel-2/owl.theme.default.min.css')}}">
    
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('admin_template_assets/css/style.css')}}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('admin_template_assets/images/favicon.png')}}" />

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- google font icon cdn -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

<!-- Ajax CDN -->
    <!-- Include jQuery library from CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- sweet alert cdn -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- Flatpickr date/time picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Nepali Clander css -->
    <link href="{{ asset('admin_lang/common/nepali_date/nepali.datepicker.css')}}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('admin_lang/common/nepali_date/nepali.datepicker.js')}}" type="text/javascript"></script>

    @section('script')
    <script>
        $(document).ready(function() {
            // Ensure Role & Permission submenu stays open when on those pages, but not on dashboard
            if ((window.location.pathname.includes('/roles') || 
                window.location.pathname.includes('/permissions') || 
                window.location.pathname.includes('/users')) && 
                !window.location.pathname.includes('/dashboard')) {
                $('#role-permission').addClass('show');
                $('[href="#role-permission"]').attr('aria-expanded', 'true');
            }
        });
    </script>
    @yield('additional_scripts')
    @endsection



    <style>
      .form-control{
          color:white !important;
      }
      
      /* Sidebar Logout Button Styles */
      .sidebar-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        background-color: #191c24;
        padding: 15px 0;
        border-top: 1px solid #2c2e33;
      }
      
      .sidebar-logout-link {
        display: flex;
        align-items: center;
        padding: 0.8rem 1.5rem;
        color: #6c7293;
        transition: all 0.3s ease;
      }
      
      .sidebar-logout-link:hover {
        color: #ffffff;
        background-color: rgba(255, 255, 255, 0.1);
      }
      
      .sidebar-logout-link .menu-icon {
        margin-right: 0.7rem;
      }
      
      /* Highlight important menu items */
      .highlight-menu {
        border-left: 3px solid #0090e7 !important;
      }
      
      .highlight-submenu {
        color: #0090e7 !important;
        font-weight: bold !important;
      }
    </style>

  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
          <a class="sidebar-brand brand-logo" href="{{ route('dashboard') }}"><img src="{{ asset('admin_template_assets/images/logo.svg')}}" alt="logo" /></a>
          <a class="sidebar-brand brand-logo-mini" href="{{ route('dashboard') }}"><img src="{{ asset('admin_template_assets/images/logo-mini.svg')}}" alt="logo" /></a>
        </div>
        <ul class="nav">
          <li class="nav-item profile">
            <div class="profile-desc">
              <div class="profile-pic">
                <div class="count-indicator">
                  <img class="img-xs rounded-circle " src="{{ asset('admin_template_assets/images/faces/face15.jpg')}}" alt="">
                  <span class="count bg-success"></span>
                </div>
                <div class="profile-name">
                  <h5 class="mb-0 font-weight-normal">Synergy</h5>
                  <span>Gold Member</span>
                </div>
              </div>
              <a href="#" id="profile-dropdown" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
              <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
                <a href="#" class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-settings text-primary"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-onepassword  text-info"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-calendar-today text-success"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1 text-small">To-do list</p>
                  </div>
                </a>
              </div>
            </div>
          </li>
          <li class="nav-item nav-category">
            <span class="nav-link">Navigation</span>
          </li>
          <li class="nav-item menu-items">
            <a class="nav-link" href="{{route('dashboard')}}">
              <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>

          <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('add-company') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-chart-bar"></i>
                </span>
                <span class="menu-title">Add Company</span>
            </a>
          </li>

          <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('reminders.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-bell-ring"></i>
                </span>
                <span class="menu-title">Reminders</span>
            </a>
          </li>

          <li class="nav-item menu-items">
            <a class="nav-link highlight-menu" data-bs-toggle="collapse" href="#role-permission" aria-expanded="{{ (Request::is('roles*') || Request::is('permissions*') || Request::is('users*')) && !Request::is('dashboard') ? 'true' : 'false' }}" aria-controls="role-permission">
                <span class="menu-icon">
                    <i class="mdi mdi-security"></i>
                </span>
                <span class="menu-title">Role & Permission</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ (Request::is('roles*') || Request::is('permissions*') || Request::is('users*')) && !Request::is('dashboard') ? 'show' : '' }}" id="role-permission">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('roles') || Request::is('roles/index') ? 'active' : '' }}" href="{{ route('roles.index') }}">Manage Roles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link highlight-submenu {{ Request::is('users/create-sub') ? 'active' : '' }}" href="{{ route('users.create.sub') }}">Create Sub-User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('permissions*') ? 'active' : '' }}" href="{{ route('permissions.index') }}">Permissions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">Manage Users</a>
                    </li>
                </ul>
            </div>
          </li>
          
         
         
        </ul>

        <!-- Logout button at bottom of sidebar -->
        <div class="sidebar-footer">
          <form method="POST" action="{{ route('logout') }}" class="w-100" id="sidebar-logout-form">
            @csrf
            <a href="javascript:;" onclick="document.getElementById('sidebar-logout-form').submit();" class="nav-link sidebar-logout-link">
              <span class="menu-icon">
                <i class="mdi mdi-logout text-danger"></i>
              </span>
              <span class="menu-title">Logout</span>
            </a>
          </form>
        </div>

      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar p-0 fixed-top d-flex flex-row">
          <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
            <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}"><img src="{{ asset('admin_template_assets/images/logo-mini.svg')}}" alt="logo" /></a>
          </div>
          <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
              <span class="mdi mdi-menu"></span>
            </button>
            <ul class="navbar-nav w-100">
              <li class="nav-item w-100">
                <form class="nav-link mt-2 mt-md-0 d-none d-lg-flex search">
                  <input type="text" class="form-control" placeholder="Search products">
                </form>
              </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right">
              <li class="nav-item nav-settings d-none d-lg-block">
                <a class="nav-link" href="#">
                  <i class="mdi mdi-view-grid"></i>
                </a>
              </li>
              <li class="nav-item dropdown border-left">
                <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="mdi mdi-email"></i>
                  <span class="count bg-success"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
                  <h6 class="p-3 mb-0">Messages</h6>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <img src="{{ asset('admin_template_assets/images/faces/face4.jpg')}}" alt="image" class="rounded-circle profile-pic">
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject ellipsis mb-1">Mark send you a message</p>
                      <p class="text-muted mb-0"> 1 Minutes ago </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <img src="{{ asset('admin_template_assets/images/faces/face2.jpg')}}" alt="image" class="rounded-circle profile-pic">
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject ellipsis mb-1">Cregh send you a message</p>
                      <p class="text-muted mb-0"> 15 Minutes ago </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <img src="{{ asset('admin_template_assets/images/faces/face3.jpg')}}" alt="image" class="rounded-circle profile-pic">
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject ellipsis mb-1">Profile picture updated</p>
                      <p class="text-muted mb-0"> 18 Minutes ago </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <p class="p-3 mb-0 text-center">4 new messages</p>
                </div>
              </li>
              <li class="nav-item dropdown border-left">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
                  <i class="mdi mdi-bell"></i>
                  <span class="count bg-danger"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                  <h6 class="p-3 mb-0">Notifications</h6>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-calendar text-success"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Event today</p>
                      <p class="text-muted ellipsis mb-0"> Just a reminder that you have an event today </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-settings text-danger"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Settings</p>
                      <p class="text-muted ellipsis mb-0"> Update dashboard </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-link-variant text-warning"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Launch Admin</p>
                      <p class="text-muted ellipsis mb-0"> New admin wow! </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <p class="p-3 mb-0 text-center">See all notifications</p>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" id="profileDropdown" href="#" data-bs-toggle="dropdown">
                  <div class="navbar-profile">
                    <img class="img-xs rounded-circle" src="{{ asset('admin_template_assets/images/faces/face15.jpg')}}" alt="">
                    <p class="mb-0 d-none d-sm-block navbar-profile-name">{{ Auth::user()->name ?? 'User' }}</p>
                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                  <h6 class="p-3 mb-0">Profile</h6>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-settings text-success"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Settings</p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a class="dropdown-item preview-item" href="javascript:;" onclick="document.getElementById('logout-form').submit();">
                      <div class="preview-thumbnail">
                        <div class="preview-icon bg-dark rounded-circle">
                          <i class="mdi mdi-logout text-danger"></i>
                        </div>
                      </div>
                      <div class="preview-item-content">
                        <p class="preview-subject mb-1">Log out</p>
                      </div>
                    </a>
                  </form>
                  <div class="dropdown-divider"></div>
                  <p class="p-3 mb-0 text-center">Advanced settings</p>
                </div>
              </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
              <span class="mdi mdi-format-line-spacing"></span>
            </button>
          </div>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper p-2 p-md-4">

            @yield('contents')
          
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              {{-- <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © SimpleSchoolSoft.com 2024</span> --}}
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('admin_template_assets/vendors/js/vendor.bundle.base.js')}}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('admin_template_assets/vendors/chart.js/Chart.min.js')}}"></script>
    <script src="{{ asset('admin_template_assets/vendors/progressbar.js/progressbar.min.js')}}"></script>
    <script src="{{ asset('admin_template_assets/vendors/jvectormap/jquery-jvectormap.min.js')}}"></script>
    <script src="{{ asset('admin_template_assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
    <script src="{{ asset('admin_template_assets/vendors/owl-carousel-2/owl.carousel.min.js')}}"></script>
    <script src="{{ asset('admin_template_assets/js/jquery.cookie.js')}}" type="text/javascript"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('admin_template_assets/js/off-canvas.js')}}"></script>
    <script src="{{ asset('admin_template_assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{ asset('admin_template_assets/js/misc.js')}}"></script>
    <script src="{{ asset('admin_template_assets/js/settings.js')}}"></script>
    <script src="{{ asset('admin_template_assets/js/todolist.js')}}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{ asset('admin_template_assets/js/dashboard.js')}}"></script>
    <!-- End custom js for this page -->
    <script src="{{ asset('admin_lang/common/nepali_date/nepali.datepicker.js')}}" type="text/javascript"></script>
  
    @yield('script')
  </body>
</html>


 <!--Nav Start-->
 <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
     <div class="container-fluid navbar-inner">
         <a href="../dashboard/index.html" class="navbar-brand">
             <!--Logo start-->
             <img src="{{ asset('assets/images/logo/Nextlogo.png') }}" alt="Nextrade Logo"
                 style="width: 200px; height: 45px;">
             <!--logo End-->
         </a>
         <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
             <i class="icon">
                 <svg width="20px" height="20px" viewBox="0 0 24 24">
                     <path fill="currentColor"
                         d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                 </svg>
             </i>
         </div>

         <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
             data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
             aria-label="Toggle navigation">
             <span class="navbar-toggler-icon">
                 <span class="mt-2 navbar-toggler-bar bar1"></span>
                 <span class="navbar-toggler-bar bar2"></span>
                 <span class="navbar-toggler-bar bar3"></span>
             </span>
         </button>
         <div class="collapse navbar-collapse" id="navbarSupportedContent">
             <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">

                 {{-- <li class="nav-item dropdown">
                     <a href="#" class="nav-link" id="notification-drop" data-bs-toggle="dropdown">
                         <svg width="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path
                                 d="M19.7695 11.6453C19.039 10.7923 18.7071 10.0531 18.7071 8.79716V8.37013C18.7071 6.73354 18.3304 5.67907 17.5115 4.62459C16.2493 2.98699 14.1244 2 12.0442 2H11.9558C9.91935 2 7.86106 2.94167 6.577 4.5128C5.71333 5.58842 5.29293 6.68822 5.29293 8.37013V8.79716C5.29293 10.0531 4.98284 10.7923 4.23049 11.6453C3.67691 12.2738 3.5 13.0815 3.5 13.9557C3.5 14.8309 3.78723 15.6598 4.36367 16.3336C5.11602 17.1413 6.17846 17.6569 7.26375 17.7466C8.83505 17.9258 10.4063 17.9933 12.0005 17.9933C13.5937 17.9933 15.165 17.8805 16.7372 17.7466C17.8215 17.6569 18.884 17.1413 19.6363 16.3336C20.2118 15.6598 20.5 14.8309 20.5 13.9557C20.5 13.0815 20.3231 12.2738 19.7695 11.6453Z"
                                 fill="currentColor"></path>
                             <path opacity="0.4"
                                 d="M14.0088 19.2283C13.5088 19.1215 10.4627 19.1215 9.96275 19.2283C9.53539 19.327 9.07324 19.5566 9.07324 20.0602C9.09809 20.5406 9.37935 20.9646 9.76895 21.2335L9.76795 21.2345C10.2718 21.6273 10.8632 21.877 11.4824 21.9667C11.8123 22.012 12.1482 22.01 12.4901 21.9667C13.1083 21.877 13.6997 21.6273 14.2036 21.2345L14.2026 21.2335C14.5922 20.9646 14.8734 20.5406 14.8983 20.0602C14.8983 19.5566 14.4361 19.327 14.0088 19.2283Z"
                                 fill="currentColor"></path>
                         </svg>
                         <span class="bg-danger dots"></span>
                     </a>
                     <div class="p-0 sub-drop dropdown-menu dropdown-menu-end" aria-labelledby="notification-drop">
                         <div class="m-0 shadow-none card">
                             <div class="py-3 card-header d-flex justify-content-between bg-primary">
                                 <div class="header-title">
                                     <h5 class="mb-0 text-white">All Notifications</h5>
                                 </div>
                             </div>
                             <div class="p-0 card-body">
                                 <a href="#" class="iq-sub-card">
                                     <div class="d-flex align-items-center">
                                         <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                             src="{{ asset('assets') }}/images/shapes/01.png" alt="">
                                         <div class="ms-3 w-100">
                                             <h6 class="mb-0 ">Emma Watson Bni</h6>
                                             <div class="d-flex justify-content-between align-items-center">
                                                 <p class="mb-0">95 MB</p>
                                                 <sm all class="float-end font-size-12">Just Now</sm>
                                             </div>
                                         </div>
                                     </div>
                                 </a>
                                 <a href="#" class="iq-sub-card">
                                     <div class="d-flex align-items-center">
                                         <div class="">
                                             <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                 src="{{ asset('assets') }}/images/shapes/02.png" alt="">
                                         </div>
                                         <div class="ms-3 w-100">
                                             <h6 class="mb-0 ">New customer is join</h6>
                                             <div class="d-flex justify-content-between align-items-center">
                                                 <p class="mb-0">Cyst Bni</p>
                                                 <small class="float-end font-size-12">5 days ago</small>
                                             </div>
                                         </div>
                                     </div>
                                 </a>
                                 <a href="#" class="iq-sub-card">
                                     <div class="d-flex align-items-center">
                                         <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                             src="{{ asset('assets') }}/images/shapes/03.png" alt="">
                                         <div class="ms-3 w-100">
                                             <h6 class="mb-0 ">Two customer is left</h6>
                                             <div class="d-flex justify-content-between align-items-center">
                                                 <p class="mb-0">Cyst Bni</p>
                                                 <small class="float-end font-size-12">2 days ago</small>
                                             </div>
                                         </div>
                                     </div>
                                 </a>
                                 <a href="#" class="iq-sub-card">
                                     <div class="d-flex align-items-center">
                                         <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                             src="{{ asset('assets') }}/images/shapes/04.png" alt="">
                                         <div class="w-100 ms-3">
                                             <h6 class="mb-0 ">New Mail from Fenny</h6>
                                             <div class="d-flex justify-content-between align-items-center">
                                                 <p class="mb-0">Cyst Bni</p>
                                                 <small class="float-end font-size-12">3 days ago</small>
                                             </div>
                                         </div>
                                     </div>
                                 </a>
                             </div>
                         </div>
                     </div>
                 </li> --}}

                 <li class="nav-item dropdown">
                     <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown"
                         role="button" data-bs-toggle="dropdown" aria-expanded="false">

                         <img src="{{ auth()->user()->profile_picture ? asset('storage/images/profilePicture/' . auth()->user()->profile_picture) : asset('assets/images/avatars/01.png') }}"
                             alt="User-Profile"
                             class="theme-color-default-img img-fluid avatar avatar-50 avatar-rounded">
                         <div class="caption ms-3 d-none d-md-block ">
                             <h6 class="mb-0 caption-title">{{ auth()->user()->full_name }}</h6>

                             <p class="mb-0 caption-sub-title">
                                 {{ session('RoleHasPermission')->roleName->name ?? '' }}</p>
                         </div>
                     </a>
                     <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                         <li><a class="dropdown-item"
                                 href="{{ route('admin:edit-user-profile', [auth()->user()->id]) }}">Edit Profile</a>
                         </li>
                         <li><a class="dropdown-item"
                                 href="{{ route('admin:view-user-profile', [auth()->user()->id]) }}">View Profile</a>
                         </li>
                         {{-- <li>
                             <hr class="dropdown-divider">
                         </li> --}}
                         <li><a class="dropdown-item" href="{{ route('admin:logout') }}">Logout</a></li>
                     </ul>
                 </li>
             </ul>
         </div>
     </div>
 </nav>
 <?php
     $segment = Request::segment(2);
     if($segment == 'dashboard'){
?>
 <div class="iq-navbar-header" style="height: 215px;">
     <div class="container-fluid iq-container">
         <div class="row">
             <div class="col-md-12">
                 <div class="flex-wrap d-flex justify-content-between align-items-center">
                     <div>
                         <h1>Hello {{ auth()->user()->full_name }}</h1>
                         <p>Welcome to Nextrade Industries Dashboard</p>
                     </div>

                 </div>
             </div>
         </div>
     </div>
     <div class="iq-header-img">
         <img src="{{ asset('assets') }}/images/dashboard/top-header.png" alt="header"
             class="theme-color-default-img img-fluid w-100 h-100 animated-scaleX">
         <img src="{{ asset('assets') }}/images/dashboard/top-header1.png" alt="header"
             class="theme-color-purple-img img-fluid w-100 h-100 animated-scaleX">
         <img src="{{ asset('assets') }}/images/dashboard/top-header2.png" alt="header"
             class="theme-color-blue-img img-fluid w-100 h-100 animated-scaleX">
         <img src="{{ asset('assets') }}/images/dashboard/top-header3.png" alt="header"
             class="theme-color-green-img img-fluid w-100 h-100 animated-scaleX">
         <img src="{{ asset('assets') }}/images/dashboard/top-header4.png" alt="header"
             class="theme-color-yellow-img img-fluid w-100 h-100 animated-scaleX">
         <img src="{{ asset('assets') }}/images/dashboard/top-header5.png" alt="header"
             class="theme-color-pink-img img-fluid w-100 h-100 animated-scaleX">
     </div>
 </div>
 <?php
     }
 ?>

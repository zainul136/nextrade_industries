@php
    $segment = Request::segment(2);
    $segmentSecondary = Request::segment(3);
@endphp
<aside class="sidebar sidebar-default navs-rounded-all ">
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="{{ route('admin:dashboard') }}" class="navbar-brand">
            <!--Logo start-->
            <img src="{{ asset('assets/images/logo/Nextlogo.png') }}" alt="Nextrade Logo"
                style="width: 200px; height: 45px;">
            <!--logo End-->
        </a>
        <!-- <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
        </div> -->
    </div>
    <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
            <!-- Sidebar Menu Start -->
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Home</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                <!-- //**************************************************************************************************************************************************// -->
                <li class="nav-item">
                    <a class="nav-link {{ $segment == 'dashboard' ? 'active' : '' }} " aria-current="page"
                        href="{{ route('admin:dashboard') }}">
                        <i class="icon">
                            <i class="fa fa-home"></i>
                        </i>
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>
                <li>
                    <hr class="hr-horizontal">
                </li>
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Manage</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->users == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'users' ? 'active' : '' }}" href="{{ route('admin:users') }}">
                            <i class="icon">
                                <i class="fa fa-users" style="font-size:15px;"></i>
                            </i>
                            <span class="item-name">Users</span>
                        </a>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->roles == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'roles' ? 'active' : '' }}" href="{{ route('admin:roles') }}">
                            <i class="icon">
                                <svg width="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4"
                                        d="M12.0865 22C11.9627 22 11.8388 21.9716 11.7271 21.9137L8.12599 20.0496C7.10415 19.5201 6.30481 18.9259 5.68063 18.2336C4.31449 16.7195 3.5544 14.776 3.54232 12.7599L3.50004 6.12426C3.495 5.35842 3.98931 4.67103 4.72826 4.41215L11.3405 2.10679C11.7331 1.96656 12.1711 1.9646 12.5707 2.09992L19.2081 4.32684C19.9511 4.57493 20.4535 5.25742 20.4575 6.02228L20.4998 12.6628C20.5129 14.676 19.779 16.6274 18.434 18.1581C17.8168 18.8602 17.0245 19.4632 16.0128 20.0025L12.4439 21.9088C12.3331 21.9686 12.2103 21.999 12.0865 22Z"
                                        fill="currentColor"></path>
                                    <path
                                        d="M11.3194 14.3209C11.1261 14.3219 10.9328 14.2523 10.7838 14.1091L8.86695 12.2656C8.57097 11.9793 8.56795 11.5145 8.86091 11.2262C9.15387 10.9369 9.63207 10.934 9.92906 11.2193L11.3083 12.5451L14.6758 9.22479C14.9698 8.93552 15.448 8.93258 15.744 9.21793C16.041 9.50426 16.044 9.97004 15.751 10.2574L11.8519 14.1022C11.7049 14.2474 11.5127 14.3199 11.3194 14.3209Z"
                                        fill="currentColor"></path>
                                </svg>
                            </i>
                            <span class="item-name">Roles</span>
                        </a>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->warehouses == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'warehouses' ? 'active' : '' }}"
                            href="{{ route('admin:warehouses') }}">
                            <i class="icon">
                                <i class="fa fa-warehouse" style="font-size:15px;"></i>
                            </i>
                            <span class="item-name">Warehouses</span>
                        </a>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->customers == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'customers' ? 'active' : '' }}"
                            href="{{ route('admin:customers') }}">
                            <i class="icon">
                                <i class="fa fa-walking" style="font-size:20px;"></i>
                            </i>
                            <span class="item-name">Customers</span>
                        </a>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->suppliers == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'suppliers' ? 'active' : '' }}"
                            href="{{ route('admin:suppliers') }}">
                            <i class="icon">
                                <i class="fa fa-shipping-fast" style="font-size:18px;"></i>
                            </i>
                            <span class="item-name">Suppliers</span>
                        </a>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->cgt_gardes == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'cgt-grades' ? 'active' : '' }}"
                            href="{{ route('admin:cgt_grades') }}">
                            <i class="icon">
                                <i class="fa fa-hashtag" style="font-size:18px;"></i>
                            </i>
                            <span class="item-name">CGT Grades</span>
                        </a>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->nt_grades == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'nt-grades' ? 'active' : '' }}"
                            href="{{ route('admin:nt_grades') }}">
                            <i class="icon">
                                <i class="fa fa-hashtag" style="font-size:18px;"></i>
                            </i>
                            <span class="item-name">NT Grades</span>
                        </a>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->colors == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'colors' ? 'active' : '' }}"
                            href="{{ route('admin:colors') }}">
                            <i class="icon">
                                <i class="fa fa-palette" style="font-size:18px;"></i>
                            </i>
                            <span class="item-name">Colors</span>
                        </a>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->product_types == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'product-type' ? 'active' : '' }}"
                            href="{{ route('admin:product.types') }}">
                            <i class="icon">
                                <i class="fas fa-swatchbook"style="font-size:18px;"></i>
                            </i>
                            <span class="item-name">Product Types</span>
                        </a>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->scan_in == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'scan-in' ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#sidebar-scan-in" role="button" aria-expanded="false"
                            aria-controls="sidebar-user">
                            <i class="icon">
                                <i class="fas fa-qrcode" style="font-size: 18px;"></i>
                            </i>
                            <span class="item-name">Scan in</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-scan-in" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ $segmentSecondary == 'new-scan-in' ? 'active' : '' }}"
                                    href="{{ route('admin:newScanIn') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8"
                                                    fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> U </i>
                                    <span class="item-name">New Scan In </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $segmentSecondary == 'scanInLogs' ? 'active' : '' }}"
                                    href="{{ route('admin:scanInLogs') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8"
                                                    fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> U </i>
                                    <span class="item-name">Scan In Details</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $segmentSecondary == 'scanInInventory' ? 'active' : '' }}"
                                    href="{{ route('admin:scanInInventory') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8"
                                                    fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> U </i>
                                    <span class="item-name">Scan in Summary</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->scan_out == 1)
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'scan-out' ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#sidebar-scan-out" role="button" aria-expanded="false"
                            aria-controls="sidebar-user">
                            <i class="icon">
                                <i class="fas fa-qrcode" style="font-size: 18px;"></i>
                            </i>
                            <span class="item-name">Scan Out</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-scan-out" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ $segmentSecondary == 'new-scan-out' ? 'active' : '' }} "
                                    href="{{ route('admin:newScanOut') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8"
                                                    fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> U </i>
                                    <span class="item-name">New Scan Out</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $segmentSecondary == 'scanOutLogs' ? 'active' : '' }}"
                                    href="{{ route('admin:scanOutLogs') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8"
                                                    fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> U </i>
                                    <span class="item-name">Scan Out Details</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif

                @if (session('RoleReportsPermission') != null &&
                        (session('RoleReportsPermission')->inventory_report == 1 ||
                            session('RoleReportsPermission')->cgt_summary == 1 ||
                            session('RoleReportsPermission')->nt_summary == 1 ||
                            session('RoleReportsPermission')->color_summary == 1 ||
                            session('RoleReportsPermission')->commulative_cgt == 1 ||
                            session('RoleReportsPermission')->commulative_nt == 1 ||
                            session('RoleReportsPermission')->customer_summary == 1 ||
                            session('RoleReportsPermission')->nexpac_report == 1 ||
                            session('RoleReportsPermission')->internal_report == 1))
                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'reports' ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#sidebar-inventory" role="button" aria-expanded="false"
                            aria-controls="sidebar-user">
                            <i class="icon">
                                <i class="fa fa-file" style="font-size: 18px;"></i>
                            </i>
                            <span class="item-name">Reports</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-inventory" data-bs-parent="#sidebar-menu">
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->inventory_report == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'inventory-summary' ? 'active' : '' }}"
                                        href="{{ route('admin:inventorySummary') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">Inventory Summary</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->cgt_summary == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'cgt-summary' ? 'active' : '' }}"
                                        href="{{ route('admin:inventory.cgtSummary') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">CGT Grade Summary</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->nt_summary == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'nt-summary' ? 'active' : '' }} "
                                        href="{{ route('admin:inventory.ntSummary') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">NT Grade Summary</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->color_summary == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'color-summary' ? 'active' : '' }}"
                                        href="{{ route('admin:inventory.color_summary') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">Colors Summary</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->commulative_cgt == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'cgt-comulative-summary' ? 'active' : '' }}"
                                        href="{{ route('admin:inventory.cgtComulativeSummary') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">Cumulative CGT Grade</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->commulative_nt == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'nt-comulative-summary' ? 'active' : '' }}"
                                        href="{{ route('admin:inventory.ntComulativeSummary') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">Cumulative NT Grade</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->customer_summary == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'customer-summary-report' ? 'active' : '' }}"
                                        href="{{ route('admin:inventory.customerSummaryReport') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">Customer Report</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->nexpac_report == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'nexpac-report' ? 'active' : '' }}"
                                        href="{{ route('admin:inventory.nexpacReport') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">NEXPAC Report</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->internal_report == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'internal-report' ? 'active' : '' }}"
                                        href="{{ route('admin:inventory.internalReport') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">Internal Report</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->billing_report == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'billing-report' ? 'active' : '' }}"
                                        href="{{ route('admin:inventory.billingReport') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">Billing Report</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('RoleReportsPermission') != null && session('RoleReportsPermission')->pnl_report == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $segmentSecondary == 'pnl-report' ? 'active' : '' }}"
                                        href="{{ route('admin:inventory.pnl_report') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor"></circle>
                                                </g>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon"> U </i>
                                        <span class="item-name">PNL Report</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if (session('RoleHasPermission') != null && session('RoleHasPermission')->orders == 1)
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ $segment == 'orders' ? 'active' : '' }}"
                            href="{{ route('admin:orders') }}">
                            <i class="icon">
                                <i class="fa fa-truck-loading" style="font-size:18px;"></i>
                            </i>
                            <span class="item-name">Orders</span>
                        </a>
                    </li> --}}

                    <li class="nav-item">
                        <a class="nav-link {{ $segment == 'orders' ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#sidebar-orders" role="button" aria-expanded="false"
                            aria-controls="sidebar-user">
                            <i class="icon">
                                <i class="fas fa-qrcode" style="font-size: 18px;"></i>
                            </i>
                            <span class="item-name">Orders</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-orders" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ $segmentSecondary == 'active-orders' ? 'active' : '' }}"
                                    href="{{ route('admin:orders') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8"
                                                    fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> U </i>
                                    <span class="item-name">Active Orders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $segmentSecondary == 'pending-orders' ? 'active' : '' }}"
                                    href="{{ route('admin:pendingOrders') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8"
                                                    fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> U </i>
                                    <span class="item-name">Pending Orders</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ $segment == 'sampleRequests' ? 'active' : '' }}"
                        href="{{ route('admin:sampleRequests') }}">
                        <i class="icon">
                            <i class="fa fa-file" style="font-size: 18px;"></i>
                        </i>
                        <span class="item-name">Sample Requests</span>
                    </a>
                </li>
            </ul>
            <br><br><br><br><br><br><br>

            <!-- Sidebar Menu End -->
        </div>
    </div>
    <div class="sidebar-footer"></div>
</aside>

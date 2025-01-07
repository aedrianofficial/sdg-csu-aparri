<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>@yield('title', 'Reviewer')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard">
    <!--end::Primary Meta Tags--><!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->

    <link rel="icon" href="{{ asset('assets/website/images/favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"><!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">

    {{-- sweet alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ asset('assets/auth/css/style.css') }}">
    <!--leaflet-->
    <link rel="stylesheet" href="{{ asset('assets/auth/css/leaflet.css') }}"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="{{ asset('assets/auth/css/Control.Geocoder.css') }}" />

    <link href="{{ asset('assets/auth/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        /* Dark mode styles for Select2 */
        [data-bs-theme="dark"] .select2-container--default .select2-selection--single {
            background-color: #343a40;
            /* Dark mode background */
            color: #ffffff;
            /* Dark mode text color */
            border: 1px solid #495057;
            /* Dark mode border color */
        }

        [data-bs-theme="dark"] .select2-container--default .select2-selection--multiple {
            background-color: #343a40;
            /* Dark mode background */
            color: #ffffff;
            /* Dark mode text color */
        }

        [data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff;
            /* Dark mode text color for selected item */
        }

        [data-bs-theme="dark"] .select2-container--default .select2-results__option {
            color: #ffffff;
            /* Dark mode text color for options */
            background-color: #343a40;
            /* Dark mode background for options */
        }

        [data-bs-theme="dark"] .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #495057;
            /* Dark mode hover background for options */
        }

        /* Dark mode styles for Select2 tags */
        [data-bs-theme="dark"] .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #495057;
            /* Dark mode tag background */
            color: #ffffff;
            /* Dark mode tag text color */
        }

        /* Light mode styles for Select2 tags */
        [data-bs-theme="light"] .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e9ecef;
            /* Light mode tag background */
            color: #495057;
            /* Light mode tag text color */
        }

        /* Tag removal button */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #ffffff;
            /* Icon color for dark mode */
        }

        [data-bs-theme="light"] .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #495057;
            /* Icon color for light mode */
        }

        .unread-notification {
            background-color: #e4e2de;
            /* Background color for unread notifications */
        }

        .notification-message {
            white-space: normal;
        }
    </style>
    @yield('styles')
</head> <!--end::Head--> <!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i> </a> </li>

                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                    <!--end::Navbar Search-->

                    {{-- <li class="nav-item dropdown"> <button
                            class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center"
                            id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown"
                            data-bs-display="static">Toggle theme <span class="theme-icon-active"> <i
                                    class="my-1"></i> </span>
                            <span class="d-lg-none ms-2" id="bd-theme-text">Toggle theme</span> </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text"
                            style="--bs-dropdown-min-width: 8rem;">
                            <li> <button type="button" class="dropdown-item d-flex align-items-center"
                                    data-bs-theme-value="light" aria-pressed="false"> <i
                                        class="bi bi-sun-fill me-2"></i>
                                    Light
                                    <i class="bi bi-check-lg ms-auto d-none"></i> </button> </li>
                            <li> <button type="button" class="dropdown-item d-flex align-items-center"
                                    data-bs-theme-value="dark" aria-pressed="false"> <i
                                        class="bi bi-moon-fill me-2"></i>
                                    Dark
                                    <i class="bi bi-check-lg ms-auto d-none"></i> </button> </li>
                            <li> <button type="button" class="dropdown-item d-flex align-items-center"
                                    data-bs-theme-value="auto" aria-pressed="true"> <i
                                        class="bi bi-circle-fill-half-stroke me-2"></i>
                                    Auto
                                    <i class="bi bi-check-lg ms-auto d-none"></i> </button> </li>
                        </ul>
                    </li> --}}

                    <!--begin::Notifications Dropdown Menu for Reviewer-->
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                            <i class="bi bi-bell-fill"></i>
                            @php
                                $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                            @endphp
                            @if ($unreadCount > 0)
                                <span class="navbar-badge badge text-bg-warning">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow">
                            <span class="dropdown-item dropdown-header text-primary">
                                {{ $unreadCount }} Unread Notifications
                            </span>
                            <div class="dropdown-divider"></div>

                            @forelse (auth()->user()->notifications()->latest()->take(4)->get() as $notification)
                                @php
                                    $data = json_decode($notification->data, true);
                                    $bgColor = $notification->read_at ? 'bg-light' : 'unread-notification';

                                    // Determine the appropriate route based on type and status for reviewers
                                    $route = '';
                                    if ($data['type'] === 'project') {
                                        if ($data['status'] === 'submitted for review') {
                                            $route = route('reviewer.projects.show', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif ($data['status'] === 'resubmitted for review') {
                                            $route = route('reviewer.projects.show', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        }
                                    } elseif ($data['type'] === 'report') {
                                        if ($data['status'] === 'submitted for review') {
                                            $route = route('reviewer.reports.show', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif ($data['status'] === 'resubmitted for review') {
                                            $route = route('reviewer.reports.show', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        }
                                    } 
                                    elseif ($data['type'] === 'status_report') {
                                        // Changed from 'report' to 'status_report'
                                        if ($data['status'] === 'submitted for review') {
                                            $route = route('reviewer.status_reports.show_project', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif ($data['status'] === 'resubmitted for review') {
                                            $route = route('reviewer.status_reports.show_project', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        
                                        }
                                    } elseif ($data['type'] === 'terminal_report') {
                                        // Changed from 'report' to 'terminal_report'
                                        if ($data['status'] === 'submitted for review') {
                                            $route = route('reviewer.terminal_reports.show_project', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif ($data['status'] === 'resubmitted for review') {
                                            $route = route('reviewer.terminal_reports.show_project', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        
                                        }
                                    }
                                    elseif ($data['type'] === 'research') {
                                        if ($data['status'] === 'submitted for review') {
                                            $route = route('reviewer.research.show', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif ($data['status'] === 'resubmitted for review') {
                                            $route = route('reviewer.research.show', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        }
                                    }
                                @endphp

                                <a href="{{ $route }}"
                                    class="dropdown-item d-flex align-items-start {{ $bgColor }}"
                                    onclick="markAsRead('{{ $notification->id }}')">

                                    <i class="bi {{ $data['icon'] ?? 'bi-bell' }} me-2"></i>
                                    <div class="flex-grow-1" style="white-space: normal;">
                                        {{ $data['message'] }}
                                        <small
                                            class="text-secondary float-end">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>

                                <div class="dropdown-divider"></div>
                            @empty
                                <span class="dropdown-item text-center text-secondary">No new notifications</span>
                            @endforelse

                            <div class="dropdown-divider"></div>
                            <a href="{{ route('reviewer.notifications') }}"
                                class="dropdown-item text-center text-primary">
                                <strong>See All Notifications</strong>
                            </a>
                        </div>
                    </li>
                    <!--end::Notifications Dropdown Menu-->


                    <!--begin::Fullscreen Toggle-->
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i
                                data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i
                                data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i>
                        </a> </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown">
                            @if (!(Auth::user()->userImage && Auth::user()->userImage->existsOnDisk()))
                                <img src="{{ asset('assets/website/images/user-png.png') }}" alt="image"
                                    class="user-image rounded-circle shadow">
                            @else
                                <img src="{{ Auth::user()->userImage->image_path }}" alt="image"
                                    class="user-image rounded-circle shadow">
                            @endif
                            <span class="d-none d-md-inline">{{ Auth::user()->first_name }}
                                {{ Auth::user()->last_name }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <!--begin::User Image-->
                            <li class="user-header text-bg-primary">
                                @if (!(Auth::user()->userImage && Auth::user()->userImage->existsOnDisk()))
                                    <img src="{{ asset('assets/website/images/user-png.png') }}"
                                        alt="Default User Image" class="rounded-circle" width="150">
                                @else
                                    <img src="{{ Auth::user()->userImage->image_path }}" alt="User Image"
                                        class="rounded-circle" width="150">
                                @endif
                                <p>
                                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} - Reviewer
                                    <small>{{Auth::user()->college->name}}</small>
                                </p>
                            </li>
                            <!--end::User Image-->

                            <li class="user-footer">
                                <a href="{{ route('reviewer.profile.show', ['id' => Auth::user()->id]) }}"
                                    class="btn btn-default btn-flat">Profile</a>

                                <!-- Sign Out Form -->
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-default btn-flat float-end"
                                        style="border: none; background: none; cursor: pointer;">
                                        <i class="mdi mdi-logout me-2 text-primary"></i> Sign out
                                    </button>
                                </form>
                            </li>
                            <!--end::Menu Footer-->
                        </ul>

                    </li> <!--end::User Menu Dropdown-->
                </ul> <!--end::End Navbar Links-->

            </div> <!--end::Container-->
        </nav> <!--end::Header--> <!--begin::Sidebar-->
        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow">
            <!--begin::Sidebar Brand-->
            <div class="sidebar-brand">
                <a href="./index.html" class="brand-link">
                    <img src="{{ asset('assets/website/images/csu-sdg-logo.png') }}" alt="Logo"
                        class="brand-image opacity-75">
                    <span class="brand-text fw-light">SDG CSU-APARRI</span>
                </a>
            </div>
            <!--end::Sidebar Brand-->

            <!--begin::Sidebar Wrapper-->
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <!--begin::Sidebar Menu-->
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu"
                        data-accordion="false">

                        <li class="nav-item">
                            <a href="{{ route('reviewer.dashboard') }}" class="nav-link">
                                <i class="nav-icon bi bi-palette"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item {{ request()->routeIs('reviewer.projects.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('reviewer.projects.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-box-seam-fill"></i>
                                <p>
                                    Projects/Programs
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.projects.under_review') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.projects.under_review') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Under Review</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.projects.reviewed_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.projects.reviewed_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Reviewed
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.projects.needchanges_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.projects.needchanges_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Need Changes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.projects.rejected') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.projects.rejected') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Rejected</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('reviewer.status_reports.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('reviewer.status_reports.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-box-seam-fill"></i>
                                <p>
                                    Status Reports
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.status_reports.under_review') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.status_reports.under_review') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Under Review</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.status_reports.reviewed_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.reports.reviewed_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Reviewed</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.status_reports.needchanges_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.status_reports.needchanges_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Need Changes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.status_reports.rejected_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.status_reports.rejected_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Rejected</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ request()->routeIs('reviewer.terminal_reports.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('reviewer.terminal_reports.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-box-seam-fill"></i>
                                <p>
                                    Terminal Reports
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.terminal_reports.under_review') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.terminal_reports.under_review') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Under Review</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.terminal_reports.reviewed_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.reports.reviewed_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Reviewed</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.terminal_reports.needchanges_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.terminal_reports.needchanges_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Need Changes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.terminal_reports.rejected_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.terminal_reports.rejected_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Rejected</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('reviewer.research.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('reviewer.research.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-box-seam-fill"></i>
                                <p>
                                    Research & Extension
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.research.under_review') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.research.under_review') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Under Review</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.research.reviewed_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.research.reviewed_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Reviewed</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.research.needchanges_list') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.research.needchanges_list') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Need Changes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reviewer.research.rejected') }}"
                                        class="nav-link {{ request()->routeIs('reviewer.research.rejected') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Rejected</p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reviewer.activity_logs') }}" class="nav-link">
                                <i class="nav-icon bi bi-palette"></i>
                                <p>Activity Logs</p>
                            </a>
                        </li>
                    </ul> <!--end::Sidebar Menu-->
                </nav>
            </div> <!--end::Sidebar Wrapper-->
        </aside> <!--end::Sidebar-->
        <!--end::Sidebar--> <!--begin::App Main-->

        <main class="app-main"> <!--begin::App Content Header-->
            @yield('content')
        </main>

        <!--end::App Main--> <!--begin::Footer-->
        <footer class="app-footer"> <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Web Development</div> <!--end::To the end-->
            <!--begin::Copyright--> <strong>
                Copyright &copy;
                <a href="" class="text-decoration-none">CSU-Aparri</a>.
            </strong>
            All rights reserved.
            <!--end::Copyright-->
        </footer>
        <!--end::Footer-->
    </div> <!--end::App Wrapper-->
    <!--begin::Script-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('assets/auth/js/script.js') }}"></script>
    <script src="{{ asset('assets/auth/vendors/js/vendor.bundle.base.js') }}"></script>

    <!--leaflet-->
    <script src="{{ asset('assets/auth/js/leaflet.js') }}" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
    <script src="{{ asset('assets/auth/js/Control.Geocoder.js') }}"></script>
    <script src="{{ asset('assets/auth/js/select2.min.js') }}"></script>
    <!--fontawesome-->
    <script src="https://kit.fontawesome.com/f60b315caa.js" crossorigin="anonymous"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->

    <script>
        const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
        const Default = {
            scrollbarTheme: "os-theme-light",
            scrollbarAutoHide: "leave",
            scrollbarClickScroll: true,
        };
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (
                sidebarWrapper &&
                typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined"
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script> <!--end::OverlayScrollbars Configure--> <!-- OPTIONAL SCRIPTS --> <!-- sortablejs -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
        integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ=" crossorigin="anonymous"></script> <!-- sortablejs -->
    <script>
        const connectedSortables =
            document.querySelectorAll(".connectedSortable");
        connectedSortables.forEach((connectedSortable) => {
            let sortable = new Sortable(connectedSortable, {
                group: "shared",
                handle: ".card-header",
            });
        });

        const cardHeaders = document.querySelectorAll(
            ".connectedSortable .card-header",
        );
        cardHeaders.forEach((cardHeader) => {
            cardHeader.style.cursor = "move";
        });
    </script>

    <link rel="stylesheet" href="{{ asset('assets/auth/vendors/css/vendor.bundle.base.css') }}">
    <script>
        // Color Mode Toggler
        (() => {
            "use strict";

            const storedTheme = localStorage.getItem("theme");

            const getPreferredTheme = () => {
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            };

            const setTheme = function(theme) {
                if (theme === "auto" && window.matchMedia("(prefers-color-scheme: dark)").matches) {
                    document.documentElement.setAttribute("data-bs-theme", "dark");
                } else {
                    document.documentElement.setAttribute("data-bs-theme", theme);
                }

                // Update Select2 styles on theme change
                updateSelect2Styles(theme);
            };

            const updateSelect2Styles = (theme) => {
                const select2Results = document.querySelectorAll(
                    '.select2-container--default .select2-results__option');
                select2Results.forEach(option => {
                    if (theme === 'dark') {
                        option.style.color = '#ffffff'; // Dark mode text color
                        option.style.backgroundColor = '#343a40'; // Dark mode background color
                    } else {
                        option.style.color = '#495057'; // Light mode text color
                        option.style.backgroundColor = ''; // Reset to default
                    }
                });
            };

            setTheme(getPreferredTheme());

            const showActiveTheme = (theme, focus = false) => {
                // ... (existing implementation)
            };

            window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", () => {
                if (storedTheme !== "light" || storedTheme !== "dark") {
                    setTheme(getPreferredTheme());
                }
            });

            window.addEventListener("DOMContentLoaded", () => {
                showActiveTheme(getPreferredTheme());

                for (const toggle of document.querySelectorAll("[data-bs-theme-value]")) {
                    toggle.addEventListener("click", () => {
                        const theme = toggle.getAttribute("data-bs-theme-value");
                        localStorage.setItem("theme", theme);
                        setTheme(theme);
                        showActiveTheme(theme, true);
                    });
                }
            });
        })();
    </script>
    <script>
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Reload the page to update notifications count and status
                        location.reload();
                    } else {
                        console.error('Failed to mark notification as read:', response.statusText);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
    <script>
        @if (Session::has('alert-success'))
            Swal.fire({
                title: "Good job!",
                text: "{{ Session::get('alert-success') }}",
                icon: "success"
            });
        @endif
    </script>


    @yield('scripts')
    <!--end::Script-->
</body><!--end::Body-->

</html>

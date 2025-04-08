<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
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
    <link rel="icon" type="image/png" href="{{ asset('assets/website/images/csulogo.png') }}">
    {{-- sweet alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ asset('assets/auth/css/style.css') }}">

    <!--leaflet-->
    <link rel="stylesheet" href="{{ asset('assets/auth/css/leaflet.css') }}"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="{{ asset('assets/auth/css/Control.Geocoder.css') }}" />

    <link href="{{ asset('assets/auth/css/select2.min.css') }}" rel="stylesheet" />
    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
                            data-bs-display="static">Toggle
                            theme <span class="theme-icon-active"> <i class="my-1"></i> </span>
                            <span class="d-lg-none ms-2" id="bd-theme-text"></span> </button>
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
                    <!--begin::Notifications Dropdown Menu for Admin-->
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

                                    // Determine the appropriate route based on type and status for admin
                                    $route = '';
                                    if ($data['type'] === 'project') {
                                        if ($data['status'] === 'request_changes') {
                                            $route = route('projects.need_changes', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif ($data['status'] === 'rejected') {
                                            $route = route('projects.rejected', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif (in_array($data['status'], ['approved', 'published', 'reviewed'])) {
                                            $route = route('projects.show', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        }
                                    } elseif ($data['type'] === 'status_report') {
                                        // Changed from 'report' to 'status_report'
                                        if ($data['status'] === 'request_changes') {
                                            $route = route('auth.status_reports.projects.need_changes', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif ($data['status'] === 'rejected') {
                                            $route = route('auth.status_reports.projects.rejected', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif (in_array($data['status'], ['approved', 'published', 'reviewed'])) {
                                            $route = route('auth.status_reports.show_research_published', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        }
                                    } elseif ($data['type'] === 'terminal_report') {
                                        // Changed from 'report' to 'terminal_report'
                                        if ($data['status'] === 'request_changes') {
                                            $route = route('auth.terminal_reports.projects.need_changes', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif ($data['status'] === 'rejected') {
                                            $route = route('auth.terminal_reports.projects.rejected', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif (in_array($data['status'], ['approved', 'published', 'reviewed'])) {
                                            $route = route('auth.terminal_reports.show_research_published', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        }
                                    } elseif ($data['type'] === 'research') {
                                        if ($data['status'] === 'request_changes') {
                                            $route = route('research.need_changes', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif ($data['status'] === 'rejected') {
                                            $route = route('research.rejected', [
                                                'id' => $notification->related_id,
                                                'notification_id' => $notification->id,
                                            ]);
                                        } elseif (in_array($data['status'], ['approved', 'published', 'reviewed'])) {
                                            $route = route('research.show', [
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
                            <a href="{{ route('admin.notifications') }}"
                                class="dropdown-item text-center text-primary">
                                <strong>See All Notifications</strong>
                            </a>
                        </div>



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
                                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} - Admin
                                    <small>Member since
                                        {{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('F Y') }}</small>
                                </p>
                            </li>
                            <!--end::User Image-->

                            <!--begin::Menu Footer-->
                            <li class="user-footer">
                                <a href="{{ route('auth.profile.show', ['id' => Auth::user()->id]) }}"
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
                <a href="{{ route('website.home') }}" class="brand-link">
                    <img src="{{ asset('assets/website/images/csulogo.png') }}" alt="Logo"
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
                            <a href="{{ route('auth.dashboard') }}"
                                class="nav-link {{ request()->routeIs('auth.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-house-door"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item {{ request()->routeIs('projects.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-briefcase"></i>
                                <p>
                                    Projects/Programs
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('projects.create') }}"
                                        class="nav-link {{ request()->routeIs('projects.create') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>Create Project</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('projects.index') }}"
                                        class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-list-ul"></i> <!-- Changed to list icon -->
                                        <p>All Projects</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('projects.my_projects') }}"
                                        class="nav-link {{ request()->routeIs('projects.my_projects') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-person-lines-fill"></i>
                                        <p>My Projects</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('auth.status_reports.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('auth.status_reports.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-file-earmark-bar-graph"></i> <!-- Changed to reports icon -->
                                <p>
                                    Status Reports
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="{{ route('auth.status_reports.index') }}"
                                        class="nav-link {{ request()->routeIs('auth.status_reports.index') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-list-ul"></i> <!-- Changed to list icon -->
                                        <p>All Status Reports</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('auth.status_reports.my_reports') }}"
                                        class="nav-link {{ request()->routeIs('auth.status_reports.my_reports') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-person-lines-fill"></i>
                                        <p>My Status Reports</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ request()->routeIs('auth.terminal_reports.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('auth.terminal_reports.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-file-earmark-bar-graph"></i> <!-- Changed to reports icon -->
                                <p>
                                    Terminal Reports
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('auth.terminal_reports.index') }}"
                                        class="nav-link {{ request()->routeIs('auth.terminal_reports.index') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-list-ul"></i> <!-- Changed to list icon -->
                                        <p>All Terminal Reports</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('auth.terminal_reports.my_reports') }}"
                                        class="nav-link {{ request()->routeIs('auth.terminal_reports.my_reportscontributor') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-person-lines-fill"></i>
                                        <p>My Terminal Reports</p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class="nav-item {{ request()->routeIs('research.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('research.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-journal-text"></i> <!-- Changed to journal icon -->
                                <p>
                                    Research & Extensions
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('research.create') }}"
                                        class="nav-link {{ request()->routeIs('research.create') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>Create Research</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('research.index') }}"
                                        class="nav-link {{ request()->routeIs('research.index') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-list-ul"></i> <!-- Changed to list icon -->
                                        <p>All Research</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('research.my_research') }}"
                                        class="nav-link {{ request()->routeIs('research.my_research') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-person-lines-fill"></i>
                                        <p>My Research</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('auth.activity_logs.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('auth.activity_logs.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-clock"></i> <!-- Changed to clock icon -->
                                <p>
                                    Activity Logs
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('auth.activity_logs.all_activity_logs') }}"
                                        class="nav-link {{ request()->routeIs('auth.activity_logs.all_activity_logs') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-list-task"></i>
                                        <p>All Activity Logs</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('auth.activity_logs.my_activity_logs') }}"
                                        class="nav-link {{ request()->routeIs('auth.activity_logs.my_activity_logs') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-person-lines-fill"></i>
                                        <p>My Activity Logs</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>User Management</p>
                            </a>
                        </li>
                    </ul> <!--end::Sidebar Menu-->
                </nav>
            </div>
            <!--end::Sidebar Wrapper-->

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
    <!--end::Required Plugin(AdminLTE)-->

    <!--fontawesome-->
    <script src="https://kit.fontawesome.com/f60b315caa.js" crossorigin="anonymous"></script>

    <!--begin::OverlayScrollbars Configure--d>
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
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
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

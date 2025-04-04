<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CSU-Aparri SDG')</title>

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">


    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/website/css/home.min.css') }}">

    <!--leaflet-->
    <link rel="stylesheet" href="{{ asset('assets/auth/css/leaflet.css') }}"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="{{ asset('assets/auth/css/Control.Geocoder.css') }}" />
    @yield('styles')
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <!-- My Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
            <div class="container">
                <a href="{{ route('website.home2') }}" class="navbar-brand">
                    <img src="{{ asset('assets/website/images/csu-sdg-logo.png') }}" alt="Logo" class="brand-image"
                        style="opacity: .8">
                    <span class="brand-text font-weight-light d-none d-md-inline">SDG CSU-APARRI</span>
                    <!-- Hidden on mobile -->
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('website.home2') }}" class="nav-link">Home</a>
                        </li>
                       
                        <li class="nav-item">
                            <a href="{{ route('website.sdg_project_main2') }}" class="nav-link">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('website.sdg_research_main2') }}" class="nav-link">Research &
                                Extensions</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('website.yearly_overview') }}" class="nav-link">Yearly Overview</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="#" class="nav-link">About Us</a>
                        </li> --}}
                    </ul>
                </div>

                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                    {{-- Check if user is authenticated --}}
                    @if (Auth::check())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if (!(Auth::user()->userImage && file_exists(public_path('images/users/' . basename(Auth::user()->userImage->image_path)))))
                                <img src="{{ asset('assets/website/images/user-png.png') }}" alt="Default User Image" 
                                    class="rounded-circle" width="30" height="30">
                            @else
                                <img src="{{ Auth::user()->userImage->image_path }}" alt="User Image" 
                                    class="rounded-circle" width="30" height="30">
                            @endif
                            <span class="ms-2">
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                            </span>
                            </a>
                        
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                <a class="dropdown-item"
                                    href="{{ route('user.profile.show', ['id' => Auth::user()->id]) }}">Profile</a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link">Login</a>
                        </li>
                    @endif

                    <!-- Theme toggle dropdown -->
                    {{-- <li class="nav-item dropdown">
                        <button
                            class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center"
                            id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown"
                            data-bs-display="static">
                            <i class="bi bi-toggle-on" id="theme-toggle-icon" style="font-size: 1.5rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text"
                            style="--bs-dropdown-min-width: 8rem;">
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center"
                                    data-bs-theme-value="light" aria-pressed="false">
                                    <i class="bi bi-sun-fill me-2"></i> Light
                                    <i class="bi bi-check-lg ms-auto d-none"></i>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center"
                                    data-bs-theme-value="dark" aria-pressed="false">
                                    <i class="bi bi-moon-fill me-2"></i> Dark
                                    <i class="bi bi-check-lg ms-auto d-none"></i>
                                </button>
                            </li>
                        </ul>
                    </li> --}}
                </ul>
            </div>
        </nav>
        <!-- /.navbar -->




        <div class="content-wrapper">

            <div class="content-header">
                <div class="container">

                </div>
            </div>


            @yield('content')

        </div>

        <!-- Main Footer -->
        <footer class="main-footer"> <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Web Development</div> <!--end::To the end-->
            <!--begin::Copyright--> <strong>
                Copyright &copy;
                <a href="" class="text-decoration-none">CSU-Aparri</a>.
            </strong>
            All rights reserved.
            <!--end::Copyright-->
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->

    {{-- sweet alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/auth/vendors/js/vendor.bundle.base.js') }}"></script>
    <!--leaflet-->
    <script src="{{ asset('assets/auth/js/leaflet.js') }}" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
    <script src="{{ asset('assets/auth/js/Control.Geocoder.js') }}"></script>

       <!--fontawesome-->
    <script src="https://kit.fontawesome.com/f60b315caa.js" crossorigin="anonymous"></script>
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
    </script> <!--end::OverlayScrollbars Configure-->

    <!--dark mode-->
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
                if (theme === "auto") {
                    theme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
                }

                // Apply theme to the <body> tag
                document.body.setAttribute("data-theme", theme);

                // Switch between light and dark mode classes based on theme
                if (theme === "dark") {
                    document.documentElement.classList.add("dark-mode");
                    updateNavbarTheme('dark');
                } else {
                    document.documentElement.classList.remove("dark-mode");
                    updateNavbarTheme('light');
                }
            };

            const updateNavbarTheme = (theme) => {
                const navbar = document.querySelector('.main-header.navbar');
                if (theme === 'dark') {
                    navbar.classList.remove('navbar-light', 'navbar-white');
                    navbar.classList.add('navbar-dark', 'bg-dark');
                } else {
                    navbar.classList.remove('navbar-dark', 'bg-dark');
                    navbar.classList.add('navbar-light', 'navbar-white');
                }
            };

            const showActiveTheme = (theme, focus = false) => {
                const themeToggles = document.querySelectorAll("[data-bs-theme-value]");
                themeToggles.forEach(toggle => {
                    toggle.classList.remove("active");
                    if (toggle.getAttribute("data-bs-theme-value") === theme) {
                        toggle.classList.add("active");
                    }
                });
            };

            setTheme(getPreferredTheme());

            window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", () => {
                if (storedTheme !== "light" && storedTheme !== "dark") {
                    setTheme(getPreferredTheme());
                }
            });

            window.addEventListener("DOMContentLoaded", () => {
                showActiveTheme(getPreferredTheme());

                document.querySelectorAll("[data-bs-theme-value]").forEach(toggle => {
                    toggle.addEventListener("click", () => {
                        const theme = toggle.getAttribute("data-bs-theme-value");
                        localStorage.setItem("theme", theme);
                        setTheme(theme);
                        showActiveTheme(theme, true);
                    });
                });
            });

        })();
    </script>

    <!-- jQuery -->
    <script src="{{ asset('assets/website/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/website/plugins/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- Script App -->
    <script src="{{ asset('assets/website/js/home.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
    <script src="{{ asset('assets/website/plugins/bs-custom-file-input.min.js') }}"></script>
    
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
</body>

</html>

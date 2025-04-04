<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/website/images/csulogo.png') }}">
    <title>@yield('title', 'CSU-APARRI-SDG')</title>
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

    <!--end::Required Plugin(AdminLTE)--><!-- apexcharts -->



    {{-- sweet alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ asset('assets/auth/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/auth/css/fontawesome.min.css') }}">
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
    </style>
    @yield('styles')
</head>

<body class="hold-transition layout-top-nav">
    <div class="app-wrapper">
        <main class="app-main"> <!--begin::App Content Header-->
            @yield('content')
        </main>

        <footer class="app-footer"> <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Web Development</div> <!--end::To the end-->
            <!--begin::Copyright--> <strong>
                Copyright &copy;
                <a href="" class="text-decoration-none">CSU-Aparri</a>.
            </strong>
            All rights reserved.
            <!--end::Copyright-->
        </footer>
    </div>
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
        @if (Session::has('alert-success'))
            Swal.fire({
                title: "Good job!",
                text: "{{ Session::get('alert-success') }}",
                icon: "success"
            });
        @endif
    </script>
    <script type="text/javascript">
        // Prevent back button use after logout
        (function() {
            if (window.history && window.history.pushState) {
                window.history.pushState(null, null, document.URL);
                window.addEventListener('popstate', function() {
                    window.history.pushState(null, null, document.URL);
                });
            }
        })();
    </script>


    @yield('scripts')
    <!--end::Script-->


</body>

</html>

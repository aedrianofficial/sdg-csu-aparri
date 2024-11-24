@extends('layouts.website2')
@section('styles')
    <style>
        #map {
            height: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }


        .leaflet-popup .fas {
            color: #007bff;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <!-- Project Section -->
        <div class="content-header">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $project->title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('website.home2') }}"><i class="fas fa-home"></i>Home</a>
                        </li>
                        <li class="breadcrumb-item active"><a href="{{ route('website.sdg_project_main2') }}">All
                                Projects</a>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Project Content -->
            <div class="col-lg-8">
                <div class="card card-primary card-outline post">
                    <div class="card-header">
                        <h5 class="card-title m-0 text-truncate" title="{{ $project->title }}">
                            <i class="fas fa-project-diagram"></i> {{ Str::limit($project->title, 40) }}
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <!-- Project Image -->

                        <img src="{{ $project->projectimg->image }}" class="card-img-top" alt="Project Image"
                            style="height: 300px; object-fit: cover;">


                        <!-- Project Details -->
                        <div class="project-details mt-3">
                            <!-- Created At -->
                            <div class="project-detail-item mb-2">
                                <i class="fas fa-calendar-alt"></i> <strong>Created On:</strong>
                                <span>{{ date('d M Y', strtotime($project->created_at)) }}</span>
                            </div>

                            <!-- Description -->
                            <div class="project-detail-item mb-2">
                                <i class="fas fa-align-left"></i> <strong>Description:</strong>
                                <span>{{ $project->description }}</span>
                            </div>
                        </div>

                        <!-- Meta Info -->
                        <div class="post-meta mt-4">
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-calendar-day"></i> {{ date('d M Y', strtotime($project->created_at)) }}
                                </li>
                                <li>
                                    <strong><i class="fas fa-tags"></i> SDGs:</strong>
                                    @foreach ($project->sdg as $project_sdgs)
                                        @php
                                            $badgeColors = [
                                                1 => 'badge-success',
                                                2 => 'badge-info',
                                                3 => 'badge-warning',
                                                4 => 'badge-danger',
                                                5 => 'badge-primary',
                                                6 => 'badge-secondary',
                                                7 => 'badge-light',
                                                8 => 'badge-dark',
                                                9 => 'badge-info',
                                                10 => 'badge-warning',
                                                11 => 'badge-danger',
                                                12 => 'badge-primary',
                                                13 => 'badge-success',
                                                14 => 'badge-light',
                                                15 => 'badge-warning',
                                                16 => 'badge-danger',
                                                17 => 'badge-primary',
                                            ];
                                            $badgeColor = $badgeColors[$project_sdgs->id] ?? 'badge-secondary';
                                        @endphp

                                        <span class="badge {{ $badgeColor }}">{{ $project_sdgs->name }}</span>
                                    @endforeach
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="map" class="card card-primary card-outline" style="height: 400px; border-radius: 10px;">
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <h3>Related Reports</h3>
                        @if ($reports !== null && count($reports) > 0)
                            <div class="row">
                                @foreach ($reports as $report)
                                    <div class="col-lg-6 mb-4"> <!-- Column for each card -->
                                        <div class="card card-success card-outline h-100 d-flex flex-column post">
                                            <div class="card-header">
                                                <h5 class="card-title m-0 text-truncate" title="{{ $report->title }}">
                                                    <i class="fas fa-file-alt"></i>
                                                    {{ Str::limit($report->title, 35) }}
                                                </h5>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <a href="{{ route('website.display_single_report2', $report->id) }}">
                                                    <img src="{{ $report->reportimg->image }}" class="card-img-top"
                                                        alt="" style="height: 200px; object-fit: cover;">
                                                </a>
                                                <div class="post-meta mt-3">
                                                    <ul class="list-unstyled">
                                                        <li>
                                                            <i class="fas fa-calendar-alt"></i>
                                                            {{ date('d M Y', strtotime($report->created_at)) }}
                                                        </li>
                                                        <li>
                                                            @foreach ($report->sdg as $report_sdgs)
                                                                <i class="fas fa-tags"></i>
                                                                {{ $report_sdgs->name }}&nbsp;
                                                            @endforeach
                                                        </li>
                                                    </ul>
                                                </div>
                                                <a href="{{ route('website.display_single_report2', $report->id) }}"
                                                    class="btn btn-success mt-auto continue-reading">
                                                    <i class="fas fa-book-open"></i> Continue Reading
                                                </a>
                                            </div>
                                        </div>
                                    </div> <!-- End of card column -->
                                @endforeach
                            </div>

                            <!-- Pagination Links -->
                            <div class="container">
                                @if (count($reports) > 0)
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <!-- Previous Button -->
                                            <li class="page-item {{ $reports->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $reports->previousPageUrl() }}"
                                                    tabindex="-1">
                                                    <i class="fas fa-chevron-left"></i> Previous
                                                </a>
                                            </li>

                                            <!-- Page Number Links -->
                                            @php
                                                $currentPage = $reports->currentPage();
                                                $lastPage = $reports->lastPage();
                                                $start = max($currentPage - 1, 1);
                                                $end = min($start + 2, $lastPage);
                                            @endphp
                                            @for ($i = $start; $i <= $end; $i++)
                                                <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                    <a class="page-link"
                                                        href="{{ $reports->url($i) }}">{{ $i }}</a>
                                                </li>
                                            @endfor

                                            <!-- Next Button -->
                                            <li class="page-item {{ $reports->hasMorePages() ? '' : 'disabled' }}">
                                                <a class="page-link" href="{{ $reports->nextPageUrl() }}">
                                                    Next <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                @else
                                    <h3 class="text-danger text-center">No reports found</h3>
                                @endif
                            </div>
                        @else
                            <h2 class="text-center text-danger mt-5">No Reports added</h2>
                        @endif
                    </div>
                </div>

            </div>

            <!-- SDGs Section -->
            <div class="col-lg-4">
                <div class="card card-widget card-danger card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0 text-truncate"><i class="fas fa-bullseye"></i> Sustainable Development
                            Goals</h5>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                            @foreach ($sdgs as $singleSdg)
                                <li class="nav-item">
                                    <a href="{{ route('website.display_project_sdg2', $singleSdg->id) }}" class="nav-link">
                                        {{ $singleSdg->name }}
                                        @php
                                            $badgeColor = 'bg-primary';
                                            if ($singleSdg->project_count == 0) {
                                                $badgeColor = 'bg-danger';
                                            } elseif ($singleSdg->project_count >= 1) {
                                                $badgeColor = 'bg-warning';
                                            } elseif ($singleSdg->project_count >= 10) {
                                                $badgeColor = 'bg-primary';
                                            } elseif ($singleSdg->project_count >= 20) {
                                                $badgeColor = 'bg-success';
                                            }
                                        @endphp
                                        <span class="float-right badge {{ $badgeColor }}">
                                            {{ $singleSdg->project_count }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Map Initialization Script -->
@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize the map
            var latitude = {{ $project->latitude }};
            var longitude = {{ $project->longitude }};
            var address = "{{ $project->location_address }}";
            var projectTitle = "{{ $project->title }}"; // Add project title

            // Set up the map
            var map = L.map('map').setView([latitude, longitude], 16);

            let currentLayer;
            let setMapLayer = (theme) => {
                if (currentLayer) {
                    map.removeLayer(currentLayer);
                }

                if (theme === "dark") {
                    currentLayer = L.tileLayer(
                        'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                            subdomains: 'abcd'
                        }).addTo(map);
                } else if (theme === "light") {
                    currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                }
            };

            const preferredTheme = localStorage.getItem("theme") || "light";
            setMapLayer(preferredTheme);

            document.querySelectorAll("[data-bs-theme-value]").forEach((toggle) => {
                toggle.addEventListener("click", () => {
                    const newTheme = toggle.getAttribute("data-bs-theme-value");
                    setMapLayer(newTheme);
                });
            });

            // Custom icon for markers
            var redMarkerIcon = L.icon({
                iconUrl: '{{ asset('assets/auth/images/leaflet/marker-icon-red.png') }}',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: '{{ asset('assets/auth/images/leaflet/marker-shadow.png') }}',
                shadowSize: [41, 41]
            });

            // Add a marker at the project's location with the updated popup content
            L.marker([latitude, longitude], {
                    icon: redMarkerIcon
                })
                .addTo(map)
                .bindPopup(`
                    <div style="font-size: 14px;">
                        <strong><i class="fas fa-project-diagram"></i> ${projectTitle}</strong><br>
                        <i class="fas fa-map-marker-alt"></i> ${address}<br>
                        <i class="fas fa-globe"></i> Coordinates: ${latitude}, ${longitude}
                    </div>
                `)
                .openPopup();
        });
    </script>
@endsection

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
        .quill-content p {
            margin-bottom: 0;
            line-height: 1.5;
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
                        <li class="breadcrumb-item"><a href="{{ route('website.home') }}"><i class="fas fa-home"></i>Home</a>
                        </li>
                        <li class="breadcrumb-item active"><a href="{{ route('website.sdg_project_main') }}">All
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
                                <span class="quill-content" style="color: black;">{!! $project->description !!}</span>
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
                <!-- SDG Sub Categories -->
                <div class="card card-primary card-outline mt-4">
                    <div class="card-header">
                        <h5 class="card-title m-0">SDG Targets:</h5>
                    </div>
                    <div class="card-body">
                        @if ($project->sdgSubCategories->isEmpty())
                            <p>No SDG Targets available.</p>
                        @else
                            <ul class="list-unstyled">
                                @foreach ($project->sdgSubCategories as $subCategory)
                                    <li>
                                        <strong>{{ $subCategory->sub_category_name }}:</strong>
                                        <span>{{ $subCategory->sub_category_description }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <p class="mt-2">
                            Source: <a
                                href="https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf"
                                target="_blank">https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf</a>
                        </p>
                    </div>
                </div>

                <div id="map" class="card card-primary card-outline" style="height: 400px; border-radius: 10px;">
                </div>


                <div class="row mt-4">
                    <div class="col-12">
                        <h3>Related Reports</h3>

                        @if ($statusReports->isEmpty() && $terminalReports->isEmpty())
                            <div class="alert">
                                <strong class="text-danger">No related reports available.</strong>
                            </div>
                        @else
                            <div class="row">
                                @foreach ($statusReports as $statusReport)
                                    <div class="col-lg-3 col-6">
                                        <div
                                            class="small-box 
                                            @if ($statusReport->log_status == 'Proposed') bg-info 
                                            @elseif($statusReport->log_status == 'On-Going') bg-primary 
                                            @elseif($statusReport->log_status == 'On-Hold') bg-warning 
                                            @elseif($statusReport->log_status == 'Rejected') bg-danger @endif">
                                            <div class="inner">
                                                <br>
                                                <p>{{ $statusReport->log_status }}</p>
                                            </div>
                                            <div class="icon">
                                                <i
                                                    class="fas 
                                                    @if ($statusReport->log_status == 'Proposed') fa-lightbulb 
                                                    @elseif($statusReport->log_status == 'On-Going') fa-spinner 
                                                    @elseif($statusReport->log_status == 'On-Hold') fa-pause 
                                                    @elseif($statusReport->log_status == 'Rejected') fa-times-circle @endif"></i>
                                            </div>
                                            <a href="{{ route('website.status_reports.show_project_published', $statusReport->id) }}"
                                                class="small-box-footer"> More info <i
                                                    class="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">
                                @foreach ($terminalReports as $terminalReport)
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-success">
                                            <div class="inner">
                                                <br>
                                                <p>Completed</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <a href="{{ route('website.terminal_reports.show_project_published', $terminalReport->id) }}"
                                                class="small-box-footer"> More info <i
                                                    class="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
                                    <a href="{{ route('website.display_project_sdg', $singleSdg->id) }}" class="nav-link">
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

@extends('layouts.reviewer')
@section('title', 'View Project/Program')
@section('styles')
    <style>
        #map {
            height: 400px;
            border: 2px solid #007bff;
            /* Border color */
            border-radius: 8px;
            /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Shadow effect */
        }


        .list-group-item {
            cursor: pointer;
            padding: 10px;
            /* Padding for better spacing */
        }

        .leaflet-popup .fas {
            color: #007bff;
            /* Icon color */
        }
    </style>
@endsection
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">View Reviewed Project/Program</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Reviewed Project/Program
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header--> <!--begin::App Content-->
    <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-4">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title:</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ $project->title }}" readonly>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description:</label>
                                    @php
                                        $description = $project->description;
                                        $rowCount =
                                            substr_count($description, "\n") + floor(strlen($description) / 100); // Adjust row count based on length
                                        $rowCount = $rowCount < 3 ? 3 : $rowCount; // Ensure a minimum of 3 rows
                                    @endphp
                                    <textarea name="description" id="description" class="form-control" rows="{{ $rowCount }}" readonly>{{ $description }}</textarea>
                                </div>

                                <!-- Project Status -->
                                <div class="mb-3">
                                    <label for="project_status" class="form-label">Project Status:</label>
                                    <input type="text" name="project_status" id="project_status" class="form-control"
                                        value="{{ $project->project_status ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Review Status -->
                                <div class="mb-3">
                                    <label for="review_status" class="form-label">Review Status:</label>
                                    <input type="text" name="review_status" id="review_status" class="form-control"
                                        value="{{ $project->reviewStatus->status ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Publish Status -->
                                <div class="mb-3">
                                    <label for="is_publish" class="form-label">Is Published:</label>
                                    <input type="text" name="is_publish" id="is_publish" class="form-control"
                                        value="{{ $project->is_publish == 1 ? 'Published' : 'Draft' }}" readonly>
                                </div>

                                <!-- SDG -->
                                <div class="mb-3">
                                    <label for="sdg" class="form-label">SDG:</label>
                                    <textarea name="sdg" id="sdg" class="form-control" rows="{{ count($project->sdg) + 2 }}" readonly>
                @foreach ($project->sdg as $sdg)
{{ $sdg->name }}
@endforeach
                                    </textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="">Image: </label>
                                    <div>
                                        <img src="{{ $project->projectimg->image }}" alt="project-image"
                                            style="max-width: 500px; height: auto;">
                                    </div>
                                </div>
                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Address:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $project->location_address }}" readonly>
                                </div>

                                <!-- Coordinates -->
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Coordinates:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $project->latitude }}, {{ $project->longitude }}" readonly>
                                </div>

                                <!-- Map -->
                                <div class="mb-3" id="map" style="height: 400px;"></div>

                                <!-- Created by -->
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created by:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $project->user->first_name }} {{ $project->user->last_name }}" readonly>
                                </div>

                                <!-- Created at -->
                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created at:</label>
                                    <input type="text" name="created_at" id="created_at" class="form-control"
                                        value="{{ $project->created_at->format('M d, Y H:i') }}" readonly>
                                </div>

                                <!-- Updated at -->
                                <div class="mb-3">
                                    <label for="updated_at" class="form-label">Updated at:</label>
                                    <input type="text" name="updated_at" id="updated_at" class="form-control"
                                        value="{{ $project->updated_at->format('M d, Y H:i') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection


@section('scripts')

    <script>
        $(document).ready(function() {
            // Initialize the map
            var latitude = {{ $project->latitude }};
            var longitude = {{ $project->longitude }};
            var address = "{{ $project->location_address }}";

            // Set up the map
            var map = L.map('map').setView([latitude, longitude], 16);

            // Add MapTiler tile layer
            L.tileLayer('https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=nnLs4mWhpJaZMAiwkL9K', {
                tileSize: 512,
                zoomOffset: -1,
                minZoom: 1,
                attribution: '&copy; <a href="https://www.maptiler.com/copyright/">MapTiler</a> | ' +
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> Contributors',
                crossOrigin: true
            }).addTo(map);

            // Custom icon for markers
            var redMarkerIcon = L.icon({
                iconUrl: '{{ asset('assets/auth/images/leaflet/marker-icon-red.png') }}',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: '{{ asset('assets/auth/images/leaflet/marker-shadow.png') }}',
                shadowSize: [41, 41]
            });

            // Add a marker at the project's location with a popup displaying the address and coordinates
            L.marker([latitude, longitude], {
                    icon: redMarkerIcon
                })
                .addTo(map)
                .bindPopup(
                    `<strong>Address:</strong> ${address}<br><strong>Coordinates:</strong> (${latitude}, ${longitude})`
                )
                .openPopup();
        });
    </script>
@endsection
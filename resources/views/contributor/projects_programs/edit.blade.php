@extends('layouts.contributor')

@section('title', 'Edit Project/Program')

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



        /* Search Address Input Styles */
        #searchAddress {
            border: 1px solid #007bff;
            /* Border color */
            border-radius: 0.25rem;
            /* Rounded corners */
            padding: 10px;
            /* Padding */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Shadow */
            transition: border-color 0.3s;
            /* Smooth transition */
        }

        #searchAddress:focus {
            border-color: #0056b3;
            /* Darker border on focus */
            outline: none;
            /* Remove outline */
        }

        /* Pop-up styles for the marker */


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
                    <h3 class="mb-0">Edit Project/Program</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Project/Program
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

                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="post" action="{{ route('contributor.projects.update', $project->id) }}"
                                enctype="multipart/form-data" id="project-form">
                                @csrf
                                @method('PUT')

                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Title" value="{{ old('title', $project->title) }}" required>
                                </div>

                                <!-- SDGs -->
                                <div class="mb-3">
                                    <label for="sdg" class="form-label">Sustainable Development Goals</label>
                                    <select name="sdg[]" id="sdg" class="form-select" required multiple>
                                        @foreach ($sdgs as $sdg)
                                            <option value="{{ $sdg->id }}"
                                                {{ in_array($sdg->id, $project->sdg->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $sdg->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Project Status Dropdown -->
                                <div class="mb-3">
                                    <label for="project_status" class="form-label">Project Status</label>
                                    <select name="project_status" id="project_status" class="form-select" required>
                                        <option value="Proposed"
                                            {{ old('project_status', $project->project_status) == 'Proposed' ? 'selected' : '' }}>
                                            Proposed</option>
                                        <option value="On-Going"
                                            {{ old('project_status', $project->project_status) == 'On-Going' ? 'selected' : '' }}>
                                            On-Going</option>
                                        <option value="On-Hold"
                                            {{ old('project_status', $project->project_status) == 'On-Hold' ? 'selected' : '' }}>
                                            On-Hold</option>
                                        <option value="Completed"
                                            {{ old('project_status', $project->project_status) == 'Completed' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="Rejected"
                                            {{ old('project_status', $project->project_status) == 'Rejected' ? 'selected' : '' }}>
                                            Rejected</option>
                                    </select>
                                </div>

                                <!-- Image Upload Input -->
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image upload (Optional)</label>

                                    @if (isset($existingImage) && $existingImage)
                                        <div class="mb-3">
                                            <label>Current Image:</label><br>
                                            <img src="{{ $existingImage }}" alt="Current Image"
                                                style="max-width: 500px; height: auto;">
                                        </div>
                                    @endif

                                    <input type="file" name="image" class="form-control" id="image">
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="10" required>{{ old('description', $project->description) }}</textarea>
                                </div>

                                <!-- Map and Search Bar -->
                                <div class="mb-3">
                                    <label for="searchAddress" class="form-label">Search Address</label>
                                    <input type="text" id="searchAddress" class="form-control"
                                        placeholder="Search for an address..." />
                                    <div id="suggestions" class="list-group" style="display: none;"></div>
                                </div>
                                <div id="map" class="mb-3"></div>

                                <!-- Hidden Inputs -->
                                <input type="hidden" name="is_publish" id="is_publish" value="0">
                                <input type="hidden" id="latitude" name="latitude"
                                    value="{{ old('latitude', $project->latitude) }}">
                                <input type="hidden" id="longitude" name="longitude"
                                    value="{{ old('longitude', $project->longitude) }}">
                                <input type="hidden" id="location_address" name="location_address"
                                    value="{{ old('location_address', $project->location_address) }}">

                                <!-- Update Button -->
                                <button type="button" class="btn btn-primary" id="update-button">Update</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div> <!--end::Container-->
    </div>
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update this project/program?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-update">Update</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::App Content-->
@endsection




@section('scripts')
    <script>
        $(document).ready(function() {
            $('#sdg').select2();


            // Initialize map
            var map = L.map('map').setView([{{ old('latitude', $project->latitude) }},
                {{ old('longitude', $project->longitude) }}
            ], 16);

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

            // Define the marker variable without initializing it
            var marker;

            // Set old values for address and marker position
            var oldAddress = "{{ old('location_address', $project->location_address) }}";
            var oldLat = {{ old('latitude', $project->latitude) }};
            var oldLng = {{ old('longitude', $project->longitude) }};

            // Display marker and address if set
            marker = L.marker([oldLat, oldLng], {
                icon: redMarkerIcon
            }).addTo(map).bindPopup(createPopupMessage(oldAddress, oldLat, oldLng)).openPopup();

            $('#location_address').val(oldAddress);
            $('#latitude').val(oldLat);
            $('#longitude').val(oldLng);

            // Rate limiting for AJAX requests
            var lastRequestTime = 0;
            var requestDelay = 500; // milliseconds

            // Handle address search and suggestions
            $('#searchAddress').on('input', function() {
                var query = $(this).val();
                if (query.length > 2) {
                    var currentTime = new Date().getTime();
                    if (currentTime - lastRequestTime >= requestDelay) {
                        lastRequestTime = currentTime; // Update the time of the last request

                        $.ajax({
                            url: 'https://nominatim.openstreetmap.org/search',
                            data: {
                                q: query,
                                format: 'json',
                                addressdetails: 1,
                                limit: 5
                            },
                            success: function(data) {
                                $('#suggestions').empty();
                                if (data.length > 0) {
                                    data.forEach(function(item) {
                                        $('#suggestions').append(
                                            '<div class="list-group-item" data-lat="' +
                                            item.lat + '" data-lng="' + item.lon +
                                            '">' + item.display_name + '</div>'
                                        );
                                    });
                                    $('#suggestions').show();
                                } else {
                                    $('#suggestions').hide();
                                }
                            }
                        });
                    }
                } else {
                    $('#suggestions').hide();
                }
            });

            // Handle suggestion click
            $(document).on('click', '.list-group-item', function() {
                var lat = $(this).data('lat');
                var lng = $(this).data('lng');

                // If a marker exists, remove it before adding a new one
                if (marker) {
                    map.removeLayer(marker);
                }

                // Create a new marker at the selected location
                marker = L.marker([lat, lng], {
                    icon: redMarkerIcon
                }).addTo(map).bindPopup(createPopupMessage($(this).text(), lat, lng)).openPopup();

                // Set the map view to the new location
                map.setView([lat, lng], 16);

                // Update the search input and other fields
                $('#searchAddress').val($(this).text());
                $('#location_address').val($(this).text());
                $('#latitude').val(lat);
                $('#longitude').val(lng);

                $('#suggestions').hide(); // Hide suggestions
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function(event) {
                if (!$(event.target).closest('#searchAddress').length) {
                    $('#suggestions').hide();
                }
            });

            // Handle map click event
            map.on('click', function(e) {
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;

                // If a marker exists, remove it before adding a new one
                if (marker) {
                    map.removeLayer(marker);
                }

                // Create a new marker at the clicked location
                marker = L.marker([lat, lng], {
                    icon: redMarkerIcon
                }).addTo(map);

                // Reverse geocode to get the address
                $.ajax({
                    url: 'https://nominatim.openstreetmap.org/reverse',
                    data: {
                        lat: lat,
                        lon: lng,
                        format: 'json'
                    },
                    success: function(data) {
                        if (data && data.display_name) {
                            var address = data.display_name;

                            // Update fields and bind a popup with the address and coordinates
                            $('#searchAddress').val(address);
                            $('#location_address').val(address);
                            $('#latitude').val(lat);
                            $('#longitude').val(lng);

                            marker.bindPopup(createPopupMessage(address, lat, lng)).openPopup();
                        }
                    }
                });
            });

            // Function to create a popup message with icons for address and coordinates
            function createPopupMessage(address, lat, lng) {
                var addressIcon = '<i class="fas fa-map-marker-alt"></i>';
                var coordinatesIcon = '<i class="fas fa-map"></i>';
                return `<div>${addressIcon} Address: ${address}</div>
            <div>${coordinatesIcon} Coordinates: ${lat.toFixed(5)}, ${lng.toFixed(5)}</div>`;
            }

            // Confirm submit button handler
            $('#update-button').click(function() {
                $('#confirmationModal').modal('show');
            });

            $('#confirm-update').click(function() {
                $('#project-form').submit();
            });
        });
    </script>
@endsection
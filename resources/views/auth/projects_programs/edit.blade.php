@extends('layouts.admin')

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
                    <h3 class="mb-0">Edit Projects/Programs</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('auth.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Projects/Programs
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

                            <form method="post" action="{{ route('auth.projects.update', $project->id) }}"
                                enctype="multipart/form-data" class="needs-validation" id="project-form" novalidate>
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <!-- Title Input -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Title" value="{{ old('title', $project->title) }}" required>
                                </div>

                                <!-- SDGs -->
                                <div class="mb-3">
                                    <label for="sdg" class="form-label">Sustainable Development Goals (Click to select
                                        SDGs)</label>
                                    <select name="sdg[]" id="sdg" class="form-select" required multiple>
                                        @foreach ($sdgs as $sdg)
                                            <option value="{{ $sdg->id }}"
                                                {{ in_array($sdg->id, $project->sdg->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $sdg->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Sub-categories Section -->
                                <div class="mb-3" id="sub-categories" style="display: none;">
                                    <label for="sdg_sub_categories" class="form-label">Select SDG Targets
                                        (Optionally)</label>
                                    <div id="sub-category-checkboxes">
                                        @foreach ($sdgs as $sdg)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sdg_sub_category[]"
                                                    value="{{ $sdg->id }}" id="subCategory{{ $sdg->id }}"
                                                    {{ in_array($sdg->id, $selectedSubCategories) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="subCategory{{ $sdg->id }}">
                                                    {{ $sdg->name }}: {{ $sdg->description }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p>
                                        Source: <a
                                            href="https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf"
                                            target="_blank">https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf</a>
                                    </p>
                                </div>

                                <!-- Project Status Select -->
                                <!-- Project Status Select -->
                                <div class="mb-3">
                                    <label for="status_id" class="form-label">Project Status</label>
                                    <select name="status_id" id="status_id" class="form-select" required>
                                        @foreach ($statuses as $status)
                                            <!-- Assuming you pass $statuses to the view -->
                                            <option value="{{ $status->id }}"
                                                {{ old('status_id', $project->status_id) == $status->id ? 'selected' : '' }}>
                                                {{ $status->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Review Status Select -->
                                <div class="mb-3">
                                    <label for="review_status_id" class="form-label">Review Status</label>
                                    <select name="review_status_id" id="review_status_id" class="form-select" required>
                                        @foreach ($reviewStatuses as $status)
                                            <option value="{{ $status->id }}"
                                                {{ old('review_status_id', $project->review_status_id) == $status->id ? 'selected' : '' }}>
                                                {{ $status->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Feedback Textarea -->
                                <div class="mb-3" id="feedback-container" style="display: none;">
                                    <label for="feedback" id="feedback-label" class="form-label">Feedback</label>
                                    <textarea name="feedback" class="form-control" id="feedback" cols="30" rows="10"></textarea>
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

                                <!-- Description Textarea -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="10" required>{{ old('description', $project->description) }}</textarea>
                                </div>

                                <!-- Created By (Display Only) -->
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created by:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $project->user->first_name }} {{ $project->user->last_name }}"
                                        readonly>
                                </div>


                                <!-- Map and Search Bar -->
                                <div class="mb-3">
                                    <label for="searchAddress" class="form-label">Search Address</label>
                                    <input type="text" id="searchAddress" class="form-control"
                                        placeholder="Search for an address...">
                                    <div id="suggestions" class="list-group" style="display: none;"></div>
                                </div>

                                <div id="map" class="mb-3" style="height: 300px;"></div>
                                <!-- Adjust height as needed -->

                                <!-- Hidden Inputs -->
                                <input type="hidden" name="is_publish" id="is_publish" value="0">
                                <input type="hidden" id="latitude" name="latitude"
                                    value="{{ old('latitude', $project->latitude) }}">
                                <input type="hidden" id="longitude" name="longitude"
                                    value="{{ old('longitude', $project->longitude) }}">
                                <input type="hidden" id="location_address" name="location_address"
                                    value="{{ old('location_address', $project->location_address) }}">

                                <!-- Submit Button -->
                                <button type="button" class="btn btn-primary me-2" id="update-button">Update</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->

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
@endsection


@section('scripts')
    <script>
        var selectedSubCategories = @json($selectedSubCategories);
    </script>
    <script>
        $(document).ready(function() {
            $('#sdg').select2();
            $(document).ready(function() {
                $('#sdg').select2({
                    width: '100%',
                    placeholder: 'Select SDGs',
                });
                // Load sub-categories based on selected SDGs
                $('#sdg').on('change', function() {
                    var selectedSdgs = $(this).val();
                    if (selectedSdgs.length > 0) {
                        $.ajax({
                            url: '{{ route('sdg.subcategories') }}',
                            method: 'GET',
                            data: {
                                sdg_ids: selectedSdgs
                            },
                            success: function(data) {
                                $('#sub-category-checkboxes').empty();
                                if (data.length > 0) {
                                    data.forEach(function(subCategory) {
                                        $('#sub-category-checkboxes').append(
                                            '<div class="form-check">' +
                                            '<input class="form-check-input" type="checkbox" name="sdg_sub_category[]" value="' +
                                            subCategory.id +
                                            '" id="subCategory' +
                                            subCategory.id + '"' + (
                                                selectedSubCategories
                                                .includes(subCategory.id) ?
                                                ' checked' : '') + '>' +
                                            '<label class="form-check-label" for="subCategory' +
                                            subCategory.id + '">' +
                                            subCategory.sub_category_name +
                                            ': ' +
                                            subCategory
                                            .sub_category_description +
                                            '</label>' +
                                            '</div>'
                                        );
                                    });
                                    $('#sub-categories').show();
                                } else {
                                    $('#sub-categories').hide();
                                }
                            }
                        });
                    } else {
                        $('#sub-categories').hide();
                    }
                });

                // Trigger change event on page load to load existing sub-categories
                $('#sdg').trigger('change');
            });
            // Show the confirmation modal when the "Update" button is clicked
            $('#update-button').on('click', function() {
                $('#confirmationModal').modal('show');
            });

            // Submit the form when the "Confirm Update" button in the modal is clicked
            $('#confirm-update').on('click', function() {
                $('#project-form').submit(); // Submit the form using the form class
            });

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


        });
    </script>
    <!-- JavaScript to handle conditional feedback display, requirement, and label -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reviewStatusSelect = document.getElementById('review_status_id');
            const feedbackContainer = document.getElementById('feedback-container');
            const feedbackTextarea = document.getElementById('feedback');
            const feedbackLabel = document.getElementById('feedback-label');

            function updateFeedbackVisibility() {
                const selectedStatus = reviewStatusSelect.options[reviewStatusSelect.selectedIndex].text;
                if (selectedStatus === 'Need Changes') {
                    feedbackContainer.style.display = 'block';
                    feedbackTextarea.required = true;
                    feedbackLabel.innerText = 'Feedback (Required)';
                } else if (selectedStatus === 'Rejected') {
                    feedbackContainer.style.display = 'block';
                    feedbackTextarea.required = false;
                    feedbackLabel.innerText = 'Feedback (Optional)';
                } else {
                    feedbackContainer.style.display = 'none';
                    feedbackTextarea.required = false;
                }
            }

            // Initialize visibility and label based on current selection
            updateFeedbackVisibility();

            // Update visibility and label on change
            reviewStatusSelect.addEventListener('change', updateFeedbackVisibility);
        });
    </script>

    <script>
        let isDirty = false;

        // Track changes in input fields
        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('input', () => {
                isDirty = true;
            });
        });

        // Handle the cancel button click
        document.getElementById('cancelButton').addEventListener('click', function() {
            if (isDirty) {
                const confirmationMessage = 'You have unsaved changes. Are you sure you want to leave?';
                if (confirm(confirmationMessage)) {
                    isDirty = false; // Reset the dirty flag
                    window.location.href = '{{ route('projects.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href = '{{ route('projects.index') }}'; // Redirect to home or desired route
            }
        });
    </script>
@endsection

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
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
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
                                <!-- Project Status Dropdown -->
                                <div class="mb-3">
                                    <label for="status_id" class="form-label">Project Status</label>
                                    <select name="status_id" id="status_id" class="form-select" required>
                                        <option disabled selected>Choose Status</option>
                                        @foreach ($projectStatuses as $status)
                                            <option value="{{ $status->id }}"
                                                {{ old('status_id', $project->status_id) == $status->id ? 'selected' : '' }}>
                                                {{ $status->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Target Beneficiaries Field -->
                                <div class="mb-3">
                                    <label for="target_beneficiaries" class="form-label">Target Beneficiaries</label>
                                    <textarea class="form-control" id="target_beneficiaries" name="target_beneficiaries" 
                                        rows="3" placeholder="Describe the target beneficiaries of your project (e.g., women, men, children, elderly, etc.)">{{ old('target_beneficiaries', $project->target_beneficiaries) }}</textarea>
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Specify who will benefit from this project. This information helps classify gender impact.
                                    </div>
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

                                <!-- Gender Impact Analysis Results Area -->
                                <div id="gender-analysis-results" class="mb-3 d-none">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <i class="fas fa-venus-mars me-2"></i>Gender Impact Analysis
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3" id="gender-loading-indicator">
                                                <div class="spinner-border text-success me-3" role="status"></div>
                                                <p class="mb-0 fs-5">Analyzing gender impact...</p>
                                            </div>
                                            <div id="gender-analysis-content" class="d-none">
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                <h6 class="card-subtitle mb-2 text-muted">Beneficiaries</h6>
                                                                <div id="gender-beneficiaries">
                                                                    <!-- Will be populated by JS -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                <h6 class="card-subtitle mb-2 text-muted">Gender Equality Focus</h6>
                                                                <div id="gender-equality-focus">
                                                                    <!-- Will be populated by JS -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="gender-notes" class="alert alert-info">
                                                    <!-- Will be populated by JS -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

            // Function to analyze gender impact
            function analyzeGenderImpact() {
                var title = $('#title').val();
                var description = $('#description').val();
                var targetBeneficiaries = $('#target_beneficiaries').val();
                
                // Check if we have content to analyze
                if (title.trim() === '' && description.trim() === '' && targetBeneficiaries.trim() === '') {
                    return;
                }
                
                // Show the analysis panel and loading indicator
                $('#gender-analysis-results').removeClass('d-none');
                $('#gender-loading-indicator').removeClass('d-none');
                $('#gender-analysis-content').addClass('d-none');
                
                // Make AJAX request to analyze gender impact
                $.ajax({
                    url: '{{ route('contributor.projects.analyze-gender') }}',
                    type: 'POST',
                    data: {
                        title: title,
                        description: description,
                        target_beneficiaries: targetBeneficiaries,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Hide loading indicator and show content
                            $('#gender-loading-indicator').addClass('d-none');
                            $('#gender-analysis-content').removeClass('d-none');
                            
                            // Display gender analysis results
                            displayGenderResults(response.data);
                        } else {
                            // Show error message
                            $('#gender-loading-indicator').addClass('d-none');
                            $('#gender-analysis-content').removeClass('d-none');
                            $('#gender-notes').html(
                                '<div class="alert alert-danger">Error analyzing gender impact: ' +
                                response.message + '</div>'
                            );
                        }
                    },
                    error: function(xhr) {
                        // Hide loading indicator and show error
                        $('#gender-loading-indicator').addClass('d-none');
                        $('#gender-analysis-content').removeClass('d-none');
                        $('#gender-notes').html(
                            '<div class="alert alert-warning">' +
                            '<h5><i class="fas fa-exclamation-triangle me-2"></i>Gender Analysis Error</h5>' +
                            '<p>There was a problem analyzing gender impact. Please fill in the Target Beneficiaries field manually.</p>' +
                            '</div>'
                        );
                    }
                });
            }
            
            // Display gender analysis results
            function displayGenderResults(data) {
                // Beneficiaries section
                var beneficiariesHtml = '<ul class="list-group">';
                
                if (data.benefits_women) {
                    beneficiariesHtml += '<li class="list-group-item list-group-item-success"><i class="fas fa-check-circle me-2"></i> Benefits Women/Girls';
                    if (data.women_count !== null) {
                        beneficiariesHtml += ' <span class="badge bg-info">' + data.women_count + ' mentioned</span>';
                    }
                    beneficiariesHtml += '</li>';
                } else {
                    beneficiariesHtml += '<li class="list-group-item list-group-item-light"><i class="fas fa-times-circle me-2"></i> Does Not Specifically Target Women/Girls</li>';
                }
                
                if (data.benefits_men) {
                    beneficiariesHtml += '<li class="list-group-item list-group-item-success"><i class="fas fa-check-circle me-2"></i> Benefits Men/Boys';
                    if (data.men_count !== null) {
                        beneficiariesHtml += ' <span class="badge bg-info">' + data.men_count + ' mentioned</span>';
                    }
                    beneficiariesHtml += '</li>';
                } else {
                    beneficiariesHtml += '<li class="list-group-item list-group-item-light"><i class="fas fa-times-circle me-2"></i> Does Not Specifically Target Men/Boys</li>';
                }
                
                if (data.benefits_all) {
                    beneficiariesHtml += '<li class="list-group-item list-group-item-success"><i class="fas fa-check-circle me-2"></i> Benefits All Genders</li>';
                }
                
                beneficiariesHtml += '</ul>';
                
                $('#gender-beneficiaries').html(beneficiariesHtml);
                
                // Gender equality focus
                var equalityHtml = '<ul class="list-group">';
                
                if (data.addresses_gender_inequality) {
                    equalityHtml += '<li class="list-group-item list-group-item-success"><i class="fas fa-check-circle me-2"></i> Addresses Gender Inequality</li>';
                } else {
                    equalityHtml += '<li class="list-group-item list-group-item-light"><i class="fas fa-info-circle me-2"></i> No Explicit Focus on Gender Inequality</li>';
                }
                
                equalityHtml += '</ul>';
                
                $('#gender-equality-focus').html(equalityHtml);
                
                // Gender notes
                if (data.gender_notes) {
                    $('#gender-notes').html('<i class="fas fa-info-circle me-2"></i> ' + data.gender_notes);
                } else {
                    $('#gender-notes').html('<i class="fas fa-info-circle me-2"></i> No additional gender impact notes available.');
                }
                
                // Create hidden inputs to store the gender impact data
                var hiddenInputs = '';
                hiddenInputs += '<input type="hidden" name="gender_benefits_men" value="' + (data.benefits_men ? '1' : '0') + '">';
                hiddenInputs += '<input type="hidden" name="gender_benefits_women" value="' + (data.benefits_women ? '1' : '0') + '">';
                hiddenInputs += '<input type="hidden" name="gender_benefits_all" value="' + (data.benefits_all ? '1' : '0') + '">';
                hiddenInputs += '<input type="hidden" name="gender_addresses_inequality" value="' + (data.addresses_gender_inequality ? '1' : '0') + '">';
                
                if (data.men_count !== null) {
                    hiddenInputs += '<input type="hidden" name="gender_men_count" value="' + data.men_count + '">';
                }
                
                if (data.women_count !== null) {
                    hiddenInputs += '<input type="hidden" name="gender_women_count" value="' + data.women_count + '">';
                }
                
                hiddenInputs += '<input type="hidden" name="gender_notes" value="' + (data.gender_notes || '') + '">';
                
                $('#gender-notes').append(hiddenInputs);
            }
            
            // Add event listeners for content changes
            $('#title, #description, #target_beneficiaries').on('input', function() {
                clearTimeout(window.genderAnalysisTimer);
                window.genderAnalysisTimer = setTimeout(function() {
                    analyzeGenderImpact();
                }, 1000); // Debounce for 1 second
            });
            
            // Run initial analysis
            analyzeGenderImpact();
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
                    window.location.href =
                        '{{ route('contributor.projects.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href =
                    '{{ route('contributor.projects.index') }}'; // Redirect to home or desired route
            }
        });
    </script>
@endsection

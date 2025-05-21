@extends('layouts.contributor')

@section('title', 'Create Projects/Programs')

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
                    <h3 class="mb-0">Create Projects/Programs</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Create Projects/Programs
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
                            <form method="post" action="{{ route('contributor.projects.store') }}"
                                enctype="multipart/form-data" id="project-form">
                                @csrf

                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Title" value="{{ old('title') }}" required>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description-container" class="form-label">Description</label>
                                    <!-- Create a div where Quill will be initialized -->
                                    <div id="description-editor" style="height: 300px;"></div>
                                    <!-- Hidden input to store the content for form submission -->
                                    <input type="hidden" name="description" id="description"
                                        value="{{ old('description') }}">
                                </div>

                                <!-- Target Beneficiaries Field -->
                                <div class="mb-3">
                                    <label for="target_beneficiaries" class="form-label">Target Beneficiaries</label>
                                    <textarea class="form-control" id="target_beneficiaries" name="target_beneficiaries" rows="3"
                                        placeholder="Describe the target beneficiaries of your project (e.g., women, men, children, elderly, etc.)">{{ old('target_beneficiaries') }}</textarea>
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Specify who will benefit from this project. This
                                        information helps classify gender impact.
                                    </div>
                                </div>

                                <!-- Add manual fallback button -->
                                <div class="mb-3" id="manual-selection-fallback">
                                    <button type="button" class="btn btn-outline-primary" id="show-manual-selection-top">
                                        <i class="fas fa-edit"></i> Use Manual SDG Selection Instead
                                    </button>
                                    <div class="form-text text-muted mt-1">
                                        <i class="fas fa-info-circle"></i> If you prefer, you can select SDGs manually
                                        without using AI analysis.
                                    </div>
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
                                                                <h6 class="card-subtitle mb-2 text-muted">Gender Equality
                                                                    Focus</h6>
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

                                <!-- AI Detection Results Area -->
                                <div id="ai-detection-results" class="mb-3 d-none">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <i class="fas fa-robot me-2"></i>AI-Detected SDGs and Targets
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3" id="ai-loading-indicator">
                                                <div class="spinner-border text-primary me-3" role="status"></div>
                                                <p class="mb-0 fs-5">Analyzing project for SDG relevance...</p>
                                            </div>
                                            <div id="ai-detection-content" class="d-none">
                                                <h5 class="card-title">The AI has analyzed your project and detected the
                                                    following:</h5>
                                                <h6 class="mt-3 mb-2">Detected Sustainable Development Goals:</h6>
                                                <div id="detected-sdgs-list" class="mb-3"></div>
                                                <h6 class="mt-4 mb-2">Detected SDG Targets:</h6>
                                                <div id="detected-subcategories-list"></div>
                                                <div id="selected-subcategories-container" class="d-none"></div>
                                                <div class="mt-4">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="show-manual-selection">
                                                        <i class="fas fa-edit"></i> Modify AI Selection
                                                    </button>
                                                    <p class="form-text text-muted mt-2">
                                                        <i class="fas fa-info-circle"></i> AI-detected targets will be
                                                        submitted. Click the button above to manually adjust if needed.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SDGs -->
                                <div class="mb-3" id="manual-sdg-selection">
                                    <label for="sdg" class="form-label">Sustainable Development Goals (Click to
                                        select SDGs)</label>
                                    <select name="sdg[]" id="sdg" class="form-select" required multiple>
                                        @if (count($sdgs) > 0)
                                            @foreach ($sdgs as $sdg)
                                                <option @selected(old('sdg') == $sdg->id) value="{{ $sdg->id }}">
                                                    {{ $sdg->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <!-- Sub-categories Section -->
                                <div class="mb-3" id="sub-categories" style="display: none;">
                                    <label for="sdg_sub_categories" class="form-label">Select SDG Targets
                                        (Optionally)</label>
                                    <div id="sub-category-checkboxes"></div>
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
                                            <option value="{{ $status->id }}" @selected(old('status_id') == $status->id)>
                                                {{ $status->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Image Upload -->
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image Upload</label>
                                    <input type="file" name="image" class="form-control" required>
                                </div>



                                <!-- Hidden Inputs -->
                                <input type="hidden" name="location_address" id="location_address">
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                <input type="hidden" name="is_publish" id="is_publish" value="0">

                                <div class="mb-3">
                                    <label for="map" class="form-label">Project Location</label>
                                    <input type="text" id="searchAddress" class="form-control"
                                        placeholder="Search for an address...">
                                    <div id="suggestions" class="list-group"
                                        style="display: none; max-height: 200px; overflow-y: auto;"></div>
                                </div>

                                <!-- Map Container -->
                                <div class="mb-3">
                                    <div id="map"></div>
                                </div>

                                <!-- "Submit for Review" Button -->
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#confirmationModal">Submit for Review</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <!-- Confirmation Modal -->
                                <div class="modal fade" id="confirmationModal" tabindex="-1"
                                    aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Submission
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to submit this project/program for review?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" id="confirmSubmit"
                                                    class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Quill editor
            var quill = new Quill('#description-editor', {
                theme: 'snow',
                placeholder: 'Write your description here...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{
                            'header': 1
                        }, {
                            'header': 2
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'direction': 'rtl'
                        }],
                        [{
                            'size': ['small', false, 'large', 'huge']
                        }],
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'font': []
                        }],
                        [{
                            'align': []
                        }],
                        ['clean'],
                        ['link', 'image', 'video']
                    ]
                }
            });

            // Set initial content if it exists
            const oldValue = document.getElementById('description').value;
            if (oldValue) {
                quill.root.innerHTML = oldValue;
            }

            // Update hidden input when text changes
            quill.on('text-change', function() {
                var htmlContent = quill.root.innerHTML;
                document.getElementById('description').value = htmlContent;

                // Run gender analysis when content changes (debounced)
                clearTimeout(window.genderAnalysisTimer);
                window.genderAnalysisTimer = setTimeout(function() {
                    analyzeGenderImpact();
                }, 1000);

                // Also run SDG analysis when content changes (with a slightly longer delay)
                clearTimeout(window.sdgAnalysisTimer);
                window.sdgAnalysisTimer = setTimeout(function() {
                    analyzeSdgImpact();
                }, 1500);
            });

            // Also trigger gender analysis when target beneficiaries changes
            $('#target_beneficiaries').on('input', function() {
                clearTimeout(window.genderAnalysisTimer);
                window.genderAnalysisTimer = setTimeout(function() {
                    analyzeGenderImpact();
                }, 1000);
            });

            // When title changes
            $('#title').on('input', function() {
                clearTimeout(window.genderAnalysisTimer);
                window.genderAnalysisTimer = setTimeout(function() {
                    analyzeGenderImpact();
                }, 1000);

                // Also run SDG analysis when title changes
                clearTimeout(window.sdgAnalysisTimer);
                window.sdgAnalysisTimer = setTimeout(function() {
                    analyzeSdgImpact();
                }, 1500);
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

            // Function to analyze SDG impact
            function analyzeSdgImpact() {
                var title = $('#title').val();
                var description = $('#description').val();

                // Validate that we have content to analyze
                if (title.trim() === '' || description.trim() === '') {
                    return; // Skip analysis if title or description is empty
                }

                // Show the AI detection panel
                $('#ai-detection-results').removeClass('d-none');
                $('#ai-loading-indicator').removeClass('d-none');
                $('#ai-detection-content').addClass('d-none');

                // Hide the manual selection options when starting analysis
                $('#manual-sdg-selection').addClass('d-none');
                $('#sub-categories').addClass('d-none');

                // Make AJAX request to analyze SDGs
                $.ajax({
                    url: '{{ route('contributor.projects.analyze-sdgs') }}',
                    type: 'POST',
                    data: {
                        title: title,
                        description: description,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Hide loading indicator and show content
                            $('#ai-loading-indicator').addClass('d-none');
                            $('#ai-detection-content').removeClass('d-none');

                            displayAiResults(response.data);
                        } else {
                            // Show error message
                            $('#ai-loading-indicator').addClass('d-none');
                            $('#ai-detection-content').removeClass('d-none');
                            $('#detected-sdgs-list').html(
                                '<div class="alert alert-danger">Error analyzing document: ' +
                                response.message + '</div>'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.log(
                            "Error connecting to AI service: " + xhr.status + " - " +
                            xhr.statusText
                        );

                        // Hide loading indicator and show content
                        $('#ai-loading-indicator').addClass('d-none');
                        $('#ai-detection-content').removeClass('d-none');

                        // Show a friendly error message to the user
                        if (xhr.status === 0) {
                            // Network error, server unreachable
                            $('#detected-sdgs-list').html(
                                '<div class="alert alert-warning">' +
                                '<h5><i class="fas fa-exclamation-triangle me-2"></i>AI Service Unavailable</h5>' +
                                '<p>The AI detection service is currently unavailable. Please use manual selection.</p>' +
                                '<button class="btn btn-primary mt-2" id="show-manual-selection-error">Switch to Manual Selection</button>' +
                                '</div>'
                            );

                            // Add event handler for the manual selection button
                            $('#show-manual-selection-error').on('click', function() {
                                switchToManualMode();
                            });
                        } else {
                            // Other errors
                            $('#detected-sdgs-list').html(
                                '<div class="alert alert-warning">' +
                                '<h5><i class="fas fa-exclamation-triangle me-2"></i>Analysis Error</h5>' +
                                '<p>There was a problem analyzing your project. Please use manual selection.</p>' +
                                '<button class="btn btn-primary mt-2" id="show-manual-selection-error">Switch to Manual Selection</button>' +
                                '</div>'
                            );

                            // Add event handler for the manual selection button
                            $('#show-manual-selection-error').on('click', function() {
                                switchToManualMode();
                            });
                        }
                    }
                });
            }

            // Function to switch to manual SDG selection mode
            function switchToManualMode() {
                // Store any AI-detected subcategory IDs that might be available
                var aiDetectedSubcategoryIds = [];
                $('.ai-detected-subcategory-input').each(function() {
                    aiDetectedSubcategoryIds.push($(this).val());
                });
                window.aiDetectedSubcategoryIds = aiDetectedSubcategoryIds;

                // Show manual selection sections
                $('#manual-sdg-selection').removeClass('d-none');
                $('#sub-categories').removeClass('d-none');
                $('#show-manual-selection').html(
                    '<i class="fas fa-robot"></i> Return to AI Selection'
                );

                // Trigger change to load subcategories with AI selections pre-checked
                var selectedSdgs = $('#sdg').val();
                if (selectedSdgs && selectedSdgs.length > 0) {
                    $('#sdg').trigger('change');
                }
            }

            // Display gender analysis results
            function displayGenderResults(data) {
                // Beneficiaries section
                var beneficiariesHtml = '<ul class="list-group">';

                if (data.benefits_women) {
                    beneficiariesHtml +=
                        '<li class="list-group-item list-group-item-success"><i class="fas fa-check-circle me-2"></i> Benefits Women/Girls';
                    if (data.women_count !== null) {
                        beneficiariesHtml += ' <span class="badge bg-info">' + data.women_count +
                            ' mentioned</span>';
                    }
                    beneficiariesHtml += '</li>';
                } else {
                    beneficiariesHtml +=
                        '<li class="list-group-item list-group-item-light"><i class="fas fa-times-circle me-2"></i> Does Not Specifically Target Women/Girls</li>';
                }

                if (data.benefits_men) {
                    beneficiariesHtml +=
                        '<li class="list-group-item list-group-item-success"><i class="fas fa-check-circle me-2"></i> Benefits Men/Boys';
                    if (data.men_count !== null) {
                        beneficiariesHtml += ' <span class="badge bg-info">' + data.men_count + ' mentioned</span>';
                    }
                    beneficiariesHtml += '</li>';
                } else {
                    beneficiariesHtml +=
                        '<li class="list-group-item list-group-item-light"><i class="fas fa-times-circle me-2"></i> Does Not Specifically Target Men/Boys</li>';
                }

                if (data.benefits_all) {
                    beneficiariesHtml +=
                        '<li class="list-group-item list-group-item-success"><i class="fas fa-check-circle me-2"></i> Benefits All Genders</li>';
                }

                beneficiariesHtml += '</ul>';

                $('#gender-beneficiaries').html(beneficiariesHtml);

                // Gender equality focus
                var equalityHtml = '<ul class="list-group">';

                if (data.addresses_gender_inequality) {
                    equalityHtml +=
                        '<li class="list-group-item list-group-item-success"><i class="fas fa-check-circle me-2"></i> Addresses Gender Inequality</li>';
                } else {
                    equalityHtml +=
                        '<li class="list-group-item list-group-item-light"><i class="fas fa-info-circle me-2"></i> No Explicit Focus on Gender Inequality</li>';
                }

                equalityHtml += '</ul>';

                $('#gender-equality-focus').html(equalityHtml);

                // Gender notes
                if (data.gender_notes) {
                    $('#gender-notes').html('<i class="fas fa-info-circle me-2"></i> ' + data.gender_notes);
                } else {
                    $('#gender-notes').html(
                        '<i class="fas fa-info-circle me-2"></i> No additional gender impact notes available.');
                }

                // Create hidden inputs to store the gender impact data
                var hiddenInputs = '';
                hiddenInputs += '<input type="hidden" name="gender_benefits_men" value="' + (data.benefits_men ?
                    '1' : '0') + '">';
                hiddenInputs += '<input type="hidden" name="gender_benefits_women" value="' + (data.benefits_women ?
                    '1' : '0') + '">';
                hiddenInputs += '<input type="hidden" name="gender_benefits_all" value="' + (data.benefits_all ?
                    '1' : '0') + '">';
                hiddenInputs += '<input type="hidden" name="gender_addresses_inequality" value="' + (data
                    .addresses_gender_inequality ? '1' : '0') + '">';

                if (data.men_count !== null) {
                    hiddenInputs += '<input type="hidden" name="gender_men_count" value="' + data.men_count + '">';
                }

                if (data.women_count !== null) {
                    hiddenInputs += '<input type="hidden" name="gender_women_count" value="' + data.women_count +
                        '">';
                }

                hiddenInputs += '<input type="hidden" name="gender_notes" value="' + (data.gender_notes || '') +
                    '">';

                $('#gender-notes').append(hiddenInputs);
            }

            // Function to display AI detection results
            function displayAiResults(results) {
                // Clear previous results
                var sdgsList = $('#detected-sdgs-list');
                var subcategoriesList = $('#detected-subcategories-list');

                sdgsList.empty();
                subcategoriesList.empty();

                // Clear any existing hidden inputs to avoid duplicates
                $('.ai-detected-subcategory-input').remove();

                // Create a Set to track unique subcategory IDs
                var addedSubcategoryIds = new Set();

                // Display detected SDGs
                if (results.sdgs && results.sdgs.length > 0) {
                    var sdgHtml = '<div class="list-group">';
                    results.sdgs.forEach(function(sdg) {
                        // Calculate confidence class based on confidence value
                        var confidenceClass = 'bg-success';
                        if (sdg.confidence < 0.7) {
                            confidenceClass = 'bg-warning';
                        } else if (sdg.confidence < 0.4) {
                            confidenceClass = 'bg-danger';
                        }

                        // Format confidence as percentage
                        var confidencePercent = Math.round(sdg.confidence * 100) + '%';

                        sdgHtml +=
                            '<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';
                        sdgHtml += '<div><i class="fas fa-check-circle text-success me-2"></i>' + sdg.name +
                            '</div>';
                        sdgHtml += '<div><span class="badge ' + confidenceClass + ' rounded-pill me-2">' +
                            confidencePercent + '</span>';
                        sdgHtml += '<span class="badge bg-primary rounded-pill">SDG ' + sdg.id +
                            '</span></div>';
                        sdgHtml += '</div>';

                        // Automatically select the SDG in the select element
                        $('#sdg option[value="' + sdg.id + '"]').prop('selected', true);
                    });
                    sdgHtml += '</div>';

                    // Add a helpful message
                    sdgHtml += '<div class="mt-3 alert alert-success">';
                    sdgHtml +=
                        '<i class="fas fa-info-circle me-2"></i>The SDG AI has analyzed your project and found ';
                    sdgHtml += results.sdgs.length + ' relevant Sustainable Development Goals. ';

                    if (results.sdgs.length > 1) {
                        sdgHtml += 'SDGs are listed in order of relevance.';
                    }

                    sdgHtml += '</div>';

                    sdgsList.html(sdgHtml);

                    // Update Select2 to reflect the changes but don't show the sections
                    $('#sdg').trigger('change.select2');
                } else {
                    sdgsList.html(
                        '<div class="alert alert-warning">No SDGs were detected in this project. Please select SDGs manually.</div>'
                    );
                }

                // Display detected subcategories
                if (results.subcategories && results.subcategories.length > 0) {
                    var subHtml = '<div class="list-group">';
                    results.subcategories.forEach(function(sub) {
                        // Skip duplicates
                        if (addedSubcategoryIds.has(sub.id)) {
                            return;
                        }

                        // Add to tracking set
                        addedSubcategoryIds.add(sub.id);

                        // Calculate confidence class
                        var confidenceClass = 'bg-success';
                        if (sub.confidence < 0.7) {
                            confidenceClass = 'bg-warning';
                        } else if (sub.confidence < 0.4) {
                            confidenceClass = 'bg-danger';
                        }

                        // Format confidence as percentage
                        var confidencePercent = sub.confidence ? Math.round(sub.confidence * 100) + '%' :
                            'N/A';

                        subHtml += '<div class="list-group-item list-group-item-action">';
                        subHtml += '<div class="d-flex w-100 justify-content-between mb-1">';
                        subHtml +=
                            '<h6 class="mb-1"><i class="fas fa-bullseye text-info me-2"></i>Target ' + sub
                            .name + '</h6>';
                        subHtml += '<span class="badge ' + confidenceClass + ' rounded-pill">' +
                            confidencePercent + '</span>';
                        subHtml += '</div>';
                        subHtml += '<p class="mb-1">' + sub.description + '</p>';
                        // Add hidden input with class for easy identification/removal
                        subHtml += '<input type="hidden" name="sdg_sub_category[]" value="' + sub.id +
                            '" class="ai-detected-subcategory-input">';
                        subHtml += '</div>';
                    });
                    subHtml += '</div>';

                    // Add helpful message
                    subHtml += '<div class="mt-3 alert alert-info">';
                    subHtml += '<i class="fas fa-info-circle me-2"></i>The AI has detected ';
                    subHtml += results.subcategories.length + ' relevant SDG targets. ';
                    subHtml += 'You can modify these selections using the button below.';
                    subHtml += '</div>';

                    subcategoriesList.html(subHtml);

                    // Also add checkboxes to the hidden manual selection area
                    $('#sub-category-checkboxes').empty(); // Clear existing checkboxes

                    // Reset the set before using it again for checkboxes
                    addedSubcategoryIds.clear();

                    results.subcategories.forEach(function(sub) {
                        // Skip duplicates
                        if (addedSubcategoryIds.has(sub.id)) {
                            return;
                        }

                        // Add to tracking set
                        addedSubcategoryIds.add(sub.id);

                        $('#sub-category-checkboxes').append(
                            '<div class="form-check">' +
                            '<input class="form-check-input" type="checkbox" name="sdg_sub_category[]" value="' +
                            sub.id + '" id="subCategory' + sub.id + '" checked>' +
                            '<label class="form-check-label" for="subCategory' + sub.id + '">' +
                            sub.name + ': ' + sub.description +
                            '</label>' +
                            '</div>'
                        );
                    });
                } else {
                    subcategoriesList.html(
                        '<div class="alert alert-info">No specific SDG targets detected. The AI suggests focusing on the main SDGs.</div>'
                    );
                }
            }

            // Add event handler for the manual selection fallback button at the top
            $('#show-manual-selection-top').on('click', function() {
                // Hide the AI detection panel
                $('#ai-detection-results').addClass('d-none');

                // Store AI-detected subcategory IDs before switching to manual mode
                var aiDetectedSubcategoryIds = [];
                $('.ai-detected-subcategory-input').each(function() {
                    aiDetectedSubcategoryIds.push($(this).val());
                });
                window.aiDetectedSubcategoryIds = aiDetectedSubcategoryIds;

                // Show the manual selection options
                $('#manual-sdg-selection').removeClass('d-none');
                $('#sub-categories').removeClass('d-none');

                // Change button text
                $(this).html('<i class="fas fa-check-circle"></i> Manual Selection Mode Active');
                $(this).addClass('btn-success').removeClass('btn-outline-primary');
                $(this).prop('disabled', true);

                // Trigger the change event to load subcategories with AI selections pre-checked
                $('#sdg').trigger('change');
            });

            // Toggle manual selection visibility
            $('#show-manual-selection').on('click', function() {
                var isHidden = $('#manual-sdg-selection').hasClass('d-none');

                // Toggle visibility of both sections
                $('#manual-sdg-selection').toggleClass('d-none');
                $('#sub-categories').toggleClass('d-none');

                if (!isHidden) {
                    // Going back to AI selection
                    $(this).html('<i class="fas fa-edit"></i> Modify AI Selection');
                } else {
                    // Showing manual selection
                    $(this).html('<i class="fas fa-robot"></i> Return to AI Selection');

                    // Now that manual selection is visible, load the subcategories first
                    var selectedSdgs = $('#sdg').val();
                    if (selectedSdgs && selectedSdgs.length > 0) {
                        // Store AI-detected subcategory IDs before triggering change
                        var aiDetectedSubcategoryIds = [];
                        $('.ai-detected-subcategory-input').each(function() {
                            aiDetectedSubcategoryIds.push($(this).val());
                        });

                        // Set a global variable to preserve the AI selections during AJAX load
                        window.aiDetectedSubcategoryIds = aiDetectedSubcategoryIds;

                        // Trigger change to load subcategories
                        $('#sdg').trigger('change');
                    }
                }
            });

            // Ensure the form captures the Quill content when submitted
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                document.getElementById('description').value = quill.root.innerHTML;
            });

            // Trigger initial analyses if fields have values
            if ($('#title').val() || oldValue || $('#target_beneficiaries').val()) {
                // Initialize analysis timers
                window.genderAnalysisTimer = null;
                window.sdgAnalysisTimer = null;

                // Run initial gender analysis
                analyzeGenderImpact();

                // Run initial SDG analysis if both title and description have content
                if ($('#title').val() && oldValue) {
                    // Slight delay to ensure content is loaded
                    setTimeout(function() {
                        analyzeSdgImpact();
                    }, 500);
                }
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Hide AI detection results initially
            $('#ai-detection-results').addClass('d-none');
            $('#manual-sdg-selection').addClass('d-none');

            // Show the Select2 SDG dropdown when using manual selection
            $('#sdg').select2();
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
                                    // Check if this subcategory was previously selected
                                    var isChecked = '';

                                    // First check previously checked boxes
                                    $('input[name="sdg_sub_category[]"]:checked').each(
                                        function() {
                                            if ($(this).val() == subCategory.id) {
                                                isChecked = 'checked';
                                                return false; // Break the loop
                                            }
                                        });

                                    // Then check if it was in the AI-detected subcategories
                                    if (window.aiDetectedSubcategoryIds &&
                                        window.aiDetectedSubcategoryIds.includes(
                                            subCategory.id.toString())) {
                                        isChecked = 'checked';
                                    }

                                    $('#sub-category-checkboxes').append(
                                        '<div class="form-check">' +
                                        '<input class="form-check-input" type="checkbox" name="sdg_sub_category[]" value="' +
                                        subCategory.id + '" id="subCategory' +
                                        subCategory.id + '" ' + isChecked +
                                        '>' +
                                        '<label class="form-check-label" for="subCategory' +
                                        subCategory.id + '">' +
                                        subCategory.sub_category_name + ': ' +
                                        subCategory.sub_category_description +
                                        '</label>' +
                                        '</div>'
                                    );
                                });
                                $('#sub-categories').show();

                                // Clear the global variable after use
                                window.aiDetectedSubcategoryIds = null;
                            } else {
                                $('#sub-categories').hide();
                            }
                        }
                    });
                } else {
                    $('#sub-categories').hide();
                }
            });

            // Initialize map
            var map = L.map('map').setView([18.3515316, 121.6489289], 16);

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

            // Set default address and marker position on load
            var defaultAddress =
                'Cagayan State University, Aparri, Maura, Cagayan, Cagayan Valley, 3515, Philippines';
            var defaultLat = 18.3515316;
            var defaultLng = 121.6489289;

            // Display default marker and address if not set
            marker = L.marker([defaultLat, defaultLng], {
                icon: redMarkerIcon
            }).addTo(map).bindPopup(createPopupMessage(defaultAddress, defaultLat, defaultLng)).openPopup();

            $('#location_address').val(defaultAddress);
            $('#latitude').val(defaultLat);
            $('#longitude').val(defaultLng);

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
            $('#confirmSubmit').click(function() {
                $('#project-form').submit();
            });
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

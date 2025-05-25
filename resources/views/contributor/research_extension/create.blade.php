@extends('layouts.contributor')

@section('title', 'Create Research')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Create Research
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
                            <h4 class="card-title">Create Research</h4>
                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="post" action="{{ route('contributor.research.store') }}"
                                enctype="multipart/form-data" id="researchForm">
                                @csrf

                                <!-- Research Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Research Title" value="{{ old('title') }}" required>
                                </div>

                                <!-- Research Categories -->
                                <div class="mb-3">
                                    <label for="researchcategory_id" class="form-label">Research Category</label>
                                    <select name="researchcategory_id" id="researchcategory_id" class="form-select"
                                        required>
                                        <option disabled selected>Choose Category</option>
                                        @foreach ($researchcategories as $category)
                                            <option value="{{ $category->id }}" @selected(old('researchcategory_id') == $category->id)>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- File Upload - Modified to upload first for AI analysis -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">Upload Research Abstract as PDF <span
                                            class="text-primary fw-bold">- Upload to automatically detect SDGs and
                                            Targets</span></label>
                                    <input type="file" class="form-control" id="file" name="file" accept=".pdf">
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Upload your research abstract as a PDF file to
                                        automatically detect relevant Sustainable
                                        Development Goals (SDGs) and their targets. <strong>Only PDF files are supported for
                                            AI analysis.</strong>
                                    </div>
                                </div>

                                <!-- Target Beneficiaries Field -->
                                <div class="mb-3">
                                    <label for="target_beneficiaries" class="form-label">Target Beneficiaries</label>
                                    <textarea class="form-control" id="target_beneficiaries" name="target_beneficiaries" rows="3"
                                        placeholder="Describe the target beneficiaries of your research (e.g., women, men, children, elderly, etc.)">{{ old('target_beneficiaries') }}</textarea>
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Specify who will benefit from this research. This
                                        information helps classify gender impact.
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

                                <!-- Add manual fallback button at the top -->
                                <div class="mb-3" id="manual-selection-fallback">
                                    <button type="button" class="btn btn-outline-primary" id="show-manual-selection-top">
                                        <i class="fas fa-edit"></i> Use Manual SDG Selection Instead
                                    </button>
                                    <div class="form-text text-muted mt-1">
                                        <i class="fas fa-info-circle"></i> If you encounter issues with the AI analysis, you
                                        can select SDGs manually.
                                    </div>
                                </div>

                                <!-- AI Detection Results Area - Improved Design -->
                                <div id="ai-detection-results" class="mb-3 d-none">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <i class="fas fa-robot me-2"></i>AI-Detected SDGs and Targets
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3" id="ai-loading-indicator">
                                                <div class="spinner-border text-primary me-3" role="status"></div>
                                                <p class="mb-0 fs-5">Analyzing document for SDG relevance...</p>
                                            </div>
                                            <div id="ai-detection-content" class="d-none">
                                                <h5 class="card-title">The AI has analyzed your file and detected the
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

                                <!-- Sustainable Development Goals (SDG) - Hidden by default -->
                                <div class="mb-3 d-none" id="manual-sdg-selection">
                                    <label for="sdg" class="form-label">Sustainable Development Goals (Manual
                                        Selection)</label>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> Select SDGs that are relevant to your
                                        research.
                                    </div>
                                    <select name="sdg[]" id="sdg" class="form-select" required multiple data-placeholder="Select relevant SDGs...">
                                        @if (count($sdgs) > 0)
                                            @foreach ($sdgs as $sdg)
                                                <option @selected(old('sdg') == $sdg->id) value="{{ $sdg->id }}">
                                                    {{ $sdg->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Sub-categories Section - Hidden by default -->
                                <div class="mb-3 d-none" id="sub-categories">
                                    <label for="sdg_sub_categories" class="form-label">SDG Targets (Manual
                                        Selection)</label>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> Select specific SDG Targets related to your
                                        chosen SDGs.
                                    </div>
                                    <div id="sub-category-checkboxes"></div>
                                    <p>
                                        Source: <a
                                            href="https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf"
                                            target="_blank">https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf</a>
                                    </p>
                                </div>
                                <!-- Research Status Dropdown -->
                                <div class="mb-3">
                                    <label for="status_id" class="form-label">Research Status</label>
                                    <select name="status_id" id="status_id" class="form-select" required>
                                        <option disabled selected>Choose Status</option>
                                        @foreach ($projectResearchStatuses as $status)
                                            <option value="{{ $status->id }}" @selected(old('status_id') == $status->id)>
                                                {{ $status->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Hidden input for is_publish -->
                                <input type="hidden" name="is_publish" value="0"> <!-- 0 indicates Draft -->

                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="file_link" class="form-label">Note: (If you have the full version of the
                                        file, please provide the link below. If not, leave it blank.)</label>
                                    <input type="text" class="form-control" id="file_link" name="file_link">
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

                                <!-- Hidden input for review_status -->
                                <input type="hidden" name="review_status" value="Forwarded to Reviewer">

                                <!-- "Submit for Review" Button -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#confirmSubmitModal">Submit for Review</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <!-- Confirmation Modal -->
                                <div class="modal fade" id="confirmSubmitModal" tabindex="-1"
                                    aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmSubmitModalLabel">Confirm Submission
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to submit this research for review? Once submitted,
                                                you will not be able to edit this draft.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary"
                                                    id="confirmSubmitButton">Submit</button>
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
            });

            // Ensure the form captures the Quill content when submitted
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                document.getElementById('description').value = quill.root.innerHTML;
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for SDGs and Research Status
            $('#sdg').select2();

            // Handle file upload for both SDG detection and gender analysis
            $('#file').on('change', function() {
                if (this.files && this.files[0]) {
                    var file = this.files[0];
                    var targetBeneficiaries = $('#target_beneficiaries').val();

                    // Show the AI detection panel for SDGs
                    $('#ai-detection-results').removeClass('d-none');
                    $('#ai-loading-indicator').removeClass('d-none');
                    $('#ai-detection-content').addClass('d-none');

                    // Also show the gender analysis panel
                    $('#gender-analysis-results').removeClass('d-none');
                    $('#gender-loading-indicator').removeClass('d-none');
                    $('#gender-analysis-content').addClass('d-none');

                    // Hide the manual selection options when file is uploaded
                    $('#manual-sdg-selection').addClass('d-none');
                    $('#sub-categories').addClass('d-none');

                    // Create FormData object to send file to backend
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    // Make AJAX request to backend endpoint for SDG analysis
                    $.ajax({
                        url: '/api/sdg-ai/analyze',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                    '<p>The AI detection service is currently unavailable. Please make sure the SDG AI Engine is running (run start_ai_engine.bat).</p>' +
                                    '<p>You can manually select SDGs and targets using the button below.</p>' +
                                    '<button class="btn btn-primary mt-2" id="show-manual-selection-error">Switch to Manual Selection</button>' +
                                    '</div>'
                                );

                                // Add event handler for the manual selection button
                                $('#show-manual-selection-error').on('click', function() {
                                    // Store any AI-detected subcategory IDs that might be available
                                    var aiDetectedSubcategoryIds = [];
                                    $('.ai-detected-subcategory-input').each(
                                        function() {
                                            aiDetectedSubcategoryIds.push($(this)
                                                .val());
                                        });
                                    window.aiDetectedSubcategoryIds =
                                        aiDetectedSubcategoryIds;

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
                                });
                            } else {
                                // Other errors
                                $('#detected-sdgs-list').html(
                                    '<div class="alert alert-warning">' +
                                    '<h5><i class="fas fa-exclamation-triangle me-2"></i>Analysis Error</h5>' +
                                    '<p>There was a problem analyzing your document. You can try again or use manual selection.</p>' +
                                    '<p>Error code: ' + xhr.status + ' - ' + xhr
                                    .statusText + '</p>' +
                                    '<button class="btn btn-primary mt-2" id="show-manual-selection-error">Switch to Manual Selection</button>' +
                                    '</div>'
                                );

                                // Add event handler for the manual selection button
                                $('#show-manual-selection-error').on('click', function() {
                                    // Store any AI-detected subcategory IDs that might be available
                                    var aiDetectedSubcategoryIds = [];
                                    $('.ai-detected-subcategory-input').each(
                                        function() {
                                            aiDetectedSubcategoryIds.push($(this)
                                                .val());
                                        });
                                    window.aiDetectedSubcategoryIds =
                                        aiDetectedSubcategoryIds;

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
                                });
                            }
                        }
                    });

                    // Make a separate AJAX request for gender analysis
                    var genderFormData = new FormData();
                    genderFormData.append('file', file);
                    genderFormData.append('target_beneficiaries', targetBeneficiaries);
                    genderFormData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    $.ajax({
                        url: '/api/gender-ai/analyze-file',
                        type: 'POST',
                        data: genderFormData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                            displayGenderAnalysisError();
                        }
                    });
                }
            });

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

            // Add event handler to update gender analysis when target beneficiaries changes
            $('#target_beneficiaries').on('change', function() {
                // If a file is already uploaded, re-analyze with the new beneficiaries text
                if ($('#file')[0].files && $('#file')[0].files[0] && $('#gender-analysis-results').is(
                        ':visible')) {
                    // Show loading
                    $('#gender-loading-indicator').removeClass('d-none');
                    $('#gender-analysis-content').addClass('d-none');

                    var file = $('#file')[0].files[0];
                    var targetBeneficiaries = $(this).val();

                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('target_beneficiaries', targetBeneficiaries);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    $.ajax({
                        url: '/api/gender-ai/analyze-file',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                            displayGenderAnalysisError();
                        }
                    });
                }
            });

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

            function simulateAiDetection() {
                // Randomly select 2-3 SDGs for demo purposes
                var allSdgs = [];
                $('#sdg option').each(function() {
                    allSdgs.push({
                        id: $(this).val(),
                        name: $(this).text().trim()
                    });
                });

                // Shuffle and pick 2-3 random SDGs
                allSdgs.sort(() => 0.5 - Math.random());
                var selectedSdgs = allSdgs.slice(0, Math.floor(Math.random() * 2) + 2);

                // Create mock data structure
                var mockDetectionResults = {
                    sdgs: selectedSdgs,
                    subcategories: []
                };

                // Simulate 1-2 subcategories for each SDG
                selectedSdgs.forEach(sdg => {
                    var subCount = Math.floor(Math.random() * 2) + 1;
                    for (var i = 1; i <= subCount; i++) {
                        mockDetectionResults.subcategories.push({
                            id: Math.floor(Math.random() * 100) + 1,
                            name: sdg.id + '.' + i,
                            description: 'Simulated target for SDG ' + sdg.name
                        });
                    }
                });

                // Display the simulated results
                displayAiResults(mockDetectionResults);
            }

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
                        '<i class="fas fa-info-circle me-2"></i>The SDG AI has analyzed your document and found ';
                    sdgHtml += results.sdgs.length + ' relevant Sustainable Development Goals. ';

                    if (results.sdgs.length > 1) {
                        sdgHtml += 'SDGs are listed in order of relevance.';
                    }

                    sdgHtml += '</div>';

                    sdgsList.html(sdgHtml);

                    // Update Select2 to reflect the changes but don't show the sections
                    // We'll use a flag to prevent the change event from showing the sections
                    $('#sdg').trigger('change.select2');
                } else {
                    sdgsList.html(
                        '<div class="alert alert-warning">No SDGs were detected in this document. Please select SDGs manually.</div>'
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

            $('#sdg').on('change', function() {
                var selectedSdgs = $(this).val();
                if (selectedSdgs.length > 0) {
                    // Only load subcategories if manual selection is visible
                    if (!$('#manual-sdg-selection').hasClass('d-none')) {
                        // Show loading indicator
                        $('#sub-category-checkboxes').html(
                            '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading SDG Targets...</p></div>'
                        );

                        // Store currently checked subcategories to preserve them
                        var checkedSubcategories = [];
                        $('input[name="sdg_sub_category[]"]:checked').each(function() {
                            checkedSubcategories.push($(this).val());
                        });

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
                                        if (checkedSubcategories.includes(subCategory.id
                                                .toString())) {
                                            isChecked = 'checked';
                                        }

                                        // Then check if it was in the AI-detected subcategories
                                        // This prioritizes AI detection when coming from the AI panel
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

                                    // Clear the global variable after use
                                    window.aiDetectedSubcategoryIds = null;
                                } else {
                                    $('#sub-category-checkboxes').html(
                                        '<div class="alert alert-info">No targets available for the selected SDGs.</div>'
                                    );
                                }
                            },
                            error: function() {
                                $('#sub-category-checkboxes').html(
                                    '<div class="alert alert-danger">Error loading SDG targets. Please try again.</div>'
                                );
                            }
                        });
                    }
                } else {
                    $('#sub-category-checkboxes').empty();
                }
            });

            // Handle confirmation modal submission
            $('#confirmSubmitButton').on('click', function() {
                $('#researchForm').submit();
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
                        '{{ route('contributor.research.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href =
                    '{{ route('contributor.research.index') }}'; // Redirect to home or desired route
            }
        });
    </script>

    <script>
        // Helper function to display gender analysis error consistently
        function displayGenderAnalysisError() {
            // Hide loading indicator and show error
            $('#gender-loading-indicator').addClass('d-none');
            $('#gender-analysis-content').removeClass('d-none');
            $('#gender-notes').html(
                '<div class="alert alert-warning">' +
                '<h5><i class="fas fa-exclamation-triangle me-2"></i>Gender Analysis Error</h5>' +
                '<p>There was a problem analyzing gender impact. The AI service may be unavailable or your content needs more information.</p>' +
                '<p>Please fill in the Target Beneficiaries field manually to improve results.</p>' +
                '</div>'
            );
        }
    </script>
@endsection

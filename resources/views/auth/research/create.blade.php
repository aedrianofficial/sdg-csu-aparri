@extends('layouts.admin')

@section('title', 'Create Research Report')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('auth.dashboard') }}">Dashboard</a></li>
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

                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="post" action="{{ route('research.store') }}" class="needs-validation"
                                enctype="multipart/form-data" id="research-form" novalidate>
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
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">File (Abstract, maximum of <strong>2mb</strong>
                                        only)</label>
                                    <input type="file" class="form-control" id="file" name="file">
                                </div>

                                <!-- Target Beneficiaries Field -->
                                <div class="mb-3">
                                    <label for="target_beneficiaries" class="form-label">Target Beneficiaries</label>
                                    <textarea class="form-control" id="target_beneficiaries" name="target_beneficiaries" 
                                        rows="3" placeholder="Describe the target beneficiaries of your research (e.g., women, men, children, elderly, etc.)">{{ old('target_beneficiaries') }}</textarea>
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Specify who will benefit from this research. This information helps classify gender impact.
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
                                
                                <!-- Add manual fallback button -->
                                <div class="mb-3" id="manual-selection-fallback">
                                    <button type="button" class="btn btn-outline-primary" id="show-manual-selection-top">
                                        <i class="fas fa-edit"></i> Use Manual SDG Selection Instead
                                    </button>
                                    <div class="form-text text-muted mt-1">
                                        <i class="fas fa-info-circle"></i> If you prefer, you can select SDGs manually without using AI analysis.
                                    </div>
                                </div>
                                
                                <!-- Sustainable Development Goals (SDG) -->
                                <div class="mb-3" id="manual-sdg-selection">
                                    <label for="sdg" class="form-label">Sustainable Development Goals (Click to select
                                        SDGs)</label>
                                    <select name="sdg[]" id="sdg" class="form-select select2-multiple"
                                        multiple="multiple" required>
                                        @foreach ($sdgs as $sdg)
                                            <option @selected(old('sdg') == $sdg->id) value="{{ $sdg->id }}">
                                                {{ $sdg->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                                <p class="mb-0 fs-5">Analyzing research for SDG relevance...</p>
                                            </div>
                                            <div id="ai-detection-content" class="d-none">
                                                <h5 class="card-title">The AI has analyzed your research and detected the following:</h5>
                                                <h6 class="mt-3 mb-2">Detected Sustainable Development Goals:</h6>
                                                <div id="detected-sdgs-list" class="mb-3"></div>
                                                <h6 class="mt-4 mb-2">Detected SDG Targets:</h6>
                                                <div id="detected-subcategories-list"></div>
                                                <div id="selected-subcategories-container" class="d-none"></div>
                                                <div class="mt-4">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="show-manual-selection">
                                                        <i class="fas fa-edit"></i> Modify AI Selection
                                                    </button>
                                                    <p class="form-text text-muted mt-2">
                                                        <i class="fas fa-info-circle"></i> AI-detected targets will be submitted. Click the button above to manually adjust if needed.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
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

                                <!-- Research Status Dropdown -->
                                <div class="mb-3">
                                    <label for="status_id" class="form-label">Research Status</label>
                                    <select name="status_id" id="status_id" class="form-select" required>
                                        <option disabled selected>Choose Status</option>
                                        @foreach ($statuses as $status)
                                            <!-- Assuming you pass $statuses to the view -->
                                            <option value="{{ $status->id }}" @selected(old('status_id') == $status->id)>
                                                {{ $status->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Hidden input for is_publish -->
                                <input type="hidden" name="is_publish" value="0">

                                <!-- Hidden input for review_status -->
                                <input type="hidden" name="review_status" value="Forwarded to Reviewer">

                                <!-- Submission Buttons -->
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#submitReviewModal">Submit for Review</button>
                                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                    data-bs-target="#publishModal">Publish Immediately</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>

                                <!-- Modals for Confirmation -->
                                <input type="hidden" name="submit_type" id="submit_type">

                                <!-- "Submit for Review" Confirmation Modal -->
                                <div class="modal fade" id="submitReviewModal" tabindex="-1"
                                    aria-labelledby="submitReviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="submitReviewModalLabel">Confirm Submission
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to submit this research for review?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" id="confirmSubmitReview"
                                                    class="btn btn-primary">Submit for Review</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- "Publish Immediately" Confirmation Modal -->
                                <div class="modal fade" id="publishModal" tabindex="-1"
                                    aria-labelledby="publishModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="publishModalLabel">Confirm Publish</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to publish this research immediately?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" id="confirmPublish"
                                                    class="btn btn-success">Publish</button>
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
            $('#sdg').select2();

            // Initialize flags for AI analysis
            window.genderAnalysisTimer = null;
            window.sdgAnalysisTimer = null;
            
            // Handle file upload change for both gender analysis and SDG detection
            $('#file').on('change', function() {
                if (this.files && this.files[0]) {
                    var file = this.files[0];
                    
                    // Get target beneficiaries text
                    var targetBeneficiaries = $('#target_beneficiaries').val();
                    
                    // Show the gender analysis panel
                    $('#gender-analysis-results').removeClass('d-none');
                    $('#gender-loading-indicator').removeClass('d-none');
                    $('#gender-analysis-content').addClass('d-none');
                    
                    // Create FormData object for gender analysis
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('target_beneficiaries', targetBeneficiaries);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    
                    // Analyze gender impact
                    $.ajax({
                        url: '{{ route('research.analyze-gender') }}',
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
                                    '<div class="alert alert-danger">' +
                                    '<h5><i class="fas fa-exclamation-triangle me-2"></i>Analysis Error</h5>' +
                                    '<p>' + (response.message || 'Error analyzing gender impact') + '</p>' +
                                    '</div>'
                                );
                            }
                        },
                        error: function(xhr) {
                            console.log("Gender analysis error: " + xhr.status + " - " + xhr.statusText);
                            
                            // Hide loading indicator and show error
                            $('#gender-loading-indicator').addClass('d-none');
                            $('#gender-analysis-content').removeClass('d-none');
                            $('#gender-notes').html(
                                '<div class="alert alert-warning">' +
                                '<h5><i class="fas fa-exclamation-triangle me-2"></i>Gender Analysis Error</h5>' +
                                '<p>There was a problem analyzing gender impact. The service may be unavailable or your content may need more information.</p>' +
                                '<p class="small text-muted">Error details: ' + xhr.status + ' ' + xhr.statusText + '</p>' +
                                '</div>'
                            );
                        }
                    });
                    
                    // Start the SDG analysis
                    analyzeForSdgs(file);
                }
            });
            
            // Update gender analysis when target beneficiaries field changes
            $('#target_beneficiaries').on('input', function() {
                var file = $('#file')[0].files && $('#file')[0].files[0];
                var targetBeneficiaries = $(this).val();
                var title = $('#title').val();
                var description = $('#description').val();
                
                // If we have content to analyze
                if ((file || (title && description)) && targetBeneficiaries) {
                    clearTimeout(window.genderAnalysisTimer);
                    window.genderAnalysisTimer = setTimeout(function() {
                        // Show the gender analysis panel
                        $('#gender-analysis-results').removeClass('d-none');
                        $('#gender-loading-indicator').removeClass('d-none');
                        $('#gender-analysis-content').addClass('d-none');
                        
                        if (file) {
                            // Analyze with file upload
                            var formData = new FormData();
                            formData.append('file', file);
                            formData.append('target_beneficiaries', targetBeneficiaries);
                            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                            
                            $.ajax({
                                url: '{{ route('research.analyze-gender') }}',
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
                                        displayGenderResults(response.data);
                                    } else {
                                        // Show error message
                                        $('#gender-loading-indicator').addClass('d-none');
                                        $('#gender-analysis-content').removeClass('d-none');
                                        $('#gender-notes').html(
                                            '<div class="alert alert-danger">' +
                                            '<h5><i class="fas fa-exclamation-triangle me-2"></i>Analysis Error</h5>' +
                                            '<p>' + (response.message || 'Error analyzing gender impact') + '</p>' +
                                            '</div>'
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
                                        '<p>There was a problem analyzing gender impact.</p>' +
                                        '<p class="small text-muted">Error details: ' + xhr.status + ' ' + xhr.statusText + '</p>' +
                                        '</div>'
                                    );
                                }
                            });
                        } else if (title && description) {
                            // Analyze with just text
                            $.ajax({
                                url: '{{ route('research.analyze-gender-text') }}',
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
                                        displayGenderResults(response.data);
                                    } else {
                                        // Show error message
                                        $('#gender-loading-indicator').addClass('d-none');
                                        $('#gender-analysis-content').removeClass('d-none');
                                        $('#gender-notes').html(
                                            '<div class="alert alert-danger">' +
                                            '<h5><i class="fas fa-exclamation-triangle me-2"></i>Analysis Error</h5>' +
                                            '<p>' + (response.message || 'Error analyzing gender impact') + '</p>' +
                                            '</div>'
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
                                        '<p>There was a problem analyzing gender impact.</p>' +
                                        '<p class="small text-muted">Error details: ' + xhr.status + ' ' + xhr.statusText + '</p>' +
                                        '</div>'
                                    );
                                }
                            });
                        }
                    }, 1000);
                }
            });
            
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
            
            // Function to analyze file for SDGs
            function analyzeForSdgs(file) {
                // Show the AI detection panel
                $('#ai-detection-results').removeClass('d-none');
                $('#ai-loading-indicator').removeClass('d-none');
                $('#ai-detection-content').addClass('d-none');
                
                // Hide the manual selection initially
                $('#manual-sdg-selection').addClass('d-none');
                $('#sub-categories').addClass('d-none');
                
                // Create FormData for SDG analysis
                var formData = new FormData();
                formData.append('file', file);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                // Send to API for analysis
                $.ajax({
                    url: '/api/sdg-ai/analyze',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Hide loading indicator and show content
                            $('#ai-loading-indicator').addClass('d-none');
                            $('#ai-detection-content').removeClass('d-none');
                            
                            // Display results
                            displayAiResults(response.data);
                        } else {
                            // Show error message
                            $('#ai-loading-indicator').addClass('d-none');
                            $('#ai-detection-content').removeClass('d-none');
                            $('#detected-sdgs-list').html(
                                '<div class="alert alert-danger">' +
                                '<h5><i class="fas fa-exclamation-triangle me-2"></i>Analysis Error</h5>' +
                                '<p>' + (response.message || 'Error analyzing document') + '</p>' +
                                '<button class="btn btn-primary mt-2" id="show-manual-selection-error">Switch to Manual Selection</button>' +
                                '</div>'
                            );
                            
                            // Add event handler for the manual selection button
                            $('#show-manual-selection-error').on('click', function() {
                                switchToManualMode();
                            });
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
                                '<p>The AI detection service is currently unavailable. Please make sure the SDG AI Engine is running.</p>' +
                                '<p class="small text-muted">Make sure the SDG AI Engine is running.</p>' +
                                '<button class="btn btn-primary mt-2" id="show-manual-selection-error">Switch to Manual Selection</button>' +
                                '</div>'
                            );
                        } else if (xhr.status === 422) {
                            // Validation error
                            $('#detected-sdgs-list').html(
                                '<div class="alert alert-warning">' +
                                '<h5><i class="fas fa-exclamation-triangle me-2"></i>Validation Error</h5>' +
                                '<p>The file you provided couldn\'t be processed. Please provide a valid PDF file or use manual selection.</p>' +
                                '<button class="btn btn-primary mt-2" id="show-manual-selection-error">Switch to Manual Selection</button>' +
                                '</div>'
                            );
                        } else if (xhr.status === 500) {
                            // Server error
                            $('#detected-sdgs-list').html(
                                '<div class="alert alert-warning">' +
                                '<h5><i class="fas fa-exclamation-triangle me-2"></i>Server Error</h5>' +
                                '<p>There was a problem with the AI service. Please use manual selection instead.</p>' +
                                '<p class="small text-muted">Error details: ' + xhr.status + ' ' + xhr.statusText + '</p>' +
                                '<button class="btn btn-primary mt-2" id="show-manual-selection-error">Switch to Manual Selection</button>' +
                                '</div>'
                            );
                        } else {
                            // Other errors
                            $('#detected-sdgs-list').html(
                                '<div class="alert alert-warning">' +
                                '<h5><i class="fas fa-exclamation-triangle me-2"></i>Analysis Error</h5>' +
                                '<p>There was a problem analyzing your research. Please use manual selection.</p>' +
                                '<p class="small text-muted">Error details: ' + xhr.status + ' ' + xhr.statusText + '</p>' +
                                '<button class="btn btn-primary mt-2" id="show-manual-selection-error">Switch to Manual Selection</button>' +
                                '</div>'
                            );
                        }

                        // Add event handler for the manual selection button
                        $('#show-manual-selection-error').on('click', function() {
                            switchToManualMode();
                        });
                    }
                });
            }
            
            // Function to switch to manual SDG selection mode
            function switchToManualMode() {
                // Hide the AI detection panel
                $('#ai-detection-results').addClass('d-none');
                
                // Store any AI-detected subcategory IDs that might be available
                var aiDetectedSubcategoryIds = [];
                $('.ai-detected-subcategory-input').each(function() {
                    aiDetectedSubcategoryIds.push($(this).val());
                });
                window.aiDetectedSubcategoryIds = aiDetectedSubcategoryIds;
                
                // Show the manual selection options
                $('#manual-sdg-selection').removeClass('d-none');
                $('#sub-categories').removeClass('d-none');
                
                // Update button text
                $('#show-manual-selection-top').html('<i class="fas fa-check-circle"></i> Manual Selection Mode Active');
                $('#show-manual-selection-top').addClass('btn-success').removeClass('btn-outline-primary');
                $('#show-manual-selection-top').prop('disabled', true);
                
                // Trigger change to load subcategories with AI selections pre-checked
                var selectedSdgs = $('#sdg').val();
                if (selectedSdgs && selectedSdgs.length > 0) {
                    $('#sdg').trigger('change');
                }
            }
            
            // Function to display AI results
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
                        
                        sdgHtml += '<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';
                        sdgHtml += '<div><i class="fas fa-check-circle text-success me-2"></i>' + sdg.name + '</div>';
                        sdgHtml += '<div><span class="badge ' + confidenceClass + ' rounded-pill me-2">' + confidencePercent + '</span>';
                        sdgHtml += '<span class="badge bg-primary rounded-pill">SDG ' + sdg.id + '</span></div>';
                        sdgHtml += '</div>';
                        
                        // Automatically select the SDG in the select element
                        $('#sdg option[value="' + sdg.id + '"]').prop('selected', true);
                    });
                    sdgHtml += '</div>';
                    
                    // Add a helpful message
                    sdgHtml += '<div class="mt-3 alert alert-success">';
                    sdgHtml += '<i class="fas fa-info-circle me-2"></i>The SDG AI has analyzed your research and found ';
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
                        var confidencePercent = sub.confidence ? Math.round(sub.confidence * 100) + '%' : 'N/A';
                        
                        subHtml += '<div class="list-group-item list-group-item-action">';
                        subHtml += '<div class="d-flex w-100 justify-content-between mb-1">';
                        subHtml += '<h6 class="mb-1"><i class="fas fa-bullseye text-info me-2"></i>Target ' + sub.name + '</h6>';
                        subHtml += '<span class="badge ' + confidenceClass + ' rounded-pill">' + confidencePercent + '</span>';
                        subHtml += '</div>';
                        subHtml += '<p class="mb-1">' + sub.description + '</p>';
                        // Add hidden input with class for easy identification/removal
                        subHtml += '<input type="hidden" name="sdg_sub_category[]" value="' + sub.id + '" class="ai-detected-subcategory-input">';
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
            
            // Show/hide manual selection when requested
            $('#show-manual-selection').on('click', function() {
                var isHidden = $('#manual-sdg-selection').hasClass('d-none');
                
                if (isHidden) {
                    // Show manual selection
                    switchToManualMode();
                    
                    // Change button text
                    $(this).html('<i class="fas fa-robot"></i> Return to AI Selection');
                } else {
                    // Hide manual selection, show AI selection
                    $('#manual-sdg-selection').addClass('d-none');
                    $('#sub-categories').addClass('d-none');
                    $('#ai-detection-results').removeClass('d-none');
                    
                    // Change button text
                    $(this).html('<i class="fas fa-edit"></i> Modify AI Selection');
                }
            });
            
            // Handle the manual selection fallback button
            $('#show-manual-selection-top').on('click', function() {
                switchToManualMode();
            });
            
            // Handle SDG select change for subcategories
            $('#sdg').on('change', function() {
                var selectedSdgs = $(this).val();
                if (selectedSdgs && selectedSdgs.length > 0) {
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
                                    // Check if this subcategory was previously selected by AI
                                    var isChecked = '';
                                    
                                    if (window.aiDetectedSubcategoryIds && 
                                        window.aiDetectedSubcategoryIds.includes(subCategory.id.toString())) {
                                        isChecked = 'checked';
                                    }
                                    
                                    $('#sub-category-checkboxes').append(
                                        '<div class="form-check">' +
                                        '<input class="form-check-input" type="checkbox" name="sdg_sub_category[]" value="' +
                                        subCategory.id + '" id="subCategory' + subCategory.id + '" ' + isChecked + '>' +
                                        '<label class="form-check-label" for="subCategory' + subCategory.id + '">' +
                                        subCategory.sub_category_name + ': ' + subCategory.sub_category_description +
                                        '</label>' +
                                        '</div>'
                                    );
                                });
                                $('#sub-categories').show();
                            } else {
                                $('#sub-categories').hide();
                            }
                            
                            // Clear the saved AI selections after applying them
                            window.aiDetectedSubcategoryIds = null;
                        },
                        error: function() {
                            $('#sub-category-checkboxes').html(
                                '<div class="alert alert-danger">Error loading SDG targets. Please try again.</div>'
                            );
                        }
                    });
                } else {
                    $('#sub-categories').hide();
                }
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
                    window.location.href = '{{ route('research.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href = '{{ route('research.index') }}'; // Redirect to home or desired route
            }
        });
    </script>
@endsection

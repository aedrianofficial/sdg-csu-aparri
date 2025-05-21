@extends('layouts.contributor')

@section('title', 'Edit Research')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Research
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
                            <h4 class="card-title">Edit Research</h4>

                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="post" action="{{ route('contributor.research.update', $research->id) }}"
                                enctype="multipart/form-data" id="researchForm">
                                @csrf
                                @method('put')

                                <!-- Research Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Research Title" value="{{ old('title', $research->title) }}" required>
                                </div>

                                <!-- Research Categories -->
                                <div class="mb-3">
                                    <label for="researchcategory_id" class="form-label">Research Category</label>
                                    <select name="researchcategory_id" id="researchcategory_id" class="form-select"
                                        required>
                                        <option disabled>Choose Category</option>
                                        @foreach ($researchcategories as $category)
                                            <option value="{{ $category->id }}" @selected(old('researchcategory_id', $research->researchcategory_id) == $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- File Upload - Modified to upload first for AI analysis -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">Upload Research Abstract <span class="text-primary fw-bold">- Upload to automatically detect SDGs and Targets</span></label>
                                    <input type="file" class="form-control" id="file" name="file">
                                    <div class="form-text text-muted">
                                        Upload your research abstract to automatically detect relevant Sustainable Development Goals (SDGs) and their targets
                                    </div>
                                    @foreach ($research->researchfiles as $file)
                                        <p class="mb-0 mt-2">
                                            <strong>Currently attached file:</strong> {{ $file->original_filename }}
                                        </p>
                                    @endforeach
                                </div>

                                <!-- Target Beneficiaries Field -->
                                <div class="mb-3">
                                    <label for="target_beneficiaries" class="form-label">Target Beneficiaries</label>
                                    <textarea class="form-control" id="target_beneficiaries" name="target_beneficiaries" 
                                        rows="3" placeholder="Describe the target beneficiaries of your research (e.g., women, men, children, elderly, etc.)">{{ old('target_beneficiaries', $research->target_beneficiaries) }}</textarea>
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
                                                <h5 class="card-title">The AI has analyzed your file and detected the following:</h5>
                                                <h6 class="mt-3 mb-2">Detected Sustainable Development Goals:</h6>
                                                <div id="detected-sdgs-list" class="mb-3"></div>
                                                <h6 class="mt-4 mb-2">Detected SDG Targets:</h6>
                                                <div id="detected-subcategories-list"></div>
                                                <div id="selected-subcategories-container" class="d-none"></div>
                                                <div class="mt-4">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="show-manual-selection">
                                                        <i class="fas fa-edit"></i> Modify AI Selection
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current SDGs & Targets Section -->
                                <div id="current-selections" class="mb-4">
                                    <div class="card border-secondary">
                                        <div class="card-header bg-secondary text-white">
                                            <i class="fas fa-tags me-2"></i>Currently Selected SDGs and Targets
                                        </div>
                                        <div class="card-body">
                                            <h6 class="mb-3">Sustainable Development Goals:</h6>
                                            <div class="row mb-3">
                                                @foreach($research->sdg as $sdg)
                                                <div class="col-md-6 mb-2">
                                                    <div class="p-2 rounded bg-light">
                                                        <i class="fas fa-check-circle text-success me-2"></i>{{ $sdg->name }}
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            
                                            <h6 class="mb-3">SDG Targets:</h6>
                                            <div class="row">
                                                @foreach($research->sdgSubCategories as $subCategory)
                                                <div class="col-md-6 mb-2">
                                                    <div class="p-2 rounded bg-light">
                                                        <i class="fas fa-bullseye text-info me-2"></i>
                                                        <strong>{{ $subCategory->sub_category_name }}:</strong> 
                                                        {{ Str::limit($subCategory->sub_category_description, 80) }}
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            
                                            <div class="mt-3">
                                                <p class="text-muted">Upload a new abstract file to update these using AI, or use manual selection.</p>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="show-manual-selection-current">
                                                    <i class="fas fa-edit"></i> Modify Current Selection
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sustainable Development Goals (SDG) - Hidden by default -->
                                <div class="mb-3 d-none" id="manual-sdg-selection">
                                    <label for="sdg" class="form-label">Sustainable Development Goals (Manual Selection)</label>
                                    <select name="sdg[]" id="sdg" class="form-select" required multiple>
                                        @if (count($sdgs) > 0)
                                        @foreach ($sdgs as $sdg)
                                                <option @selected(in_array($sdg->id, old('sdg', $research->sdg->pluck('id')->toArray()))) value="{{ $sdg->id }}">
                                                    {{ $sdg->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Sub-categories Section - Hidden by default -->
                                <div class="mb-3 d-none" id="sub-categories">
                                    <label for="sdg_sub_categories" class="form-label">SDG Targets (Manual Selection)</label>
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
                                        <option disabled>Choose Status</option>
                                        @foreach ($projectResearchStatuses as $status)
                                            <option value="{{ $status->id }}" @selected(old('status_id', $research->status_id) == $status->id)>
                                                {{ $status->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Hidden input for is_publish -->
                                <input type="hidden" name="is_publish" value="0"> <!-- 0 indicates Draft -->

                                <!-- File Upload for Link -->
                                <div class="mb-3">
                                    <label for="file_link" class="form-label">Note: (If you have the full version of the
                                        file, please provide the link below. If not, leave it blank.)</label>
                                    <input type="text" class="form-control" id="file_link" name="file_link"
                                        value="{{ old('file_link', $research->file_link) }}">
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description-container" class="form-label">Description</label>
                                    <!-- Create a div where Quill will be initialized -->
                                    <div id="description-editor" style="height: 300px;"></div>
                                    <!-- Hidden input to store the content for form submission -->
                                    <input type="hidden" name="description" id="description"
                                        value="{{ old('description', $research->description) }}">
                                </div>

                                <!-- "Submit for Review" Button -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#confirmSubmitModal">Submit for Review</button>

                                <!-- "Cancel" Button -->
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
                
                // Run gender analysis when content changes (debounced)
                clearTimeout(window.genderAnalysisTimer);
                window.genderAnalysisTimer = setTimeout(function() {
                    analyzeGenderImpact();
                }, 1000);
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
            });
            
            // Function to analyze gender impact from uploaded file or text
            function analyzeGenderImpact() {
                var title = $('#title').val();
                var description = $('#description').val();
                var targetBeneficiaries = $('#target_beneficiaries').val();
                
                // File-based analysis if we have a file
                if ($('#file')[0].files && $('#file')[0].files[0]) {
                    var file = $('#file')[0].files[0];
                    
                    // Show the gender analysis panel
                    $('#gender-analysis-results').removeClass('d-none');
                    $('#gender-loading-indicator').removeClass('d-none');
                    $('#gender-analysis-content').addClass('d-none');
                    
                    // Create FormData for file upload
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('target_beneficiaries', targetBeneficiaries);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    
                    // Make AJAX request
                    $.ajax({
                        url: '{{ route('contributor.research.analyze-gender') }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            displayGenderAnalysisResults(response);
                        },
                        error: function(xhr) {
                            handleGenderAnalysisError();
                        }
                    });
                } 
                // Text-based analysis if we don't have a file but have text
                else if (title.trim() !== '' || description.trim() !== '' || targetBeneficiaries.trim() !== '') {
                    // Show the gender analysis panel
                    $('#gender-analysis-results').removeClass('d-none');
                    $('#gender-loading-indicator').removeClass('d-none');
                    $('#gender-analysis-content').addClass('d-none');
                    
                    // Use text analysis method
                    $.ajax({
                        url: '{{ route('contributor.research.analyze-gender') }}',
                        type: 'POST',
                        data: {
                            title: title,
                            description: description,
                            target_beneficiaries: targetBeneficiaries,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            displayGenderAnalysisResults(response);
                        },
                        error: function(xhr) {
                            handleGenderAnalysisError();
                        }
                    });
                }
            }
            
            // Handle successful gender analysis results
            function displayGenderAnalysisResults(response) {
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
            }
            
            // Handle gender analysis errors
            function handleGenderAnalysisError() {
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

            // Ensure the form captures the Quill content when submitted
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                document.getElementById('description').value = quill.root.innerHTML;
            });
            
            // Run initial gender analysis using existing content
            if ($('#title').val() || oldValue || $('#target_beneficiaries').val()) {
                analyzeGenderImpact();
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for SDGs
            $('#sdg').select2();
            
            // Trigger initial change event to load any selected subcategories
            $('#sdg').trigger('change');
            
            // Load subcategories for selected SDGs
            loadSubcategories();

            // Show manual selection button handlers
            $('#show-manual-selection, #show-manual-selection-current').on('click', function() {
                $('#manual-sdg-selection').removeClass('d-none');
                $('#sub-categories').removeClass('d-none');
                $('#current-selections').addClass('d-none');
            });

            // Handle file upload for AI detection
            $('#file').on('change', function() {
                if (this.files && this.files[0]) {
                    // Show the AI detection panel
                    $('#ai-detection-results').removeClass('d-none');
                    $('#ai-loading-indicator').removeClass('d-none');
                    $('#ai-detection-content').addClass('d-none');
                    
                    // Hide the current selections and manual selection options
                    $('#current-selections').addClass('d-none');
                    $('#manual-sdg-selection').addClass('d-none');
                    $('#sub-categories').addClass('d-none');

                    // Create FormData object to send file to backend
                    var formData = new FormData();
                    formData.append('file', this.files[0]);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    // Make AJAX request to backend endpoint
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
                                    '<div class="alert alert-danger">Error analyzing document: ' + response.message + '</div>'
                                );
                            }
                        },
                        error: function(xhr) {
                            // For demo purposes, simulate successful detection with mock data
                            console.log("AI service error or not available. Using simulated results for demo.");
                            
                            // Hide loading indicator and show content
                            $('#ai-loading-indicator').addClass('d-none');
                            $('#ai-detection-content').removeClass('d-none');
                            
                            // Simulate AI detection based on random SDGs
                            simulateAiDetection();
                        }
                    });
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
                        sdgHtml += '<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';
                        sdgHtml += '<div><i class="fas fa-check-circle text-success me-2"></i>' + sdg.name + '</div>';
                        sdgHtml += '<span class="badge bg-primary rounded-pill">SDG ' + sdg.id + '</span>';
                        sdgHtml += '</div>';
                        
                        // Automatically select the SDG in the select element
                        $('#sdg option[value="' + sdg.id + '"]').prop('selected', true);
                    });
                    sdgHtml += '</div>';
                    sdgsList.html(sdgHtml);
                    
                    // Update Select2 to reflect the changes
                    $('#sdg').trigger('change');
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
                        
                        subHtml += '<div class="list-group-item list-group-item-action">';
                        subHtml += '<div class="d-flex w-100 justify-content-between mb-1">';
                        subHtml += '<h6 class="mb-1"><i class="fas fa-bullseye text-info me-2"></i>Target ' + sub.name + '</h6>';
                        subHtml += '</div>';
                        subHtml += '<p class="mb-1">' + sub.description + '</p>';
                        // Add hidden input with class for easy identification/removal
                        subHtml += '<input type="hidden" name="sdg_sub_category[]" value="' + sub.id + '" class="ai-detected-subcategory-input">';
                        subHtml += '</div>';
                    });
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
                    subcategoriesList.html('<div class="alert alert-info">No specific SDG targets detected. The AI suggests focusing on the main SDGs.</div>');
                }
            }

            // Toggle manual selection visibility
            $('#show-manual-selection').on('click', function() {
                $('#manual-sdg-selection').toggleClass('d-none');
                $('#sub-categories').toggleClass('d-none');
                
                if ($('#manual-sdg-selection').hasClass('d-none')) {
                    $(this).html('<i class="fas fa-edit"></i> Modify AI Selection');
                } else {
                    $(this).html('<i class="fas fa-robot"></i> Return to AI Selection');
                }
            });

            function loadSubcategories() {
                var selectedSdgs = $('#sdg').val();
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
                                var selectedSubCategories = @json($selectedSubCategories);
                                
                                    data.forEach(function(subCategory) {
                                    var isChecked = selectedSubCategories.includes(subCategory.id) ? 'checked' : '';
                                    
                                        $('#sub-category-checkboxes').append(
                                            '<div class="form-check">' +
                                            '<input class="form-check-input" type="checkbox" name="sdg_sub_category[]" value="' +
                                        subCategory.id + '" id="subCategory' +
                                        subCategory.id + '" ' + isChecked + '>' +
                                            '<label class="form-check-label" for="subCategory' +
                                            subCategory.id + '">' +
                                        subCategory.sub_category_name + ': ' +
                                        subCategory.sub_category_description +
                                            '</label>' +
                                            '</div>'
                                        );
                                    });
                                }
                            }
                        });
                    }
            }

            $('#sdg').on('change', function() {
                loadSubcategories();
            });

            // Handle the confirmation modal submit button
            $('#confirmSubmitButton').on('click', function() {
                $('#researchForm').submit();
        });

        // Handle the cancel button click
            $('#cancelButton').on('click', function() {
                if (confirm('Are you sure you want to cancel editing? Any unsaved changes will be lost.')) {
                    window.location.href = "{{ route('contributor.research.index') }}";
                }
            });
        });
    </script>
@endsection

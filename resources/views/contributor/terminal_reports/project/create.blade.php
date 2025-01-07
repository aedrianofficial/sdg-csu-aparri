@extends('layouts.contributor')

@section('title', 'Create Terminal Report')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Terminal Report for '{{ request('related_title') }}'
                        @if (request('related_type') == App\Models\Project::class)
                            Projects/Programs
                        @elseif (request('related_type') == App\Models\Research::class)
                            Research
                        @endif
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Create Terminal Report
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
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

                            <form method="post" action="{{ route('contributor.terminal_reports.store_project') }}"
                                enctype="multipart/form-data" id="terminal-report-form" novalidate>
                                @csrf

                                <!-- Hidden Inputs -->
                                <input type="hidden" name="related_type" value="{{ request('related_type') }}">
                                <input type="hidden" name="related_id" value="{{ request('related_id') }}">
                                <input type="hidden" name="related_title" value="{{ request('related_title') }}">
                                <input type="hidden" name="submit_type" id="submit_type">

                                <!-- Cooperating Agency -->
                                <div class="mb-3">
                                    <label for="cooperating_agency_id" class="form-label">Cooperating Agency</label>
                                    <div class="input-group">
                                        <select name="cooperating_agency_id" id="cooperating_agency_id" class="form-control"
                                            required>
                                            <option value="">Select Cooperating Agency</option>
                                            @foreach ($cooperatingAgencies as $agency)
                                                <option value="{{ $agency->id }}">{{ $agency->agency }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#addAgencyModal">Add New</button>
                                    </div>
                                </div>

                                <!-- Funding Agency -->
                                <div class="mb-3">
                                    <label for="funding_agency_id" class="form-label">Funding Agency</label>
                                    <div class="input-group">
                                        <select name="funding_agency_id" id="funding_agency_id" class="form-control"
                                            required>
                                            <option value="">Select Funding Agency</option>
                                            @foreach ($fundingAgencies as $agency)
                                                <option value="{{ $agency->id }}">{{ $agency->agency }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#addFundingAgencyModal">Add New</button>
                                    </div>
                                </div>

                                <!-- Researchers -->
                                <div class="mb-3">
                                    <label for="researchers" class="form-label">Researchers</label>
                                    <div class="input-group">
                                        <select name="researchers_id[]" id="researchers" class="form-control" multiple
                                            required>
                                            @foreach ($researchers as $researcher)
                                                <option value="{{ $researcher->id }}">{{ $researcher->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#addResearcherModal">Add New</button>
                                    </div>
                                </div>

                                <!-- Budget Fields -->
                                <div class="mb-3">
                                    <label for="total_approved_budget" class="form-label">Total Approved Budget</label>
                                    <input type=" number" id="total_approved_budget" name="total_approved_budget"
                                        class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="actual_released_budget" class="form-label">Actual Released Budget</label>
                                    <input type="number" id="actual_released_budget" name="actual_released_budget"
                                        class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="actual_expenditure" class="form-label">Actual Expenditure</label>
                                    <input type="number" id="actual_expenditure" name="actual_expenditure"
                                        class="form-control" required>
                                </div>
                                <!-- Abstract -->
                                <div class="mb-3">
                                    <label for="abstract" class="form-label">Abstract</label>
                                    <textarea name="abstract" class="form-control" id="abstract" rows="5" required>{{ old('abstract') }}</textarea>
                                </div>
                                <!-- File Input for Terminal Report File -->
                                <div class="mb-3">
                                    <label for="terminal_report_file" class="form-label">Optionally: Upload Terminal
                                        Report File
                                        (Maximum of 2mb)</label>
                                    <input type="file" name="terminal_report_file" class="form-control"
                                        id="terminal_report_file" required>
                                </div>

                                <!-- Text Input for Related Link -->
                                <div class="mb-3">
                                    <label for="related_link" class="form-label">Related Link (Optionally: Please enter
                                        the
                                        URL of related to this report)</label>
                                    <input type="text" name="related_link" class="form-control" id="related_link"
                                        required placeholder="https://example.com">
                                </div>

                                <!-- Date Fields -->
                                <div class="mb-3">
                                    <label for="date_started" class="form-label">Date Started</label>
                                    <input type="date" id="date_started" name="date_started" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="date_ended" class="form-label">Date Ended</label>
                                    <input type="date" id="date_ended" name="date_ended" class="form-control"
                                        required>
                                </div>

                                <!-- Add Agency Modal -->
                                <div class="modal fade" id="addAgencyModal" tabindex="-1"
                                    aria-labelledby="addAgencyModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addAgencyModalLabel">Add New Cooperating
                                                    Agency</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addAgencyForm">
                                                    <div class="mb-3">
                                                        <label for="new_agency_name" class="form-label">Agency
                                                            Name</label>
                                                        <input type="text" class="form-control" id="new_agency_name"
                                                            required>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary" id="submitNewAgency">Add
                                                    Agency</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Funding Agency Modal -->
                                <div class="modal fade" id="addFundingAgencyModal" tabindex="-1"
                                    aria-labelledby="addFundingAgencyModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addFundingAgencyModalLabel">Add New Funding
                                                    Agency</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addFundingAgencyForm">
                                                    <div class="mb-3">
                                                        <label for="new_funding_agency_name" class="form-label">Agency
                                                            Name</label>
                                                        <input type="text" class="form-control"
                                                            id="new_funding_agency_name" required>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type "button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary"
                                                    id="submitNewFundingAgency">Add Funding Agency</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Researcher Modal -->
                                <div class="modal fade" id="addResearcherModal" tabindex="-1"
                                    aria-labelledby="addResearcherModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addResearcherModalLabel">Add New Researcher
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addResearcherForm">
                                                    <div class="mb-3">
                                                        <label for="new_researcher_name" class="form-label">Researcher
                                                            Name</label>
                                                        <input type="text" class="form-control"
                                                            id="new_researcher_name" required>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary"
                                                    id="submitNewResearcher">Add Researcher</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submission Buttons -->
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#submitReviewModal">Submit for Review</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>

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
                                                Are you sure you want to submit this terminal report for review?
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

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        $(document).ready(function() {
            $('#related_id').select2();
            $('#researchers').select2();
            $('#cooperating_agency_id').select2();
            $('#funding_agency_id').select2();
        });

        // Handle confirmation modal for form submission
        document.getElementById('confirmSubmitReview').addEventListener('click', function() {
            document.getElementById('submit_type').value = 'review'; // Set the submit type to 'review'
            document.getElementById('terminal-report-form').submit(); // Submit the form
        });
    </script>
    <script>
        $(document).ready(function() {
            // Add New Cooperating Agency
            $('#submitNewAgency').click(function() {
                const agencyName = $('#new_agency_name').val();
                $.ajax({
                    url: '{{ route('contributor.cooperating_agencies.store') }}',
                    method: 'POST',
                    data: {
                        agency: agencyName,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#cooperating_agency_id').append(new Option(response.agency, response
                            .id));
                        $('#addAgencyModal').modal('hide');
                        $('#new_agency_name').val(''); // Clear the input

                        // Display SweetAlert notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message, // Use the message from the response
                            confirmButtonText: 'OK'
                        });
 },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error adding agency: ' + xhr.responseText,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Add New Funding Agency
            $('#submitNewFundingAgency').click(function() {
                const fundingAgencyName = $('#new_funding_agency_name').val();
                $.ajax({
                    url: '{{ route('contributor.funding_agencies.store') }}',
                    method: 'POST',
                    data: {
                        agency: fundingAgencyName,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#funding_agency_id').append(new Option(response.agency, response
                            .id));
                        $('#addFundingAgencyModal').modal('hide');
                        $('#new_funding_agency_name').val(''); // Clear the input

                        // Display SweetAlert notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message, // Use the message from the response
                            confirmButtonText: 'OK'
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error adding funding agency: ' + xhr.responseText,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Add New Researcher
            $('#submitNewResearcher').click(function() {
                const researcherName = $('#new_researcher_name').val();
                $.ajax({
                    url: '{{ route('contributor.researchers.store') }}',
                    method: 'POST',
                    data: {
                        name: researcherName,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#researchers').append(new Option(response.name, response.id));
                        $('#addResearcherModal').modal('hide');
                        $('#new_researcher_name').val(''); // Clear the input

                        // Display SweetAlert notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message, // Use the message from the response
                            confirmButtonText: 'OK'
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error adding researcher: ' + xhr.responseText,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
    <script>
        let isDirty = false;

        // Track changes in input fields
        document.querySelectorAll('input, textarea, select').forEach(input => {
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
                    window.location.href = '{{ route('contributor.projects.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href = '{{ route('contributor.projects.index') }}'; // Redirect to home or desired route
            }
        });
    </script>
@endsection
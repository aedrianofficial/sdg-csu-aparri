@extends('layouts.admin')

@section('title', 'Edit Terminal Report')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Terminal Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('auth.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Terminal Report
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header-->

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

                            <form method="post" action="{{ route('auth.terminal_reports.update', $terminalReport->id) }}"
                                class="needs-validation" enctype="multipart/form-data" id="terminal-report-form" novalidate>
                                @csrf
                                @method('PUT')

                                <!-- Related Title -->
                                <div class="mb-3">
                                    <label for="related_title" class="form-label">Related Title</label>
                                    <input type="text" class="form-control" id="related_title" name="related_title"
                                        placeholder="Related Title"
                                        value="{{ old('related_title', $terminalReport->related_title) }}" required>
                                </div>

                                <!-- Related Type -->
                                <div class="mb-3">
                                    <label for="related_type" class="form-label">Related Type</label>
                                    @php
                                        $relatedTypeDisplay =
                                            $terminalReport->related_type == App\Models\Project::class
                                                ? 'Project'
                                                : ($terminalReport->related_type == App\Models\Research::class
                                                    ? 'Research'
                                                    : ucfirst(class_basename($terminalReport->related_type)));
                                    @endphp

                                    <input type="text" class="form-control" id="related_type" name="related_type"
                                        placeholder="Related Type" value="{{ old('related_type', $relatedTypeDisplay) }}"
                                        readonly>
                                </div>

                                <!-- Cooperating Agency -->
                                <div class="mb-3">
                                    <label for="cooperating_agency_id" class="form-label">Cooperating Agency</label>
                                    <div class="input-group">
                                        <select name="cooperating_agency_id" id="cooperating_agency_id" class="form-control"
                                            required>
                                            <option value="">Select Cooperating Agency</option>
                                            @foreach ($cooperatingAgencies as $agency)
                                                <option value="{{ $agency->id }}" @selected(old('cooperating_agency_id', $terminalReport->cooperating_agency_id) == $agency->id)>
                                                    {{ $agency->agency }}
                                                </option>
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
                                                <option value="{{ $agency->id }}" @selected(old('funding_agency_id', $terminalReport->funding_agency_id) == $agency->id)>
                                                    {{ $agency->agency }}
                                                </option>
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
                                                <option value="{{ $researcher->id }}" @selected(in_array($researcher->id, old('researchers_id', $terminalReport->researchers->pluck('id')->toArray())))>
                                                    {{ $researcher->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#addResearcherModal">Add New</button>
                                    </div>
                                </div>

                                <!-- Budget Fields -->
                                <div class="mb-3">
                                    <label for="total_approved_budget" class="form-label">Total Approved Budget</label>
                                    <input type="number" id="total_approved_budget" name="total_approved_budget"
                                        class="form-control"
                                        value="{{ old('total_approved_budget', $terminalReport->total_approved_budget) }}"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="actual_released_budget" class="form-label">Actual Released Budget</label>
                                    <input type="number" id="actual_released_budget" name="actual_released_budget"
                                        class="form-control"
                                        value="{{ old('actual_released_budget', $terminalReport->actual_released_budget) }}"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="actual_expenditure" class="form-label">Actual Expenditure</label>
                                    <input type="number" id="actual_expenditure" name="actual_expenditure"
                                        class="form-control"
                                        value="{{ old('actual_expenditure', $terminalReport->actual_expenditure) }}"
                                        required>
                                </div>

                                <!-- Abstract -->
                                <div class="mb-3">
                                    <label for="abstract" class="form-label">Abstract</label>
                                    <textarea name="abstract" class="form-control" id="abstract" rows="5" required>{{ old('abstract', $terminalReport->abstract) }}</textarea>
                                </div>


                                <!-- File Input for Terminal Report File -->
                                <div class="mb-3">
                                    <label for="terminal_report_file" class="form-label">Old File:</label>
                                    @if ($terminalReport->terminalReportFiles->isEmpty())
                                        <input type="text" name="terminal_report_file" id="terminal_report_file"
                                            class="form-control" value="No files available for this terminal report."
                                            readonly>
                                    @else
                                        @foreach ($terminalReport->terminalReportFiles as $file)
                                            <div class="input-group">
                                                <a href="{{ route('terminal.report.file.download', $file->id) }}"
                                                    class="form-control" target="_blank" rel="noopener noreferrer">
                                                    <span>Download</span>
                                                    {{ $file->original_filename ?? $terminalReport->related_title }}
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                    <label for="terminal_report_file" class="form-label">Update File (Maximum of <strong>2
                                            mb</strong> only)</label>
                                    <input type="file" class="form-control" id="terminal_report_file"
                                        name="terminal_report_file">
                                </div>

                                <!-- Related Link -->
                                <div class="mb-3">
                                    <label for="related_link" class="form-label">Related Link</label>
                                    <input type="url" class="form-control" id="related_link" name="related_link"
                                        value="{{ old('related_link', $terminalReport->related_link) }}"
                                        placeholder="https://example.com">
                                </div>
                                <!-- Review Status -->
                                <div class="mb-3">
                                    <label for="review_status_id" class="form-label">Review Status</label>
                                    <select name="review_status_id" id="review_status_id" class="form-select" required>
                                        @foreach ($reviewStatuses as $status)
                                            <option value="{{ $status->id }}" @selected(old('review_status_id', $terminalReport->review_status_id) == $status->id)>
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

                                <!-- Date Fields -->
                                <div class="mb-3">
                                    <label for="date_started" class="form-label">Date Started</label>
                                    <input type="date" id="date_started" name="date_started" class="form-control"
                                        value="{{ old('date_started', $terminalReport->date_started->format('Y-m-d')) }}"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="date_ended" class="form-label">Date Ended</label>
                                    <input type="date" id="date_ended" name="date_ended" class="form-control"
                                        value="{{ old('date_ended', $terminalReport->date_ended->format('Y-m-d')) }}"
                                        required>
                                </div>

                                <!-- Update Button -->
                                <button type="button" class="btn btn-primary me-2" id="update-button">Update Terminal
                                    Report</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>

                                <!-- Confirmation Modal -->

                                <div class="modal fade" id="confirmationModal" tabindex="-1"
                                    aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Update</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to update this terminal report?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary"
                                                    id="confirm-update">Update</button>
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

    <!-- Modal for Adding New Cooperating Agency -->
    <div class="modal fade" id="addAgencyModal" tabindex="-1" aria-labelledby="addAgencyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAgencyModalLabel">Add New Cooperating Agency</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="new_agency_name" class="form-control" placeholder="Agency Name" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitNewAgency">Add Agency</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding New Funding Agency -->
    <div class="modal fade" id="addFundingAgencyModal" tabindex="-1" aria-labelledby="addFundingAgencyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFundingAgencyModalLabel">Add New Funding Agency</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="new_funding_agency_name" class="form-control"
                        placeholder="Funding Agency Name" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitNewFundingAgency">Add Funding
                        Agency</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding New Researcher -->
    <div class="modal fade" id="addResearcherModal" tabindex="-1" aria-labelledby="addResearcherModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addResearcherModalLabel">Add New Researcher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="new_researcher_name" class="form-control" placeholder="Researcher Name"
                        required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitNewResearcher">Add Researcher</button>
                </div>
            </div>
        </div>
    </div>

    <!--end::App Content-->
@endsection

@section('scripts')
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
    <!--Script for related_id, researchers, cooperating agency, funding agency-->
    <script>
        $(document).ready(function() {
            $(document).ready(function() {
                $('#related_id').select2();
                $('#researchers').select2();
                $('#cooperating_agency_id').select2();
                $('#funding_agency_id').select2();
            });

            // Add New Cooperating Agency
            $('#submitNewAgency').click(function() {
                const agencyName = $('#new_agency_name').val();
                $.ajax({
                    url: '{{ route('auth.cooperating_agencies.store') }}',
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
                    url: '{{ route('auth.funding_agencies.store') }}',
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
                    url: '{{ route('auth.researchers.store') }}',
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

            $('#update-button').click(function() {
                $('#confirmationModal').modal('show');
            });

            $('#confirm-update').click(function() {
                $('#terminal-report-form').submit();
            });

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
                        window.location.href =
                            '{{ route('auth.terminal_reports.index') }}'; // Redirect to home or desired route
                    }
                } else {
                    window.location.href =
                        '{{ route('auth.terminal_reports.index') }}'; // Redirect to home or desired route
                }
            });
        });
    </script>
@endsection

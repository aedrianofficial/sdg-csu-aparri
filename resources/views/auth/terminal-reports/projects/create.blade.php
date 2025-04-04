@extends('layouts.admin')

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
                        <li class="breadcrumb-item"><a href="{{ route('auth.dashboard') }}">Dashboard</a></li>
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

                            <form method="post" action="{{ route('auth.terminal_reports.store_project') }}"
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
                                    <input type="number" id="total_approved_budget" name="total_approved_budget"
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
                                                <button type="button" class="btn btn-secondary"
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
                                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                    data-bs-target="#publishModal">Publish Immediately</button>
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
                                                Are you sure you want to publish this terminal report immediately?
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
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the budget input fields
            const totalApprovedBudgetField = document.getElementById('total_approved_budget');
            const actualReleasedBudgetField = document.getElementById('actual_released_budget');
            const actualExpenditureField = document.getElementById('actual_expenditure');

            // Add input event listeners for real-time validation
            totalApprovedBudgetField.addEventListener('input', validateBudgets);
            actualReleasedBudgetField.addEventListener('input', validateBudgets);
            actualExpenditureField.addEventListener('input', validateBudgets);

            // Function to validate all budget relationships in real-time
            function validateBudgets() {
                // Clear all existing popovers first
                clearAllPopovers();

                // Get current values
                const totalApproved = parseFloat(totalApprovedBudgetField.value) || 0;
                const actualReleased = parseFloat(actualReleasedBudgetField.value) || 0;
                const actualExpenditure = parseFloat(actualExpenditureField.value) || 0;

                // Validate total approved budget
                if (totalApprovedBudgetField.value !== '' && (isNaN(totalApproved) || totalApproved <= 0)) {
                    showPopover(totalApprovedBudgetField, 'Please enter a valid positive number');
                }

                // Validate actual released budget
                if (actualReleasedBudgetField.value !== '') {
                    if (isNaN(actualReleased) || actualReleased < 0) {
                        showPopover(actualReleasedBudgetField, 'Please enter a valid number (0 or higher)');
                    } else if (totalApproved > 0 && actualReleased > totalApproved) {
                        showPopover(actualReleasedBudgetField, 'Cannot exceed Total Approved Budget');
                    }
                }

                // Validate actual expenditure
                if (actualExpenditureField.value !== '') {
                    if (isNaN(actualExpenditure) || actualExpenditure < 0) {
                        showPopover(actualExpenditureField, 'Please enter a valid number (0 or higher)');
                    } else if (actualReleased > 0 && actualExpenditure > actualReleased) {
                        showPopover(actualExpenditureField, 'Cannot exceed Actual Released Budget');
                    }
                }
            }

            // Function to show popover error message
            function showPopover(element, message) {
                // Add invalid class for Bootstrap validation styling
                element.classList.add('is-invalid');

                // Create popover container if it doesn't exist
                let popover = element.nextElementSibling;
                if (!popover || !popover.classList.contains('popover-error')) {
                    popover = document.createElement('div');
                    popover.className = 'popover-error position-absolute';
                    popover.style.cssText =
                        'background-color: #f8d7da; color: #842029; padding: 5px 10px; border-radius: 4px; font-size: 14px; z-index: 1000; margin-top: 2px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);';
                    element.parentNode.style.position = 'relative';
                    element.parentNode.appendChild(popover);
                }

                // Set popover content and show it
                popover.textContent = message;
                popover.style.display = 'block';
            }

            // Function to clear all popovers
            function clearAllPopovers() {
                const fields = [totalApprovedBudgetField, actualReleasedBudgetField, actualExpenditureField];

                fields.forEach(field => {
                    field.classList.remove('is-invalid');
                    const popover = field.parentNode.querySelector('.popover-error');
                    if (popover) {
                        popover.style.display = 'none';
                    }
                });
            }

            // Also validate on form submission
            const form = totalApprovedBudgetField.closest('form');
            if (form) {
                form.addEventListener('submit', function(event) {
                    validateBudgets();

                    // Check if any field has errors
                    const hasErrors = document.querySelectorAll('.is-invalid').length > 0;
                    if (hasErrors) {
                        event.preventDefault();
                    }
                });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#related_id').select2();
            $('#researchers').select2();
            $('#cooperating_agency_id').select2();
            $('#funding_agency_id').select2();
            $('#review_status_id').select2();
        });

        // Handle confirmation modal for form submission
        document.getElementById('confirmSubmitReview').addEventListener('click', function() {
            document.getElementById('submit_type').value = 'review'; // Set the submit type to 'review'
            document.getElementById('terminal-report-form').submit(); // Submit the form
        });

        document.getElementById('confirmPublish').addEventListener('click', function() {
            document.getElementById('submit_type').value =
                'publish'; // Set the submit type to 'publish'
            document.getElementById('terminal-report-form').submit(); // Submit the form
        });
    </script>
    <script>
        $(document).ready(function() {
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
                            confirmButtonText: ' OK'
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
                    window.location.href = '{{ route('projects.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href = '{{ route('projects.index') }}'; // Redirect to home or desired route
            }
        });
    </script>
@endsection

@extends('layouts.contributor')

@section('title', 'Create Status Report')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Status Report for '{{ request('related_title') }}'
                        @if (request('related_type') == Research::class)
                            Research
                        @endif
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Create Status Report
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

                            <form method="post" action="{{ route('contributor.status_reports.store_research') }}"
                                enctype="multipart/form-data" id="status-report-form" novalidate>
                                @csrf

                                <!-- Hidden Inputs -->
                                <input type="hidden" name="related_type" value="{{ request('related_type') }}">
                                <input type="hidden" name="related_id" value="{{ request('related_id') }}">
                                <input type="hidden" name="related_title" value="{{ request('related_title') }}">

                                <!-- Log Status -->
                                <div class="mb-3">
                                    <label for="log_status" class="form-label">Log Status</label>
                                    <input type="text" id="log_status" name="log_status" class="form-control"
                                        @if (array_key_exists($research->status_id, $statusesToCheck)) value="{{ $statusesToCheck[$research->status_id] }}"
                                        @else
                                            value="" @endif
                                        readonly required>
                                </div>

                                <!-- Remarks -->
                                <div class="mb-3">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea name="remarks" class="form-control" id="remarks" rows="5" required>{{ old('remarks') }}</textarea>
                                </div>

                                <!-- Text Input for Related Link -->
                                <div class="mb-3">
                                    <label for="related_link" class="form-label">Related Link (Optionally: Please enter the
                                        URL of related to this report)</label>
                                    <input type="text" name="related_link" class="form-control" id="related_link"
                                        required placeholder="https://example.com">
                                </div>

                                <!-- File Input for Status Report File -->
                                <div class="mb-3">
                                    <label for="status_report_file" class="form-label">Upload Status Report File (Maximum of 2mb)</label>
                                    <input type="file" name="status_report_file" class="form-control"
                                        id="status_report_file" required>
                                </div>

                                <!-- Submission Buttons -->
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#submitReviewModal">Submit for Review</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <!-- Hidden Inputs -->
                                <input type="hidden" name="submit_type" id="submit_type">

                                <!-- "Submit for Review" Confirmation Modal -->
                                <div class="modal fade" id="submitReviewModal" tabindex="-1"
                                    aria-labelledby="submitReviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="submitReviewModal Label">Confirm Submission</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to submit this status report for review?
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
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection

@section('scripts')

    <script>
        $(document).ready(function() {
            $('#related_id').select2();
        });

        // Handle confirmation modal for form submission
        document.getElementById('confirmSubmitReview').addEventListener('click', function() {
            document.getElementById('submit_type').value = 'review';
            document.getElementById('status-report-form').submit();
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
                    window.location.href = '{{ route('contributor.research.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href = '{{ route('contributor.research.index') }}'; // Redirect to home or desired route
            }
        });
    </script>
@endsection
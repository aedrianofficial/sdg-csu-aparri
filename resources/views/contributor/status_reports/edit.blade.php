@extends('layouts.contributor')

@section('title', 'Edit Status Report')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Status Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Status Report
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

                            <form method="post"
                                action="{{ route('contributor.status_reports.update', $statusReport->id) }}"
                                class="needs-validation" enctype="multipart/form-data" id="status-report-form" novalidate>
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status_report_id" value="{{ $statusReport->id }}">

                                <!-- Related Title -->
                                <div class="mb-3">
                                    <label for="related_title" class="form-label">Related Title</label>
                                    <input type="text" class="form-control" id="related_title" name="related_title"
                                        placeholder="Related Title"
                                        value="{{ old('related_title', $statusReport->related_title) }}" required>
                                </div>

                                <!-- Related Type -->
                                <div class="mb-3">
                                    <label for="related_type" class="form-label">Related Type</label>
                                    @php
                                        $relatedTypeDisplay =
                                            $statusReport->related_type == App\Models\Project::class
                                                ? 'Project'
                                                : ($statusReport->related_type == App\Models\Research::class
                                                    ? 'Research'
                                                    : ucfirst(class_basename($statusReport->related_type)));
                                    @endphp

                                    <input type="text" class="form-control" id="related_type" name="related_type"
                                        placeholder="Related Type" value="{{ old('related_type', $relatedTypeDisplay) }}"
                                        readonly>
                                </div>

                                <!-- Log Status -->
                                <div class="mb-3">
                                    <label for="log_status" class="form-label">Log Status</label>
                                    <input type="text" class="form-control" id="log_status" name="log_status"
                                        value="{{ old('log_status', $statusReport->log_status) }}" readonly>
                                </div>

                                <!-- Remarks -->
                                <div class="mb-3">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea name="remarks" class="form-control" id="remarks" rows="5" required>{{ old('remarks', $statusReport->remarks) }}</textarea>
                                </div>

                                <!-- Related Link -->
                                <div class="mb-3">
                                    <label for="related_link" class="form-label">Related Link</label>
                                    <input type="url" class="form-control" id="related_link" name="related_link"
                                        value="{{ old('related_link', $statusReport->related_link) }}"
                                        placeholder="https://example.com">
                                </div>



                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="status_report_file" class="form-label">Old File:</label>
                                    @if ($statusReport->statusReportFiles->isEmpty())
                                        <input type="text" name="status_report_file" id="status_report_file"
                                            class="form-control" value="No files available for this status report."
                                            readonly>
                                    @else
                                        @foreach ($statusReport->statusReportFiles as $file)
                                            <div class="input-group">
                                                <a href="{{ route('status.report.file.download', $file->id) }}"
                                                    class="form-control" target="_blank" rel="noopener noreferrer">
                                                    <span>Download</span>
                                                    {{ $file->original_filename ?? $statusReport->related_title }}
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                    <label for="status_report_file" class="form-label">Update File (Maximum of <strong>2
                                            mb</strong> only)</label>
                                    <input type="file" class="form-control" id="status_report_file"
                                        name="status_report_file">
                                </div>

                                <!-- Created By (Display Only) -->
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created by:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $statusReport->loggedBy->first_name }} {{ $statusReport->loggedBy->last_name }}"
                                        readonly>
                                </div>

                                <!-- Update Button -->
                                <button type="button" class="btn btn-primary me-2" id="update-button">Update Status
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
                                                Are you sure you want to update this status report?
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

    <!--end::App Content-->
@endsection

@section('scripts')
    <script>
        $('#update-button').click(function() {
            $('#confirmationModal').modal('show');
        });

        $('#confirm-update').click(function() {
            $('#status-report-form').submit();
        });

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
                        '{{ route('contributor.status_reports.my_reports') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href =
                    '{{ route('contributor.status_reports.my_reports') }}'; // Redirect to home or desired route
            }
        });
    </script>

@endsection

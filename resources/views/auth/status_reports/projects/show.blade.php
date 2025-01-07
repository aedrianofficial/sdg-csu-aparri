@extends('layouts.admin')
@section('title', 'View Status Report')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">View Status Report for "{{ $statusReport->related_title }}"</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('auth.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Status Report
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
                            <form action="" class="forms-sample">
                                @if ($notificationData)
                                    <div class="alert alert-info">
                                        <strong>Notification:</strong> {{ $notificationData['message'] }}<br>
                                        <strong>
                                            {{ $notificationData['role'] ?? 'N/A' }}:
                                            {{ $notificationData['name'] ?? 'N/A' }}
                                        </strong>
                                    </div>
                                @endif

                               

                                <!-- Log Status -->
                                <div class="mb-3">
                                    <label for="log_status" class="form-label">Log Status:</label>
                                    <input type="text" name="log_status" id="log_status" class="form-control"
                                        value="{{ $statusReport->log_status }}" readonly>
                                </div>

                                <!-- Remarks -->
                                <div class="mb-3">
                                    <label for="remarks" class="form-label">Remarks:</label>
                                    <textarea name="remarks" id="remarks" cols="30" rows="3" class="form-control" readonly>{{ $statusReport->remarks }}</textarea>
                                </div>

                                <!-- Related Link -->
                                <div class="mb-3">
                                    <label for="related_link" class="form-label">Related Link:</label>
                                    <input type="text" name="related_link" id="related_link" class="form-control"
                                        value="{{ $statusReport->related_link  ?? 'N/A'}}" readonly>
                                </div>

                                <!-- Status Report File -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">Files:</label>
                                    @if ($statusReport->statusReportFiles->isEmpty())
                                        <input type="text" name="file" id="file" class="form-control"
                                            value="No files available for this status report." readonly>
                                    @else
                                        @foreach ($statusReport->statusReportFiles as $file)
                                            <div class="input-group">
                                                <!-- Display clickable filename as a link -->
                                                <a href="{{ route('status.report.file.download', $file->id) }}"
                                                    class="form-control" target="_blank" rel="noopener noreferrer">
                                                    <span>Download</span>
                                                    {{ $file->original_filename ?? 'status_report_file' }}
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Logged By -->
                                <div class="mb-3">
                                    <label for="logged_by" class="form-label">Logged By:</label>
                                    <input type="text" name="logged_by" id="logged_by" class="form-control"
                                        value="{{ $statusReport->loggedBy->first_name ?? 'N/A' }} {{ $statusReport->loggedBy->last_name ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Created At -->
                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created At:</label>
                                    <input type="text" name="created_at" id="created_at" class="form-control"
                                        value="{{ $statusReport->created_at->format('M d, Y H:i') }}" readonly>
                                </div>

                                <!-- Updated At -->
                                <div class="mb-3">
                                    <label for="updated_at" class=" form-label">Updated At:</label>
                                    <input type="text" name="updated_at" id="updated_at" class="form-control"
                                        value="{{ $statusReport->updated_at->format('M d, Y H:i') }}" readonly>
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

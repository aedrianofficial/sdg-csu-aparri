@extends('layouts.contributor')

@section('title', 'View Terminal Report')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">View Terminal Report for "{{ $terminalReport->related_title }}"</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Terminal Report
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


                                <!-- Cooperating Agency -->
                                <div class="mb-3">
                                    <label for="cooperating_agency" class="form-label">Cooperating Agency:</label>
                                    <input type="text" name="cooperating_agency" id="cooperating_agency"
                                        class="form-control"
                                        value="{{ $terminalReport->cooperatingAgency->agency ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Funding Agency -->
                                <div class="mb-3">
                                    <label for="funding_agency" class="form-label">Funding Agency:</label>
                                    <input type="text" name="funding_agency" id="funding_agency" class="form-control"
                                        value="{{ $terminalReport->fundingAgency->agency ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Researchers -->
                                <div class="mb-3">
                                    <label for="researchers" class="form-label">Researchers:</label>
                                    <input type="text" name="researchers" id="researchers" class="form-control"
                                        value="{{ implode(', ', $terminalReport->researchers->pluck('name')->unique()->toArray()) ?? 'N/A' }}"
                                        readonly>
                                </div>

                                <!-- Budget Fields -->
                                <div class="mb-3">
                                    <label for="total_approved_budget" class="form-label">Total Approved Budget:</label>
                                    <input type="text" name="total_approved_budget" id="total_approved_budget"
                                        class="form-control" value="{{ $terminalReport->total_approved_budget }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="actual_released_budget" class="form-label">Actual Released Budget:</label>
                                    <input type="text" name="actual_released_budget" id="actual_released_budget"
                                        class="form-control" value="{{ $terminalReport->actual_released_budget }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="actual_expenditure" class="form-label">Actual Expenditure:</label>
                                    <input type="text" name="actual_expenditure" id="actual_expenditure"
                                        class="form-control" value="{{ $terminalReport->actual_expenditure }}" readonly>
                                </div>

                                <!-- Abstract -->
                                <div class="mb-3">
                                    <label for="abstract" class="form-label">Abstract:</label>
                                    <textarea name="abstract" id="abstract" class="form-control" rows="5" readonly>{{ $terminalReport->abstract }}</textarea>
                                </div>

                                <!-- Related Link -->
                                <div class="mb-3">
                                    <label for="related_link" class="form-label">Related Link:</label>
                                    <input type="text" name="related_link" id="related_link" class="form-control"
                                        value="{{ $terminalReport->related_link ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Terminal Report File -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">Files:</label>
                                    @if (!$terminalReportFile)
                                        <input type="text" name="file" id="file" class="form-control"
                                            value="No files available for this terminal report." readonly>
                                    @else
                                        <div class="input-group">
                                            <a href="{{ route('terminal.report.file.download', $terminalReportFile->id) }}"
                                                class="form-control" target="_blank" rel="noopener noreferrer">
                                                <span>Download</span>
                                                {{ $terminalReportFile->original_filename ?? 'terminal_report_file' }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <!-- Logged By -->
                                <div class="mb-3">
                                    <label for="logged_by" class="form-label">Logged By:</label>
                                    <input type="text" name="logged_by" id="logged_by" class="form-control"
                                        value="{{ $terminalReport->user->first_name ?? 'N/A' }} {{ $terminalReport->user->last_name ?? 'N/A' }}"
                                        readonly>
                                </div>

                                <!-- Created At -->
                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created At:</label>
                                    <input type="text" name="created_at" id="created_at" class="form-control"
                                        value="{{ $terminalReport->created_at->format('M d, Y H:i') }}" readonly>
                                </div>

                                <!-- Updated At -->
                                <div class="mb-3">
                                    <label for="updated_at" class="form-label">Updated At:</label>
                                    <input type="text" name="updated_at" id="updated_at" class="form-control"
                                        value="{{ $terminalReport->updated_at->format('M d, Y H:i') }}" readonly>
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

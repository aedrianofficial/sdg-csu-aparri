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
                            @if ($notificationData)
                                <div class="alert alert-info">
                                    <strong>Notification:</strong> {{ $notificationData['message'] }}<br>
                                    <strong>
                                        @php
                                            // Initialize an array to hold role names
                                            $roleNames = [];

                                            // Variable to hold the final name to display
                                            $finalName = 'N/A'; // Default name if none are found

                                            // Check if role is an array
                                            if (is_array($notificationData['role'])) {
                                                foreach ($notificationData['role'] as $role) {
                                                    if ($role === 'contributor') {
                                                        $roleNames[] = 'Contributor';
                                                        $finalName = $notificationData['contributor'] ?? 'N/A'; // Save contributor name
                                                    } elseif ($role === 'reviewer') {
                                                        $roleNames[] = 'Reviewer';
                                                        $finalName = $notificationData['reviewer'] ?? 'N/A'; // Save reviewer name
                                                    } elseif ($role === 'approver') {
                                                        $roleNames[] = 'Approver';
                                                        $finalName = $notificationData['approver'] ?? 'N/A'; // Save approver name
                                                    } elseif ($role === 'publisher') {
                                                        $roleNames[] = 'Publisher';
                                                        $finalName = $notificationData['publisher'] ?? 'N/A'; // Save publisher name
                                                    } elseif ($role === 'admin') {
                                                        // Handle admin role
                                                        $roleNames[] = 'Admin';
                                                        $finalName = $notificationData['admin'] ?? 'N/A'; // Save admin name
                                                    }
                                                }
                                            } else {
                                                // Handle the case where role is a single string
                                                if ($notificationData['role'] === 'contributor') {
                                                    $roleNames[] = 'Contributor';
                                                    $finalName = $notificationData['contributor'] ?? 'N/A';
                                                } elseif ($notificationData['role'] === 'reviewer') {
                                                    $roleNames[] = 'Reviewer';
                                                    $finalName = $notificationData['reviewer'] ?? 'N/A';
                                                } elseif ($notificationData['role'] === 'approver') {
                                                    $roleNames[] = 'Approver';
                                                    $finalName = $notificationData['approver'] ?? 'N/A';
                                                } elseif ($notificationData['role'] === 'publisher') {
                                                    $roleNames[] = 'Publisher';
                                                    $finalName = $notificationData['publisher'] ?? 'N/A';
                                                } elseif ($notificationData['role'] === 'admin') {
                                                    // Handle admin role
                                                    $roleNames[] = 'Admin';
                                                    $finalName = $notificationData['admin'] ?? 'N/A'; // Save admin name
                                                }
                                            }

                                            // Join role names with a comma
                                            $rolesString = implode(', ', $roleNames);

                                            // Combine roles and final name for output
                                            $outputString = !empty($finalName)
                                                ? "{$rolesString}: {$finalName}"
                                                : $rolesString;
                                        @endphp

                                        {{ $outputString }}
                                    </strong>
                                </div>
                            @endif
                            <!-- Feedback Section -->
                            @if ($terminalReport->feedbacks->count() > 0)
                                <h4>Feedback</h4>
                                <div class="form-sample">
                                    @foreach ($terminalReport->feedbacks as $feedback)
                                        <div class="feedback-item mb-3">
                                            <div class="mb-3">
                                                <label for="feedback" class="form-label">Feedback:</label>
                                                @php
                                                    $feedbackText = $feedback->feedback;
                                                    $rowCount =
                                                        substr_count($feedbackText, "\n") +
                                                        ceil(strlen($feedbackText) / 100); // Adjust based on length
                                                    $rowCount = $rowCount < 3 ? 3 : $rowCount; // Ensure at least 3 rows
                                                @endphp
                                                <textarea name="feedback" id="feedback" class="form-control" rows="{{ $rowCount }}" readonly>{{ $feedbackText }}</textarea>
                                            </div>
                                            <strong>{{ $feedback->user->first_name }}
                                                {{ $feedback->user->last_name }}</strong>
                                            <small class="text-muted">on
                                                {{ $feedback->created_at->format('M d, Y H:i') }}</small>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div>
                                    <h4>Feedback</h4>
                                    <p>No feedback available for this Project.</p>
                                    <hr>
                                </div>
                            @endif
                            <!-- End Feedback Section -->
                            <form action="" class="forms-sample">

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
                                    <label for="actual _released_budget" class="form-label">Actual Released Budget:</label>
                                    <input type="text" name="actual_released_budget" id="actual_released_budget"
                                        class="form-control" value="{{ $terminalReport->actual_released_budget }}"
                                        readonly>
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

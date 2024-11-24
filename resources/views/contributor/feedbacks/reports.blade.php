@extends('layouts.contributor')
@section('title', 'View Report')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Need Changes Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Need Changes Report
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
                            @if ($report->feedbacks->count() > 0)
                                <h4>Feedback</h4>
                                <div class="mb-4">
                                    @foreach ($report->feedbacks as $feedback)
                                        <div class="feedback-item mb-3">
                                            <div class="mb-3">
                                                <label for="feedback-{{ $feedback->id }}" class="form-label">Need
                                                    Changes:</label>
                                                @php
                                                    $feedbackText = $feedback->feedback;
                                                    $rowCount =
                                                        substr_count($feedbackText, "\n") +
                                                        ceil(strlen($feedbackText) / 100); // Adjust based on length
                                                    $rowCount = $rowCount < 3 ? 3 : $rowCount; // Ensure at least 3 rows
                                                @endphp
                                                <textarea name="feedback" id="feedback-{{ $feedback->id }}" class="form-control" rows="{{ $rowCount }}" readonly>{{ $feedbackText }}</textarea>
                                            </div>
                                            <strong>{{ $feedback->user->name }}</strong>
                                            <small class="text-muted">on
                                                {{ $feedback->created_at->format('M d, Y H:i') }}</small>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div>
                                    <h4>Feedback</h4>
                                    <p>No feedback available for this Report.</p>
                                    <hr>
                                </div>
                            @endif
                            <!-- End Feedback Section -->

                            <form action="" class="forms-sample">
                                <h4>Report Content</h4>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title:</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ $report->title }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description:</label>
                                    @php
                                        $description = $report->description;
                                        $rowCount =
                                            substr_count($description, "\n") + floor(strlen($description) / 100); // Adjust the row count based on length
                                        $rowCount = $rowCount < 3 ? 3 : $rowCount; // Ensure a minimum of 3 rows for short descriptions
                                    @endphp
                                    <textarea name="description" id="description" cols="30" rows="{{ $rowCount }}" class="form-control" readonly>{{ $description }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="sdg" class="form-label">SDGs:</label>
                                    <textarea name="sdg" id="sdg" cols="30" rows="3" class="form-control" readonly>
                                        @foreach ($report->sdg as $sdg)
{{ $sdg->name }}
@endforeach
                                    </textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="">Image: </label>
                                    <div>
                                        <img src="{{ $report->reportimg->image }}" alt="report-image"
                                            style="max-width: 500px; height: auto;">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="related_type" class="form-label">Project/Research Type:</label>
                                    <input type="text" name="related_type" id="related_type" class="form-control"
                                        value="{{ ucfirst($report->related_type) }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="related_title" class="form-label">Project/Research Item:</label>
                                    <input type="text" name="related_title" id="related_title" class="form-control"
                                        value="{{ $report->related_title }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status:</label>
                                    <input type="text" name="status" id="status" class="form-control"
                                        value="{{ $report->is_publish == 1 ? 'Published' : 'Draft' }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="review_status" class="form-label">Review Status:</label>
                                    <input type="text" name="review_status" id="review_status" class="form-control"
                                        value="{{ $report->reviewStatus ? $report->reviewStatus->status : 'N/A' }}"
                                        readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created by:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $report->user->first_name }} {{ $report->user->last_name }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created at:</label>
                                    <input type="text" name="created_at" id="created_at" class="form-control"
                                        value="{{ $report->created_at->format('M d, Y H:i') }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="updated_at" class="form-label">Updated at:</label>
                                    <input type="text" name="updated_at" id="updated_at" class="form-control"
                                        value="{{ $report->updated_at->format('M d, Y H:i') }}" readonly>
                                </div>

                                <a href="{{ route('contributor.reports.edit', $report->id) }}"
                                    class="btn btn-sm btn-info">Edit Report Content</a>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection

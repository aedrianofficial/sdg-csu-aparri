@extends('layouts.publisher')
@section('title', 'View Report')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">View Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Report
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
                            <h4 class="card-title">Report Details</h4>
                            <div class="forms-sample">
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
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title:</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ $report->title }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="related_title" class="form-label">Related Title:</label>
                                    <input type="text" name="related_title" id="related_title" class="form-control"
                                        value="{{ $report->related_title }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="related_type" class="form-label">Related Type:</label>
                                    <input type="text" name="related_type" id="related_type" class="form-control"
                                        value="{{ $report->related_type }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description:</label>
                                    @php
                                        $description = $report->description;
                                        $rowCount =
                                            substr_count($description, "\n") + floor(strlen($description) / 100);
                                        $rowCount = $rowCount < 3 ? 3 : $rowCount;
                                    @endphp
                                    <textarea name="description" id="description" cols="30" rows="{{ $rowCount }}" class="form-control" readonly>{{ $description }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="review_status" class="form-label">Review Status:</label>
                                    <input type="text" name="review_status" id="review_status" class="form-control"
                                        value="{{ $report->reviewStatus->status ?? 'N/A' }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="is_publish" class="form-label">Is Published:</label>
                                    <input type="text" name="is_publish" id="is_publish" class="form-control"
                                        value="{{ $report->is_publish == 1 ? 'Published' : 'Draft' }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="">Image: </label>
                                    <div>
                                        <img src="{{ $report->reportimg->image }}" alt="report-image"
                                            style="max-width: 500px; height: auto;">
                                    </div>
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

                                <!-- Publish Button with Confirmation Modal -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#publishModal">Publish Report</button>
                                </div>

                                <!-- Publish Confirmation Modal -->
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
                                                Are you sure you want to publish this report?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('publisher.reports.published', $report->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-primary">Publish</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
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
            $('#publishModal').on('shown.bs.modal', function() {
                $('#publish').trigger('focus');
            });
        });
    </script>
@endsection
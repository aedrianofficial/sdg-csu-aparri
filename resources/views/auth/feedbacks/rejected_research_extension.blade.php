@extends('layouts.admin')
@section('title', 'View Research')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Rejected Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Rejected Research
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
                            @if ($research->feedbacks->count() > 0)
                                <h4>Feedback</h4>
                                <div class="mb-4">
                                    @foreach ($research->feedbacks as $feedback)
                                        <div class="feedback-item mb-3">
                                            <div class="mb-3">
                                                <label for="feedback-{{ $feedback->id }}" class="form-label">Need
                                                    Changes:</label>
                                                <input type="text" name="feedback" id="feedback-{{ $feedback->id }}"
                                                    class="form-control" value="{{ $feedback->feedback }}" readonly>
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
                                    <p>No feedback available for this Research.</p>
                                    <hr>
                                </div>
                            @endif
                            <!-- End Feedback Section -->

                            <form action="" class="forms-sample">
                                <h4>Research Content</h4>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title:</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ $research->title }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="research_category" class="form-label">Research Category:</label>
                                    <input type="text" name="research_category" id="research_category"
                                        class="form-control" value="{{ $research->researchcategory->name ?? 'N/A' }}"
                                        readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="sdg" class="form-label">SDGs:</label>
                                    <textarea name="sdg" id="sdg" cols="30" rows="3" class="form-control" readonly>
                                        @foreach ($research->sdg as $sdg)
{{ $sdg->name }}
@endforeach
                                    </textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description:</label>
                                    @php
                                        $description = $research->description;
                                        $rowCount =
                                            substr_count($description, "\n") + floor(strlen($description) / 100);
                                        $rowCount = $rowCount < 3 ? 3 : $rowCount;
                                    @endphp
                                    <textarea name="description" id="description" cols="30" rows="{{ $rowCount }}" class="form-control" readonly>{{ $description }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="research_status" class="form-label">Research Status:</label>
                                    <input type="text" name="research_status" id="research_status" class="form-control"
                                        value="{{ $research->research_status ?? 'N/A' }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="is_publish" class="form-label">Publish Status:</label>
                                    <input type="text" name="is_publish" id="is_publish" class="form-control"
                                        value="{{ $research->is_publish == 1 ? 'Published' : 'Draft' }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="review_status" class="form-label">Review Status:</label>
                                    <input type="text" name="review_status" id="review_status" class="form-control"
                                        value="{{ $research->reviewStatus->status ?? 'N/A' }}" readonly>
                                </div>
                                <!-- File -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">File:</label>
                                    @if ($research->researchfiles->isEmpty())
                                        <input type="text" name="file" id="file" class="form-control"
                                            value="No files available for this research." readonly>
                                    @else
                                        @foreach ($research->researchfiles as $file)
                                            <div class="input-group">
                                                <!-- Display clickable filename as a link -->
                                                <a href="{{ route('research.file.download', $file->id) }}"
                                                    class="form-control" target="_blank" rel="noopener noreferrer">
                                                    <span>Download</span>
                                                    {{ $file->original_filename ?? $research->title }}
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created by:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $research->user->first_name }} {{ $research->user->last_name }}"
                                        readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created at:</label>
                                    <input type="text" name="created_at" id="created_at" class="form-control"
                                        value="{{ $research->created_at->format('M d, Y H:i') }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="updated_at" class="form-label">Updated at:</label>
                                    <input type="text" name="updated_at" id="updated_at" class="form-control"
                                        value="{{ $research->updated_at->format('M d, Y H:i') }}" readonly>
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
@extends('layouts.approver')
@section('title', 'View Report')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">View Rejected Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Rejected Report
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
                            <div class="mb-4">
                                <!-- Feedback Section -->
                                @if ($report->feedbacks->count() > 0)
                                    <h4>Feedback</h4>
                                    <div class="mb-4">
                                        @foreach ($report->feedbacks as $feedback)
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection

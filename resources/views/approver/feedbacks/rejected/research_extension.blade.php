@extends('layouts.approver')
@section('title', 'View Research')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">View Rejected Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Rejected Research
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
                            <!-- Feedback Section -->
                            @if ($research->feedbacks->count() > 0)
                                <h4>Feedback</h4>
                                <div class="mb-4">
                                    @foreach ($research->feedbacks as $feedback)
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
                                    <p>No feedback available for this Research & Extension.</p>
                                    <hr>
                                </div>
                            @endif
                            <!-- End Feedback Section -->

                            <div class="mb-4">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title:</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ $research->title }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description:</label>
                                    @php
                                        $description = $research->description;
                                        $rowCount =
                                            substr_count($description, "\n") + floor(strlen($description) / 100); // Adjust the row count based on length
                                        $rowCount = $rowCount < 3 ? 3 : $rowCount; // Ensure a minimum of 3 rows for short descriptions
                                    @endphp
                                    <textarea name="description" id="description" cols="30" rows="{{ $rowCount }}" class="form-control" readonly>{{ $description }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="research_category" class="form-label">Research Category:</label>
                                    <input type="text" name="research_category" id="research_category"
                                        class="form-control" value="{{ $research->researchcategory->name ?? 'N/A' }}"
                                        readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="research_status" class="form-label">Research Status:</label>
                                    <input type="text" name="research_status" id="research_status" class="form-control"
                                        value="{{ $research->research_status ?? 'N/A' }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="review_status" class="form-label">Review Status:</label>
                                    <input type="text" name="review_status" id="review_status" class="form-control"
                                        value="{{ $research->reviewStatus->status ?? 'N/A' }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="is_publish" class="form-label">Is Published:</label>
                                    <input type="text" name="is_publish" id="is_publish" class="form-control"
                                        value="{{ $research->is_publish == 1 ? 'Published' : 'Draft' }}" readonly>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection

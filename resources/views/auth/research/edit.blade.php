@extends('layouts.admin')

@section('title', 'Edit Research')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Research
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
                            <h4 class="card-title">Edit Research</h4>

                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="post" action="{{ route('research.update', $research->id) }}"
                                class="needs-validation" enctype="multipart/form-data" id="research-form" novalidate>
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="research_id" value="{{ $research->id }}">
                                <!-- Research Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Research Title" value="{{ old('title', $research->title) }}" required>
                                </div>

                                <!-- Research Categories -->
                                <div class="mb-3">
                                    <label for="researchcategory_id" class="form-label">Research Category</label>
                                    <select name="researchcategory_id" id="researchcategory_id" class="form-select"
                                        required>
                                        <option disabled selected>Choose Category</option>
                                        @foreach ($researchcategories as $category)
                                            <option value="{{ $category->id }}" @selected(old('researchcategory_id', $research->researchcategory_id) == $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Sustainable Development Goals (SDG) -->
                                <div class="mb-3">
                                    <label for="sdg" class="form-label">Sustainable Development Goals</label>
                                    <select name="sdg[]" id="sdg" class="form-select select2-multiple" required
                                        multiple="multiple">
                                        @foreach ($sdgs as $sdg)
                                            <option value="{{ $sdg->id }}"
                                                {{ in_array($sdg->id, $research->sdg->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $sdg->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Research Status Dropdown -->
                                <div class="mb-3">
                                    <label for="research_status" class="form-label">Research Status</label>
                                    <select name="research_status" id="research_status" class="form-select" required>
                                        <option value="Proposed" @selected(old('research_status', $research->research_status) == 'Proposed')>Proposed</option>
                                        <option value="On-Going" @selected(old('research_status', $research->research_status) == 'On-Going')>On-Going</option>
                                        <option value="On-Hold" @selected(old('research_status', $research->research_status) == 'On-Hold')>On-Hold</option>
                                        <option value="Completed" @selected(old('research_status', $research->research_status) == 'Completed')>Completed</option>
                                        <option value="Rejected" @selected(old('research_status', $research->research_status) == 'Rejected')>Rejected</option>
                                    </select>
                                </div>

                                <!-- Review Status -->
                                <div class="mb-3">
                                    <label for="review_status_id" class="form-label">Review Status</label>
                                    <select name="review_status_id" id="review_status_id" class="form-select" required>
                                        @foreach ($reviewStatuses as $status)
                                            <option value="{{ $status->id }}" @selected(old('review_status_id', $research->review_status_id) == $status->id)>
                                                {{ $status->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Feedback Textarea -->
                                <div class="mb-3" id="feedback-container" style="display: none;">
                                    <label for="feedback" id="feedback-label" class="form-label">Feedback</label>
                                    <textarea name="feedback" class="form-control" id="feedback" cols="30" rows="10"></textarea>
                                </div>
                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">Old File(Abstract):</label>
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
                                    <label for="file" class="form-label">Update File (Abstract, Maximum of <strong>2
                                            mb</strong> only)</label>
                                    <input type="file" class="form-control" id="file" name="file">
                                </div>

                                <!-- Research Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" rows="10" required>{{ old('description', $research->description) }}</textarea>
                                </div>
                                <!-- Created By (Display Only) -->
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created by:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $research->user->first_name }} {{ $research->user->last_name }}"
                                        readonly>
                                </div>
                                <!-- Update Button -->
                                <button type="button" class="btn btn-primary me-2" id="update-button">Update
                                    Research</button>

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
                                                Are you sure you want to update this research?
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
        $(document).ready(function() {
            // Initialize Select2 for SDGs and Research Status
            $('#sdg').select2();

            $('#update-button').click(function() {
                $('#confirmationModal').modal('show');
            });

            $('#confirm-update').click(function() {
                $('#research-form').submit();
            });
        });
    </script>
    <!-- JavaScript to handle conditional feedback display, requirement, and label -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reviewStatusSelect = document.getElementById('review_status_id');
            const feedbackContainer = document.getElementById('feedback-container');
            const feedbackTextarea = document.getElementById('feedback');
            const feedbackLabel = document.getElementById('feedback-label');

            function updateFeedbackVisibility() {
                const selectedStatus = reviewStatusSelect.options[reviewStatusSelect.selectedIndex].text;
                if (selectedStatus === 'Need Changes') {
                    feedbackContainer.style.display = 'block';
                    feedbackTextarea.required = true;
                    feedbackLabel.innerText = 'Feedback (Required)';
                } else if (selectedStatus === 'Rejected') {
                    feedbackContainer.style.display = 'block';
                    feedbackTextarea.required = false;
                    feedbackLabel.innerText = 'Feedback (Optional)';
                } else {
                    feedbackContainer.style.display = 'none';
                    feedbackTextarea.required = false;
                }
            }

            // Initialize visibility and label based on current selection
            updateFeedbackVisibility();

            // Update visibility and label on change
            reviewStatusSelect.addEventListener('change', updateFeedbackVisibility);
        });
    </script>
@endsection

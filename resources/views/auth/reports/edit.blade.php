@extends('layouts.admin')

@section('title', 'Edit Report')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('auth.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Report
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
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="post" action="{{ route('reports.update', $report->id) }}"
                                enctype="multipart/form-data" id="report-form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="report_id" value="{{ $report->id }}">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Title" value="{{ old('title', $report->title) }}" required>
                                </div>

                                <!-- SDG -->
                                <div class="mb-3">
                                    <label for="sdg" class="form-label">Sustainable Development Goals</label>
                                    <select name="sdg[]" id="sdg" class="form-select select2-multiple" required
                                        multiple>
                                        @foreach ($sdgs as $sdg)
                                            <option value="{{ $sdg->id }}"
                                                {{ in_array($sdg->id, $report->sdg->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $sdg->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Related Type -->
                                <div class="mb-3">
                                    <label for="related_type" class="form-label">Select Project/Research</label>
                                    <select id="related_type" name="related_type" class="form-select" required>
                                        <option value="" disabled>Select Type</option>
                                        <option value="project"
                                            {{ old('related_type', $report->related_type) == 'project' ? 'selected' : '' }}>
                                            Projects/Programs
                                        </option>
                                        <option value="research"
                                            {{ old('related_type', $report->related_type) == 'research' ? 'selected' : '' }}>
                                            Research & Extension
                                        </option>
                                    </select>
                                </div>

                                <!-- Related Item -->
                                <div class="mb-3">
                                    <label for="related_id" class="form-label">Select Project/Research Item</label>
                                    <select id="related_id" name="related_id" class="form-select" required>
                                        <!-- Options will be populated by AJAX -->
                                    </select>
                                </div>

                                <!-- Review Status -->
                                <div class="mb-3">
                                    <label for="review_status_id" class="form-label">Review Status</label>
                                    <select name="review_status_id" id="review_status_id" class="form-select" required>
                                        @foreach ($reviewStatuses as $status)
                                            <option value="{{ $status->id }}"
                                                {{ old('review_status_id', $report->review_status_id) == $status->id ? 'selected' : '' }}>
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
                                <!-- Image Upload -->
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image upload (Optional)</label>

                                    @if (isset($existingImage) && $existingImage)
                                        <div class="mb-3">
                                            <label>Current Image:</label><br>
                                            <img src="{{ $existingImage }}" alt="Current Image"
                                                style="max-width: 500px; height: auto;">
                                        </div>
                                    @endif

                                    <input type="file" name="image" class="form-control" id="image">
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="10" required>{{ old('description', $report->description) }}</textarea>
                                </div>
                                <!-- Created By (Display Only) -->
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created by:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $report->user->first_name }} {{ $report->user->last_name }}" readonly>
                                </div>
                                <!-- Update Button -->
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#confirmationModal">Update</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <!-- Confirmation Modal -->
                                <div class="modal fade" id="confirmationModal" tabindex="-1"
                                    aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Edit</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to update this report?
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
            $('#sdg').select2();
            $('#related_id').select2();


            $('#related_type').change(function() {
                let type = $(this).val();
                let relatedId = $('#related_id');

                relatedId.empty().append(
                    '<option value="" disabled selected>Select Option</option>'); // Reset dropdown

                if (type) {
                    $.ajax({
                        url: "{{ route('reports.get_related_records') }}",
                        method: 'GET',
                        data: {
                            type: type
                        },
                        success: function(data) {
                            data.forEach(function(item) {
                                relatedId.append(
                                    `<option value="${item.id}" ${item.id == {{ $report->related_id }} ? 'selected' : ''}>${item.title || item.name}</option>`
                                );
                            });
                        }
                    });
                }
            }).trigger('change'); // Trigger change to populate dropdown on page load if related_type is set

            // Initialize select2 for related_id
            $('#related_id').select2();

            $('#update-button').click(function() {
                $('#confirmationModal').modal('show');
            });


            $('#confirm-update').on('click', function() {
                $('#report-form').submit();
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
    <script>
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
                    window.location.href = '{{ route('reports.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href = '{{ route('reports.index') }}'; // Redirect to home or desired route
            }
        });

     
    </script>
@endsection

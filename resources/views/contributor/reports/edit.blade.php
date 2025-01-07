@extends('layouts.contributor')

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
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
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
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="post" action="{{ route('contributor.reports.update', $report->id) }}"
                                class="needs-validation" enctype="multipart/form-data" id="report-form" novalidate>
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Title" value="{{ old('title', $report->title) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="sdg" class="form-label">Sustainable Development Goals</label>
                                    <select name="sdg[]" id="sdg" class="form-select" required multiple>
                                        @foreach ($sdgs as $sdg)
                                            <option value="{{ $sdg->id }}"
                                                {{ in_array($sdg->id, $report->sdg->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $sdg->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="related_type" class="form-label">Select Project/Research</label>
                                    <select id="related_type" name="related_type" class="form-select" required>
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="project"
                                            {{ old('related_type', $report->related_type) == 'project' ? 'selected' : '' }}>
                                            Projects/Programs</option>
                                        <option value="research"
                                            {{ old('related_type', $report->related_type) == 'research' ? 'selected' : '' }}>
                                            Research & Extension</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="related_id" class="form-label">Select Project/Research Item</label>
                                    <select id="related_id" name="related_id" class="form-select" required>
                                        <!-- Options will be populated by AJAX -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="10" required>{{ old('description', $report->description) }}</textarea>
                                </div>

                                <!-- Image Upload Input -->
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
                                                <button type="submit" class="btn btn-primary">Update</button>
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

            $('#confirm-update').click(function() {
                $('#report-form').submit();
            });
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
                    window.location.href = '{{ route('contributor.reports.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href = '{{ route('contributor.reports.index') }}'; // Redirect to home or desired route
            }
        });

      
    </script>
@endsection

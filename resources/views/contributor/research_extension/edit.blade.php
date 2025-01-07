@extends('layouts.contributor')

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
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
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

                            <form method="post" action="{{ route('contributor.research.update', $research->id) }}"
                                enctype="multipart/form-data" id="research-form">
                                @csrf
                                @method('PUT')

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
                                    <select name="sdg[]" id="sdg" class="form-select" required multiple>
                                        @foreach ($sdgs as $sdg)
                                            <option value="{{ $sdg->id }}"
                                                {{ in_array($sdg->id, $research->sdg->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $sdg->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Sub-categories Section -->
                                <div class="mb-3" id="sub-categories" style="display: none;">
                                    <label for="sdg_sub_categories" class="form-label">Select SDG Targets
                                        (Optionally)</label>
                                    <div id="sub-category-checkboxes">
                                        @foreach ($sdgs as $sdg)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sdg_sub_category[]"
                                                    value="{{ $sdg->id }}" id="subCategory{{ $sdg->id }}"
                                                    {{ in_array($sdg->id, $selectedSubCategories) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="subCategory{{ $sdg->id }}">
                                                    {{ $sdg->name }}: {{ $sdg->description }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p>
                                        Source: <a
                                            href="https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf"
                                            target="_blank">https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf</a>
                                    </p>
                                </div>
                                <!-- Research Status Dropdown -->
                                <div class="mb-3">
                                    <label for="status_id" class="form-label">Research Status</label>
                                    <select name="status_id" id="status_id" class="form-select" required>
                                        <option disabled selected>Choose Status</option>
                                        @foreach ($projectResearchStatuses as $status)
                                            <option value="{{ $status->id }}" @selected(old('status_id', $research->status_id) == $status->id)>
                                                {{ $status->status }}
                                            </option>
                                        @endforeach
                                    </select>
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


                                <div class="mb-3">
                                    <label for="file_link" class="form-label">Note: (If you have the full version of the
                                        file, please provide the link below. If not, leave it blank.) (Optional)</label>
                                    <input type="text" class="form-control" id="file_link" name="file_link"
                                        value="{{ old('file_link', $research->file_link) }}" placeholder="">
                                </div>

                                <!-- Research Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="10" required>{{ old('description', $research->description) }}</textarea>
                                </div>
                                <!-- Created By (Display Only) -->
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created by:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $research->user->first_name }} {{ $research->user->last_name }}"
                                        readonly>
                                </div>
                                <!-- Update Button -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#confirmationModal">Update Research</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
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
        var selectedSubCategories = @json($selectedSubCategories);
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for SDGs and Research Status
            $('#sdg').select2();
            $(document).ready(function() {
                $('#sdg').select2({
                    width: '100%',
                    placeholder: 'Select SDGs',
                });
                // Load sub-categories based on selected SDGs
                $('#sdg').on('change', function() {
                    var selectedSdgs = $(this).val();
                    if (selectedSdgs.length > 0) {
                        $.ajax({
                            url: '{{ route('sdg.subcategories') }}',
                            method: 'GET',
                            data: {
                                sdg_ids: selectedSdgs
                            },
                            success: function(data) {
                                $('#sub-category-checkboxes').empty();
                                if (data.length > 0) {
                                    data.forEach(function(subCategory) {
                                        $('#sub-category-checkboxes').append(
                                            '<div class="form-check">' +
                                            '<input class="form-check-input" type="checkbox" name="sdg_sub_category[]" value="' +
                                            subCategory.id +
                                            '" id="subCategory' +
                                            subCategory.id + '"' + (
                                                selectedSubCategories
                                                .includes(subCategory.id) ?
                                                ' checked' : '') + '>' +
                                            '<label class="form-check-label" for="subCategory' +
                                            subCategory.id + '">' +
                                            subCategory.sub_category_name +
                                            ': ' +
                                            subCategory
                                            .sub_category_description +
                                            '</label>' +
                                            '</div>'
                                        );
                                    });
                                    $('#sub-categories').show();
                                } else {
                                    $('#sub-categories').hide();
                                }
                            }
                        });
                    } else {
                        $('#sub-categories').hide();
                    }
                });

                // Trigger change event on page load to load existing sub-categories
                $('#sdg').trigger('change');
            });
            $('#update-button').click(function() {
                $('#confirmationModal').modal('show');
            });

            $('#confirm-update').click(function() {
                $('#research-form').submit();
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
                    window.location.href =
                        '{{ route('contributor.research.index') }}'; // Redirect to home or desired route
                }
            } else {
                window.location.href =
                    '{{ route('contributor.research.index') }}'; // Redirect to home or desired route
            }
        });
    </script>
@endsection

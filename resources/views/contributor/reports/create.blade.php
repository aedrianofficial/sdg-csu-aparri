@extends('layouts.contributor')

@section('title', 'Create Report')


@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Create Report
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

                            <form id="reportForm" method="post" action="{{ route('contributor.reports.store') }}"
                                class="needs-validation" enctype="multipart/form-data" novalidate>
                                @csrf

                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Title" value="{{ old('title') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="related_type" class="form-label">Select Project/Research</label>
                                    <select id="related_type" name="related_type" class="form-select" required>
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="project">Projects/Programs</option>
                                        <option value="research">Research & Extension</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="related_id" class="form-label">Select Project/Research Item</label>
                                    <select id="related_id" name="related_id" class="form-select" required>
                                        <option value="" disabled selected>Select Option</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File Upload</label>
                                    <input type="file" name="image" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="10" required>{{ old('description') }}</textarea>
                                </div>

                                <!-- "Submit for Review" Button -->
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#confirmationModal">Submit</button>
                                <button type="button" class="btn btn-secondary" id="cancelButton">
                                    <i class="fas fa-times"></i> Cancel
                                </button>

                                <!-- Confirmation Modal -->
                                <div class="modal fade" id="confirmationModal" tabindex="-1"
                                    aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Submission</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to submit this report for review?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" id="confirmSubmit"
                                                    class="btn btn-primary">Submit</button>
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


            $('#related_id').select2();
            $('#related_type').change(function() {
                var relatedType = $(this).val();
                var relatedIdSelect = $('#related_id');

                if (relatedType) {
                    $.ajax({
                        url: "{{ route('reports.get_related_records') }}",
                        type: 'GET',
                        data: {
                            type: relatedType
                        },
                        success: function(data) {
                            relatedIdSelect.empty().append(
                                '<option value="" disabled selected>Select Option</option>'
                            );
                            $.each(data, function(index, item) {
                                relatedIdSelect.append('<option value="' + item.id +
                                    '">' + item.title + '</option>');
                            });
                        }
                    });
                } else {
                    relatedIdSelect.empty().append(
                        '<option value="" disabled selected>Select Option</option>'
                    );
                }
            });

            // Handle confirmation modal submission
            $('#confirmSubmit').on('click', function() {
                $('#reportForm').submit(); // Submit the form when confirmed
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

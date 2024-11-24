@extends('layouts.admin')

@section('title', 'Create Report')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Reports</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Create Reports
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

                            <form method="post" action="{{ route('reports.store') }}" enctype="multipart/form-data"
                                id="report-form" novalidate>
                                @csrf

                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Title" value="{{ old('title') }}" required>
                                </div>

                                <!-- Project/Research Type -->
                                <div class="mb-3">
                                    <label for="related_type" class="form-label">Select Project/Research</label>
                                    <select id="related_type" name="related_type" class="form-select" required>
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="project">Projects/Programs</option>
                                        <option value="research">Research & Extension</option>
                                    </select>
                                </div>

                                <!-- Project/Research Item -->
                                <div class="mb-3">
                                    <label for="related_id" class="form-label">Select Project/Research Item</label>
                                    <select id="related_id" name="related_id" class="form-select" required>
                                        <option value="" disabled selected>Select Option</option>
                                    </select>
                                </div>

                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image upload (Maximum of <strong>2 mb</strong>
                                        only)</label>
                                    <input type="file" name="image" class="form-control" id="image" required>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" rows="10" required>{{ old('description') }}</textarea>
                                </div>

                                <!-- Submission Buttons -->
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#submitReviewModal">Submit for Review</button>
                                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                    data-bs-target="#publishModal">Publish Immediately</button>

                                <!-- Hidden Inputs -->
                                <input type="hidden" name="submit_type" id="submit_type">

                                <!-- "Submit for Review" Confirmation Modal -->
                                <div class="modal fade" id="submitReviewModal" tabindex="-1"
                                    aria-labelledby="submitReviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="submitReviewModalLabel">Confirm Submission</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to submit this report for review?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" id="confirmSubmitReview"
                                                    class="btn btn-primary">Submit for Review</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- "Publish Immediately" Confirmation Modal -->
                                <div class="modal fade" id="publishModal" tabindex="-1" aria-labelledby="publishModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="publishModalLabel">Confirm Publish</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to publish this report immediately?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" id="confirmPublish"
                                                    class="btn btn-success">Publish</button>
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


            // Handle related_type change and load corresponding items
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
                                '<option value="" disabled selected>Select Option</option>');
                            $.each(data, function(index, item) {
                                relatedIdSelect.append('<option value="' + item.id +
                                    '">' + item.title + '</option>');
                            });
                        }
                    });
                } else {
                    relatedIdSelect.empty().append(
                        '<option value="" disabled selected>Select Option</option>');
                }
            });
        });

        // Handle confirmation modal for form submission
        document.getElementById('confirmSubmitReview').addEventListener('click', function() {
            document.getElementById('submit_type').value = 'review';
            document.getElementById('report-form').submit();
        });

        document.getElementById('confirmPublish').addEventListener('click', function() {
            document.getElementById('submit_type').value = 'publish';
            document.getElementById('report-form').submit();
        });
    </script>

@endsection

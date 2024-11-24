@extends('layouts.contributor')

@section('title', 'Create Research')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Create Research
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
                            <h4 class="card-title">Create Research</h4>
                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="post" action="{{ route('contributor.research.store') }}"
                                enctype="multipart/form-data" id="researchForm">
                                @csrf

                                <!-- Research Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Research Title" value="{{ old('title') }}" required>
                                </div>

                                <!-- Research Categories -->
                                <div class="mb-3">
                                    <label for="researchcategory_id" class="form-label">Research Category</label>
                                    <select name="researchcategory_id" id="researchcategory_id" class="form-select"
                                        required>
                                        <option disabled selected>Choose Category</option>
                                        @foreach ($researchcategories as $category)
                                            <option value="{{ $category->id }}" @selected(old('researchcategory_id') == $category->id)>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Sustainable Development Goals (SDG) -->
                                <div class="mb-3">
                                    <label for="sdg" class="form-label">Sustainable Development Goals</label>
                                    <select name="sdg[]" id="sdg" class="form-select" required multiple>
                                        @if (count($sdgs) > 0)
                                            @foreach ($sdgs as $sdg)
                                                <option @selected(old('sdg') == $sdg->id) value="{{ $sdg->id }}">
                                                    {{ $sdg->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Research Status Dropdown -->
                                <div class="mb-3">
                                    <label for="research_status" class="form-label">Research Status</label>
                                    <select name="research_status" id="research_status" class="form-select" required>
                                        <option disabled selected>Choose Status</option>
                                        <option @selected(old('research_status') == 'Proposed') value="Proposed">Proposed</option>
                                        <option @selected(old('research_status') == 'On-Going') value="On-Going">On-Going</option>
                                        <option @selected(old('research_status') == 'On-Hold') value="On-Hold">On-Hold</option>
                                        <option @selected(old('research_status') == 'Completed') value="Completed">Completed</option>
                                        <option @selected(old('research_status') == 'Rejected') value="Rejected">Rejected</option>
                                    </select>
                                </div>

                                <!-- Hidden input for is_publish -->
                                <input type="hidden" name="is_publish" value="0"> <!-- 0 indicates Draft -->

                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">File (Abstract)</label>
                                    <input type="file" class="form-control" id="file" name="file">
                                </div>

                                <!-- Research Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="10" required>{{ old('description') }}</textarea>
                                </div>

                                <!-- Hidden input for review_status -->
                                <input type="hidden" name="review_status" value="Forwarded to Reviewer">

                                <!-- "Submit for Review" Button -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#confirmSubmitModal">Submit for Review</button>

                                <!-- Confirmation Modal -->
                                <div class="modal fade" id="confirmSubmitModal" tabindex="-1"
                                    aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmSubmitModalLabel">Confirm Submission</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to submit this research for review? Once submitted,
                                                you will not be able to edit this draft.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary"
                                                    id="confirmSubmitButton">Submit</button>
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



            // Handle confirmation modal submission
            $('#confirmSubmitButton').on('click', function() {
                $('#researchForm').submit();
            });
        });
    </script>
@endsection

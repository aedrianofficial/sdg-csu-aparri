@extends('layouts.reviewer')
@section('title', 'All Project/Program Lists')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Under Review Projects/Programs</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('reviewer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Under Review Projects/Programs
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
                            <h4 class="card-title">All Projects/Programs</h4>

                            <!-- Search and Filter Form -->
                            <form action="{{ route('reviewer.projects.under_review') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <!-- Title Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">Search:</label>
                                        <input id="search" type="text" name="title" class="form-control"
                                            value="{{ request('title') }}" placeholder="Enter Title">
                                    </div>
                                </div>

                                <h6>Filter by:</h6>
                                <div class="row">
                                    <!-- Project Status Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="project_status" class="form-label">Project Status:</label>
                                        <select id="project_status" name="project_status" class="form-select">
                                            <option value="" disabled selected>Select Project Status</option>
                                            <option value="Proposed"
                                                {{ request('project_status') == 'Proposed' ? 'selected' : '' }}>Proposed
                                            </option>
                                            <option value="On-Going"
                                                {{ request('project_status') == 'On-Going' ? 'selected' : '' }}>On-Going
                                            </option>
                                            <option value="On-Hold"
                                                {{ request('project_status') == 'On-Hold' ? 'selected' : '' }}>On-Hold
                                            </option>
                                            <option value="Completed"
                                                {{ request('project_status') == 'Completed' ? 'selected' : '' }}>Completed
                                            </option>
                                            <option value="Rejected"
                                                {{ request('project_status') == 'Rejected' ? 'selected' : '' }}>Rejected
                                            </option>
                                        </select>
                                    </div>

                                    <!-- SDG Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="sdg" class="form-label">Select SDG:</label>
                                        <select id="select2-multiple" name="sdg[]" class="form-select" multiple>
                                            @foreach ($sdgs as $sdg)
                                                <option value="{{ $sdg->id }}"
                                                    {{ in_array($sdg->id, request('sdg', [])) ? 'selected' : '' }}>
                                                    {{ $sdg->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Filter and Reset Buttons -->
                                <div class="row">
                                    <div class="col-md-12 text-left">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('reviewer.projects.under_review') }}"
                                            class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <!-- Responsive Projects Table -->
                            <div class="table-responsive">
                                @if (count($projects) > 0)
                                    <table id="projects-table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>SDG</th>
                                                <th>Project Status</th>
                                                <th>Review Status</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($projects as $project)
                                                <tr>
                                                    <td>{{ $project->title }}</td>
                                                    <td>
                                                        <ul>
                                                            @foreach ($project->sdg as $sdg)
                                                                <li>{{ $sdg->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $project->status->status }}</td>
                                                    <td>{{ $project->reviewStatus->status ?? 'N/A' }}</td>
                                                    <td>{{ $project->created_at->format('F j, Y, g:i A') }}</td>
                                                    <td>
                                                        <a href="{{ route('reviewer.projects.show', $project->id) }}"
                                                            class="btn btn-sm btn-success">View</a>




                                                    </td>
                                                    <td><!-- Button for 'Need Changes' -->
                                                        <button type="button" class="btn btn-sm btn-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#needChangesModal{{ $project->id }}">
                                                            Changes</button>
                                                    </td>
                                                    <td>
                                                        <!-- Button for 'Reject' -->
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $project->id }}">Reject</button>

                                                    </td>
                                                    <td><!-- Forward to Approver Button -->
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmReviewModal{{ $project->id }}">Reviewed</button>
                                                    </td>
                                                </tr>

                                                <!-- 'Reviewed' Modal -->
                                                <div class="modal fade" id="confirmReviewModal{{ $project->id }}"
                                                    tabindex="-1" aria-labelledby="confirmReviewModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="confirmReviewModalLabel">Confirm
                                                                    Review</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to mark this project as "Reviewed" and
                                                                forward it to the approver?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <form
                                                                    action="{{ route('reviewer.projects.reviewed', $project->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="btn btn-success">Confirm</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 'Need Changes' Modal -->
                                                <div class="modal fade" id="needChangesModal{{ $project->id }}"
                                                    tabindex="-1" aria-labelledby="needChangesModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="needChangesModalLabel">Need
                                                                    Changes Feedback</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('reviewer.projects.needchanges') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="project_id"
                                                                    value="{{ $project->id }}">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="feedback" class="form-label">Feedback
                                                                            (Required)
                                                                            :</label>
                                                                        <textarea name="feedback" id="feedback" class="form-control" rows="4" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">Submit</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 'Reject' Modal -->
                                                <div class="modal fade" id="rejectModal{{ $project->id }}"
                                                    tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="rejectModalLabel">Reject
                                                                    Project</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('reviewer.projects.reject') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="project_id"
                                                                    value="{{ $project->id }}">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="feedback" class="form-label">Feedback
                                                                            (Optional):</label>
                                                                        <textarea name="feedback" id="feedback" class="form-control" rows="4"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-danger">Reject
                                                                        Project</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>

                            <!-- Pagination -->
                            <div class="container">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous Button -->
                                        <li class="page-item {{ $projects->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link"
                                                href="{{ $projects->appends(request()->query())->previousPageUrl() }}"
                                                tabindex="-1">
                                                Previous
                                            </a>
                                        </li>

                                        <!-- Page Number Links -->
                                        @php
                                            $currentPage = $projects->currentPage(); // Current page number
                                            $lastPage = $projects->lastPage(); // Last page number
                                            $start = max($currentPage - 1, 1); // Calculate start of the visible page items
                                            $end = min($start + 2, $lastPage); // Calculate end of the visible page items
                                        @endphp

                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $projects->appends(request()->query())->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        <!-- Next Button -->
                                        <li class="page-item {{ $projects->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link"
                                                href="{{ $projects->appends(request()->query())->nextPageUrl() }}">
                                                Next
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            @else
                                <h3 class="text-danger text-center">No projects found</h3>
                                @endif
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

@section('scripts')

    <script>
        $(document).ready(function() {
            $('#select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });
        });
    </script>
@endsection

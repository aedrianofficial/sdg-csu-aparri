@extends('layouts.admin')
@section('title', 'My Project/Program Lists')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">My Projects</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            My Projects
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
                            <!-- Search and Filter Form -->
                            <form action="{{ route('projects.my_projects') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <!-- Title Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="title" class="form-label">Search:</label>
                                        <input id="search" type="text" name="title" class="form-control"
                                            value="{{ request('title') }}" placeholder="Enter Title">
                                    </div>
                                </div>
                                <h6>Filter by:</h6>
                                <div class="row">
                                    <!-- Project Status Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="project_status" class="form-label">Project Status:</label>
                                        <select id="project_status" name="project_status" class="form-select select2">
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

                                    <!-- Review Status Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="review_status" class="form-label">Review Status:</label>
                                        <select id="review_status" name="review_status" class="form-select select2">
                                            <option value="">All</option>
                                            @foreach ($reviewStatuses as $status)
                                                <option value="{{ $status->id }}"
                                                    {{ request('review_status') == $status->id ? 'selected' : '' }}>
                                                    {{ $status->status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- SDG Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="sdg" class="form-label">Select SDG:</label>
                                        <select id="sdg" name="sdg[]" class="form-select select2-multiple" multiple>
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
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('projects.my_projects') }}" class="btn btn-secondary">Reset</a>
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
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($projects as $project)
                                                <tr>
                                                    <td>{{ Str::limit($project->title, 15, '...') }}</td>
                                                    <td>
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach ($project->sdg as $sdg)
                                                                <li>{{ Str::limit($sdg->name, 15, '...') }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ Str::limit($project->project_status, 15, '...') }}</td>
                                                    <td>{{ $project->reviewStatus->status ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($project->reviewStatus->status === 'Need Changes')
                                                            <a href="{{ route('projects.need_changes', $project->id) }}"
                                                                class="btn btn-sm btn-success">
                                                                View
                                                            </a>
                                                        @elseif ($project->reviewStatus->status === 'Rejected')
                                                            <a href="{{ route('projects.rejected', $project->id) }}"
                                                                class="btn btn-sm btn-success">
                                                                View
                                                            </a>
                                                        @else
                                                            <a href="{{ route('projects.show', $project->id) }}"
                                                                class="btn btn-sm btn-success">
                                                                View
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('projects.edit', $project->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-danger text-center">No projects found</h3>
                                @endif
                            </div>

                            <!-- Custom Pagination Links -->
                            <div class="d-flex justify-content-center mt-4">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <!-- Previous Button -->
                                        <li class="page-item {{ $projects->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $projects->previousPageUrl() }}"
                                                tabindex="-1">Previous</a>
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
                                                    href="{{ $projects->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        <!-- Next Button -->
                                        <li class="page-item {{ $projects->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $projects->nextPageUrl() }}">Next</a>
                                        </li>
                                    </ul>
                                </nav>
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
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });

            $('.select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });
        });
    </script>
@endsection
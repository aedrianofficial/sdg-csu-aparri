@extends('layouts.reviewer')
@section('title', 'Rejected Project/Program Lists')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Rejected Projects/Programs</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Rejected Projects/Programs
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header--> <!--begin::App Content-->
    <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">


                                <!-- Search and Filter Form -->
                                <form action="{{ route('reviewer.projects.rejected') }}" method="GET" class="mb-4">
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
                                                    {{ request('project_status') == 'Completed' ? 'selected' : '' }}>
                                                    Completed</option>
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
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('reviewer.projects.rejected') }}"
                                            class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>

                                <!-- Responsive Projects Table -->
                                <div class="table-responsive">
                                    @if (count($projectsPaginated) > 0)
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
                                                @foreach ($projectsPaginated as $project)
                                                    <tr>
                                                        <td>{{ Str::limit($project->title, 15, '...') }}</td>
                                                        <td>
                                                            <ul>
                                                                @foreach ($project->sdg as $sdg)
                                                                    <li>{{ Str::limit($sdg->name, 15, '...') }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </td>
                                                        <td>{{ Str::limit($project->project_status, 15, '...') }}</td>
                                                        <td>{{ $project->reviewStatus->status ?? 'N/A' }}</td>
                                                        <td>
                                                            <a href="{{ route('reviewer.projects.feedback_rejected', $project->id) }}"
                                                                class="btn btn-sm btn-success">View</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                </div>

                                <div class="container">
                                    <!-- Custom Pagination Links -->
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination justify-content-center">
                                            <!-- Previous Button -->
                                            <li
                                                class="page-item {{ $projectsPaginated->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $projectsPaginated->previousPageUrl() }}"
                                                    tabindex="-1">Previous</a>
                                            </li>

                                            <!-- Page Number Links -->
                                            @php
                                                $currentPage = $projectsPaginated->currentPage();
                                                $lastPage = $projectsPaginated->lastPage();
                                                $start = max($currentPage - 1, 1);
                                                $end = min($start + 2, $lastPage);
                                            @endphp

                                            @for ($i = $start; $i <= $end; $i++)
                                                <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                    <a class="page-link"
                                                        href="{{ $projectsPaginated->url($i) }}">{{ $i }}</a>
                                                </li>
                                            @endfor

                                            <!-- Next Button -->
                                            <li
                                                class="page-item {{ $projectsPaginated->hasMorePages() ? '' : 'disabled' }}">
                                                <a class="page-link"
                                                    href="{{ $projectsPaginated->nextPageUrl() }}">Next</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            @else
                                <h3 class="text-danger text-center">No projects found</h3>
                                @endif
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
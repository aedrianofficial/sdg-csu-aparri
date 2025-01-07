@extends('layouts.admin')
@section('title', 'Project/Program Lists')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Projects</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('auth.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            All Projects
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
                            <form action="{{ route('projects.index') }}" method="GET" class="mb-4">
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
                                        <select id="project_status" name="status_id" class="form-select select2">
                                            <option value="" disabled selected>Select Project Status</option>
                                            @foreach ($projectStatuses as $status)
                                                <option value="{{ $status->id }}"
                                                    {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                                    {{ $status->status }}
                                                </option>
                                            @endforeach
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
                                <div class="d-flex justify-content-start mb-3">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('projects.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>

                            <!-- Responsive Projects Table -->
                            <div class="table-responsive">
                                @if (count($projects) > 0)
                                    <table id="projects-table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a
                                                        href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}">
                                                        Title
                                                        @if (request('sort_by') === 'title')
                                                            <i
                                                                class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>SDG</th> <!-- No sorting for SDG -->
                                                <th>
                                                    <a
                                                        href="{{ request()->fullUrlWithQuery(['sort_by' => 'status_id', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}">
                                                        Project Status
                                                        @if (request('sort_by') === 'status_id')
                                                            <i
                                                                class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>
                                                    <a
                                                        href="{{ request()->fullUrlWithQuery(['sort_by' => 'review_status_id', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}">
                                                        Review Status
                                                        @if (request('sort_by') === 'review_status_id')
                                                            <i
                                                                class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>
                                                    <a
                                                        href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}">
                                                        Created At
                                                        @if (request('sort_by') === 'created_at')
                                                            <i
                                                                class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>Action</th> <!-- No sorting for Action -->
                                                <th></th>
                                                <th>Reports</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($projects as $project)
                                                <tr>
                                                    <td>{{ $project->title }}</td>
                                                    <td>
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach ($project->sdg as $sdg)
                                                                <li>{{ $sdg->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $project->status->status ?? 'N/A' }}</td>
                                                    <!-- Update this line -->
                                                    <td>{{ $project->reviewStatus->status ?? 'N/A' }}</td>

                                                    <td>{{ $project->created_at->format('F j, Y, g:i A') }}</td>
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

                                                    </td>
                                                    <td> <a href="{{ route('projects.edit', $project->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            Edit
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @php
                                                            // Fetch all relevant status reports for the current project
                                                            $statusReports = App\Models\StatusReport::where(
                                                                'related_type',
                                                                App\Models\Project::class,
                                                            )
                                                                ->where('related_id', $project->id)
                                                                ->where('review_status_id', 3) // Completed
                                                                ->where('is_publish', 1) // Published
                                                                ->get();

                                                            // Fetch all relevant terminal reports for the current project
                                                            $terminalReports = App\Models\TerminalReport::where(
                                                                'related_type',
                                                                App\Models\Project::class,
                                                            )
                                                                ->where('related_id', $project->id)
                                                                ->where('is_publish', 1) // Published
                                                                ->get();

                                                            // Define the statuses that require status reports
                                                            $statusesToCheckForStatusReport = [
                                                                1 => 'Proposed',
                                                                2 => 'On-Going',
                                                                3 => 'On-Hold',
                                                                5 => 'Rejected',
                                                            ]; // Map status IDs to their string representations for status reports

                                                            // Define the statuses that require terminal reports
                                                            $statusesToCheckForTerminalReport = [
                                                                4 => 'Completed',
                                                            ]; // Map status IDs to their string representations for terminal reports

                                                            // Messages for each status report
                                                            $statusMessages = [];
                                                            foreach (
                                                                $statusesToCheckForStatusReport
                                                                as $statusId => $statusName
                                                            ) {
                                                                // Check if a status report exists for this status
                                                                $reportExists = $statusReports->contains(function (
                                                                    $report,
                                                                ) use ($statusName) {
                                                                    return $report->log_status == $statusName; // Compare with string representation
                                                                });

                                                                // Store the existence of the report with the status name
                                                                $statusMessages[$statusId] = $reportExists
                                                                    ? "'{$statusName}' status has a report."
                                                                    : "'{$statusName}' status has no report.";
                                                            }

                                                            // Check if all status reports are generated
                                                            $allStatusReportsGenerated = collect(
                                                                $statusMessages,
                                                            )->every(fn($msg) => str_contains($msg, 'has a report'));

                                                            // Check if the current project status has a report
                                                            $currentStatusHasReport = false;
                                                            if (
                                                                in_array(
                                                                    $project->status_id,
                                                                    array_keys($statusesToCheckForStatusReport),
                                                                )
                                                            ) {
                                                                $currentStatusHasReport = $statusReports->contains(
                                                                    function ($report) use (
                                                                        $project,
                                                                        $statusesToCheckForStatusReport,
                                                                    ) {
                                                                        return $report->log_status ==
                                                                            $statusesToCheckForStatusReport[
                                                                                $project->status_id
                                                                            ]; // Check if the current status has a report
                                                                    },
                                                                );
                                                            }

                                                            // Check if a terminal report exists for the current project status
                                                            $currentTerminalReportExists = $terminalReports->contains(
                                                                function ($report) use (
                                                                    $project,
                                                                    $statusesToCheckForTerminalReport,
                                                                ) {
                                                                    return $report->related_title == $project->title; // Adjust this condition as needed
                                                                },
                                                            );

                                                            // Messages for terminal report
                                                            $terminalReportMessage = $currentTerminalReportExists
                                                                ? "'Completed' status has a terminal report."
                                                                : "'Completed' status has no terminal report.";
                                                        @endphp

                                                        {{-- Display status indicators for status reports --}}
                                                        <div class="status-indicators">
                                                            <div class="status-icons">
                                                                @foreach ($statusesToCheckForStatusReport as $statusId => $statusName)
                                                                    <span class="status-icon" title="{{ $statusMessages[$statusId] }}">
                                                                        @if (str_contains($statusMessages[$statusId], 'has a report'))
                                                                            @php
                                                                                // Get the report for the current status
                                                                                $report = $statusReports->firstWhere('log_status', $statusName);
                                                                            @endphp
                                                                            <a href="{{ route('auth.status_reports.show_project_published', $report->id) }}">
                                                                                <i class="fas fa-check-circle text-success"></i>
                                                                                <!-- Check icon for report exists -->
                                                                            </a>
                                                                        @else
                                                                            <i class="fas fa-times-circle text-danger"></i>
                                                                            <!-- Cross icon for no report -->
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                        
                                                                {{-- Display status indicator for terminal report --}}
                                                                <span class="status-icon" title="{{ $terminalReportMessage }}">
                                                                    @if ($currentTerminalReportExists)
                                                                        @php
                                                                            // Get the terminal report for the current project
                                                                            $terminalReport = $terminalReports->firstWhere('related_title', $project->title);
                                                                        @endphp
                                                                        <a href="{{ route('auth.terminal_reports.show_project_published', $terminalReport->id) }}">
                                                                            <i class="fas fa-check-circle text-success"></i>
                                                                            <!-- Check icon for terminal report exists -->
                                                                        </a>
                                                                    @else
                                                                        <i class="fas fa-times-circle text-danger"></i>
                                                                        <!-- Cross icon for no terminal report -->
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        
                                                            {{-- Display the message for the status that has a report --}}
                                                            @foreach ($statusesToCheckForStatusReport as $statusId => $statusName)
                                                                @if (str_contains($statusMessages[$statusId], 'has a report'))
                                                                    <div class="badge bg-success text-white">
                                                                        '{{ ucfirst($statusName) }}' Status Report generated.
                                                                    </div>
                                                                    @break  <!-- Exit the loop after displaying the first generated report message -->
                                                                @endif
                                                            @endforeach
                                                        
                                                            {{-- Display terminal report message if it exists --}}
                                                            @if ($currentTerminalReportExists)
                                                                <div class="badge bg-success text-white">
                                                                    Terminal Report generated.
                                                                </div>
                                                            @endif
                                                        </div>

                                                        {{-- Overall status badge or action --}}
                                                        @if ($allStatusReportsGenerated)
                                                            <span class="badge bg-success text-white">All Status Reports
                                                                Generated</span>
                                                        @elseif (in_array($project->status_id, array_keys($statusesToCheckForStatusReport)) &&
                                                                $project->is_publish == 1 &&
                                                                !$currentStatusHasReport)
                                                            <a href="{{ route('auth.status_reports.create_project', [
                                                                'related_type' => App\Models\Project::class,
                                                                'related_id' => $project->id,
                                                                'related_title' => $project->title,
                                                                'log_status' => $statusesToCheckForStatusReport[$project->status_id], // Pass the string representation of the current status
                                                            ]) }}"
                                                                class="btn btn-sm btn-primary m-1">Generate Status Report</a>
                                                        @elseif ($project->status_id == 4 && $project->is_publish == 1)
                                                            {{-- Uncomment to show the Terminal Report button for 'Completed' projects --}}
                                                            @if (!$currentTerminalReportExists)
                                                                <a href="{{ route('auth.terminal_reports.create_project', [
                                                                    'related_type' => App\Models\Project::class,
                                                                    'related_id' => $project->id,
                                                                    'related_title' => $project->title,
                                                                ]) }}"
                                                                    class="btn btn-sm btn-primary m-1">Generate Terminal
                                                                    Report</a>
                                                           
                                                            @endif
                                                        @else
                                                            <span class="badge bg-danger text-white">Unable to generate
                                                                reports</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-danger text-center">No projects found</h3>
                                @endif
                            </div>


                            <div class="container">
                                <!-- Custom Pagination Links -->
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


            $('.select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });
        });
    </script>
@endsection

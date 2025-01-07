@extends('layouts.contributor')
@section('title', 'All Research Lists')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">My Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('contributor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            My Research
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
                            <form action="{{ route('contributor.research.index') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <!-- Title Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">Search:</label>
                                        <input id="search" type="text" name="title" class="form-control"
                                            value="{{ request('title') }}" placeholder="Enter Title">
                                    </div>
                                </div>

                                <h6>Filter by:</h6>
                                <div class="row"> <!-- Start a new row for the next filter -->
                      
                                    <!-- Research Status Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="research_status" class="form-label">Research Status:</label>
                                        <select id="research_status" name="status_id" class="form-select select2">
                                            <option value="" disabled selected>Select Research Status</option>
                                            @foreach ($researchStatuses as $status)
                                                <option value="{{ $status->id }}"
                                                    {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                                    {{ $status->status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Research Category Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="researchcategory_id" class="form-label">Research Category:</label>
                                        <select name="researchcategory_id" class="form-select select2">
                                            <option value="">Select Category</option>
                                            @foreach ($researchCategories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request('researchcategory_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
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
                                <div class="row">
                                    <div class="col-md-12 text-left">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('contributor.research.index') }}"
                                            class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <!-- Responsive Research Table -->
                            <div class="table-responsive">
                                @if (count($researches) > 0)
                                    <h4 class="card-title">All Research</h4>
                                    <table id="research-table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Research Categories</th>
                                                <th>SDGs</th>
                                                <th>Review Status</th>
                                                <th>Research Status</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($researches as $research)
                                                <tr>
                                                    <td>{{ $research->title }}</td>
                                                    <td>{{ $research->researchcategory->name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            @foreach ($research->sdg as $sdg)
                                                                <li>{{ $sdg->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $research->reviewStatus->status ?? 'N/A' }}</td>
                                                    <td>{{  $research->status->status }}</td>
                                                    <td>{{ $research->created_at->format('F j, Y, g:i A') }}</td>
                                                    <td>
                                                        <a href="{{ route('contributor.research.show', $research->id) }}"
                                                            class="btn btn-sm btn-success">View</a>
                                                    </td>
                                                    <td>
                                                        @php
                                                            // Fetch all relevant status reports for the current research
                                                            $statusReports = App\Models\StatusReport::where(
                                                                'related_type',
                                                                App\Models\Research::class,
                                                            )
                                                                ->where('related_id', $research->id)
                                                                ->where('review_status_id', 3) // Completed
                                                                ->where('is_publish', 1) // Published
                                                                ->get();

                                                            // Fetch all relevant terminal reports for the current research
                                                            $terminalReports = App\Models\TerminalReport::where(
                                                                'related_type',
                                                                App\Models\Research::class,
                                                            )
                                                                ->where('related_id', $research->id)
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

                                                            // Check if the current research status has a report
                                                            $currentStatusHasReport = false;
                                                            if (
                                                                in_array(
                                                                    $research->status_id,
                                                                    array_keys($statusesToCheckForStatusReport),
                                                                )
                                                            ) {
                                                                $currentStatusHasReport = $statusReports->contains(
                                                                    function ($report) use (
                                                                        $research,
                                                                        $statusesToCheckForStatusReport,
                                                                    ) {
                                                                        return $report->log_status ==
                                                                            $statusesToCheckForStatusReport[
                                                                                $research->status_id
                                                                            ]; // Check if the current status has a report
                                                                    },
                                                                );
                                                            }

                                                            // Check if a terminal report exists for the current research status
                                                            $currentTerminalReportExists = $terminalReports->contains(
                                                                function ($report) use (
                                                                    $research,
                                                                    $statusesToCheckForTerminalReport,
                                                                ) {
                                                                    return $report->related_title == $research->title; // Adjust this condition as needed
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
                                                                            <a href="{{ route('contributor.status_reports.show_project_published', $report->id) }}">
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
                                                                            // Get the terminal report for the current research
                                                                            $terminalReport = $terminalReports->firstWhere('related_title', $research->title);
                                                                        @endphp
                                                                        <a href="{{ route('contributor.terminal_reports.show_project_published', $terminalReport->id) }}">
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
                                                            <span class="badge bg-secondary text-white">All Status Reports
                                                                Generated</span>
                                                        @elseif (in_array($research->status_id, array_keys($statusesToCheckForStatusReport)) &&
                                                                $research->is_publish == 1 &&
                                                                !$currentStatusHasReport)
                                                            <a href="{{ route('contributor.status_reports.create_research', [
                                                                'related_type' => App\Models\Research::class,
                                                                'related_id' => $research->id,
                                                                'related_title' => $research->title,
                                                                'log_status' => $statusesToCheckForStatusReport[$research->status_id], // Pass the string representation of the current status
                                                            ]) }}"
                                                                class="btn btn-sm btn-primary m-1">Generate Status Report</a>
                                                        @elseif ($research->status_id == 4 && $research->is_publish == 1)
                                                            {{-- Uncomment to show the Terminal Report button for 'Completed' researchs --}}
                                                            @if (!$currentTerminalReportExists)
                                                                <a href="{{ route('contributor.terminal_reports.create_research', [
                                                                    'related_type' => App\Models\Research::class,
                                                                    'related_id' => $research->id,
                                                                    'related_title' => $research->title,
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
                            </div>

                            <div class="container">
                                <!-- Custom Pagination Links -->
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous Button -->
                                        <li class="page-item {{ $researches->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link"
                                                href="{{ $researches->appends(request()->query())->previousPageUrl() }}"
                                                tabindex="-1">Previous</a>
                                        </li>

                                        <!-- Page Number Links -->
                                        @php
                                            $currentPage = $researches->currentPage(); // Current page number
                                            $lastPage = $researches->lastPage(); // Last page number
                                            $start = max($currentPage - 1, 1); // Calculate start of the visible page items
                                            $end = min($start + 2, $lastPage); // Calculate end of the visible page items
                                        @endphp

                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $researches->appends(request()->query())->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        <!-- Next Button -->
                                        <li class="page-item {{ $researches->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link"
                                                href="{{ $researches->appends(request()->query())->nextPageUrl() }}">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            @else
                                <h3 class="text-danger text-center">No research found</h3>
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


            $('.select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });
        });
    </script>
@endsection

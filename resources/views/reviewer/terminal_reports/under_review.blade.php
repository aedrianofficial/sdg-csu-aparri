@extends('layouts.reviewer')
@section('title', 'Terminal Reports')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Terminal Reports</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('reviewer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            All Terminal Reports
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

                            <form action="{{ route('reviewer.terminal_reports.under_review') }}" method="GET"
                                class="mb-4">
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
                                    <!-- Related Type Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="related_type" class="form-label">Related Type:</label>
                                        <select name="related_type" class="form-select select2">
                                            <option value="">Select Related Type</option>
                                            <option value="App\Models\Project"
                                                {{ request()->related_type == 'App\Models\Project' ? 'selected' : '' }}>
                                                Project
                                            </option>
                                            <option value="App\Models\Research"
                                                {{ request()->related_type == 'App\Models\Research' ? 'selected' : '' }}>
                                                Research
                                            </option>
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
                                <div class="text-start">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('reviewer.terminal_reports.under_review') }}"
                                        class="btn btn-secondary">Reset</a>
                                </div>
                            </form>

                            <!-- Table for Terminal Reports -->
                            <div class="table-responsive">
                                @if (count($reports) > 0)
                                    <table id="reports-table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a
                                                        href="{{ request()->fullUrlWithQuery(['sort_by' => 'related_title', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}">
                                                        Related Title
                                                        @if (request('sort_by') === 'related_title')
                                                            <i
                                                                class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>SDGs</th> <!-- No sorting for SDGs -->
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
                                                <th>Related Type</th>
                                                <th>Action</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reports as $report)
                                                <tr>
                                                    <td>{{ $report->related_title }}</td>
                                                    <td>
                                                        <ul>
                                                            @foreach ($report->sdgs as $sdg)
                                                                <!-- Use the new sdgs property -->
                                                                <li>{{ $sdg->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $report->reviewStatus ? $report->reviewStatus->status : 'N/A' }}
                                                    </td>
                                                    <td>{{ $report->created_at->format('F j, Y, g:i A') }}</td>
                                                    <td>
                                                        @if ($report->related_type == App\Models\Project::class)
                                                            Project
                                                        @elseif ($report->related_type == App\Models\Research::class)
                                                            Research
                                                        @else
                                                            {{ ucfirst(class_basename($report->related_type)) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($report->related_type === App\Models\Project::class)
                                                            <a href="{{ route('reviewer.terminal_reports.show_project', $report->id) }}"
                                                                class="btn btn-sm btn-success">View</a>
                                                        @elseif ($report->related_type === App\Models\Research::class)
                                                            <a href="{{ route('reviewer.terminal_reports.show_research', $report->id) }}"
                                                                class="btn btn-sm btn-success">View</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <!-- Button for 'Need Changes' -->
                                                        <button type="button" class="btn btn-sm btn-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#needChangesModal{{ $report->id }}">
                                                            Changes</button>
                                                    </td>
                                                    <td>
                                                        <!-- Button for 'Reject' -->
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $report->id }}">Reject</button>

                                                    </td>
                                                    <td>
                                                        <!-- Forward to Approver Button -->
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmReviewModal{{ $report->id }}">Reviewed</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-danger text-center">No terminal reports found</h3>
                                @endif
                            </div>

                            <!-- Modals for Actions -->
                            @foreach ($reports as $report)
                                <!-- 'Reviewed' Modal -->
                                <div class="modal fade" id="confirmReviewModal{{ $report->id }}" tabindex="-1"
                                    aria-labelledby="confirmReviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmReviewModalLabel">Confirm Review</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to mark this report as "Reviewed" and forward it to
                                                the approver?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('reviewer.terminal_reports.reviewed', $report->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success">Confirm</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 'Need Changes' Modal -->
                                <div class="modal fade" id="needChangesModal{{ $report->id }}" tabindex="-1"
                                    aria-labelledby="needChangesModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="needChangesModalLabel">Need Changes Feedback
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('reviewer.terminal_reports.needchanges') }}"
                                                method="POST">
                                                @csrf
                                                
                                                <input type="hidden" name="terminal_report_id"
                                                    value="{{ $report->id }}">
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
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- 'Reject' Modal -->
                                <div class="modal fade" id="rejectModal{{ $report->id }}" tabindex="-1"
                                    aria-labelledby="rejectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectModalLabel">Reject Report</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('reviewer.terminal_reports.reject') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="terminal_report_id"
                                                    value="{{ $report->id }}">
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
                                                    <button type="submit" class="btn btn-danger">Reject Report</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Pagination -->
                            <div class="container">
                                @if (count($reports) > 0)
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <!-- Previous Button -->
                                            <li class="page-item {{ $reports->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $reports->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                                                    tabindex="-1">Previous</a>
                                            </li>

                                            <!-- Page Number Links -->
                                            @php
                                                $currentPage = $reports->currentPage();
                                                $lastPage = $reports->lastPage();
                                                $start = max($currentPage - 1, 1);
                                                $end = min($start + 2, $lastPage);
                                            @endphp
                                            @for ($i = $start; $i <= $end; $i++)
                                                <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                    <a class="page-link"
                                                        href="{{ $reports->url($i) . '&' . http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                                </li>
                                            @endfor

                                            <!-- Next Button -->
                                            <li class="page-item {{ $reports->hasMorePages() ? '' : 'disabled' }}">
                                                <a class="page-link"
                                                    href="{{ $reports->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Next</a>
                                            </li>
                                        </ul>
                                    </nav>
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
            $('.select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });
        });
    </script>
@endsection

@extends('layouts.publisher')
@section('title', 'Status Reports')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Status Reports</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('publisher.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            All Status Reports
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

                            <form action="{{ route('publisher.status_reports.index') }}" method="GET"
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
                                <div class="text-start">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('publisher.status_reports.index') }}"
                                        class="btn btn-secondary">Reset</a>
                                </div>
                            </form>

                            <!-- Table for Status Reports -->
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
                                                                class="fa fa-sort-{{ request('sort _order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>Log Status</th>
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
                                                    <td>{{ $report->log_status }}</td>
                                                    <td>
                                                        <ul>
                                                            @foreach ($report->sdgs as $sdg)
                                                                <li>{{ $sdg->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $report->reviewStatus ? $report->reviewStatus->status : 'N/A' }}
                                                    </td>
                                                    <td>{{ $report->created_at->format('F j, Y, g:i A') }}</td>
                                                    <td>
                                                        @if ($report->related_type === 'App\\Models\\Project')
                                                            Project
                                                        @elseif ($report->related_type === 'App\\Models\\Research')
                                                            Research
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($report->related_type === App\Models\Project::class)
                                                            <a href="{{ route('publisher.status_reports.show_project', $report->id) }}"
                                                                class="btn btn-sm btn-success">View</a>
                                                        @elseif ($report->related_type === App\Models\Research::class)
                                                            <a href="{{ route('publisher.status_reports.show_research', $report->id) }}"
                                                                class="btn btn-sm btn-success">View</a>
                                                        @endif
                                                    </td>
                                                  
                                                 
                                                    <td>
                                                        <!-- Published  Button -->
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmPublishedModal{{ $report->id }}">Published</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-danger text-center">No status reports found</h3>
                                @endif
                            </div>

                            <!-- Modals for Actions -->
                            @foreach ($reports as $report)
                                <!-- 'Published' Modal -->
                                <div class="modal fade" id="confirmPublishedModal{{ $report->id }}" tabindex="-1"
                                    aria-labelledby="confirmPublishedModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmPublishedModalLabel">Confirm Review</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to mark this report as "Published"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <form
                                                    action="{{ route('publisher.status_reports.published', $report->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success">Confirm</button>
                                                </form>
                                            </div>
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

@extends('layouts.admin')
@section('title', 'My Activity Logs')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">My Activity Logs</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('auth.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Activity Logs</li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header-->

    <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Search and Filter Form -->
                            <form action="{{ route('auth.activity_logs.my_activity_logs') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <!-- Search Bar for Description -->
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">Search:</label>
                                        <input id="search" type="text" name="title" class="form-control"
                                            value="{{ request('title') }}" placeholder="Enter Description">
                                    </div>
                                    <!-- Event Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="event" class="form-label">Event:</label>
                                        <select id="event" name="event" class="form-select">
                                            <option value="" disabled selected>Select Event</option>
                                            <option value="submitted for review"
                                                {{ request('event') == 'submitted for review' ? 'selected' : '' }}>
                                                Submitted for Review
                                            </option>
                                            <option value="resubmitted for review"
                                                {{ request('event') == 'resubmitted for review' ? 'selected' : '' }}>
                                                Resubmitted for Review
                                            </option>
                                            <option value="reviewed" {{ request('event') == 'reviewed' ? 'selected' : '' }}>
                                                Reviewed
                                            </option>
                                            <option value="requested change"
                                                {{ request('event') == 'requested change' ? 'selected' : '' }}>
                                                Requested Change
                                            </option>
                                            <option value="rejected" {{ request('event') == 'rejected' ? 'selected' : '' }}>
                                                Rejected
                                            </option>
                                            <option value="approved" {{ request('event') == 'approved' ? 'selected' : '' }}>
                                                Approved
                                            </option>
                                            <option value="published"
                                                {{ request('event') == 'published' ? 'selected' : '' }}>
                                                Published
                                            </option>
                                            <option value="user_updated"
                                                {{ request('event') == 'user_updated' ? 'selected' : '' }}>
                                                User Updated
                                            </option>
                                            <option value="role_updated"
                                                {{ request('event') == 'role_updated' ? 'selected' : '' }}>
                                                Role Updated
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Type Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="type" class="form-label">Type:</label>
                                        <select id="type" name="type" class="form-select">
                                            <option value="" disabled selected>Select Type</option>
                                            <option value="Project" {{ request('type') == 'Project' ? 'selected' : '' }}>
                                                Project</option>
                                            <option value="User" {{ request('type') == 'User' ? 'selected' : '' }}>User
                                            </option>
                                            <option value="Report" {{ request('type') == 'Report' ? 'selected' : '' }}>
                                                Report</option>
                                            <option value="Research" {{ request('type') == 'Research' ? 'selected' : '' }}>
                                                Research</option>
                                        </select>
                                    </div>
                                    <!-- Date Range Filters -->
                                    <div class="col-md-3 mb-3">
                                        <label for="start_date" class="form-label">Start Date:</label>
                                        <input id="start_date" type="date" name="start_date" class="form-control"
                                            value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="end_date" class="form-label">End Date:</label>
                                        <input id="end_date" type="date" name="end_date" class="form-control"
                                            value="{{ request('end_date') }}">
                                    </div>

                                </div>

                                <!-- Filter and Reset Buttons -->
                                <div class="row">
                                    <div class="col-md-12 text-left">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('auth.activity_logs.my_activity_logs') }}"
                                            class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <!-- Responsive Activity Logs Table -->
                            <div class="table-responsive">
                                @if ($activityLogs->count() > 0)
                                    <table id="activity-logs-table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a href="{{ route('auth.activity_logs.my_activity_logs', array_merge(request()->query(), ['sort_by' => 'log_name', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                        Log Name
                                                        @if (request('sort_by') === 'log_name')
                                                            <i class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="{{ route('auth.activity_logs.my_activity_logs', array_merge(request()->query(), ['sort_by' => 'description', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                        Description
                                                        @if (request('sort_by') === 'description')
                                                            <i class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="{{ route('auth.activity_logs.my_activity_logs', array_merge(request()->query(), ['sort_by' => 'event', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                        Event
                                                        @if (request('sort_by') === 'event')
                                                            <i class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="{{ route('auth.activity_logs.my_activity_logs', array_merge(request()->query(), ['sort_by' => 'role', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                        Role
                                                        @if (request('sort_by') === 'role')
                                                            <i class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="{{ route('auth.activity_logs.my_activity_logs', array_merge(request()->query(), ['sort_by' => 'subject_type', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                        Type
                                                        @if (request('sort_by') === 'subject_type')
                                                            <i class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="{{ route('auth.activity_logs.my_activity_logs', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                        Timestamp
                                                        @if (request('sort_by') === 'created_at')
                                                            <i class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            @foreach ($activityLogs as $log)
                                                @php
                                                    $user = \App\Models\User::find($log->causer_id);
                                                @endphp
                                                <tr>
                                                    <td>{{ $log->log_name }}</td>
                                                    <td>{{ $log->description }}</td>
                                                    <td>{{ $log->event }}</td>
                                                    <td>{{ $user->role ?? 'N/A' }}</td>
                                                    <td>{{ class_basename($log->subject_type) }}</td>
                                                    <td>{{ $log->created_at->format('F j, Y, g:i A') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-danger text-center">No activity logs found</h3>
                                @endif
                            </div>
                            <div class="container">
                                <!-- Custom Pagination Links -->
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous Button -->
                                        <li class="page-item {{ $activityLogs->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link"
                                               href="{{ $activityLogs->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                                               tabindex="-1">Previous</a>
                                        </li>
                            
                                        <!-- Page Numbers -->
                                        @php
                                            $start = max($activityLogs->currentPage() - 2, 1); // Start from 2 pages before the current
                                            $end = min($start + 4, $activityLogs->lastPage()); // Limit range to 5 pages total
                                            $start = max($end - 4, 1); // Ensure start is at least 1
                                        @endphp
                            
                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $activityLogs->currentPage() == $i ? 'active' : '' }}">
                                                <a class="page-link"
                                                   href="{{ $activityLogs->url($i) . '&' . http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                            </li>
                                        @endfor
                            
                                        <!-- Next Button -->
                                        <li class="page-item {{ $activityLogs->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link"
                                               href="{{ $activityLogs->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Next</a>
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

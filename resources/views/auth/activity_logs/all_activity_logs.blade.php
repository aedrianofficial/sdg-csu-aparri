@extends('layouts.admin')
@section('title', 'All Users Activity Logs')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Activity Logs</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('auth.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">All Activity Logs</li>
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
                            <form action="{{ route('auth.activity_logs.all_activity_logs') }}" method="GET"
                                class="mb-4">
                                <div class="row">
                                    <!-- Search Bar -->
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">Search:</label>
                                        <input id="search" type="text" name="search" class="form-control"
                                            value="{{ request('search') }}"
                                            placeholder="Search by Username, Email, or Role">
                                    </div>

                                    <!-- User Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="user_id" class="form-label">User:</label>
                                        <select id="user_id" name="user_id" class="form-select">
                                            <option value="" selected>All Users</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->first_name }} {{ $user->last_name }} ({{ $user->role }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Role Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="role" class="form-label">Role:</label>
                                        <select id="role" name="role" class="form-select">
                                            <option value="" selected>All Roles</option>
                                            <option value="contributor"
                                                {{ request('role') == 'contributor' ? 'selected' : '' }}>Contributor
                                            </option>
                                            <option value="reviewer" {{ request('role') == 'reviewer' ? 'selected' : '' }}>
                                                Reviewer</option>
                                            <option value="approver" {{ request('role') == 'approver' ? 'selected' : '' }}>
                                                Approver</option>
                                            <option value="publisher"
                                                {{ request('role') == 'publisher' ? 'selected' : '' }}>Publisher</option>
                                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Event Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="event" class="form-label">Event:</label>
                                        <select id="event" name="event" class="form-select">
                                            <option value="" selected>All Events</option>
                                            <option value="submitted for review"
                                                {{ request('event') == 'submitted for review' ? 'selected' : '' }}>
                                                Submitted for Review
                                            </option>
                                            <option value="resubmitted for review"
                                                {{ request('event') == 'resubmitted for review' ? 'selected' : '' }}>
                                                Resubmitted for Review
                                            </option>
                                            <option value="reviewed"
                                                {{ request('event') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                            <option value="requested change"
                                                {{ request('event') == 'requested change' ? 'selected' : '' }}>
                                                Requested Change
                                            </option>
                                            <option value="rejected"
                                                {{ request('event') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="approved"
                                                {{ request('event') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="published"
                                                {{ request('event') == 'published' ? 'selected' : '' }}>Published
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="type" class="form-label">Type:</label>
                                        <select id="type" name="type" class="form-select">
                                            <option value="" selected>All Types</option>
                                            <option value="Project" {{ request('type') == 'Project' ? 'selected' : '' }}>
                                                Project</option>
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
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('auth.activity_logs.all_activity_logs') }}"
                                    class="btn btn-secondary">Reset</a>
                            </form>

                            <!-- Logs Table -->
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a
                                                    href="{{ route('auth.activity_logs.all_activity_logs', array_merge(request()->query(), ['sort_by' => 'causer_id', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                    User
                                                    @if (request('sort_by') === 'causer_id')
                                                        <i
                                                            class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a
                                                    href="{{ route('auth.activity_logs.all_activity_logs', array_merge(request()->query(), ['sort_by' => 'username', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                    Username
                                                    @if (request('sort_by') === 'username')
                                                        <i
                                                            class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a
                                                    href="{{ route('auth.activity_logs.all_activity_logs', array_merge(request()->query(), ['sort_by' => 'role', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                    Role
                                                    @if (request('sort_by') === 'role')
                                                        <i
                                                            class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a
                                                    href="{{ route('auth.activity_logs.all_activity_logs', array_merge(request()->query(), ['sort_by' => 'event', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                    Event
                                                    @if (request('sort_by') === 'event')
                                                        <i
                                                            class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a
                                                    href="{{ route('auth.activity_logs.all_activity_logs', array_merge(request()->query(), ['sort_by' => 'description', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                    Description
                                                    @if (request('sort_by') === 'description')
                                                        <i
                                                            class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a
                                                    href="{{ route('auth.activity_logs.all_activity_logs', array_merge(request()->query(), ['sort_by' => 'subject_type', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                    Type
                                                    @if (request('sort_by') === 'subject_type')
                                                        <i
                                                            class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a
                                                    href="{{ route('auth.activity_logs.all_activity_logs', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                                    Timestamp
                                                    @if (request('sort_by') === 'created_at')
                                                        <i
                                                            class="fa fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($activityLogs as $log)
                                            <tr>
                                                <td>{{ $log->causer?->first_name ?? 'Unknown' }}
                                                    {{ $log->causer?->last_name ?? '' }}</td>
                                                <td>{{ $log->causer?->username ?? 'Unknown' }}</td>
                                                <td>{{ $log->causer?->role ?? 'N/A' }}</td>
                                                <td>{{ $log->event }}</td>
                                                <td>{{ $log->description }}</td>
                                                <td>{{ class_basename($log->subject_type) }}</td>
                                                <td>{{ $log->created_at->format('F j, Y, g:i A') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-danger">No logs found</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
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
                                            <li
                                                class="page-item {{ $activityLogs->currentPage() == $i ? 'active' : '' }}">
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
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#user_id').select2({
                placeholder: "Select a user", // Placeholder text
                allowClear: true, // Option to clear the selection
                width: '100%' // Ensures it fits in the container
            });
        });
    </script>
@endsection

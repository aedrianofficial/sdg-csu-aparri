@extends('layouts.admin')
@section('title', 'User Management')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Users</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">User Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <!-- Search and Filter Form -->
                            <form action="{{ route('users.index') }}" method="GET" class="mb-4">
                                <h5 class="mb-0">Search by:</h5>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="first_name" class="form-label">First Name:</label>
                                        <input id="first_name" type="text" name="first_name" class="form-control"
                                            value="{{ request('first_name') }}" placeholder="Enter First Name">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="last_name" class="form-label">Last Name:</label>
                                        <input id="last_name" type="text" name="last_name" class="form-control"
                                            value="{{ request('last_name') }}" placeholder="Enter Last Name">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">Username or Email:</label>
                                        <input id="search" type="text" name="search" class="form-control"
                                            value="{{ request('search') }}" placeholder="Enter Username or Email">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="role" class="form-label">Role:</label>
                                        <select id="role" name="role" class="form-select">
                                            <option value="">All</option>
                                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin
                                            </option>
                                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User
                                            </option>
                                            <option value="contributor"
                                                {{ request('role') == 'contributor' ? 'selected' : '' }}>Contributor
                                            </option>
                                            <option value="reviewer" {{ request('role') == 'reviewer' ? 'selected' : '' }}>
                                                Reviewer</option>
                                            <option value="approver" {{ request('role') == 'approver' ? 'selected' : '' }}>
                                                Approver</option>
                                            <option value="publisher"
                                                {{ request('role') == 'publisher' ? 'selected' : '' }}>Publisher</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-start mb-3">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>

                            <!-- Responsive Users Table -->
                            <div class="table-responsive">
                                @if ($users->count() > 0)
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{{ $user->first_name ?? 'N/A' }}</td>
                                                    <td>{{ $user->last_name ?? 'N/A' }}</td>
                                                    <td>{{ $user->username ?? 'N/A' }}</td>
                                                    <td>{{ $user->email ?? 'N/A' }}</td>
                                                    <td>{{ ucfirst($user->role) ?? 'N/A' }}</td>
                                                    <td>
                                                        <a href="{{ route('users.show', $user->id) }}"
                                                            class="btn btn-sm btn-success">View</a>
                                                        <a href="{{ route('users.edit', $user->id) }}"
                                                            class="btn btn-sm btn-info">Edit</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-danger text-center">No users found</h3>
                                @endif
                            </div>

                            <div class="container">
                                <!-- Custom Pagination Links -->
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous Button -->
                                        <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $users->previousPageUrl() }}"
                                                tabindex="-1">Previous</a>
                                        </li>

                                        <!-- Page Number Links -->
                                        @php
                                            $currentPage = $users->currentPage(); // Current page number
                                            $lastPage = $users->lastPage(); // Last page number
                                            $start = max($currentPage - 1, 1); // Calculate start of the visible page items
                                            $end = min($start + 2, $lastPage); // Calculate end of the visible page items
                                        @endphp

                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        <!-- Next Button -->
                                        <li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $users->nextPageUrl() }}">Next</a>
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

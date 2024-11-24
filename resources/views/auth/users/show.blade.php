@extends('layouts.admin')
@section('title', 'View User')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">View User</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View User</li>
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
                            <form class="forms-sample">

                                <!-- First Name -->
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name:</label>
                                    <input type="text" id="first_name" class="form-control"
                                        value="{{ $user->first_name ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Last Name -->
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name:</label>
                                    <input type="text" id="last_name" class="form-control"
                                        value="{{ $user->last_name ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username:</label>
                                    <input type="text" id="username" class="form-control"
                                        value="{{ $user->username ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" id="email" class="form-control"
                                        value="{{ $user->email ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Phone Number -->
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number:</label>
                                    <input type="text" id="phone_number" class="form-control"
                                        value="{{ $user->phone_number ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Bio -->
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio:</label>
                                    <textarea id="bio" class="form-control" rows="3" readonly>{{ $user->bio ?? 'N/A' }}</textarea>
                                </div>

                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address:</label>
                                    <input type="text" id="address" class="form-control"
                                        value="{{ $user->address ?? 'N/A' }}" readonly>
                                </div>

                                <!-- Date of Birth -->
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth:</label>
                                    <input type="text" id="date_of_birth" class="form-control"
                                        value="{{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'N/A' }}"
                                        readonly>
                                </div>

                                <!-- Role -->
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role:</label>
                                    <input type="text" id="role" class="form-control"
                                        value="{{ ucfirst($user->role ?? 'N/A') }}" readonly>
                                </div>

                                <!-- Created at -->
                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created at:</label>
                                    <input type="text" id="created_at" class="form-control"
                                        value="{{ $user->created_at ? $user->created_at->format('M d, Y H:i') : 'N/A' }}"
                                        readonly>
                                </div>

                                <!-- Updated at -->
                                <div class="mb-3">
                                    <label for="updated_at" class="form-label">Updated at:</label>
                                    <input type="text" id="updated_at" class="form-control"
                                        value="{{ $user->updated_at ? $user->updated_at->format('M d, Y H:i') : 'N/A' }}"
                                        readonly>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

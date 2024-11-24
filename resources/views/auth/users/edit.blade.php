@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit User</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit User</li>
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
                            <h4 class="card-title mb-4">Edit User</h4>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="post" action="{{ route('users.update', $user->id) }}"
                                enctype="multipart/form-data" id="user-form">
                                @csrf
                                @method('PUT')
                                <!-- Image Upload -->
                                <div class="mb-3">
                                    <label for="image" class="form-label">Profile Image (Optional)</label>

                                    @if ($user->userImage)
                                        <div class="mb-3">
                                            <label>Current Image:</label><br>
                                            <img src="{{ $user->userImage->image_path }}" alt="Current Image"
                                                style="max-width: 150px; height: auto;">
                                        </div>
                                    @endif

                                    <input type="file" name="image" class="form-control" id="image">
                                </div>
                                <!-- Role -->
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role (Click to select)</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                            User</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                            Admin</option>
                                        <option value="contributor"
                                            {{ old('role', $user->role) == 'contributor' ? 'selected' : '' }}>Contributor
                                        </option>
                                        <option value="reviewer"
                                            {{ old('role', $user->role) == 'reviewer' ? 'selected' : '' }}>Reviewer</option>
                                        <option value="approver"
                                            {{ old('role', $user->role) == 'approver' ? 'selected' : '' }}>Approver</option>
                                        <option value="publisher"
                                            {{ old('role', $user->role) == 'publisher' ? 'selected' : '' }}>Publisher
                                        </option>
                                    </select>
                                </div>

                                <!-- First Name -->
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="{{ old('first_name', $user->first_name) }}" required>
                                </div>

                                <!-- Last Name -->
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="{{ old('last_name', $user->last_name) }}" required>
                                </div>

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="{{ old('username', $user->username) }}" required>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" required>
                                </div>

                                <!-- Phone Number -->
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        value="{{ old('phone_number', $user->phone_number) }}">
                                </div>

                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="{{ old('address', $user->address) }}">
                                </div>

                                <!-- Date of Birth -->
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                                </div>



                                <!-- Bio -->
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea name="bio" class="form-control" id="bio" cols="30" rows="5">{{ old('bio', $user->bio) }}</textarea>
                                </div>



                                <!-- Update Button -->
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#confirmationModal">Update</button>

                                <!-- Confirmation Modal -->
                                <div class="modal fade" id="confirmationModal" tabindex="-1"
                                    aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Edit</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to update this user?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

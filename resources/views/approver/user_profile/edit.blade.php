@extends('layouts.approver')
@section('title', 'View Profile')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/auth/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/website/plugins/bootstrap/bootstrap.min.css') }}">
    <style>
        /* Custom Styles */
        .profile-card {
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            padding: 30px;
            margin-top: 20px;
        }

        .profile-picture img {
            border: 5px solid #fff;
            border-radius: 50%;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .profile-picture img:hover {
            transform: scale(1.05);
        }

        .form-label {
            font-weight: bold;
        }

        .btn-success {
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-success:hover {
            background-color: #28a745;
            border-color: #218838;
            box-shadow: 0px 4px 10px rgba(0, 123, 255, 0.2);
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .profile-card {
                padding: 20px;
            }

            .form-control {
                font-size: 0.9rem;
            }

            .btn-success {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">User Profile</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Profile</li>
                    </ol>
                </nav>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card profile-card">
                        <div class="card-body">
                            <!-- Update Profile Form -->
                            <form action="{{ route('approver.profile.update', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Profile Picture -->
                                <div class="mb-3 text-center profile-picture">
                                    <img src="{{ $user->userImage ? $user->userImage->image_path : asset('assets/website/images/img_avatar.webp') }}"
                                        alt="Profile Picture" width="150" height="150">
                                    <div class="mt-3">
                                        <label for="avatar" class="form-label">Change Profile Picture:</label>
                                        <input type="file" class="form-control" id="avatar" name="avatar">
                                    </div>
                                </div>

                                <!-- First Name -->
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name:</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="{{ old('first_name', $user->first_name) }}">
                                </div>

                                <!-- Last Name -->
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name:</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="{{ old('last_name', $user->last_name) }}">
                                </div>


                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username:</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="{{ old('username', $user->username) }}">
                                </div>

                                <!-- Phone Number -->
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number:</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        value="{{ old('phone_number', $user->phone_number) }}">
                                </div>

                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address:</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="{{ old('address', $user->address) }}">
                                </div>

                                <!-- Date of Birth -->
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth:</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                </div>

                                <!-- Bio -->
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Profile Bio:</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
                                </div>

                                <!-- Save Button -->
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('scripts')
        <script src="{{ asset('assets/website/plugins/bootstrap/bootstrap.min.js') }}"></script>
    @endsection

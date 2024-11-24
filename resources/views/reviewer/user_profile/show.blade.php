@extends('layouts.reviewer')
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
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            color: #333333;
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 1.6rem;
        }

        .profile-picture {
            position: relative;
            margin-bottom: 30px;
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

        .table th,
        .table td {
            font-weight: 600;
            padding: 15px 10px;
            font-size: 1rem;
            color: #5a5a5a;
        }

        .table th {
            background-color: #f9f9f9;
        }

        .btn-primary {
            padding: 12px 30px;
            font-size: 1rem;
            background-color: #007bff;
            border-color: #007bff;
            transition: all 0.3s ease-in-out;
            border-radius: 30px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            box-shadow: 0px 4px 10px rgba(0, 91, 187, 0.2);
        }

        .icon {
            margin-right: 5px;
            color: #6c757d;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .profile-card {
                padding: 20px;
            }

            .table th,
            .table td {
                font-size: 0.9rem;
                padding: 10px 8px;
            }

            .btn-primary {
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
                            <!-- Profile Header -->
                            <h4 class="profile-header">Profile Information</h4>

                            <!-- Profile Picture -->
                            <div class="profile-picture text-center">
                                @if ($user->userImage)
                                    <img src="{{ $user->userImage->image_path }}" alt="Profile Picture" width="150"
                                        height="150">
                                @else
                                    <img src="{{ asset('assets/website/images/img_avatar.webp') }}" alt="Default Avatar"
                                        width="150" height="150">
                                @endif
                            </div>

                            <!-- Profile Details -->
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th><i class="icon fas fa-user"></i>First Name:</th>
                                        <td>{{ $user->first_name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="icon fas fa-user"></i>Last Name:</th>
                                        <td>{{ $user->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="icon fas fa-user-circle"></i>Username:</th>
                                        <td>{{ $user->username ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="icon fas fa-phone"></i>Phone Number:</th>
                                        <td>{{ $user->phone_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="icon fas fa-map-marker-alt"></i>Address:</th>
                                        <td>{{ $user->address ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="icon fas fa-calendar-alt"></i>Date of Birth:</th>
                                        <td>{{ $user->date_of_birth ? $user->date_of_birth->format('F d, Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="icon fas fa-info-circle"></i>Profile Bio:</th>
                                        <td>{{ $user->bio ?? 'No bio available' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Edit Button -->
                            <div class="text-center">
                                <a href="{{ route('reviewer.profile.edit', ['id' => $user->id]) }}"
                                    class="btn btn-primary btn-lg mt-3">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="{{ asset('assets/website/plugins/bootstrap/bootstrap.min.js') }}"></script>
    @endsection

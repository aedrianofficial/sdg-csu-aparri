@extends('layouts.website2')

@section('title', 'Edit Profile')

@section('styles')
    <style>
        body {

            color: #1a202c;
            text-align: left;
            background-color: #e2e8f0;
        }

        .main-body {
            padding: 15px;
        }

        .card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
        }

        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1rem;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: .25rem;
        }

        .form-control:focus {
            border-color: #5a9;
            box-shadow: 0 0 0 0.2rem rgba(72, 180, 97, 0.25);
        }
    </style>
@endsection

@section('content')

    <div class="content">
        <div class="container">
            <!-- Reports Section -->
            <div class="content-header">
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Edit Profile</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('website.home2') }}"><i
                                            class="fas fa-home"></i> Home</a></li>
                                <li class="breadcrumb-item">Edit Profile

                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-body">
                <div class="row gutters-sm">
                    <div class="col-md-4 mb-3">
                        <div class="card card-primary card-outline">
                            <div class="card-body text-center">
                                @if (!($user->userImage && $user->userImage->existsOnDisk()))
                                    <img src="{{ asset('assets/website/images/user-png.png') }}" alt="Default User Image"
                                        class="rounded-circle shadow" width="150">
                                @else
                                    <img src="{{ $user->userImage->image_path }}" alt="User Image"
                                        class="rounded-circle shadow" width="150">
                                @endif


                                <h4 class="mt-3">{{ $user->first_name }} {{ $user->last_name }}</h4>
                                <p class="text-secondary">{{ ucfirst($user->role) }} / <small>Member since
                                        {{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('F Y') }}</small> </p>

                            </div>

                        </div>
                    </div>
                    <div class="col-md-8">
                        <form action="{{ route('user.profile.update', $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card">
                                <div class="card-body">
                                    <div class="row gutters-sm">
                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="first_name" class="icon-text">
                                                    <i class="fas fa-user"></i> First Name
                                                </label>
                                                <input type="text" class="form-control" id="first_name" name="first_name"
                                                    value="{{ old('first_name', $user->first_name) }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="username" class="icon-text">
                                                    <i class="fas fa-user-circle"></i> Username
                                                </label>
                                                <input type="text" class="form-control" id="username" name="username"
                                                    value="{{ old('username', $user->username) }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone_number" class="icon-text">
                                                    <i class="fas fa-phone"></i> Phone Number
                                                </label>
                                                <input type="text" class="form-control" id="phone_number"
                                                    name="phone_number"
                                                    value="{{ old('phone_number', $user->phone_number) }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="address" class="icon-text">
                                                    <i class="fas fa-map-marker-alt"></i> Address
                                                </label>
                                                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                            </div>
                                        </div>
                                        <!-- Right Column -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="last_name" class="icon-text">
                                                    <i class="fas fa-user"></i> Last Name
                                                </label>
                                                <input type="text" class="form-control" id="last_name" name="last_name"
                                                    value="{{ old('last_name', $user->last_name) }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email" class="icon-text">
                                                    <i class="fas fa-envelope"></i> Email
                                                </label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ old('email', $user->email) }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="date_of_birth" class="icon-text">
                                                    <i class="fas fa-calendar-alt"></i> Date of Birth
                                                </label>
                                                <input type="date" class="form-control" id="date_of_birth"
                                                    name="date_of_birth"
                                                    value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="bio" class="icon-text">
                                                    <i class="fas fa-info-circle"></i> Bio
                                                </label>
                                                <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="avatar" class="icon-text">
                                            <i class="fas fa-image"></i> Profile Image (Maximum of 2MB)
                                        </label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="avatar"
                                                name="avatar">
                                            <label class="custom-file-label" for="exampleInputFile">Choose an
                                                image</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
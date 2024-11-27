@extends('layouts.admin')

@section('title', 'View Profile')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin-top: 20px;
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

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 0 solid rgba(0, 0, 0, .125);
            border-radius: .25rem;
        }

        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1rem;
        }

        .gutters-sm {
            margin-right: -8px;
            margin-left: -8px;
        }

        .gutters-sm>.col,
        .gutters-sm>[class*=col-] {
            padding-right: 8px;
            padding-left: 8px;
        }

        .mb-3,
        .my-3 {
            margin-bottom: 1rem !important;
        }

        .bg-gray-300 {
            background-color: #e2e8f0;
        }

        .h-100 {
            height: 100% !important;
        }

        .shadow-none {
            box-shadow: none !important;
        }

        .icon-text {
            display: flex;
            align-items: center;
        }

        .icon-text i {
            margin-right: 10px;
            color: #4a5568;
        }
    </style>
@endsection

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Profile</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Profile
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header-->

    <div class="main-body">
        <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            @if (!($user->userImage && $user->userImage->existsOnDisk()))
                                <img src="{{ asset('assets/website/images/user-png.png') }}" alt="Default User Image"
                                    class="rounded-circle shadow" width="150">
                            @else
                                <img src="{{ $user->userImage->image_path }}" alt="User Image" class="rounded-circle shadow"
                                    width="150">
                            @endif


                            <div class="mt-3">
                                <h4>{{ $user->first_name }} {{ $user->last_name }}</h4>
                                <p class="text-secondary ">{{ ucfirst($user->role) }} / <small>Member since
                                        {{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('F Y') }}</small> </p>


                                <a href="{{ route('publisher.profile.edit', $user->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Profile
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card card-primary card-outline mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3 icon-text">
                                <i class="fas fa-user"></i> Full Name
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3 icon-text">
                                <i class="fas fa-user"></i> Username
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $user->username }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3 icon-text">
                                <i class="fas fa-envelope"></i> Email
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $user->email }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3 icon-text">
                                <i class="fas fa-phone"></i> Phone
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $user->phone_number ?? 'Not Provided' }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3 icon-text">
                                <i class="fas fa-calendar-alt"></i> Date of Birth
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $user->date_of_birth ? date('F d, Y', strtotime($user->date_of_birth)) : 'Not Provided' }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3 icon-text">
                                <i class="fas fa-map-marker-alt"></i> Address
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $user->address ?? 'Not Provided' }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3 icon-text">
                                <i class="fas fa-info-circle"></i> Bio
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $user->bio ?? 'Not Provided' }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

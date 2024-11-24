@extends('layouts.website')
@section('title', 'Contact Us')
@section('content')
    <section class="page-title bg-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <h1>Drop Us A Message</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact form start -->
    <section class="contact-form">
        <div class="container">
            <form class="row" id="contact-form" method="POST" action="{{ route('feedback.store') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="col-md-6 col-sm-12">
                    <div class="block">
                        <div class="form-group">
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Your Name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input name="email" type="text" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email Address" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input name="subject" type="text" class="form-control @error('subject') is-invalid @enderror"
                                placeholder="Subject" value="{{ old('subject') }}">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="block">
                        <div class="form-group-2">
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4"
                                placeholder="Your Message">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-default" type="submit">Send Message</button>
                    </div>
                </div>
                @if (session('alert-success'))
                    <div class="success alert alert-success" role="alert">{{ session('alert-success') }}</div>
                @endif
                @if (session('alert-error'))
                    <div class="error alert alert-danger" role="alert">{{ session('alert-error') }}</div>
                @endif
            </form>
            <div class="contact-box row">
                <div class="col-md-6 col-sm-12">
                    <div class="block">
                        <h2>Stop By For A Visit</h2>
                        <ul class="address-block">
                            <li>
                                <i class="ion-ios-location-outline"></i>Maura, Aparri, Cagayan
                            </li>
                            <li>
                                <i class="ion-ios-email-outline"></i>Email: contact@mail.com
                            </li>
                            <li>
                                <i class="ion-ios-telephone-outline"></i>Phone: 09*********
                            </li>
                        </ul>
                        <ul class="social-icons">
                            <li>
                                <a href="http://www.themefisher.com"><i class="ion-social-googleplus-outline"></i></a>
                            </li>
                            <li>
                                <a href="http://www.themefisher.com"><i class="ion-social-instagram-outline"></i></a>
                            </li>
                            <li>
                                <a href="http://www.themefisher.com"><i class="ion-social-facebook-outline"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 mt-5 mt-md-0">
                    <div class="block">
                        <img src="{{ asset('assets/website/images/spot-map.jpg') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

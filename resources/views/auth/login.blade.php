@extends('layouts.guest')
@section('title', 'User Login')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-header text-center">
                            <a href="{{ route('website.home') }}">
                                <img src="{{ asset('assets/website/images/csulogo.png') }}" alt="Logo"
                                    class="img-fluid mb-3" style="max-width: 200px;">
                            </a>
                            <h3>Login</h3>
                            <p class="text-muted">Welcome back! Please enter your credentials to access your account.</p>
                        </div>
                        <div class="card-body">
                            <form id="login-form" method="POST" action="{{ route('login') }}">
                                @csrf

                                <!-- Display error message if available -->
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <!-- Email or Username -->
                                <div class="mb-3 form-group position-relative">
                                    <label for="login" class="form-label">{{ __('*Email or Username') }}</label>
                                    <input type="text" name="username_or_email" id="login" class="form-control"
                                        required autofocus autocomplete="username" value="{{ old('username_or_email') }}">
                                    <div id="login_error" class="invalid-feedback">Please enter a valid email or username.
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-3 form-group">
                                    <label for="password" class="form-label">{{ __('*Password') }}</label>
                                    <input type="password" name="password" id="password" class="form-control" required
                                        autocomplete="current-password">
                                    <div id="password_error" class="invalid-feedback">Password must be at least 8
                                        characters.</div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="show-password">
                                        <label class="form-check-label" for="show-password">Show Password</label>
                                    </div>
                                </div>

                                <!-- Remember Me -->
                                {{-- <div class="mb-3 form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                        <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                                    </div>
                                </div> --}}

                                <!-- Forgot Password and Login Button -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    {{-- <span><a href="{{ route('password.request') }}"
                                            class="text-primary">{{ __('Forgot Password?') }}</a></span> --}}
                                    <button type="submit" class="btn btn-primary" id="login-btn">
                                        {{ __('Login') }}
                                    </button>
                                </div>

                                <!-- Register Link -->
                                <div class="text-center">
                                    <span>Don't have an account? <a href="{{ route('register') }}"
                                            class="text-primary">{{ __('Create One') }}</a></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loginForm = document.getElementById('login-form');
            const loginInput = document.getElementById('login');
            const passwordInput = document.getElementById('password');
            const loginBtn = document.getElementById('login-btn');

            // Validation function
            function validateInput(input) {
                const value = input.value.trim();
                if (input.id === 'login') {
                    const errorElement = document.getElementById('login_error');
                    if (value === '' || value.length < 3) {
                        errorElement.style.display = 'block';
                        input.classList.add('is-invalid');
                        return false;
                    } else {
                        errorElement.style.display = 'none';
                        input.classList.remove('is-invalid');
                        return true;
                    }
                }

                if (input.id === 'password') {
                    const errorElement = document.getElementById('password_error');
                    if (value.length < 8) {
                        errorElement.style.display = 'block';
                        input.classList.add('is-invalid');
                        return false;
                    } else {
                        errorElement.style.display = 'none';
                        input.classList.remove('is-invalid');
                        return true;
                    }
                }

                return true; // Valid input
            }

            // Add input event listeners for validation
            loginInput.addEventListener('input', function() {
                validateInput(loginInput);
            });

            passwordInput.addEventListener('input', function() {
                validateInput(passwordInput);
            });

            // Form submit handler
            loginForm.addEventListener('submit', function(event) {
                const isLoginValid = validateInput(loginInput);
                const isPasswordValid = validateInput(passwordInput);

                if (!isLoginValid || !isPasswordValid) {
                    event.preventDefault(); // Prevent form submission if validation fails
                } else {
                    // Show loading message
                    loginBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Authenticating...';
                    loginBtn.disabled = true;

                    // Add loading notification
                    if (!document.getElementById('login-notification')) {
                        const notification = document.createElement('div');
                        notification.id = 'login-notification';
                        notification.className = 'alert alert-info mt-3 d-flex align-items-center';
                        notification.innerHTML = `
                    <div class="spinner-grow spinner-grow-sm text-info me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>
                        <strong>Please wait!</strong> Verifying your credentials...
                    </div>
                `;
                        loginForm.appendChild(notification);
                    }
                }
            });

            // Show Password Functionality
            document.getElementById("show-password").addEventListener("change", function() {
                const passwordField = document.getElementById("password");
                passwordField.type = this.checked ? "text" : "password"; // Toggle password visibility
            });
        });
    </script>
   
@endsection

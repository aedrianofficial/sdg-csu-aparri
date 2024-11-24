@extends('layouts.guest')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-header text-center">{{ __('Register') }}</div>
                        <div class="card-body">
                            <form id="register-form" method="POST" action="{{ route('register') }}">
                                @csrf

                                <!-- First Name -->
                                <div class="mb-3 form-group">
                                    <label for="first_name" class="form-label">{{ __('*First Name') }}</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control" required
                                        autofocus autocomplete="given-name">
                                    <div id="first_name_error" class="invalid-feedback">Please enter a valid first name.
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="mb-3 form-group">
                                    <label for="last_name" class="form-label">{{ __('*Last Name') }}</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control" required
                                        autocomplete="family-name">
                                    <div id="last_name_error" class="invalid-feedback">Please enter a valid last name.</div>
                                </div>

                                <!-- Username -->
                                <div class="mb-3 form-group">
                                    <label for="username" class="form-label">{{ __('*Username') }}</label>
                                    <input type="text" name="username" id="username" class="form-control" required
                                        autocomplete="username">
                                    <div id="username_empty_error" class="invalid-feedback">Username must not be empty.
                                    </div>
                                    <div id="username_taken_error" class="invalid-feedback">Username is already taken.</div>
                                </div>

                                <!-- Email -->
                                <div class="mb-3 form-group">
                                    <label for="email" class="form-label">{{ __('*Email Address') }}</label>
                                    <input type="email" name="email" id="email" class="form-control" required
                                        autocomplete="email">
                                    <div id="email_error" class="invalid-feedback">Please enter a valid email.</div>
                                </div>

                                <!-- Password -->
                                <div class="mb-3 form-group">
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                    <input type="password" name="password" id="password" class="form-control" required
                                        autocomplete="new-password">
                                    <div id="password_error" class="invalid-feedback">Password must be at least 8
                                        characters.</div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3 form-group">
                                    <label for="password_confirmation"
                                        class="form-label">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" required autocomplete="new-password">
                                    <div id="password_confirmation_error" class="invalid-feedback">Passwords must match.
                                    </div>
                                </div>

                                <!-- Already Registered -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Already have an account? <a href="{{ route('login') }}"
                                            class="text-primary">{{ __('Login') }}</a></span>

                                    <!-- Register Button (Disabled by default) -->
                                    <button type="submit" class="btn btn-primary" id="register-btn" disabled>
                                        {{ __('Register') }}
                                    </button>
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
            const registerButton = document.getElementById("register-btn");

            // Function to validate input
            function validateInput(inputElement, validationFn, errorMessageId) {
                if (validationFn(inputElement.value)) {
                    inputElement.classList.remove("is-invalid");
                    inputElement.classList.add("is-valid");
                    document.getElementById(errorMessageId).style.display = 'none';
                } else {
                    inputElement.classList.remove("is-valid");
                    inputElement.classList.add("is-invalid");
                    document.getElementById(errorMessageId).style.display = 'block';
                }
                checkAllValidations(); // Check if all fields are valid
            }

            // Check if all inputs are valid to enable the button
            function checkAllValidations() {
                const allInputs = document.querySelectorAll(".form-control");
                const allValid = Array.from(allInputs).every(input => input.classList.contains("is-valid"));
                registerButton.disabled = !allValid; // Enable/Disable button
            }

            // First Name Validation
            document.getElementById("first_name").addEventListener("blur", function() {
                validateInput(this, function(value) {
                    return value.length > 0;
                }, 'first_name_error');
            });

            // Last Name Validation
            document.getElementById("last_name").addEventListener("blur", function() {
                validateInput(this, function(value) {
                    return value.length > 0;
                }, 'last_name_error');
            });

            // Username Validation - Check if it's empty first
            document.getElementById("username").addEventListener("blur", function() {
                const username = this.value;

                // Check if username is empty
                if (username.length === 0) {
                    this.classList.add("is-invalid");
                    this.classList.remove("is-valid");
                    document.getElementById("username_empty_error").style.display = 'block';
                    document.getElementById("username_taken_error").style.display = 'none';
                    checkAllValidations();
                    return;
                } else {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                    document.getElementById("username_empty_error").style.display = 'none';
                }

                // Check if username is already taken
                fetch('{{ route('check.username') }}?username=' + username)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            this.classList.add("is-invalid");
                            this.classList.remove("is-valid");
                            document.getElementById("username_taken_error").style.display = 'block';
                        } else {
                            this.classList.remove("is-invalid");
                            this.classList.add("is-valid");
                            document.getElementById("username_taken_error").style.display = 'none';
                        }
                        checkAllValidations();
                    });
            });

            // Email Validation
            document.getElementById("email").addEventListener("blur", function() {
                validateInput(this, function(value) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailPattern.test(value);
                }, 'email_error');
            });

            // Password Validation
            document.getElementById("password").addEventListener("blur", function() {
                validateInput(this, function(value) {
                    return value.length >= 8;
                }, 'password_error');
            });

            // Confirm Password Validation
            document.getElementById("password_confirmation").addEventListener("blur", function() {
                const password = document.getElementById("password").value;
                const confirmPassword = this.value;

                // If confirm password is empty or doesn't match the password, mark both as invalid
                if (confirmPassword === '' || confirmPassword !== password) {
                    document.getElementById("password").classList.add("is-invalid");
                    this.classList.add("is-invalid");
                    document.getElementById("password_confirmation_error").style.display = 'block';
                } else {
                    // If confirm password is valid, mark both as valid
                    document.getElementById("password").classList.remove("is-invalid");
                    document.getElementById("password").classList.add("is-valid");
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                    document.getElementById("password_confirmation_error").style.display = 'none';
                }
                checkAllValidations(); // Check if all fields are valid
            });
        });
    </script>
@endsection

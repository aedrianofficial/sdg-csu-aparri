@extends('layouts.guest')
@section('title', 'User Registration')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-sm-8 mx-auto">
                    <div class="card">
                        <div class="card-header text-center">
                            <a href="{{ route('website.home') }}">
                                <img src="{{ asset('assets/website/images/csulogo.png') }}" alt="Logo"
                                    class="img-fluid mb-3" style="max-width: 200px;">
                            </a>
                            <h3>Register</h3>
                            <p class="text-muted">Join us today! Fill in the details below to create your account.</p>
                        </div>
                        <div class="card-body">
                            <form id="register-form" method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="row">
                                    <!-- First Column -->
                                    <div class="col-md-6">
                                        <!-- First Name -->
                                        <div class="mb-3 form-group">
                                            <label for="first_name" class="form-label">{{ __('*First Name') }}</label>
                                            <input type="text" name="first_name" id="first_name" class="form-control"
                                                required autofocus autocomplete="given-name">
                                            <div id="first_name_error" class="invalid-feedback">Please enter a valid first
                                                name.</div>
                                        </div>

                                        <!-- Last Name -->
                                        <div class="mb-3 form-group">
                                            <label for="last_name" class="form-label">{{ __('*Last Name') }}</label>
                                            <input type="text" name="last_name" id="last_name" class="form-control"
                                                required autocomplete="family-name">
                                            <div id="last_name_error" class="invalid-feedback">Please enter a valid last
                                                name.</div>
                                        </div>

                                        <!-- Username -->
                                        <div class="mb-3 form-group">
                                            <label for="username"
                                                class="form-label">{{ __('*Username(The username must be unique)') }}</label>
                                            <input type="text" name="username" id="username" class="form-control"
                                                required autocomplete="username">
                                            <div id="username_empty_error" class="invalid-feedback">Username must not be
                                                empty.</div>
                                            <div id="username_taken_error" class="invalid-feedback">Username is already
                                                taken.</div>
                                        </div>

                                        <!-- Email -->
                                        <div class="mb-3 form-group">
                                            <label for="email"
                                                class="form-label">{{ __('*Email(The email must be unique)') }}</label>
                                            <input type="email" name="email" id="email" class="form-control"
                                                required autocomplete="email">
                                            <div id="email_empty_error" class="invalid-feedback">Email must not be empty.
                                            </div>
                                            <div id="email_taken_error" class="invalid-feedback">Email is already taken.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second Column -->
                                    <div class="col-md-6">
                                        <!-- Campus (Aparri) -->
                                        <input type="hidden" name="campus_id" value="2">

                                        <!-- College -->
                                        <div class="form-group mb-3">
                                            <label for="college" class="form-label">{{ __('*College') }}</label>
                                            <select name="college_id" id="college" class="form-control" required>
                                                @foreach ($colleges as $college)
                                                    <option value="{{ $college->id }}">{{ $college->name }}</option>
                                                @endforeach
                                            </select>
                                            <div id="college_error" class="invalid-feedback" style="display: none;">Please
                                                select a college.</div>
                                        </div>

                                        <!-- Password -->
                                        <div class="mb-3 form-group">
                                            <label for="password" class="form-label">{{ __('*Password') }}</label>
                                            <input type="password" name="password" id="password"
                                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                                                class="form-control" required autocomplete="new-password">
                                            <div id="password_error" class="invalid-feedback">Must contain at least one
                                                number and one uppercase and lowercase letter, and at least 8 or more
                                                characters</div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="show-password">
                                                <label class="form-check-label" for="show-password">Show Password</label>
                                            </div>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="mb-3 form-group">
                                            <label for="password_confirmation"
                                                class="form-label">{{ __('*Confirm Password') }}</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control" required autocomplete="new-password">
                                            <div id="password_confirmation_error" class="invalid-feedback">Passwords must
                                                match.</div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="show-confirm-password">
                                                <label class="form-check-label" for="show-confirm-password">Show Confirm
                                                    Password</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Already Registered and Submit -->
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span>Already have an account? <a href="{{ route('login') }}"
                                            class="text-primary">{{ __('Login') }}</a></span>
                                    <button type="submit" class="btn btn-primary" id="register-btn"
                                        disabled>{{ __('Register') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
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
            // Email Validation - Check if it's empty first
            document.getElementById("email").addEventListener("blur", function() {
                const email = this.value;

                // Check if email is empty
                if (email.length === 0) {
                    this.classList.add("is-invalid");
                    this.classList.remove("is-valid");
                    document.getElementById("email_empty_error").style.display = 'block';
                    document.getElementById("email_taken_error").style.display = 'none';
                    checkAllValidations();
                    return;
                } else {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                    document.getElementById("email_empty_error").style.display = 'none';
                }

                // Check if email is already taken
                fetch('{{ route('check.email') }}?email=' + encodeURIComponent(email))
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            this.classList.add("is-invalid");
                            this.classList.remove("is-valid");
                            document.getElementById("email_taken_error").style.display = 'block';
                        } else {
                            this.classList.remove("is-invalid");
                            this.classList.add("is-valid");
                            document.getElementById("email_taken_error").style.display = 'none';
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
                const password = this.value;
                const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/; // Regex pattern

                if (passwordPattern.test(password)) {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                    document.getElementById("password_error").style.display = 'none';
                } else {
                    this.classList.add("is-invalid");
                    this.classList.remove("is-valid");
                    document.getElementById("password_error").style.display = 'block';
                }
                checkAllValidations(); // Check if all fields are valid
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

            // College Validation
            document.getElementById("college").addEventListener("change", function() {
                validateInput(this, value => value !== "", 'college_error');
            });
            // Show Password Functionality
            document.getElementById("show-password").addEventListener("change", function() {
                const passwordField = document.getElementById("password");
                passwordField.type = this.checked ? "text" : "password"; // Toggle password visibility
            });

            document.getElementById("show-confirm-password").addEventListener("change", function() {
                const confirmPasswordField = document.getElementById("password_confirmation");
                confirmPasswordField.type = this.checked ? "text" :
                    "password"; // Toggle confirm password visibility
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const collegeSelect = document.getElementById("college");

            // Create the placeholder option
            const placeholderOption = document.createElement("option");
            placeholderOption.textContent = "Click to select college";
            placeholderOption.value = "";
            placeholderOption.disabled = true;
            placeholderOption.selected = true;

            // Prepend the placeholder option to the select element
            collegeSelect.prepend(placeholderOption);
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const registerForm = document.getElementById("register-form");
            const registerBtn = document.getElementById("register-btn");

            registerForm.addEventListener('submit', function(event) {
                // Check if form is valid before showing loading message
                const allInputs = document.querySelectorAll(".form-control");
                const allValid = Array.from(allInputs).every(input => input.classList.contains("is-valid"));

                if (allValid) {
                    // Show loading spinner in button
                    registerBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registering...';
                    registerBtn.disabled = true;

                    // Add loading notification
                    if (!document.getElementById('register-notification')) {
                        const notification = document.createElement('div');
                        notification.id = 'register-notification';
                        notification.className = 'alert alert-info mt-3 d-flex align-items-center';
                        notification.innerHTML = `
                    <div class="spinner-grow spinner-grow-sm text-info me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>
                        <strong>Please wait!</strong> Creating your account...
                    </div>
                `;
                        registerForm.appendChild(notification);
                    }
                } else {
                    event.preventDefault(); // Prevent form submission if validation fails
                }
            });
        });
    </script>
@endsection

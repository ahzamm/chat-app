<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
            padding-top: 4rem;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .form-tabs {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #ddd;
            z-index: 10;
        }

        .tab-content {
            padding: 2rem;
            padding-top: 3rem;
        }

        .form-container h2 {
            margin-bottom: 1.5rem;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <!-- Fixed Tab navigation -->
        <ul class="nav nav-tabs form-tabs" id="authTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button"
                        role="tab" aria-controls="login" aria-selected="true">Login</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup" type="button"
                        role="tab" aria-controls="signup" aria-selected="false">Signup</button>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="authTabContent">
            <!-- Login Form -->
            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                <h2>Login</h2>
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="emailLogin" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="emailLogin" placeholder="Enter email">
                    </div>
                    <div class="mb-3">
                        <label for="passwordLogin" class="form-label">Password</label>
                        <input type="password" class="form-control" id="passwordLogin" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>

            <!-- Signup Form -->
            <div class="tab-pane fade" id="signup" role="tabpanel" aria-labelledby="signup-tab">
                <h2>Signup</h2>
                <form id="signupForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailSignup" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="emailSignup" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label for="passwordSignup" class="form-label">Password</label>
                        <input type="password" class="form-control" id="passwordSignup" placeholder="Password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
                    </div>
                    <div class="mb-3">
                        <label for="profilePic" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profilePic" accept="image/*">
                        <div class="mt-3">
                            <img id="profilePicPreview" src="#" alt="Profile Preview"
                                 style="display: none; width: 100px; height: 100px; border-radius: 50%;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Signup</button>
                </form>
            </div>

        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // AJAX for Login Form
            $('#loginForm').on('submit', function(event) {
                event.preventDefault();
                let email = $('#emailLogin').val();
                let password = $('#passwordLogin').val();

                $.ajax({
                    url: '{{ route('login') }}', // This route will point to login processing
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        email: email,
                        password: password
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            window.location.href = response.redirect; // Redirect to the target page
                        }, 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            // Handle 401 Unauthorized specifically
                            toastr.error(xhr.responseJSON.message || 'Invalid credentials.');
                        } else if (xhr.status === 422) {
                            // Handle validation errors (422 Unprocessable Entity)
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]); // Show each validation error
                            });
                        } else {
                            // Handle other types of errors
                            toastr.error('An error occurred. Please try again.');
                        }
                    }

                });
            });

            // Image preview
            $('#profilePic').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profilePicPreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            // AJAX for Signup Form
            $('#signupForm').on('submit', function(event) {
                event.preventDefault();

                let formData = new FormData();
                formData.append('name', $('#name').val());
                formData.append('email', $('#emailSignup').val());
                formData.append('password', $('#passwordSignup').val());
                formData.append('password_confirmation', $('#confirmPassword').val());

                const profilePic = $('#profilePic')[0].files[0];
                if (profilePic) {
                    formData.append('profile_pic', profilePic); // Add profile picture to form data
                }

                $.ajax({
                    url: '{{ route('signup') }}', // This route will point to signup processing
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            window.location.href = response.redirect; // Redirect to the target page
                        }, 1000);
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]); // Show each error message
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>

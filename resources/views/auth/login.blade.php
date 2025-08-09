<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Company Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: #000000;
            background: linear-gradient(145deg, #000000 0%, #191C24 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-container {
            max-width: 450px;
            margin: auto;
            padding: 30px;
            background: rgba(25, 28, 36, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #fff;
            font-size: 28px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 8px;
            color: #fff;
            padding: 12px 15px;
            padding-left: 45px;
            height: auto;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px #8F5FE8;
            color: #fff;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 44px;
            color: #8F5FE8;
        }

        .btn-login {
            background: #8F5FE8;
            border: none;
            border-radius: 8px;
            color: white;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #7a4bd3;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(143, 95, 232, 0.4);
        }

        .invalid-feedback {
            color: #ff4d6b;
            font-size: 0.85rem;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container img {
            width: 80px;
            margin-bottom: 15px;
        }

        .remember-me {
            color: #fff;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo-container">
                <i class="fas fa-building fa-3x text-primary mb-3"></i>
                <h2>Company Portal</h2>
            </div>
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="email" class="text-white mb-2">Email Address</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Enter your email"
                           autocomplete="email"
                           required>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="text-white mb-2">Password</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password"
                           placeholder="Enter your password"
                           required>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="remember-me form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn btn-login btn-block w-100">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>

                @if (Route::has('register'))
                <div class="mt-3 text-center">
                    <a href="{{ route('register') }}" class="text-light">
                        <i class="fas fa-user-plus me-1"></i> New user? Register here
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle form submission
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                // Get form elements
                const form = $(this);
                const btn = form.find('button[type="submit"]');
                const originalBtnText = btn.html();
                
                // Reset previous states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.alert').remove();
                
                // Show loading state
                btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Signing in...');
                btn.prop('disabled', true);

                // Get form data
                const formData = {
                    email: form.find('[name="email"]').val(),
                    password: form.find('[name="password"]').val(),
                    remember: form.find('[name="remember"]').is(':checked')
                };

                // Make AJAX request
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                'Login successful! Redirecting...' +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>');
                            $('.login-container').prepend(successAlert);
                            
                            // Redirect after a short delay
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        } else {
                            // Handle validation errors
                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(key) {
                                    const input = form.find(`[name="${key}"]`);
                                    input.addClass('is-invalid');
                                    input.after(`<span class="invalid-feedback" role="alert"><strong>${response.errors[key]}</strong></span>`);
                                });
                            } else {
                                // Show general error message
                                const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    response.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>');
                                $('.login-container').prepend(errorAlert);
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An unexpected error occurred. Please try again.';
                        
                        if (xhr.status === 422) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                const input = form.find(`[name="${key}"]`);
                                input.addClass('is-invalid');
                                input.after(`<span class="invalid-feedback" role="alert"><strong>${errors[key]}</strong></span>`);
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            errorMessage +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>');
                        $('.login-container').prepend(errorAlert);
                    },
                    complete: function() {
                        // Reset button state
                        btn.html(originalBtnText);
                        btn.prop('disabled', false);
                    }
                });
            });

            // Clear validation errors when input is focused
            $('input').on('focus', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });
        });
    </script>
</body>
</html>
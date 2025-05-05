<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register - CompanySecure</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('admin_template_assets/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_template_assets/vendors/css/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_template_assets/css/style.css')}}">
    <link rel="shortcut icon" href="{{ asset('admin_template_assets/images/favicon.png')}}" />
    <style>
        .auth-form-light {
            background-color: #191c24;
            border: 1px solid #2c2e33;
        }
        .form-control {
            color: white !important;
            background-color: #2A3038 !important;
            border-color: #2c2e33 !important;
        }
        .auth-link {
            color: #6c7293;
        }
        .auth-link:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="row w-100 m-0">
                <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
                    <div class="card col-lg-4 mx-auto">
                        <div class="card-body px-5 py-5">
                            <h3 class="card-title text-center mb-3">Register</h3>
                            
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control p_input" required autofocus>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control p_input" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Password *</label>
                                    <input type="password" name="password" class="form-control p_input" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Confirm Password *</label>
                                    <input type="password" name="password_confirmation" class="form-control p_input" required>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-block enter-btn">Register</button>
                                </div>
                                
                                <p class="sign-up text-center mt-3">Already have an Account?<a href="{{ route('login') }}"> Login</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('admin_template_assets/vendors/js/vendor.bundle.base.js')}}"></script>
    <script src="{{ asset('admin_template_assets/js/off-canvas.js')}}"></script>
    <script src="{{ asset('admin_template_assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{ asset('admin_template_assets/js/misc.js')}}"></script>
    <script src="{{ asset('admin_template_assets/js/settings.js')}}"></script>
</body>
</html> 
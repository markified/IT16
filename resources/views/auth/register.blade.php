<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Register</title>
    <!-- Custom fonts for this template-->
    <link href="{{ asset('admin_assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('admin_assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Nunito', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url("{{ asset('image/bg6.png') }}") no-repeat center center fixed;
            background-size: cover;
            position: relative;
            padding: 20px 0;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 20, 60, 0.6);
            z-index: 0;
        }

        .floating-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            margin: 20px;
            padding: 35px;
            background: linear-gradient(135deg, #001d6d 0%, #002a8f 50%, #0039b3 100%);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-header h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .form-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 5px 12px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            color: #333333;
            font-size: 15px;
            transition: all 0.3s ease;
            
        }

        .form-control:focus {
            outline: none;
            border-color: #0096ff;
            box-shadow: 0 0 0 4px rgba(0, 150, 255, 0.2);
        }

        .form-control::placeholder {
            color: #999999;
        }

        .form-control.is-invalid {
            border-color: #e74a3b;
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-color: rgba(255, 255, 255, 0.95);
            padding-right: 40px;
            cursor: pointer;
            color: #333333;
        }

        select.form-control option {
            background: #ffffff;
            color: #333333;
            padding: 10px;
        }

        select.form-control option:disabled {
            color: #999999;
        }

        .invalid-feedback {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 6px;
        }

        .alert-danger {
            background: rgba(231, 74, 59, 0.15);
            border: 1px solid rgba(231, 74, 59, 0.3);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
            color: #ff6b6b;
            font-size: 13px;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #0096ff 0%, #0077cc 100%);
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #00a8ff 0%, #0088dd 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 150, 255, 0.4);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 18px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .divider span {
            padding: 0 15px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }

        .login-link {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .login-link a {
            color: #0096ff;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #00b4ff;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .floating-container {
                margin: 15px;
                padding: 30px 25px;
            }

            .form-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="floating-container">
        <div class="form-header">
            <h1><i class="fas fa-user-plus mr-2"></i> Create Account</h1>
            <p>Fill in your details to get started</p>
        </div>

        <form action="{{ route('register.save') }}" method="POST">
            @csrf
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-group">
                <label for="name">Full Name <span class="text-danger">*</span></label>
                <input name="name" id="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Enter your full name" value="{{ old('name') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address <span class="text-danger">*</span></label>
                <input name="email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email address" value="{{ old('email') }}" required>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password <span class="text-danger">*</span></label>
                <input name="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Create a strong password" required>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                <input name="password_confirmation" id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm your password" required>
                @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="role">Role <span class="text-danger">*</span></label>
                <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select your role</option>
                    <option value="inventory" {{ old('role') == 'inventory' ? 'selected' : '' }}>Inventory</option>
                    <option value="security" {{ old('role') == 'security' ? 'selected' : '' }}>Security</option>
                </select>
                @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="divider">
            <span>OR</span>
        </div>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('admin_assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin_assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin_assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin_assets/js/sb-admin-2.min.js') }}"></script>
</body>

</html>
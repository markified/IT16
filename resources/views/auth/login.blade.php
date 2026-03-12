<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Login</title>
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

        body {
            font-family: 'Nunito', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url("{{ asset('image/bg6.png') }}") no-repeat center center fixed;
            background-size: cover;
            position: relative;
            padding: 20px;
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

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1000px;
            display: flex;
            gap: 30px;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo-container {
            flex: 1;
            min-width: 280px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .logo-container img {
            max-width: 100%;
            height: auto;
            width: 250px;
            margin-bottom: 20px;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
        }

        .logo-container h2 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
        }

        .logo-container p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            text-align: center;
        }

        .floating-container {
            position: relative;
            flex: 1;
            min-width: 320px;
            padding: 40px;
            background: linear-gradient(135deg, #001d6d 0%, #002a8f 50%, #0039b3 100%);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
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

        .invalid-feedback {
            display: block;
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 6px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.15);
            border: 1px solid rgba(40, 167, 69, 0.3);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            color: #28a745;
            font-size: 13px;
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

        .btn-login {
            width: 100%;
            padding: 14px;
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

        .btn-login:hover {
            background: linear-gradient(135deg, #00a8ff 0%, #0088dd 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 150, 255, 0.4);
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-password a {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #0096ff;
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
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

        .register-link {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .register-link a {
            color: #0096ff;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #00b4ff;
            text-decoration: underline;
        }

        .captcha-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .captcha-question {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px 18px;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            white-space: nowrap;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-wrapper {
                flex-direction: column;
                gap: 20px;
            }

            .logo-container {
                min-width: 100%;
                padding: 30px 20px;
            }

            .logo-container img {
                width: 200px;
            }

            .logo-container h2 {
                font-size: 20px;
            }

            .floating-container {
                min-width: 100%;
                padding: 30px 25px;
            }

            .form-header h1 {
                font-size: 24px;
            }

            .captcha-wrapper {
                flex-direction: column;
                align-items: stretch;
            }

            .captcha-question {
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .logo-container {
                padding: 20px 15px;
            }

            .logo-container img {
                width: 150px;
            }

            .floating-container {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <!-- Logo Section -->
        <div class="logo-container">
            <img src="{{ asset('image/logo.png') }}" alt="Logo">
            <h2>Inventory Management System</h2>
            <p>Secure and efficient inventory tracking</p>
        </div>

        <!-- Form Section -->
        <div class="floating-container">
        <div class="form-header">
            <h1><i class="fas fa-sign-in-alt mr-2"></i> Welcome Back!</h1>
            <p>Sign in to continue to your account</p>
        </div>

        <form action="{{ route('login.action') }}" method="POST">
            @csrf
            @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif
            @if (session('info'))
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-group">
                <label for="email">Email Address <span class="text-danger">*</span></label>
                <input name="email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email address" value="{{ old('email') }}" required>
                @error('email')
                <div class="invalid-feedback"><i class="fas fa-times-circle mr-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password <span class="text-danger">*</span></label>
                <input name="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                @error('password')
                <div class="invalid-feedback"><i class="fas fa-times-circle mr-1"></i>{{ $message }}</div>
                @enderror
            </div>

            @if($show_captcha ?? false)
            <div class="form-group">
                <label for="captcha">Security Check <span class="text-danger">*</span></label>
                <div class="captcha-wrapper">
                    <div class="captcha-question">
                        <i class="fas fa-shield-alt mr-2"></i>{{ $captcha_question }}
                    </div>
                    <input name="captcha" id="captcha" type="number" class="form-control @error('captcha') is-invalid @enderror" placeholder="Answer" required style="flex: 1;">
                </div>
                @error('captcha')
                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                @enderror
            </div>
            @endif

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>

            <div class="forgot-password">
                <a href="{{ route('password.request') }}">Forgot your password?</a>
            </div>
        </form>

        <div class="divider">
            <span>OR</span>
        </div>

        <div class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Create Account</a>
        </div>
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
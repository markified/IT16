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
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
            background: url("{{ asset('image/bg6.png') }}") no-repeat center center;
            background-size: cover;
        }

        .card {
            width: 100%;
            max-width: 400px;
            border-radius: 30px;
            background: rgba(0, 29, 109, 0.93);
        }

        /* Style for input fields */
        .form-control {
            color: #000000 !important; /* Black color for typed text */
            background-color: #ffffff !important; /* White background */
        }

        /* Style for placeholder text */
        .form-control::placeholder {
            color: #999999 !important; /* Gray color for placeholder */
            opacity: 1 !important;
        }

        .form-control::-webkit-input-placeholder {
            color: #999999 !important;
        }

        .form-control::-moz-placeholder {
            color: #999999 !important;
        }

        .form-control:-ms-input-placeholder {
            color: #999999 !important;
        }
    </style>
</head>

<body class="">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="d-flex justify-content-center align-items-center vh-100">
                    <div class="card o-hidden border-0 shadow-lg">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <img src="{{ asset('image/logo.png') }}" alt="Logo" style="width: 250px; height: auto;" class="mb-3"> 
                                <h1 class="h4 text-white-900" style="color: rgb(255, 255, 255); font-family: ', sans-serif;">Welcome Back!</h1>
                            </div>
                            <form action="{{ route('login.action') }}" method="POST" class="user">
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
                                    <input name="email" type="email" class="form-control form-control-user" placeholder="Enter Email Address...">
                                </div>
                                <div class="form-group">
                                    <input name="password" type="password" class="form-control form-control-user" placeholder="Password">
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block" style="background-color: rgb(0, 150, 255); border-color: #000000;">Login</button>
                            </form>
                            <hr>
                            <div class="text-center text-white-900">
                                Don't have an account?
                                <a class="small text-white" href="{{ route('register') }}">Create an Account!</a>
                            </div>
                        </div>
                    </div>
                </div>
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
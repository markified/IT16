<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Justin&Friends - Register</title>
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
            background: url("{{ asset('image/bg3.jpeg') }}") no-repeat center center;
            background-size: cover;
        }

        .card {
            width: 100%;
            max-width: 450px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body class="">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('image/logo.png') }}" alt="Logo" style="width: 150px; height: auto;" class="mb-3">
                        <h1 class="h4 text-white-900" style="color: white;">Create an Account!</h1>
                    </div>
                    <form action="{{ route('register.save') }}" method="POST" class="user">
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
                            <input name="name" type="text" class="form-control form-control-user @error('name')is-invalid @enderror" placeholder="Name">
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input name="email" type="email" class="form-control form-control-user @error('email')is-invalid @enderror" placeholder="Email Address">
                            @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input name="password" type="password" class="form-control form-control-user @error('password')is-invalid @enderror" placeholder="Password">
                            @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input name="password_confirmation" type="password" class="form-control form-control-user @error('password_confirmation')is-invalid @enderror" placeholder="Repeat Password">
                            @error('password_confirmation')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-user btn-block">Register Account</button>
                    </form>
                    <hr>
                    <div class="text-center text-white-900">
                        Already have an account?
                        <a class="small text-white" href="{{ route('login') }}">Login!</a>
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
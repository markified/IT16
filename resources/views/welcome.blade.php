<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background-color: rgb(0, 88, 177);
            /* Light background for login theme */
            color: #212529;
            /* Dark text for contrast */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="card text-center shadow p-4" style="max-width: 400px; width: 100%; border-radius: 8px;">
        <img src="{{ file_exists(public_path('image/logo.png')) ? asset('image/logo.png') : asset('image/default-logo.png') }}" alt="Logo" class="img-fluid" style="max-height: 200px;">
        @if (Route::has('login'))
        @auth
        <a href="{{ url('/dashboard') }}" class="btn btn-dark mb-2">Dashboard</a>
        @else
        <a href="{{ route('login') }}" class="btn btn-dark mb-2">Log in</a>
        @if (Route::has('register'))
        <a href="{{ route('register') }}" class="btn btn-dark">Register</a>
        @endif
        @endauth
        @endif
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
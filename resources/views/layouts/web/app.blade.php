<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f4f9;
            padding-top: 70px; /* Make space for fixed navbar */
        }

        .navbar {
            background-color: #f8f8f8ff !important; /* Dark navy from your brand */
            height: 70px; /* Fixed height */
            padding: 0 1rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff !important;
        }

       

        .navbar-brand .logo-img {
            height: 71px;
            width: 134px;
            margin-top: -6px;
        }

        .main-content {
            padding: 2rem;
        }

        .hover-shadow:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
            transition: 0.3s;
        }
    </style>
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('storage/logo/sc1.png') }}" alt="Logo" class="logo-img">
            
        </a>

        <div class="ms-auto d-flex align-items-center">
            @auth
                <span class="text-white me-3">
                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm" type="submit">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="main-content">
    @yield('content')
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

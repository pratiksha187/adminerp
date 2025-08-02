<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f4f9;
            padding-top: 70px;
        }

        .navbar {
            background-color: #ffffff !important;
            height: 70px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: bold;
            color: #1c2c3e !important;
        }

        .logo-img {
            height: 70px;
            width: auto;
            margin-top: -6px;
        }

        .sidebar {
            width: 250px;
            position: fixed;
            top: 70px;
            left: 0;
            height: 100%;
            background-color: #1c2c3e;
            padding-top: 1rem;
            overflow-y: auto;
            transition: margin-left 0.3s ease;
            z-index: 1030;
        }

        .sidebar.collapsed {
            margin-left: -250px;
        }

        .sidebar a {
            color: #adb5bd;
            padding: 0.75rem 1.5rem;
            display: block;
            text-decoration: none;
            transition: 0.3s;
            border-radius: 0 20px 20px 0;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #e5ad38;
            color: #fff;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        #toggleSidebar {
            font-size: 1.2rem;
            border: none;
            background: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-md navbar-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('storage/logo/sc1.png') }}" alt="Logo" class="logo-img">
        </a>

        <!-- Sidebar Toggle Button -->
        <button id="toggleSidebar" class="btn me-3">
            <i class="bi bi-list"></i>
        </button>

        <div class="ms-auto d-flex align-items-center">
            @auth
                <span class="text-dark me-3">
                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-dark btn-sm" type="submit">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door me-2"></i> Dashboard
    </a>

    <a class="d-flex justify-content-between align-items-center {{ request()->is('register*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#registerMenu"
       role="button"
       aria-expanded="{{ request()->is('register*') ? 'true' : 'false' }}"
       aria-controls="registerMenu">
        <span><i class="bi bi-person me-2"></i> User</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ request()->is('register*') ? 'show' : '' }}" id="registerMenu">
        <a href="{{ route('register') }}" class="ps-5 {{ request()->routeIs('register.chalan') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Add User
        </a>
       
    </div>

    <a class="d-flex justify-content-between align-items-center {{ request()->is('accounts*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#accountsMenu"
       role="button"
       aria-expanded="{{ request()->is('accounts*') ? 'true' : 'false' }}"
       aria-controls="accountsMenu">
        <span><i class="bi bi-gear me-2"></i> Accounts</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ request()->is('accounts*') ? 'show' : '' }}" id="accountsMenu">
        <a href="{{ route('challan') }}" class="ps-5 {{ request()->routeIs('accounts.chalan') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Chalan
        </a>
    </div>

    <a class="d-flex justify-content-between align-items-center {{ request()->is('engg*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#enggMenu"
       role="button"
       aria-expanded="{{ request()->is('engg*') ? 'true' : 'false' }}"
       aria-controls="enggMenu">
        <span><i class="bi bi-book me-2"></i> Engineering</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ request()->is('engg*') ? 'show' : '' }}" id="enggMenu">
        <a href="{{ route('work-entry.index') }}" class="ps-5 {{ request()->routeIs('engg.engineering') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Daily Work Report
        </a>
    </div>


    <a class="d-flex justify-content-between align-items-center {{ request()->is('userauth*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#userauthMenu"
       role="button"
       aria-expanded="{{ request()->is('userauth*') ? 'true' : 'false' }}"
       aria-controls="userauthMenu">
        <span><i class="bi bi-clock me-2"></i>Login/Logout Time</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ request()->is('userauth*') ? 'show' : '' }}" id="userauthMenu">
        <a href="{{ route('attendance.report') }}" class="ps-5 {{ request()->routeIs('userauth.engineering') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Daily Login/Logout
        </a>
    </div>

    <a class="d-flex justify-content-between align-items-center {{ request()->is('manualattendence*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#manualattendenceMenu"
       role="button"
       aria-expanded="{{ request()->is('manualattendence*') ? 'true' : 'false' }}"
       aria-controls="manualattendenceMenu">
        <span><i class="bi bi-clock me-2"></i>Manual Attendence</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ request()->is('manualattendence*') ? 'show' : '' }}" id="manualattendenceMenu">
        <a href="{{ route('attendance.manualattendence') }}" class="ps-5 {{ request()->routeIs('manualattendence.manualattendence') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Attendence
        </a>

        <a href="{{ route('attendance.acceptattendence') }}" class="ps-5 {{ request()->routeIs('manualattendence.manualattendence') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Accept Attendence
        </a>

        
    </div>

     <a class="d-flex justify-content-between align-items-center {{ request()->is('letterhead*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#letterheadMenu"
       role="button"
       aria-expanded="{{ request()->is('letterhead*') ? 'true' : 'false' }}"
       aria-controls="letterheadMenu">
        <span><i class="bi bi-clock me-2"></i>Letter Head</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ request()->is('letterhead*') ? 'show' : '' }}" id="letterheadMenu">
        <a href="{{ route('letterhead') }}" class="ps-5 {{ request()->routeIs('letterhead') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Letter Head details
        </a>
    </div>
</div>

<!-- Main Content -->
<main class="main-content" id="mainContent">
    @yield('content')
</main>

<!-- Sidebar Toggle Script -->
<script>
    $(document).ready(function () {
        $('#toggleSidebar').click(function () {
            $('#sidebar').toggleClass('collapsed');
            $('#mainContent').toggleClass('expanded');
        });
    });
</script>

</body>
</html>

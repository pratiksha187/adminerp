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
            border-radius: 0;
            transition: background 0.2s, color 0.2s;
            font-size: 15px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #2e3e52;
            color: #ffffff;
        }

        .sidebar .collapse a {
            padding-left: 2.5rem;
            font-size: 14px;
        }

        .sidebar .collapse a.active {
            background-color: #3e5771;
            color: #ffffff;
        }

        .sidebar i {
            font-size: 1rem;
        }

        .sidebar a.active-parent {
            background-color: #2e3e52;
            color: #ffffff;
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
    <!-- Dashboard -->
  
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door me-2"></i> Dashboard
    </a>
 
    <!-- User -->
    @if(in_array($role, [1,2])) 
    @php $userActive = request()->is('register*'); @endphp
    <a class="d-flex justify-content-between align-items-center {{ $userActive ? 'active-parent' : '' }}"
       data-bs-toggle="collapse" href="#registerMenu" aria-expanded="{{ $userActive ? 'true' : 'false' }}">
        <span><i class="bi bi-person me-2"></i> User</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ $userActive ? 'show' : '' }}" id="registerMenu">
        <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">
            <i class="bi bi-person-plus me-2"></i> Add User
        </a>
    </div>
    @endif


    @if(in_array($role, [1,2,10]))   
    @php $accountsActive = request()->routeIs('challan'); @endphp
    <a class="d-flex justify-content-between align-items-center {{ $accountsActive ? 'active-parent' : '' }}"
    data-bs-toggle="collapse" href="#accountsMenu" aria-expanded="{{ $accountsActive ? 'true' : 'false' }}">
        <span><i class="bi bi-gear me-2"></i> Accounts</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ $accountsActive ? 'show' : '' }}" id="accountsMenu">
        <a href="{{ route('challan') }}" class="{{ request()->routeIs('challan') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Chalan
        </a>
    </div>
    @endif

    @if(in_array($role, [1,4,2])) 
    @php $enggActive = request()->routeIs('work-entry.index'); @endphp
    <a class="d-flex justify-content-between align-items-center {{ $enggActive ? 'active-parent' : '' }}"
    data-bs-toggle="collapse" href="#enggMenu" aria-expanded="{{ $enggActive ? 'true' : 'false' }}">
        <span><i class="bi bi-book me-2"></i> DPR</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ $enggActive ? 'show' : '' }}" id="enggMenu">
        <a href="{{ route('work-entry.index') }}" class="{{ request()->routeIs('work-entry.index') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Daily Work Report
        </a>
    </div>
    @endif


    @php
        $uid = $role;

        $canSee = [
            'attendance.calendar.view'     => in_array($uid, [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17]),  
            'attendance.report'            => in_array($uid, [1,4]),  
            'attendance.manualattendence'  => in_array($uid, [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17]),     
            'attendance.acceptattendence'  => in_array($uid, [1,4]),     
        ];

        $showAttendance = in_array(true, $canSee, true);

        // Active state should consider only links the user can actually see
        $attendanceRoutes = array_keys(array_filter($canSee));
        $attendanceActive = collect($attendanceRoutes)->contains(fn($r) => request()->routeIs($r));
    @endphp

    @if($showAttendance)
        <a class="d-flex justify-content-between align-items-center {{ $attendanceActive ? 'active-parent' : '' }}"
        data-bs-toggle="collapse" href="#userauthMenu" aria-expanded="{{ $attendanceActive ? 'true' : 'false' }}">
            <span><i class="bi bi-clock me-2"></i> Attendance</span>
            <i class="bi bi-chevron-down small"></i>
        </a>

        <div class="collapse {{ $attendanceActive ? 'show' : '' }}" id="userauthMenu">
           
            @if($canSee['attendance.calendar.view'])
                <a href="{{ route('attendance.calendar.view') }}" class="{{ request()->routeIs('attendance.calendar.view') ? 'active' : '' }}">
                    <i class="bi bi-list-check me-2"></i> Attendance Calendar
                </a>
            @endif

            @if($canSee['attendance.report'])
                <a href="{{ route('attendance.report') }}" class="{{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                    <i class="bi bi-list-check me-2"></i> Daily Login/Logout
                </a>
            @endif

            @if($canSee['attendance.manualattendence'])
                <a href="{{ route('attendance.manualattendence') }}" class="{{ request()->routeIs('attendance.manualattendence') ? 'active' : '' }}">
                    <i class="bi bi-pencil-square me-2"></i> Manual Attendance
                </a>
            @endif

            @if($canSee['attendance.acceptattendence'])
                <a href="{{ route('attendance.acceptattendence') }}" class="{{ request()->routeIs('attendance.acceptattendence') ? 'active' : '' }}">
                    <i class="bi bi-check2-circle me-2"></i> Accept Attendance
                </a>
            @endif
        </div>
    @endif

    @if(in_array($role, [1,9,2])) 
    @php $paymentActive = request()->is('payments*'); @endphp

        <a class="d-flex justify-content-between align-items-center {{ $paymentActive ? 'active-parent' : '' }}"
        data-bs-toggle="collapse" href="#paymentMenu" aria-expanded="{{ $paymentActive ? 'true' : 'false' }}">
            <span><i class="bi bi-currency-rupee me-2"></i> Payments</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ $paymentActive ? 'show' : '' }}" id="paymentMenu">
            <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.index') ? 'active' : '' }}">
                <i class="bi bi-list-ul me-2"></i> All Payments
            </a>
            <a href="{{ route('payments.create') }}" class="{{ request()->routeIs('payments.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle me-2"></i> New Payment
            </a>
        </div>
    @endif

     @if(in_array($role, [1,2])) 
    <!-- Letter Head --> 
    @php $letterheadActive = request()->is('letterhead*'); @endphp
    <a class="d-flex justify-content-between align-items-center {{ $letterheadActive ? 'active-parent' : '' }}"
       data-bs-toggle="collapse" href="#letterheadMenu" aria-expanded="{{ $letterheadActive ? 'true' : 'false' }}">
        <span><i class="bi bi-file-earmark-text me-2"></i> Letter Head</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ $letterheadActive ? 'show' : '' }}" id="letterheadMenu">
        <a href="{{ route('letterhead') }}" class="{{ request()->routeIs('letterhead') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> Letter Head Details
        </a>
    </div>
     @endif
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

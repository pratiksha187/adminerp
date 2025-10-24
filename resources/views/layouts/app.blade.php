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
        body { background-color:#f1f4f9; padding-top:70px; }
        .navbar{ background:#fff!important; height:70px; box-shadow:0 2px 6px rgba(0,0,0,.1); }
        .navbar-brand{ display:flex; align-items:center; gap:12px; font-weight:bold; color:#1c2c3e!important; }
        .logo-img{ height:70px; width:auto; margin-top:-6px; }

        .sidebar{ width:250px; position:fixed; top:70px; left:0; height:100%; background:#1c2c3e; padding-top:1rem; overflow-y:auto; transition:margin-left .3s ease; z-index:1030; }
        .sidebar.collapsed{ margin-left:-250px; }
        .sidebar a{ color:#adb5bd; padding:.75rem 1.5rem; display:block; text-decoration:none; transition:.2s; font-size:15px; }
        .sidebar a:hover, .sidebar a.active{ background:#2e3e52; color:#fff; }
        .sidebar .collapse a{ padding-left:2.5rem; font-size:14px; }
        .sidebar .collapse a.active{ background:#3e5771; color:#fff; }
        .sidebar i{ font-size:1rem; }
        .sidebar a.active-parent{ background:#2e3e52; color:#fff; }

        .main-content{ margin-left:250px; padding:2rem; transition:margin-left .3s ease; }
        .main-content.expanded{ margin-left:0; }

        #toggleSidebar{ font-size:1.2rem; border:none; background:none; }

        @media (max-width:768px){
            .sidebar{ margin-left:-250px; }
            .sidebar.show{ margin-left:0; }
            .main-content{ margin-left:0; }
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

        <button id="toggleSidebar" class="btn me-3"><i class="bi bi-list"></i></button>

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

    @php
        // Normalize roleId safely
        $roleId = (int) (
            $roleId
            ?? (is_object($role) ? ($role->role ?? 0) : ($role ?? (auth()->user()->role ?? 0)))
        );
    @endphp
<!-- <h1>{{ $roleId}}</h1> -->
    {{-- ===== User (roles 1,2,17) ===== --}}
    @php $userActive = request()->routeIs('register*'); @endphp
    @if(in_array($roleId, [1, 2, 17], true))
        <a class="d-flex justify-content-between align-items-center {{ $userActive ? 'active-parent' : '' }}"
           data-bs-toggle="collapse" href="#userMenu" aria-expanded="{{ $userActive ? 'true' : 'false' }}">
            <span><i class="bi bi-person me-2"></i> User</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ $userActive ? 'show' : '' }}" id="userMenu">
            <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">
                <i class="bi bi-person-plus me-2"></i> Add User
            </a>
        </div>
    @endif

    {{-- ===== Accounts (roles 1,2,10,17) ===== --}}
    @php $accountsActive = request()->routeIs('challan*'); @endphp
    @if(in_array($roleId, [1, 2, 10, 17], true))
        <a class="d-flex justify-content-between align-items-center {{ $accountsActive ? 'active-parent' : '' }}"
           data-bs-toggle="collapse" href="#accountsMenu" aria-expanded="{{ $accountsActive ? 'true' : 'false' }}">
            <span><i class="bi bi-gear me-2"></i> Accounts</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ $accountsActive ? 'show' : '' }}" id="accountsMenu">
            <a href="{{ route('challan') }}" class="{{ request()->routeIs('challan*') ? 'active' : '' }}">
                <i class="bi bi-receipt me-2"></i> Challan
            </a>
        </div>
    @endif

    {{-- ===== Lead (roles 1,2,10,17) ===== --}}
    @php $leadActive = request()->routeIs('crm*'); @endphp
    @if(in_array($roleId, [1, 2, 10], true))
        <a class="d-flex justify-content-between align-items-center {{ $leadActive ? 'active-parent' : '' }}"
           data-bs-toggle="collapse" href="#leadMenu" aria-expanded="{{ $leadActive ? 'true' : 'false' }}">
            <span><i class="bi bi-gear me-2"></i> CRM</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ $leadActive ? 'show' : '' }}" id="leadMenu">
            <a href="{{ route('crm/lead-management') }}" class="{{ request()->routeIs('crm/lead-management*') ? 'active' : '' }}">
                <i class="bi bi-receipt me-2"></i> Lead
            </a>
        </div>
    @endif

{{-- ===== DPR (roles 1,2,4,17) ===== --}}
@php
    $canSee = [
        'work-entry.index'                => in_array($roleId, [1,2,4,18], true), // Engg Work Report
        'store-requirement.list'          => in_array($roleId, [1,2,17,18], true),   // Material Requirement
        'store-requirement.accepted.list' => in_array($roleId, [1,2,17,18], true),   // Accept Material List
        'store-dpr.list'                  => in_array($roleId, [1,4,17,18], true),   // Store Manager Report
    ];

    // check active route
    $dprRoutes   = array_keys(array_filter($canSee));
    $dprActive   = collect($dprRoutes)->contains(fn($r) => request()->routeIs($r));
    $storeActive = collect(['store-requirement.*','store-dpr.*'])
                    ->contains(fn($r) => request()->routeIs($r));
@endphp

@if(in_array(true, $canSee, true))
    <a class="d-flex justify-content-between align-items-center {{ $dprActive ? 'active-parent' : '' }}"
       data-bs-toggle="collapse" href="#enggMenu" aria-expanded="{{ $dprActive ? 'true' : 'false' }}">
        <span><i class="bi bi-book me-2"></i> DPR</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse {{ $dprActive ? 'show' : '' }}" id="enggMenu">

        {{-- Engg Work Report --}}
        @if($canSee['work-entry.index'])
            <a href="{{ route('work-entry.index') }}" 
               class="{{ request()->routeIs('work-entry.index') ? 'active' : '' }}">
                <i class="bi bi-receipt me-2"></i> Engg Daily Work Report
            </a>
        @endif

        {{-- Store Manager Main --}}
        @if($canSee['store-requirement.list'] || $canSee['store-requirement.accepted.list'] || $canSee['store-dpr.list'])
            <a class="d-flex justify-content-between align-items-center {{ $storeActive ? 'active-parent' : '' }}"
               data-bs-toggle="collapse" href="#storeMenu" aria-expanded="{{ $storeActive ? 'true' : 'false' }}">
                <span><i class="bi bi-box me-2"></i> Store Manager</span>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <div class="collapse {{ $storeActive ? 'show' : '' }}" id="storeMenu">
                @if($canSee['store-requirement.list'])
                    <a href="{{ route('store-requirement.list') }}" 
                       class="{{ request()->routeIs('store-requirement.*') ? 'active' : '' }}">
                        <i class="bi bi-cart-check me-2"></i> Material Requirement
                    </a>
                @endif
                @if($canSee['store-requirement.accepted.list'])
                    <a href="{{ route('store-requirement.accepted.list') }}" 
                       class="{{ request()->routeIs('store-requirement.accepted.list') ? 'active' : '' }}">
                        <i class="bi bi-box-seam me-2"></i> Accept Material List
                    </a>
                @endif
                @if($canSee['store-dpr.list'])
                    <a href="{{ route('store-dpr.list') }}" 
                       class="{{ request()->routeIs('store-dpr.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam me-2"></i> Store Manager Report
                    </a>
                @endif
            </div>
        @endif
    </div>
@endif

    {{-- ===== Attendance ===== --}}
    @php
        $allowAll     = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];
        $allowManager = [1,2,17];
        $canSee = [
            'attendance.calendar.view'    => in_array($roleId, $allowAll, true),
            'attendance.report'           => in_array($roleId, $allowManager, true),
            'attendance.manualattendence' => in_array($roleId, $allowAll, true),
            'attendance.acceptattendence' => in_array($roleId, $allowManager, true),
        ];
        $attendanceRoutes = array_keys(array_filter($canSee));
        $attendanceActive = collect($attendanceRoutes)->contains(fn($r) => request()->routeIs($r));
    @endphp

    @if(in_array(true, $canSee, true))
        <a class="d-flex justify-content-between align-items-center {{ $attendanceActive ? 'active-parent' : '' }}"
           data-bs-toggle="collapse" href="#attendanceMenu" aria-expanded="{{ $attendanceActive ? 'true' : 'false' }}">
            <span><i class="bi bi-clock me-2"></i> Attendance</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ $attendanceActive ? 'show' : '' }}" id="attendanceMenu">
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

   @php
    // check active route for leave menu
    $leaveActive = request()->routeIs('leave.*') || request()->routeIs('hr.leaves.*');

    // roles/IDs allowed
    $canLeave           = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];
    $allowManagerLeave  = [1,2,17];
    @endphp
    @if(in_array( $roleId ?? auth()->id(), $canLeave))
        <a class="d-flex justify-content-between align-items-center {{ $leaveActive ? 'active-parent' : '' }}"
        data-bs-toggle="collapse" href="#leaveMenu" aria-expanded="{{ $leaveActive ? 'true' : 'false' }}">
            <span><i class="bi bi-calendar-x me-2"></i> Leave</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ $leaveActive ? 'show' : '' }}" id="leaveMenu">
            
            {{-- Everyone who has leave permission --}}
            <a href="{{ route('leave.index') }}" class="{{ request()->routeIs('leave.index') ? 'active' : '' }}">
                <i class="bi bi-list-ul me-2"></i> My Leaves
            </a>

            {{-- Only Manager/HR roles can respond on leave --}}
            @if(in_array($roleId  ?? auth()->id(), $allowManagerLeave))
                <a href="{{ route('hr.leaves.index') }}" class="{{ request()->routeIs('hr.leaves.index') ? 'active' : '' }}">
                    <i class="bi bi-check2-circle me-2"></i> Respond on Leaves
                </a>
            @endif
        </div>
    @endif



    {{-- ===== Payments (roles 1,2,9,17) ===== --}}
    @php $paymentActive = request()->routeIs('payments.*'); @endphp
    @if(in_array($roleId, [1, 2, 9, 17], true))
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

    {{-- ===== Letterhead (roles 1,2,17) ===== --}}
    @php $letterheadActive = request()->routeIs('letterhead*'); @endphp
    @if(in_array($roleId, [1, 2, 17], true))
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
    $(function () {
        $('#toggleSidebar').on('click', function () {
            $('#sidebar').toggleClass('collapsed');
            $('#mainContent').toggleClass('expanded');
        });
    });
</script>

</body>
</html>

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ERP System') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary:#1c2c3e;
            --primary-light:#22384f;
            --accent:#f25c05;
            --accent-dark:#d94d00;
            --gold:#b89a44;
            --bg:#f5f7fb;
            --white:#ffffff;
            --border:#e5e7eb;
            --text:#111827;
            --muted:#6b7280;

            --sidebar-width:280px;
            --sidebar-mini-width:92px;
            --topbar-height:82px;

            --shadow:0 10px 30px rgba(15, 23, 42, 0.08);
            --shadow-sm:0 4px 16px rgba(15, 23, 42, 0.06);
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Inter', sans-serif;
            background:var(--bg);
            color:var(--text);
            overflow-x:hidden;
        }

        a{
            text-decoration:none;
        }

        /* =======================
           TOPBAR
        ======================= */
        .topbar{
            position:fixed;
            top:0;
            left:0;
            right:0;
            height:var(--topbar-height);
            background:rgba(255,255,255,0.96);
            backdrop-filter:blur(10px);
            border-bottom:1px solid var(--border);
            z-index:1050;
            display:flex;
            align-items:center;
            padding:0 22px;
            box-shadow:var(--shadow-sm);
        }

        .topbar-inner{
            width:100%;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
        }

        .topbar-left{
            display:flex;
            align-items:center;
            gap:14px;
            min-width:0;
        }

        .brand-wrap{
            display:flex;
            align-items:center;
        }

        .brand-link{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .brand-logo{
            height:48px;
            width:auto;
            object-fit:contain;
            transition:all .3s ease;
        }

        .brand-title{
            font-size:22px;
            font-weight:800;
            color:var(--gold);
            line-height:1;
            white-space:nowrap;
            transition:all .3s ease;
        }

        .menu-toggle{
            width:46px;
            height:46px;
            border:none;
            border-radius:14px;
            background:#eef2f7;
            color:var(--primary);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:22px;
            transition:all .3s ease;
            flex-shrink:0;
        }

        .menu-toggle:hover{
            background:#e2e8f0;
            color:var(--accent);
        }

        .topbar-right{
            display:flex;
            align-items:center;
            gap:12px;
            flex-shrink:0;
        }

        .user-pill{
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px 18px;
            background:var(--white);
            border:1px solid var(--border);
            border-radius:999px;
            font-weight:600;
            color:var(--text);
            min-height:48px;
        }

        .user-pill i{
            color:var(--gold);
            font-size:18px;
        }

        .logout-btn{
            border:1.5px solid #1f2937;
            background:var(--white);
            color:#111827;
            border-radius:16px;
            padding:11px 18px;
            font-weight:700;
            transition:all .3s ease;
        }

        .logout-btn:hover{
            background:var(--primary);
            color:#fff;
            border-color:var(--primary);
        }

        /* =======================
           SIDEBAR
        ======================= */
        .sidebar{
            position:fixed;
            top:var(--topbar-height);
            left:0;
            width:var(--sidebar-width);
            height:calc(100vh - var(--topbar-height));
            background:linear-gradient(180deg, #1c2c3e 0%, #152434 60%, #0d1722 100%);
            z-index:1040;
            padding:20px 14px 24px;
            overflow-y:auto;
            transition:width .3s ease, padding .3s ease, transform .3s ease;
            box-shadow:12px 0 30px rgba(2, 6, 23, 0.20);
        }

        .sidebar.collapsed{
            width:var(--sidebar-mini-width);
            transform:translateX(0);
            padding:20px 10px 24px;
        }

        .sidebar-title{
            color:rgba(255,255,255,.45);
            font-size:12px;
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:1px;
            padding:8px 14px 14px;
            transition:all .3s ease;
        }

        .sidebar .nav-link-item,
        .sidebar .menu-parent{
            display:flex;
            align-items:center;
            justify-content:space-between;
            width:100%;
            color:rgba(255,255,255,0.82);
            padding:13px 14px;
            border-radius:16px;
            margin-bottom:8px;
            transition:all .25s ease;
            border:1px solid transparent;
            background:transparent;
            cursor:pointer;
        }

        .sidebar .nav-link-item:hover,
        .sidebar .menu-parent:hover{
            color:#fff;
            background:rgba(255,255,255,0.08);
            transform:translateX(2px);
        }

        .sidebar .nav-link-item.active,
        .sidebar .menu-parent.active-parent{
            background:linear-gradient(90deg, rgba(242,92,5,0.18), rgba(255,255,255,0.06));
            color:#fff;
            border-color:rgba(242,92,5,0.28);
        }

        .sidebar .nav-left{
            display:flex;
            align-items:center;
            gap:12px;
        }

        .sidebar .nav-left i{
            font-size:17px;
            min-width:20px;
            text-align:center;
        }

        .submenu{
            display:none;
            padding-left:10px;
            margin-top:-2px;
            margin-bottom:8px;
        }

        .submenu.show{
            display:block;
        }

        .submenu a{
            display:flex;
            align-items:center;
            gap:10px;
            color:rgba(255,255,255,0.76);
            padding:11px 14px 11px 42px;
            border-radius:14px;
            margin-bottom:5px;
            transition:all .25s ease;
        }

        .submenu a:hover{
            background:rgba(255,255,255,0.07);
            color:#fff;
        }

        .submenu a.active{
            background:rgba(255,255,255,0.12);
            color:#fff;
        }

        .submenu-toggle-icon{
            transition:transform .25s ease;
        }

        .menu-parent.active-parent .submenu-toggle-icon{
            transform:rotate(180deg);
        }

        /* mini sidebar */
        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .nav-left span,
        .sidebar.collapsed .submenu-toggle-icon,
        .sidebar.collapsed .submenu{
            display:none !important;
        }

        .sidebar.collapsed .nav-link-item,
        .sidebar.collapsed .menu-parent{
            justify-content:center;
            padding:14px 8px;
        }

        .sidebar.collapsed .nav-left{
            gap:0;
            justify-content:center;
        }

        .sidebar.collapsed .nav-left i{
            font-size:20px;
        }

        /* =======================
           OVERLAY
        ======================= */
        .sidebar-overlay{
            position:fixed;
            inset:0;
            background:rgba(15, 23, 42, 0.45);
            z-index:1035;
            opacity:0;
            visibility:hidden;
            transition:all .3s ease;
        }

        .sidebar-overlay.show{
            opacity:1;
            visibility:visible;
        }

        /* =======================
           MAIN CONTENT
        ======================= */
        .main-wrapper{
            padding-top:var(--topbar-height);
        }

        .main-content{
            margin-left:var(--sidebar-width);
            min-height:100vh;
            transition:margin-left .3s ease;
            padding:30px;
        }

        .main-content.expanded{
            margin-left:var(--sidebar-mini-width);
        }

        .page-card{
            background:rgba(255,255,255,.82);
            border:1px solid #e7ebf1;
            border-radius:24px;
            padding:28px;
            box-shadow:var(--shadow-sm);
        }

        /* topbar state when collapsed */
        body.sidebar-collapsed .brand-logo{
            height:44px;
        }

        body.sidebar-collapsed .brand-title{
            opacity:1;
            transform:none;
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar{
            width:6px;
        }

        .sidebar::-webkit-scrollbar-thumb{
            background:rgba(255,255,255,.20);
            border-radius:10px;
        }

        /* =======================
           MOBILE
        ======================= */
        @media (max-width: 991.98px){
            .sidebar{
                transform:translateX(-100%);
                width:var(--sidebar-width);
                padding:20px 14px 24px;
            }

            .sidebar.show{
                transform:translateX(0);
            }

            .sidebar.collapsed{
                width:var(--sidebar-width);
                transform:translateX(-100%);
            }

            .sidebar.collapsed .sidebar-title,
            .sidebar.collapsed .nav-left span,
            .sidebar.collapsed .submenu-toggle-icon{
                display:initial !important;
            }

            .sidebar.collapsed .submenu{
                display:none !important;
            }

            .main-content,
            .main-content.expanded{
                margin-left:0;
                padding:18px;
            }

            .topbar{
                padding:0 14px;
            }

            .brand-title{
                font-size:18px;
            }

            .brand-logo{
                height:42px;
            }
        }

        @media (max-width: 767.98px){
            .user-pill{
                padding:8px 14px;
                font-size:14px;
            }

            .logout-btn{
                padding:10px 14px;
                font-size:14px;
            }

            .topbar-right{
                gap:8px;
            }

            .main-content{
                padding:14px;
            }

            .page-card{
                padding:18px;
                border-radius:18px;
            }
        }

        @media (max-width: 575.98px){
            .brand-title{
                display:none;
            }

            .user-pill span{
                display:none;
            }

            .user-pill{
                padding:10px 12px;
            }

            .logout-btn span{
                display:none;
            }

            .logout-btn{
                padding:10px 12px;
            }
        }
    </style>
</head>
<body>

    <header class="topbar">
        <div class="topbar-inner">

            <div class="topbar-left">
                <div class="brand-wrap">
                    <a href="{{ url('/') }}" class="brand-link">
                        <img src="{{ asset('storage/logo/sc1.png') }}" alt="Logo" class="brand-logo">
                        <span class="brand-title">Logo</span>
                    </a>
                </div>

                <button type="button" class="menu-toggle" id="menuToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <div class="topbar-right">
                @auth
                    <div class="user-pill">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                @endauth
            </div>

        </div>
    </header>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-title">Main Menu</div>

        <a href="{{ route('dashboard') }}" class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <div class="nav-left">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </div>
        </a>

        @php
            $roleId = (int) (
                $roleId
                ?? (is_object($role) ? ($role->role ?? 0) : ($role ?? (auth()->user()->role ?? 0)))
            );
        @endphp

        @php $userActive = request()->routeIs('register*'); @endphp
        @if(in_array($roleId, [1, 2, 17], true))
            <div class="menu-parent {{ $userActive ? 'active-parent' : '' }}" data-target="userMenu">
                <div class="nav-left">
                    <i class="bi bi-people"></i>
                    <span>User</span>
                </div>
                <i class="bi bi-chevron-down submenu-toggle-icon"></i>
            </div>
            <div class="submenu {{ $userActive ? 'show' : '' }}" id="userMenu">
                <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i> Add User
                </a>
            </div>
        @endif

        @php $accountsActive = request()->routeIs('challan*'); @endphp
        @if(in_array($roleId, [1, 2, 10, 17], true))
            <div class="menu-parent {{ $accountsActive ? 'active-parent' : '' }}" data-target="accountsMenu">
                <div class="nav-left">
                    <i class="bi bi-wallet2"></i>
                    <span>Accounts</span>
                </div>
                <i class="bi bi-chevron-down submenu-toggle-icon"></i>
            </div>
            <div class="submenu {{ $accountsActive ? 'show' : '' }}" id="accountsMenu">
                <a href="{{ route('challan') }}" class="{{ request()->routeIs('challan*') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i> Challan
                </a>
            </div>
        @endif

        @php $leadActive = request()->routeIs('crm*'); @endphp
        @if(in_array($roleId, [1, 2, 10], true))
            <div class="menu-parent {{ $leadActive ? 'active-parent' : '' }}" data-target="leadMenu">
                <div class="nav-left">
                    <i class="bi bi-diagram-3"></i>
                    <span>CRM</span>
                </div>
                <i class="bi bi-chevron-down submenu-toggle-icon"></i>
            </div>
            <div class="submenu {{ $leadActive ? 'show' : '' }}" id="leadMenu">
                <a href="{{ route('crm/lead-management') }}" class="{{ request()->routeIs('crm/lead-management*') ? 'active' : '' }}">
                    <i class="bi bi-person-vcard"></i> Lead
                </a>
            </div>
        @endif

        @php
            $canSee = [
                'allenggworkentry'                => in_array($roleId, [1,2,4,18], true),
                'work-entry.index'               => in_array($roleId, [1,2,4,18], true),
                'store-requirement.list'         => in_array($roleId, [1,2,17,18], true),
                'store-requirement.accepted.list'=> in_array($roleId, [1,2,17,18], true),
                'store-dpr.list'                 => in_array($roleId, [1,4,17,18], true),
            ];

            $dprRoutes = array_keys(array_filter($canSee));
            $dprActive = collect($dprRoutes)->contains(fn($r) => request()->routeIs($r));
            $storeActive = collect(['store-requirement.*','store-dpr.*'])->contains(fn($r) => request()->routeIs($r));
        @endphp

        @if(in_array(true, $canSee, true))
            <div class="menu-parent {{ $dprActive ? 'active-parent' : '' }}" data-target="enggMenu">
                <div class="nav-left">
                    <i class="bi bi-journal-text"></i>
                    <span>DPR</span>
                </div>
                <i class="bi bi-chevron-down submenu-toggle-icon"></i>
            </div>

            <div class="submenu {{ $dprActive ? 'show' : '' }}" id="enggMenu">
                @if($canSee['allenggworkentry'])
                    <a href="{{ route('allenggworkentry') }}" class="{{ request()->routeIs('allenggworkentry') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> All Engg Work Entry
                    </a>
                @endif

                @if($canSee['store-requirement.list'] || $canSee['store-requirement.accepted.list'] || $canSee['store-dpr.list'])
                    <div class="menu-parent {{ $storeActive ? 'active-parent' : '' }}" data-target="storeMenu" style="margin-left:12px;width:calc(100% - 12px);">
                        <div class="nav-left">
                            <i class="bi bi-box-seam"></i>
                            <span>Store Manager</span>
                        </div>
                        <i class="bi bi-chevron-down submenu-toggle-icon"></i>
                    </div>

                    <div class="submenu {{ $storeActive ? 'show' : '' }}" id="storeMenu">
                        @if($canSee['store-requirement.list'])
                            <a href="{{ route('store-requirement.list') }}" class="{{ request()->routeIs('store-requirement.*') ? 'active' : '' }}">
                                <i class="bi bi-cart-check"></i> Material Requirement
                            </a>
                        @endif

                        @if($canSee['store-requirement.accepted.list'])
                            <a href="{{ route('store-requirement.accepted.list') }}" class="{{ request()->routeIs('store-requirement.accepted.list') ? 'active' : '' }}">
                                <i class="bi bi-check-square"></i> Accept Material List
                            </a>
                        @endif

                        @if($canSee['store-dpr.list'])
                            <a href="{{ route('store-dpr.list') }}" class="{{ request()->routeIs('store-dpr.*') ? 'active' : '' }}">
                                <i class="bi bi-clipboard-data"></i> Store Manager Report
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @endif

       @php
    $payrollActive = request()->routeIs('payroll.*');

    $canSeePayroll = [
        'payroll.upload.form' => in_array($roleId, [1,2,17], true),
        'payroll.index'       => in_array($roleId, [1,2,17], true),
    ];

    $payrollRoutes = array_keys(array_filter($canSeePayroll));
    $payrollMenuActive = collect($payrollRoutes)->contains(fn($r) => request()->routeIs($r));
@endphp

@if(in_array(true, $canSeePayroll, true))
    <div class="menu-parent {{ $payrollMenuActive ? 'active-parent' : '' }}" data-target="payrollMenu">
        <div class="nav-left">
            <i class="bi bi-cash-stack"></i>
            <span>Payroll</span>
        </div>
        <i class="bi bi-chevron-down submenu-toggle-icon"></i>
    </div>

    <div class="submenu {{ $payrollMenuActive ? 'show' : '' }}" id="payrollMenu">
        @if($canSeePayroll['payroll.upload.form'])
            <a href="{{ route('payroll.upload.form') }}" class="{{ request()->routeIs('payroll.upload.form') ? 'active' : '' }}">
                <i class="bi bi-upload"></i> Upload Excel
            </a>
        @endif

        @if($canSeePayroll['payroll.index'])
            <a href="{{ route('payroll.index') }}" class="{{ request()->routeIs('payroll.index') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Payment Slips
            </a>
        @endif
    </div>
@endif


@php
    $allowAll     = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];
    $allowManager = [1,2,17];

    $canSeeAttendance = [
        'calendar' => in_array($roleId, $allowAll, true),
        'report'   => in_array($roleId, $allowManager, true),
        'manual'   => in_array($roleId, $allowAll, true),
        'accept'   => in_array($roleId, $allowManager, true),
    ];

    $attendanceActive = request()->routeIs('attendance.*');
@endphp


@if(in_array(true, $canSeeAttendance, true))

    <!-- ATTENDANCE MENU -->
    <div class="menu-parent {{ $attendanceActive ? 'active-parent' : '' }}" data-target="attendanceMenu">
        <div class="nav-left">
            <i class="bi bi-clock"></i>
            <span>Attendance</span>
        </div>
        <i class="bi bi-chevron-down submenu-toggle-icon"></i>
    </div>

    <div class="submenu {{ $attendanceActive ? 'show' : '' }}" id="attendanceMenu">

        @if($canSeeAttendance['calendar'])
            <a href="{{ route('attendance.calendar.view') }}"
               class="{{ request()->routeIs('attendance.calendar.view') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Attendance Calendar
            </a>
        @endif

        @if($canSeeAttendance['report'])
            <a href="{{ route('attendance.report') }}"
               class="{{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Daily Login/Logout
            </a>
        @endif

        @if($canSeeAttendance['manual'])
            <a href="{{ route('attendance.manualattendence') }}"
               class="{{ request()->routeIs('attendance.manualattendence') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i> Manual Attendance
            </a>
        @endif

        @if($canSeeAttendance['accept'])
            <a href="{{ route('attendance.acceptattendence') }}"
               class="{{ request()->routeIs('attendance.acceptattendence') ? 'active' : '' }}">
                <i class="bi bi-check2-circle"></i> Accept Attendance
            </a>
        @endif

    </div>

@endif

        @php
            $leaveActive = request()->routeIs('leave.*') || request()->routeIs('hr.leaves.*');
            $canLeave = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];
            $allowManagerLeave = [1,2,17];
        @endphp

        @if(in_array($roleId, $canLeave, true))
            <div class="menu-parent {{ $leaveActive ? 'active-parent' : '' }}" data-target="leaveMenu">
                <div class="nav-left">
                    <i class="bi bi-calendar-x"></i>
                    <span>Leave</span>
                </div>
                <i class="bi bi-chevron-down submenu-toggle-icon"></i>
            </div>

            <div class="submenu {{ $leaveActive ? 'show' : '' }}" id="leaveMenu">
                <a href="{{ route('leave.index') }}" class="{{ request()->routeIs('leave.index') ? 'active' : '' }}">
                    <i class="bi bi-list-ul"></i> My Leaves
                </a>

                @if(in_array($roleId, $allowManagerLeave, true))
                    <a href="{{ route('hr.leaves.index') }}" class="{{ request()->routeIs('hr.leaves.index') ? 'active' : '' }}">
                        <i class="bi bi-check2-square"></i> Respond on Leaves
                    </a>
                @endif
            </div>
        @endif

        @php $paymentActive = request()->routeIs('payments.*'); @endphp
        @if(in_array($roleId, [1, 2, 9, 17], true))
            <div class="menu-parent {{ $paymentActive ? 'active-parent' : '' }}" data-target="paymentMenu">
                <div class="nav-left">
                    <i class="bi bi-cash-coin"></i>
                    <span>Payments</span>
                </div>
                <i class="bi bi-chevron-down submenu-toggle-icon"></i>
            </div>

            <div class="submenu {{ $paymentActive ? 'show' : '' }}" id="paymentMenu">
                <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.index') ? 'active' : '' }}">
                    <i class="bi bi-list-ul"></i> All Payments
                </a>
                <a href="{{ route('payments.create') }}" class="{{ request()->routeIs('payments.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i> New Payment
                </a>
            </div>
        @endif

        @php $letterheadActive = request()->routeIs('letterhead*'); @endphp
        @if(in_array($roleId, [1, 2, 17], true))
            <div class="menu-parent {{ $letterheadActive ? 'active-parent' : '' }}" data-target="letterheadMenu">
                <div class="nav-left">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Letter Head</span>
                </div>
                <i class="bi bi-chevron-down submenu-toggle-icon"></i>
            </div>

            <div class="submenu {{ $letterheadActive ? 'show' : '' }}" id="letterheadMenu">
                <a href="{{ route('letterhead') }}" class="{{ request()->routeIs('letterhead') ? 'active' : '' }}">
                    <i class="bi bi-file-text"></i> Letter Head Details
                </a>
            </div>
        @endif

        @php $poActive = request()->routeIs('po*') || request()->routeIs('showpo'); @endphp
        @if(in_array($roleId, [1, 2, 17, 4], true))
            <div class="menu-parent {{ $poActive ? 'active-parent' : '' }}" data-target="poMenu">
                <div class="nav-left">
                    <i class="bi bi-file-earmark-ruled"></i>
                    <span>PO</span>
                </div>
                <i class="bi bi-chevron-down submenu-toggle-icon"></i>
            </div>

            <div class="submenu {{ $poActive ? 'show' : '' }}" id="poMenu">
                <a href="{{ route('showpo') }}" class="{{ request()->routeIs('showpo') ? 'active' : '' }}">
                    <i class="bi bi-plus-square"></i> Add PO
                </a>
            </div>
        @endif
        @php 
    $invoiceActive = request()->routeIs('invoice*') || request()->routeIs('showinvoice'); 
@endphp



    </aside>

    <div class="main-wrapper">
        <main class="main-content" id="mainContent">
            <div class="page-card">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function isMobile() {
            return window.innerWidth <= 991.98;
        }

        menuToggle.addEventListener('click', function () {
            if (isMobile()) {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                document.body.classList.toggle('sidebar-collapsed');
            }
        });

        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });

        window.addEventListener('resize', function () {
            if (!isMobile()) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            } else {
                document.body.classList.remove('sidebar-collapsed');
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        });

        document.querySelectorAll('.menu-parent').forEach(function(menu) {
            menu.addEventListener('click', function(e) {
                if (sidebar.classList.contains('collapsed') && !isMobile()) {
                    return;
                }

                const targetId = this.getAttribute('data-target');
                const targetMenu = document.getElementById(targetId);

                if (targetMenu) {
                    this.classList.toggle('active-parent');
                    targetMenu.classList.toggle('show');
                }
            });
        });
    </script>
</body>
</html>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    .custom-navbar{
        background:#fff;
        border-bottom:1px solid #e9ecef;
        height:80px;
        padding:0 18px;
        box-shadow:0 4px 20px rgba(0,0,0,0.04);
        position:sticky;
        top:0;
        z-index:1050;
    }

    .custom-navbar .container-fluid{
        height:100%;
    }

    .navbar-left{
        display:flex;
        align-items:center;
        gap:14px;
        min-width:0;
    }

    .sidebar-toggle-btn{
        width:44px;
        height:44px;
        border:none;
        outline:none;
        background:#f4f7fb;
        border-radius:12px;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#1c2c3e;
        font-size:22px;
        transition:all .3s ease;
        flex-shrink:0;
    }

    .sidebar-toggle-btn:hover{
        background:#e9f0f8;
        color:#f25c05;
    }

    .navbar-brand{
        text-decoration:none;
        display:flex;
        align-items:center;
        gap:10px;
        margin:0;
    }

    .brand-logo{
        width:44px;
        height:44px;
        border-radius:12px;
        overflow:hidden;
        background:#f8f9fa;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-shrink:0;
    }

    .brand-logo img{
        max-width:100%;
        max-height:100%;
        object-fit:contain;
    }

    .brand-text{
        font-size:22px;
        font-weight:700;
        color:#1c2c3e;
        letter-spacing:.3px;
        white-space:nowrap;
        margin:0;
    }

    .navbar-right{
        display:flex;
        align-items:center;
        gap:12px;
        margin-left:auto;
    }

    .user-pill{
        display:flex;
        align-items:center;
        gap:8px;
        padding:10px 18px;
        border:1px solid #e4e7ec;
        border-radius:50px;
        background:#fff;
        color:#1c2c3e;
        font-weight:500;
        min-height:46px;
        white-space:nowrap;
    }

    .user-pill i{
        color:#b08d2f;
        font-size:18px;
    }

    .logout-btn{
        padding:10px 20px;
        border-radius:14px;
        border:1px solid #d0d5dd;
        background:#fff;
        color:#1c2c3e;
        font-weight:600;
        transition:all .3s ease;
        min-height:46px;
    }

    .logout-btn:hover{
        background:#1c2c3e;
        color:#fff;
        border-color:#1c2c3e;
    }

    @media (max-width: 991.98px){
        .custom-navbar{
            height:72px;
            padding:0 12px;
        }

        .brand-text{
            font-size:18px;
        }

        .user-pill{
            padding:8px 14px;
        }

        .logout-btn{
            padding:9px 14px;
        }
    }

    @media (max-width: 767.98px){
        .brand-text{
            font-size:16px;
        }

        .brand-logo{
            width:38px;
            height:38px;
        }

        .sidebar-toggle-btn{
            width:40px;
            height:40px;
            font-size:20px;
        }

        .user-pill span{
            display:none;
        }

        .logout-btn span{
            display:none;
        }

        .user-pill{
            padding:9px 11px;
        }

        .logout-btn{
            padding:9px 11px;
        }
    }
</style>

<nav class="custom-navbar navbar">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        <!-- Left -->
        <div class="navbar-left">
            <button class="sidebar-toggle-btn" type="button" id="sidebarToggleBtn">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand" href="{{ url('/') }}">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                </div>
                <span class="brand-text">Admin Panel</span>
            </a>
        </div>

        <!-- Right -->
        <div class="navbar-right">
            @guest
                <a class="btn btn-outline-primary rounded-pill px-3" href="{{ route('login') }}">Login</a>
                <a class="btn btn-primary rounded-pill px-3" href="{{ route('register') }}">Register</a>
            @else
                <div class="user-pill">
                    <i class="bi bi-person-circle"></i>
                    <span>{{ Auth::user()->name }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="btn logout-btn" type="submit">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @endguest
        </div>

    </div>
</nav>

<script>
    document.getElementById('sidebarToggleBtn').addEventListener('click', function () {
        document.body.classList.toggle('sidebar-collapsed');
    });
</script>
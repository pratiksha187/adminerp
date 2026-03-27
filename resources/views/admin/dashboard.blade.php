@extends('layouts.app')

@section('content')

<style>
    .dashboard-wrapper {
        padding: 10px 8px 30px;
    }

    .top-time-box {
        background: #ffffff;
        border-radius: 14px;
        padding: 10px 18px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.06);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #1c2c3e;
    }

    .custom-breadcrumb {
        background: #ffffff;
        border-radius: 16px;
        padding: 14px 20px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.06);
        border: none;
    }

    .hero-card {
        background: linear-gradient(135deg, #1c2c3e 0%, #24384d 60%, #f25c05 140%);
        border-radius: 22px;
        padding: 30px;
        color: #fff;
        box-shadow: 0 15px 35px rgba(28, 44, 62, 0.18);
        position: relative;
        overflow: hidden;
    }

    .hero-card::after {
        content: "";
        position: absolute;
        right: -50px;
        top: -50px;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }

    .hero-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 8px;
        position: relative;
        z-index: 2;
    }

    .hero-subtitle {
        color: rgba(255,255,255,0.85);
        margin-bottom: 0;
        position: relative;
        z-index: 2;
    }

    .hero-icon {
        font-size: 2.8rem;
        color: #fff;
        position: relative;
        z-index: 2;
    }

    .attendance-card {
        border: none;
        border-radius: 22px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .attendance-card .card-body {
        padding: 28px;
    }

    .section-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1c2c3e;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-box {
        background: #f8fafc;
        border: 1px solid #e9eef5;
        border-radius: 18px;
        padding: 18px 20px;
        height: 100%;
        transition: 0.3s ease;
    }

    .status-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.05);
    }

    .status-label {
        font-size: 14px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .status-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        word-break: break-word;
    }

    .location-box {
        margin-top: 18px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 18px;
        padding: 18px 20px;
    }

    .location-box h5 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #9a3412;
        margin-bottom: 10px;
    }

    .location-status {
        margin-bottom: 8px;
        font-weight: 600;
    }

    .coords-box {
        color: #334155;
        font-size: 14px;
        line-height: 1.7;
    }

    .clock-btn {
        width: 100%;
        border: none;
        border-radius: 14px;
        padding: 14px 18px;
        font-size: 1rem;
        font-weight: 700;
        transition: 0.3s ease;
    }

    .clock-in-btn {
        background: linear-gradient(90deg, #16a34a, #15803d);
        color: #fff;
        box-shadow: 0 10px 22px rgba(22, 163, 74, 0.20);
    }

    .clock-in-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(22, 163, 74, 0.28);
    }

    .clock-out-btn {
        background: linear-gradient(90deg, #dc2626, #b91c1c);
        color: #fff;
        box-shadow: 0 10px 22px rgba(220, 38, 38, 0.20);
    }

    .clock-out-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(220, 38, 38, 0.28);
    }

    .done-box {
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        color: #166534;
        font-weight: 700;
        text-align: center;
        border-radius: 16px;
        padding: 16px;
    }

    .alert {
        border-radius: 14px;
    }

    @media (max-width: 768px) {
        .hero-card {
            padding: 22px;
        }

        .hero-title {
            font-size: 1.5rem;
        }

        .attendance-card .card-body {
            padding: 20px;
        }
    }
</style>

<div class="container-fluid dashboard-wrapper">

    <!-- Current Device Time -->
    <div class="d-flex justify-content-end mb-3">
        <div class="top-time-box">
            <i class="bi bi-clock-history"></i>
            <span>Current Time: <span id="deviceTime" class="fw-bold"></span></span>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb custom-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>

    <!-- Welcome Section -->
    <div class="hero-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-9">
                <h2 class="hero-title">Welcome, {{ Auth::user()->name }} 👋</h2>
                <p class="hero-subtitle">Here’s a quick look at your attendance and today’s status.</p>
            </div>
            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                <i class="bi bi-calendar-check hero-icon"></i>
            </div>
        </div>
    </div>

    @php
        use App\Models\Attendance;
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('clock_in', now()->toDateString())
            ->first();
    @endphp

    <!-- Attendance Card -->
    <div class="card attendance-card">
        <div class="card-body">
            <h5 class="section-title">
                <i class="bi bi-clock-history text-primary"></i> Attendance Status
            </h5>

            @if(session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="status-box">
                        <div class="status-label">Clock In</div>
                        <div class="status-value">
                            {{ $attendance?->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('d M Y, h:i:s A') : 'Not yet' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="status-box">
                        <div class="status-label">Clock Out</div>
                        <div class="status-value">
                            {{ $attendance?->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('d M Y, h:i:s A') : 'Not yet' }}
                        </div>
                    </div>
                </div>
            </div>

            @if(!$attendance)
                <form action="{{ route('attendance.clockin') }}" method="POST" id="clockInForm">
                    @csrf

                    <div class="location-box mb-4">
                        <h5><i class="bi bi-geo-alt-fill me-2"></i>Your Current Location</h5>
                        <p id="status" class="location-status text-muted">Fetching location...</p>
                        <div id="coords" class="coords-box"></div>
                    </div>

                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="address" id="address">
                    <input type="hidden" name="device_time" id="clockInTime">

                    <button type="submit" class="clock-btn clock-in-btn">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Clock In
                    </button>
                </form>

            @elseif($attendance && !$attendance->clock_out)

                <form action="{{ route('attendance.clockout') }}" method="POST" id="clockOutForm">
                    @csrf
                    <input type="hidden" name="latitude" id="latitudeOut">
                    <input type="hidden" name="longitude" id="longitudeOut">
                    <input type="hidden" name="device_time" id="clockOutTime">

                    <button type="submit" class="clock-btn clock-out-btn">
                        <i class="bi bi-box-arrow-right me-2"></i> Clock Out
                    </button>
                </form>

            @else
                <div class="done-box">
                    ✅ You have completed today’s attendance.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Device Time Script -->
<script>
    function getFormattedDeviceTime() {
        const now = new Date();
        return now.toLocaleString('en-IN', {
            hour: 'numeric',
            minute: 'numeric',
            hour12: true,
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function getISOTimestamp() {
        return new Date().toISOString();
    }

    document.getElementById('deviceTime').textContent = getFormattedDeviceTime();

    document.getElementById('clockInForm')?.addEventListener('submit', function () {
        document.getElementById('clockInTime').value = getISOTimestamp();
    });

    document.getElementById('clockOutForm')?.addEventListener('submit', function () {
        document.getElementById('clockOutTime').value = getISOTimestamp();
    });
</script>

<!-- Location + Reverse Geocoding -->
<script>
    let userLat = null;
    let userLng = null;

    function getLocation() {
        const status = document.getElementById("status");
        const coords = document.getElementById("coords");

        if (!navigator.geolocation) {
            status.innerText = "❌ Geolocation is not supported by your browser";
            return;
        }

        status.innerText = "📡 Locating...";

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;

                document.getElementById('latitude').value = userLat;
                document.getElementById('longitude').value = userLng;

                status.innerText = "✅ Location found successfully";

                coords.innerHTML = `
                    <div><strong>Latitude:</strong> ${userLat}</div>
                    <div><strong>Longitude:</strong> ${userLng}</div>
                    <div><strong>Accuracy:</strong> ±${position.coords.accuracy} meters</div>
                `;

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${userLat}&lon=${userLng}`);
                    const data = await response.json();

                    if (data && data.display_name) {
                        coords.innerHTML += `<div class="mt-2"><strong>Address:</strong> ${data.display_name}</div>`;
                        document.getElementById('address').value = data.display_name;
                    }
                } catch (err) {
                    coords.innerHTML += `<div class="mt-2 text-danger"><strong>Address lookup failed</strong></div>`;
                }
            },
            (error) => {
                status.innerText = "❌ Unable to retrieve location: " + error.message;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    window.onload = getLocation;

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('clockInForm');
        if (form) {
            form.addEventListener('submit', function() {
                const button = form.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Please wait...';
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        navigator.geolocation.getCurrentPosition(
            pos => {
                const latOut = document.getElementById('latitudeOut');
                const lngOut = document.getElementById('longitudeOut');

                if (latOut) latOut.value = pos.coords.latitude;
                if (lngOut) lngOut.value = pos.coords.longitude;
            },
            err => console.log('Location error: ' + err.message),
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
</script>

@endsection
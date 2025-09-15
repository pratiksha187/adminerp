
@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- 🔁 CURRENT DEVICE TIME -->
    <div class="d-flex justify-content-end mb-3">
        <span class="text-muted fw-medium">Current Time: <span id="deviceTime" class="fw-bold text-dark"></span></span>
    </div>

    <!-- 📌 Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white p-3 rounded shadow-sm">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>

    <!-- 👋 Welcome -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Welcome, {{ Auth::user()->name }} 👋</h2>
            <p class="text-muted">Here’s a quick look at your attendance today.</p>
        </div>
        <div>
            <i class="bi bi-calendar-check-fill text-primary" style="font-size: 2rem;"></i>
        </div>
    </div>

    @php
        use App\Models\Attendance;
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('clock_in', now()->toDateString())
            ->first();
    @endphp

    <!-- 🕘 Attendance Card -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body">
            <h5 class="card-title mb-4 text-primary">
                <i class="bi bi-clock-history me-2"></i> Attendance Status
            </h5>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border d-flex justify-content-between">
                        <span><strong>Clock In</strong></span>
                        <span>
                            {{ $attendance?->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('d M Y, h:i:s A') : 'Not yet' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border d-flex justify-content-between">
                        <span><strong>Clock Out</strong></span>
                        <span>
                            {{ $attendance?->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('d M Y, h:i:s A') : 'Not yet' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ⏰ Clock In / Clock Out --}}
            @if(!$attendance)
                <form action="{{ route('attendance.clockin') }}" method="POST" id="clockInForm">
                    @csrf

                    <div class="mb-3">
                        <h5>📍 Your Current Location</h5>
                        <p id="status" class="text-muted">Fetching location...</p>
                        <p id="coords" class="fw-medium text-dark"></p>
                    </div>

                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="address" id="address">
                    <input type="hidden" name="device_time" id="clockInTime">

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Clock In
                    </button>
                </form>
            @elseif($attendance && !$attendance->clock_out)
                <form action="{{ route('attendance.clockout') }}" method="POST" id="clockOutForm">
                    @csrf
                    <input type="hidden" name="device_time" id="clockOutTime">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-box-arrow-right me-1"></i> Clock Out
                    </button>
                </form>
            @else
                <div class="alert alert-success text-center">
                    ✅ You have completed today’s attendance.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ✅ Device Time Script -->
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

<!-- 📍 Location + Reverse Geocoding -->
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

        status.innerText = "📡 Locating…";

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;

                document.getElementById('latitude').value = userLat;
                document.getElementById('longitude').value = userLng;

                status.innerText = "✅ Location found!";
                coords.innerHTML = `Latitude: ${userLat}<br>
                                    Longitude: ${userLng}<br>
                                    Accuracy: ±${position.coords.accuracy} meters`;

                // 🔁 Reverse Geocoding (OpenStreetMap API)
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${userLat}&lon=${userLng}`);
                    const data = await response.json();

                    if (data && data.display_name) {
                        coords.innerHTML += `<br><strong>Address:</strong> ${data.display_name}`;
                        document.getElementById('address').value = data.display_name;
                    }
                } catch (err) {
                    coords.innerHTML += "<br><strong>Address lookup failed</strong>";
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

    // Auto fetch location on page load
    window.onload = getLocation;

    // Prevent submit if location not available
    document.getElementById('clockInForm')?.addEventListener('submit', function (e) {
        if (!userLat || !userLng) {
            e.preventDefault();
            alert("⚠️ Unable to get your location. Please allow location access.");
        }
    });
</script>
@endsection

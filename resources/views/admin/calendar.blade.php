@extends('layouts.app')

@section('title', 'My Attendance Calendar')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">My Attendance Calendar</h4>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge" style="background:#16a34a">Complete (In–Out)</span>
            <span class="badge" style="background:#f59e0b">Open (No Out)</span>
            <span class="badge text-dark" style="background:#ECFDF5;border:1px solid #86efac;">Present day</span>
        </div>
    </div>

    <div id="attendanceCalendar"></div>
</div>

{{-- FullCalendar --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const el = document.getElementById('attendanceCalendar');

  const calendar = new FullCalendar.Calendar(el, {
    initialView: 'dayGridMonth',
    height: 'auto',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    firstDay: 1,
    nowIndicator: true,
    navLinks: true,
    dayMaxEvents: true,

    // ✅ Use 12-hour formatting (for week/day/list if you ever enable times)
    eventTimeFormat: { hour: 'numeric', minute: '2-digit', hour12: true },

    // ✅ Hide FullCalendar's automatic time everywhere,
    // so you only see the time from your event title.
    displayEventTime: false,

    // If you prefer to hide time only in month view, use this instead:
    // views: { dayGridMonth: { displayEventTime: false, eventDisplay: 'block' } },

    events: {
      url: "{{ route('attendance.calendar.events') }}",
      failure: () => alert('Could not load attendance.')
    },

    // Keep your tooltip, etc.
    eventDidMount(info) {
      const { lat, lng } = info.event.extendedProps || {};
      const tip = [info.event.title, lat ? ('Lat: ' + lat) : '', lng ? ('Lng: ' + lng) : '']
        .filter(Boolean).join('\n');
      info.el.setAttribute('title', tip);
    }
  });

  calendar.render();
});
</script>

@endsection

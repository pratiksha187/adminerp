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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<style>
  /* Force multi-line content in month cells */
  .fc .fc-daygrid-event { padding: 2px 4px; }
  .fc .fc-daygrid-event .fc-event-title,
  .fc .fc-daygrid-event .fc-event-title-container { white-space: normal !important; }

  /* Our custom layout */
  .fc .att-wrap { white-space: normal; }
  .fc .att-line { display: block !important; line-height: 1.2; }
  .fc .att-label { font-weight: 600; margin-right: 4px; }
  .fc .att-dur { font-size: .85em; opacity: .85; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const el = document.getElementById('attendanceCalendar');

  const calendar = new FullCalendar.Calendar(el, {
    initialView: 'dayGridMonth',
    height: 'auto',
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
    firstDay: 1,
    nowIndicator: true,
    navLinks: true,
    dayMaxEvents: true,

    // Hide default time (we render our own)
    displayEventTime: false,

    // Important: in month view, render events as BLOCKS (not dots)
    views: {
      dayGridMonth: {
        eventDisplay: 'block'   // ← this is the key change
      }
    },

    // If you switch to week/day/list, keep 12-hr format
    eventTimeFormat: { hour: 'numeric', minute: '2-digit', hour12: true },

    events: {
      url: "{{ route('attendance.calendar.events') }}",
      failure: () => alert('Could not load attendance.')
    },

    // Put "In" and "Out" on separate rows with custom HTML
    eventContent: function(arg) {
      // Skip background events (present-day highlights)
      const p = arg.event.extendedProps || {};
      if (!p.status) return true;

      const dur = p.durText ? ` <span class="att-dur">(${p.durText})</span>` : '';
      const html =
        `<div class="att-wrap">
           <span class="att-line"><span class="att-label">In</span> ${p.inText}</span>
           <span class="att-line"><span class="att-label">Out</span> ${p.outText}${dur}</span>
         </div>`;

      // Use domNodes to avoid any sanitization surprises
      const wrapper = document.createElement('div');
      wrapper.innerHTML = html;
      return { domNodes: [wrapper.firstChild] };
    },

    // Optional tooltip
    eventDidMount: function(info) {
      const p = info.event.extendedProps || {};
      if (!p.status) return; // ignore background events
      const tip = [
        `In ${p.inText}`,
        `Out ${p.outText}${p.durText ? ' ('+p.durText+')' : ''}`,
        p.lat ? ('Lat: ' + p.lat) : '',
        p.lng ? ('Lng: ' + p.lng) : ''
      ].filter(Boolean).join('\n');
      info.el.setAttribute('title', tip);
    }
  });

  calendar.render();
});
</script>
@endsection

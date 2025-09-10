@extends('layouts.app')

@section('title', 'Attendance Calendar')

@section('content')
<style>
  .att-flags { margin-top: 2px; display: flex; gap: 6px; flex-wrap: wrap; }
  .flag { display:inline-block; padding:1px 6px; border-radius: 999px; font-size: 11px; font-weight:700; }
  .flag-late  { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
  .flag-early { background:#fff3df; color:#7c2d12; border:1px solid #ffe2b9; }
  .flag-ot    { background:#e0ebff; color:#1e3a8a; border:1px solid #bfdbfe; }
</style>

<div class="container-fluid py-4">
  {{-- Header / Hero --}}
  <div class="ck-hero mb-4 rounded-3 shadow-sm p-4 text-white">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <div class="d-flex align-items-center gap-2">
          <span class="ck-hero-icon d-inline-flex align-items-center justify-content-center rounded-circle">
            <i class="bi bi-calendar-week"></i>
          </span>
          <h2 class="mb-0 fw-bold">My Attendance Calendar</h2>
        </div>
        <p class="mb-0 opacity-75 small mt-2">Track your daily punches, holidays & weekly offs at a glance.</p>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button id="btnToday" class="btn btn-light btn-sm">
          <i class="bi bi-compass me-1"></i> Today
        </button>
        <button id="btnPrint" class="btn btn-outline-light btn-sm">
          <i class="bi bi-printer me-1"></i> Print
        </button>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-xl-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="ck-legend d-flex flex-wrap align-items-center gap-2">
            <span class="chip chip-green"><i class="bi bi-check2-circle me-1"></i> Complete (In–Out)</span>
            <span class="chip chip-amber"><i class="bi bi-hourglass-split me-1"></i> Open (No Out)</span>
            <span class="chip chip-mint"><i class="bi bi-square-fill me-1"></i> Present day</span>
            <span class="chip chip-rose"><i class="bi bi-calendar-event me-1"></i> Holiday</span>
            <span class="chip chip-apricot"><i class="bi bi-power me-1"></i> Weekly Off</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-4">
      <div class="row g-3">
        <div class="col-6">
          <div class="ck-stat card border-0 shadow-sm">
            <div class="card-body">
              <div class="ck-stat-label">Hours (month)</div>
              <div class="ck-stat-value" id="statHours">—</div>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="ck-stat card border-0 shadow-sm">
            <div class="card-body">
              <div class="ck-stat-label">Days Present</div>
              <div class="ck-stat-value" id="statPresent">—</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Calendar --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div id="attendanceCalendar"></div>
    </div>
  </div>
</div>

{{-- FullCalendar --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

{{-- Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
  :root{
    --ck-navy:#1c2c3e; --ck-orange:#f25c05;
    --ck-green:#16a34a; --ck-amber:#f59e0b; --ck-mint:#ECFDF5;
    --ck-rose:#FEE2E2; --ck-apricot:#FFEFE3;
    --ck-text:#2a3344; --ck-muted:#6b7280; --ck-card:#ffffff;
    --ck-border:#e9eef7; --ck-bg:#f6f8fc;
  }
  body{ background: var(--ck-bg); color: var(--ck-text); }
  .ck-hero{ background: linear-gradient(135deg, var(--ck-navy) 0%, #0f1f33 100%); }
  .ck-hero-icon{ width: 42px; height: 42px; background: rgba(255,255,255,.15); font-size: 18px; }

  .chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.35rem .6rem; border-radius:999px; font-weight:600; font-size:.85rem; }
  .chip-green{ background:#e8f7ee; color:#136c34; border:1px solid #bce6ca; }
  .chip-amber{ background:#fff3df; color:#915d06; border:1px solid #ffe2b9; }
  .chip-mint{ background:var(--ck-mint); color:#065f46; border:1px solid #86efac; }
  .chip-rose{ background:var(--ck-rose); color:#7f1d1d; border:1px solid #fca5a5; }
  .chip-apricot{ background:var(--ck-apricot); color:#7c2d12; border:1px solid #fdba74; }

  .ck-stat .ck-stat-label{ font-size:.8rem; color:var(--ck-muted); }
  .ck-stat .ck-stat-value{ font-weight:800; font-size:1.25rem; color:var(--ck-navy); }

  .fc{
    --fc-border-color: var(--ck-border);
    --fc-button-text-color:#fff;
    --fc-button-bg-color: var(--ck-navy);
    --fc-button-border-color: var(--ck-navy);
    --fc-button-hover-bg-color:#21334b;
    --fc-button-hover-border-color:#21334b;
    --fc-button-active-bg-color: var(--ck-orange);
    --fc-button-active-border-color: var(--ck-orange);
    --fc-today-bg-color: rgba(242,92,5,.08);
    font-size:14px;
  }
  .fc .fc-toolbar-title{ font-weight:800; color:var(--ck-navy); }
  .fc .fc-daygrid-day-number{ font-weight:700; }
  .fc .fc-daygrid-event{ padding:4px 6px; border-radius:.5rem; box-shadow:0 1px 0 rgba(16,24,40,.04); }
  .fc .fc-daygrid-event .fc-event-title,
  .fc .fc-daygrid-event .fc-event-title-container{ white-space:normal !important; }
  .fc .att-wrap{ white-space:normal; }
  .fc .att-line{ display:block !important; line-height:1.25; }
  .fc .att-label{ font-weight:700; margin-right:4px; color:var(--ck-navy); }
  .fc .att-dur{ font-size:.85em; opacity:.85; }

  .fc .holiday-pill, .fc .weeklyoff-pill { display:inline-block; padding:2px 8px; border-radius:999px; font-size:12px; font-weight:700; color:#fff; }
  .fc .holiday-pill{ background:#ef4444; }
  .fc .weeklyoff-pill{ background:#fb923c; }

  @media print{
    .ck-hero, #btnPrint, #btnToday{ display:none !important; }
    .container-fluid{ padding:0 !important; }
    .card, .shadow-sm{ box-shadow:none !important; }
  }
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

    displayEventTime: false,          // we render our own text
    views: { dayGridMonth: { eventDisplay: 'block' } },
    eventTimeFormat: { hour: 'numeric', minute: '2-digit', hour12: true },

    events: { url: "{{ route('attendance.calendar.events') }}", failure: () => alert('Could not load events.') },

    eventContent(arg) {
      const p = arg.event.extendedProps || {};
      if (!p.kind) return true;

      if (p.kind === 'holiday-label') {
        const n = document.createElement('div');
        n.innerHTML = `<span class="holiday-pill">Holiday: ${p.title}</span>`;
        return { domNodes: [n] };
      }
      if (p.kind === 'weeklyoff-label') {
        const n = document.createElement('div');
        n.innerHTML = `<span class="weeklyoff-pill">${p.title}</span>`;
        return { domNodes: [n] };
      }
      if (p.kind === 'attendance') {
        const dur  = p.durText ? ` <span class="att-dur">(${p.durText})</span>` : '';
        const late  = (p.lateMins  > 0) ? `<span class="flag flag-late">Late In ${p.lateText}</span>` : '';
        const early = (p.earlyMins > 0) ? `<span class="flag flag-early">Early Leave ${p.earlyText}</span>` : '';
        const ot    = (p.otMins    > 0) ? `<span class="flag flag-ot">OT ${p.otText}</span>` : '';

        const wrap = document.createElement('div');
        wrap.className = 'att-wrap';
        wrap.innerHTML = `
          <span class="att-line"><span class="att-label">In</span> ${p.inText}</span>
          <span class="att-line"><span class="att-label">Out</span> ${p.outText}${dur}</span>
          ${(late || early || ot) ? `<div class="att-flags">${late}${early}${ot}</div>` : ''}
        `;
        return { domNodes: [wrap] };
      }
      return true;
    },

    eventDidMount(info) {
      const p = info.event.extendedProps || {};
      if (!p.kind || p.kind !== 'attendance') return;
      const tip = [
        `In ${p.inText}`,
        `Out ${p.outText}${p.durText ? ' ('+p.durText+')' : ''}`,
        p.lateMins  ? `Late In +${p.lateMins}m` : '',
        p.earlyMins ? `Early Leave ${p.earlyMins}m` : '',
        p.otMins    ? `OT +${p.otMins}m` : '',
        p.lat ? ('Lat: ' + p.lat) : '',
        p.lng ? ('Lng: ' + p.lng) : ''
      ].filter(Boolean).join('\n');
      info.el.setAttribute('title', tip);
    },

    eventsSet() {
      // compute monthly hours + present days
      const events = calendar.getEvents();
      let totalMins = 0;
      const presentDates = new Set();

      events.forEach(ev => {
        const p = ev.extendedProps || {};
        if (p.kind === 'attendance' && p.status === 'complete') {
          if (typeof p.durMins === 'number') totalMins += p.durMins;
          else if (p.durText) {
            const m = p.durText.match(/(\d+)h(\d{1,2})m/);
            if (m) totalMins += (+m[1])*60 + (+m[2]);
          }
          const d = ev.start; if (d) presentDates.add(d.toISOString().slice(0,10));
        }
      });

      const hours = Math.floor(totalMins/60);
      const mins  = totalMins%60;
      document.getElementById('statHours').textContent = `${hours}h ${String(mins).padStart(2,'0')}m`;
      document.getElementById('statPresent').textContent = presentDates.size;
    }
  });

  calendar.render();

  document.getElementById('btnToday').addEventListener('click', () => calendar.today());
  document.getElementById('btnPrint').addEventListener('click', () => window.print());
});
</script>
@endsection

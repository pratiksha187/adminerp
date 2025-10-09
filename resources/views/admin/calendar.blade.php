@extends('layouts.app')
@section('title', 'Attendance Calendar')
@section('content')
<style>
   /* General Styles */
   :root {
      --ck-navy: #1c2c3e;
      --ck-orange: #f25c05;
      --ck-green: #16a34a;
      --ck-amber: #f59e0b;
      --ck-mint: #ECFDF5;
      --ck-rose: #FEE2E2;
      --ck-apricot: #FFEFE3;
      --ck-text: #2a3344;
      --ck-muted: #6b7280;
      --ck-card: #ffffff;
      --ck-border: #e9eef7;
      --ck-bg: #f6f8fc;
      --ck-button-hover-bg: #21334b;
      --ck-button-active-bg: var(--ck-orange);
   }

   body {
      font-family: 'Poppins', sans-serif;
      background: var(--ck-bg);
      color: var(--ck-text);
   }

   /* Hero Section */
   .ck-hero {
      background: linear-gradient(135deg, var(--ck-navy) 0%, #0f1f33 100%);
      color: #fff;
      padding: 2rem;
      border-radius: 12px;
   }

   .ck-hero h2 {
      font-weight: 700;
   }

   .ck-hero p {
      opacity: 0.75;
      font-size: 0.875rem;
   }

   .ck-hero-icon {
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      font-size: 18px;
   }

   /* Buttons */
   .btn-light, .btn-outline-light {
      border-radius: 8px;
      padding: 0.5rem 1.25rem;
      font-size: 0.875rem;
   }

   .btn-light:hover, .btn-outline-light:hover {
      background-color: var(--ck-button-hover-bg);
   }

   .btn-outline-light {
      border: 1px solid #fff;
   }

   .btn-outline-light:hover {
      background-color: var(--ck-button-active-bg);
      border-color: var(--ck-button-active-bg);
   }

   /* Legend & Stats */
   .ck-legend {
      margin-bottom: 1rem;
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      font-size: 0.875rem;
   }

   .chip {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.35rem 0.75rem;
      border-radius: 999px;
      font-weight: 600;
   }

   .chip-green { background-color: #e8f7ee; color: #136c34; border: 1px solid #bce6ca; }
   .chip-amber { background-color: #fff3df; color: #915d06; border: 1px solid #ffe2b9; }
   .chip-mint { background-color: var(--ck-mint); color: #065f46; border: 1px solid #86efac; }
   .chip-rose { background-color: var(--ck-rose); color: #7f1d1d; border: 1px solid #fca5a5; }
   .chip-apricot { background-color: var(--ck-apricot); color: #7c2d12; border: 1px solid #fdba74; }

   .ck-stat {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
   }

   .ck-stat .ck-stat-label {
      font-size: 0.875rem;
      color: var(--ck-muted);
      margin-bottom: 0.5rem;
   }

   .ck-stat .ck-stat-value {
      font-weight: 800;
      font-size: 1.5rem;
      color: var(--ck-navy);
   }

   /* Calendar Section */
   .card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
   }

   .fc {
      --fc-border-color: var(--ck-border);
      --fc-button-text-color: #fff;
      --fc-button-bg-color: var(--ck-navy);
      --fc-button-border-color: var(--ck-navy);
      --fc-button-hover-bg-color: var(--ck-button-hover-bg);
      --fc-button-active-bg-color: var(--ck-orange);
      --fc-today-bg-color: rgba(242, 92, 5, 0.08);
      font-size: 14px;
   }

   .fc-toolbar-title {
      font-weight: 800;
      color: var(--ck-navy);
   }

   .fc-daygrid-day-number {
      font-weight: 700;
   }

   /* Attending Events Styling */
   .att-flags {
      margin-top: 6px;
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
   }

   .flag {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
   }

   .flag-late { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
   .flag-early { background-color: #fff3df; color: #7c2d12; border: 1px solid #ffe2b9; }
   .flag-ot { background-color: #e0ebff; color: #1e3a8a; border: 1px solid #bfdbfe; }

   /* Print Styles */
   @media print {
      .ck-hero, #btnPrint, #btnToday {
         display: none !important;
      }
      .container-fluid {
         padding: 0 !important;
      }
      .card, .shadow-sm {
         box-shadow: none !important;
      }
   }

</style>

<div class="container-fluid py-4">
   <!-- Header Section -->
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

   <!-- Statistics Section -->
   <div class="row g-3 mb-4">
      <div class="col-12 col-xl-6">
         <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
               <div class="ck-legend d-flex flex-wrap align-items-center gap-2">
                  <span class="chip chip-green"><i class="bi bi-check2-circle me-1"></i> Complete (In–Out)</span>
                  <span class="chip chip-amber"><i class="bi bi-hourglass-split me-1"></i> Open (No Out)</span>
                  <span class="chip chip-mint"><i class="bi bi-square-fill me-1"></i> Present day</span>
                  <span class="chip chip-rose"><i class="bi bi-calendar-event me-1"></i> Holiday</span>
                  <span class="chip chip-apricot"><i class="bi bi-power me-1"></i> Weekly Off</span>
                  <span class="chip" style="background-color:#2563EB;color:#fff;border:1px solid #93c5fd;">
                     <i class="bi bi-calendar-check me-1"></i> C.Off
                  </span>
                  <span class="chip" style="background-color:#3b82f6;color:#fff;border:1px solid #93c5fd;">
                     <i class="bi bi-person-x-fill me-1"></i> Leave (Approved)
                  </span>


               </div>
            </div>
         </div>
      </div>
      <div class="col-12 col-xl-6">
         <div class="row g-3">
            
            <div class="col-3 d-none">
              <div class="ck-stat card border-0 shadow-sm">
                  <div class="card-body">
                    <div class="ck-stat-label">Hours (month)</div>
                    <div class="ck-stat-value" id="statHours">—</div>
                  </div>
              </div>
            </div>

            <div class="col-3">
               <div class="ck-stat card border-0 shadow-sm">
                  <div class="card-body">
                     <div class="ck-stat-label">Days Present</div>
                     <div class="ck-stat-value" id="statPresent">—</div>
                  </div>
               </div>
            </div>
            
            <div class="col-3">
               <div class="ck-stat card border-0 shadow-sm">
                  <div class="card-body">
                     <div class="ck-stat-label">Holidays</div>
                     <div class="ck-stat-value text-danger" id="statHoliday">—</div>
                  </div>
               </div>
            </div>
            <div class="col-3">
               <div class="ck-stat card border-0 shadow-sm">
                  <div class="card-body">
                     <div class="ck-stat-label">Weekly Offs</div>
                     <div class="ck-stat-value text-warning" id="statWeeklyOff">—</div>
                  </div>
               </div>
            </div>
            <div class="col-3">
               <div class="ck-stat card border-0 shadow-sm">
                  <div class="card-body">
                     <div class="ck-stat-label">C.Off Days</div>
                     <div class="ck-stat-value text-primary" id="statCoff">—</div>
                  </div>
               </div>
            </div>

            <div class="col-3">
               <div class="ck-stat card border-0 shadow-sm">
                  <div class="card-body">
                     <div class="ck-stat-label">Total Days Present</div>
                     <div class="ck-stat-value" id="statTotalPresent">—</div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Calendar Section -->
   <div class="card border-0 shadow-sm">
      <div class="card-body">
         <div id="attendanceCalendar"></div>
      </div>
   </div>
</div>

<!-- FullCalendar JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<!-- Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">



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
   
       displayEventTime: false,
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
            const cOff  = p.c_off ? `<span class="flag" style="background-color:#2563EB;color:#fff;border:1px solid #93c5fd;">C.Off</span>` : '';

            const wrap = document.createElement('div');
            wrap.className = 'att-wrap';
            wrap.innerHTML = `
               <span class="att-line"><span class="att-label">In</span> ${p.inText}</span><br>
               <span class="att-line"><span class="att-label">Out</span> ${p.outText}${dur}</span>
               ${(late || early || ot || cOff) ? `<div class="att-flags">${late}${early}${ot}${cOff}</div>` : ''}
            `;
            return { domNodes: [wrap] };
         }
         if (p.kind === 'leave-label') {
            const n = document.createElement('div');
            n.innerHTML = `<span class="flag" 
               style="background-color:#3b82f6;color:#fff;border:1px solid #93c5fd;">
               ${p.title}
            </span>`;
            return { domNodes: [n] };
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
//    eventsSet(info) {
//   const events = calendar.getEvents();
//   let totalMins = 0;
//   let presentDayFraction = 0;
//   let holidayCount = 0;
//   let weeklyOffCount = 0;
//   let leaveCount = 0; // ✅ new counter

//   const currentMonth = calendar.view.currentStart.getMonth();
//   const currentYear  = calendar.view.currentStart.getFullYear();

//   const dayMap = {};

//   events.forEach(ev => {
//     const p = ev.extendedProps || {};
//     const d = ev.start;
//     if (!d) return;

//     // ✅ Only count if event date belongs to the current visible month
//     if (d.getMonth() !== currentMonth || d.getFullYear() !== currentYear) return;

//     if (p.kind === 'attendance' && p.status === 'complete') {
//       let durMins = 0;
//       if (typeof p.durMins === 'number') durMins = p.durMins;
//       else if (p.durText) {
//         const m = p.durText.match(/(\d+)h(\d{1,2})m/);
//         if (m) durMins = (+m[1]) * 60 + (+m[2]);
//       }

//       totalMins += durMins;
//       const dayStr = d.toISOString().slice(0, 10);
//       if (!dayMap[dayStr] || durMins > dayMap[dayStr]) {
//         dayMap[dayStr] = durMins;
//       }
//     }

//     if (p.kind === 'holiday-label') holidayCount++;
//     if (p.kind === 'weeklyoff-label') weeklyOffCount++;

//     // ✅ Handle approved leave as present (not absent)
//     if (p.kind === 'leave-label') leaveCount++;
//   });

//   // ✅ Half-day rule for attendance
//   Object.values(dayMap).forEach(mins => {
//     if (mins < 240) presentDayFraction += 0.5;
//     else presentDayFraction += 1;
//   });

//   // Update stats
//   const hours = Math.floor(totalMins / 60);
//   const mins  = totalMins % 60;
//   document.getElementById('statHours').textContent = `${hours}h ${String(mins).padStart(2,'0')}m`;
//   document.getElementById('statPresent').textContent = presentDayFraction;
//   document.getElementById('statHoliday').textContent = holidayCount;
//   document.getElementById('statWeeklyOff').textContent = weeklyOffCount;

//   // ✅ Total Present = Present + Holiday + WeeklyOff + Leave
//   const totalDays = presentDayFraction + holidayCount + weeklyOffCount + leaveCount;
//   document.getElementById('statTotalPresent').textContent = totalDays;
// }
eventsSet(info) {
  const events = calendar.getEvents();
  let totalMins = 0;
  let presentDayFraction = 0;
  let holidayCount = 0;
  let weeklyOffCount = 0;
  let leaveCount = 0;
  let cOffCount = 0; // ✅ NEW counter for Comp Off

  const currentMonth = calendar.view.currentStart.getMonth();
  const currentYear  = calendar.view.currentStart.getFullYear();

  const dayMap = {};

  events.forEach(ev => {
    const p = ev.extendedProps || {};
    const d = ev.start;
    if (!d) return;

    // ✅ Only count if event belongs to the visible month
    if (d.getMonth() !== currentMonth || d.getFullYear() !== currentYear) return;

    if (p.kind === 'attendance' && p.status === 'complete') {
      let durMins = 0;
      if (typeof p.durMins === 'number') durMins = p.durMins;
      else if (p.durText) {
        const m = p.durText.match(/(\d+)h(\d{1,2})m/);
        if (m) durMins = (+m[1]) * 60 + (+m[2]);
      }

      totalMins += durMins;
      const dayStr = d.toISOString().slice(0, 10);
      if (!dayMap[dayStr] || durMins > dayMap[dayStr]) {
        dayMap[dayStr] = durMins;
      }

      // ✅ Count as Comp Off if attendance has c_off true
      if (p.c_off) cOffCount++;
    }

    if (p.kind === 'holiday-label') holidayCount++;
    if (p.kind === 'weeklyoff-label') weeklyOffCount++;
    if (p.kind === 'leave-label') leaveCount++;
  });

  // ✅ Half-day rule
  Object.values(dayMap).forEach(mins => {
    if (mins < 240) presentDayFraction += 0.5;
    else presentDayFraction += 1;
  });

  // ✅ Update stats
  const hours = Math.floor(totalMins / 60);
  const mins  = totalMins % 60;
  document.getElementById('statHours').textContent = `${hours}h ${String(mins).padStart(2,'0')}m`;
  document.getElementById('statPresent').textContent = presentDayFraction;
  document.getElementById('statHoliday').textContent = holidayCount;
  document.getElementById('statWeeklyOff').textContent = weeklyOffCount;

  // ✅ Show Comp Off count
  document.getElementById('statCoff').textContent = cOffCount;

  // ✅ Total Present = Present + Holiday + WeeklyOff + Leave + C.Off
  const totalDays = presentDayFraction + holidayCount + weeklyOffCount + leaveCount + cOffCount;
  document.getElementById('statTotalPresent').textContent = totalDays;
}


     });
   
     calendar.render();
   
     document.getElementById('btnToday').addEventListener('click', () => calendar.today());
     document.getElementById('btnPrint').addEventListener('click', () => window.print());
   });
</script>
@endsection
@extends('layouts.app')

@section('content')

{{-- Keep your global CSS/JS in layout. Only DataTables JS is loaded here per your snippet --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
  /* ========== Slate tokens ========== */
  :root{
    --bg:#f4f6f9;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#64748b;
    --brand:#475569;   /* slate */
    --brand-2:#334155;
    --head-bg:#f1f5f9;
    --row-alt:#f8fafc;
    --hover:#eef2f7;
    --border:#e5e7eb;
  }
  body{ background:var(--bg); }

  /* ========== Card & header ========== */
  .card{ border:1px solid var(--border); border-radius:14px; background:var(--card); }
  .card.shadow-sm{ box-shadow:0 10px 30px rgba(2,6,23,.05)!important; }

  .section-header{
    background:linear-gradient(135deg, rgba(71,85,105,.95), rgba(71,85,105,.75));
    color:#fff; border-radius:12px; padding:16px 18px;
    display:flex; align-items:center; justify-content:space-between; gap:.75rem;
  }
  .section-header h5{ margin:0; font-weight:800; letter-spacing:.2px; }
  .legend .badge{ opacity:.9 }

  /* ========== Table polish ========== */
  .table{ border-color:var(--border)!important; }
  .table thead th{
    background:var(--head-bg)!important; color:var(--ink);
    border-bottom:1px solid var(--border)!important;
    position:sticky; top:0; z-index:1;
  }
  .table tbody tr:nth-child(odd){ background:var(--row-alt); }
  .table tbody tr:hover{ background:var(--hover); }

  /* DataTables controls */
  .dataTables_wrapper .dataTables_length select,
  .dataTables_wrapper .dataTables_filter input{
    border:1px solid var(--border); border-radius:10px; height:38px; padding:.375rem .75rem;
  }
  .dataTables_wrapper .dataTables_filter label{ color:var(--muted); }
  .dataTables_wrapper .dataTables_paginate .paginate_button{
    border:1px solid var(--border)!important; border-radius:8px!important; padding:.25rem .6rem!important;
    color:var(--brand-2)!important; margin:0 .15rem!important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current{
    background:var(--brand)!important; color:#fff!important; border-color:var(--brand)!important;
  }

  /* ========== Action button (keep your btn-dark but make it pretty) ========== */
  .dropdown .btn.btn-dark{
    background:linear-gradient(180deg, #475569, #334155);
    border:1px solid #334155;
    border-radius:10px;
  }
  .dropdown .btn.btn-dark:hover{
    background:linear-gradient(180deg, #4b5563, #334155);
  }
  .dropdown-menu{
    border-radius:12px; border:1px solid var(--border);
    box-shadow:0 12px 30px rgba(2,6,23,.12);
  }
  .dropdown-item{ padding:.45rem .9rem }
  .dropdown-item:hover{ background:var(--head-bg); color:var(--brand-2) }
</style>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="section-header">
      <h5 class="mb-0">Manual Attendance Requests</h5>
      <div class="legend d-flex align-items-center gap-2">
        <span class="badge bg-secondary">Pending</span>
        <span class="badge bg-success">Accepted</span>
        <span class="badge bg-danger">Rejected</span>
      </div>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table id="attendanceTable" class="table table-bordered table-hover align-middle w-100">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>user_id</th>
              <th>Date</th>
              <th>Clock In</th>
              <th>Clock Out</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
  const table = $('#attendanceTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("attendance.manual.data") }}',
    columns: [
      { data: 'id' },
      { data: 'user_id' },
      { data: 'date' },
      { data: 'clock_in' },
      { data: 'clock_out' },
      {
        data: 'status',
        render: function (data) {
          let label = 'Pending', cls = 'secondary';
          if (data == 1) { label = 'Accepted'; cls = 'success'; }
          else if (data == 2) { label = 'Rejected'; cls = 'danger'; }
          return `<span class="badge bg-${cls}">${label}</span>`;
        }
      },
      {
        data: null,
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          if (row.status == 0) {
            return `
              <div class="dropdown">
                <button class="btn btn-sm btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Action
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item action-btn" href="#" data-id="${row.id}" data-action="accept">Accept</a></li>
                  <li><a class="dropdown-item action-btn" href="#" data-id="${row.id}" data-action="reject">Reject</a></li>
                </ul>
              </div>`;
          } else {
            return `<span class="text-muted">No Action</span>`;
          }
        }
      }
    ],
    order: [[2, 'desc']] // default: newest date first (visual only)
  });

  // Ensure Bootstrap dropdowns re-init after each draw (if Bootstrap JS is in your layout)
  table.on('draw', function () {
    $('[data-bs-toggle="dropdown"]').each(function () {
      new bootstrap.Dropdown(this);
    });
  });

  // Delegate dropdown actions (unchanged)
  $('#attendanceTable').on('click', '.action-btn', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let action = $(this).data('action');

    if (confirm(`Are you sure you want to ${action} this request?`)) {
      $.post('{{ route("attendance.manual.action") }}', {
        _token: '{{ csrf_token() }}',
        id: id,
        action: action
      }, function () {
        table.ajax.reload(null, false);
      }).fail(function () {
        alert('An error occurred. Please try again.');
      });
    }
  });
});
</script>

@endsection

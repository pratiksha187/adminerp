@extends('layouts.app')

@section('content')

<!-- ✅ Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- ✅ DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<!-- ✅ jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- ✅ Bootstrap Bundle JS (includes Popper.js for dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
  /* =================== Slate design tokens =================== */
  :root{
    --bg:#f4f6f9;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#64748b;
    --brand:#475569;     /* slate */
    --brand-2:#334155;
    --head-bg:#f1f5f9;   /* table head bg */
    --row-alt:#f8fafc;
    --hover:#eef2f7;
    --border:#e5e7eb;
  }
  body{ background:var(--bg); }

  /* =================== Cards & headers =================== */
  .card{ border:1px solid var(--border); border-radius:14px; background:var(--card); }
  .card.shadow-sm{ box-shadow:0 10px 30px rgba(2,6,23,.05)!important; }

  .section-header{
    background: linear-gradient(135deg, rgba(71,85,105,.95), rgba(71,85,105,.75));
    color:#fff; border-radius:12px; padding:16px 18px;
    display:flex; align-items:center; gap:.75rem;
  }
  .section-header .icon{
    width:44px; height:44px; border-radius:10px;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
    display:flex; align-items:center; justify-content:center;
  }
  .section-header h5{ margin:0; font-weight:800; letter-spacing:.2px; }

  /* =================== Form polish =================== */
  .form-label{ font-weight:600; color:var(--ink) }
  .form-control, .form-select{
    border:1px solid var(--border); border-radius:10px;
  }
  .form-control:focus, .form-select:focus{
    border-color:var(--brand);
    box-shadow:0 0 0 .25rem rgba(71,85,105,.15);
  }
  .input-group-text{
    border-radius:10px 0 0 10px; background:var(--head-bg); color:var(--brand-2);
    border-color:var(--border);
  }
  .btn-slate{
    background: linear-gradient(180deg, #475569, #334155);
    color:#fff; border:1px solid #334155; border-radius:10px;
  }
  .btn-slate:hover{ background: linear-gradient(180deg, #4b5563, #334155); color:#fff; }

  /* =================== DataTables polish =================== */
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
  .table{ border-color:var(--border)!important; }
  .table thead th{
    background:var(--head-bg)!important; color:var(--ink);
    border-bottom:1px solid var(--border)!important;
    position:sticky; top:0; z-index:1;
  }
  .table tbody tr:nth-child(odd){ background:var(--row-alt); }
  .table tbody tr:hover{ background:var(--hover); }

  /* Status legend (optional) */
  .legend .badge{ opacity:.85 }
</style>

<div class="container py-4">

  <!-- =================== Manual Attendance Form =================== -->
  <div class="card mb-4 shadow-sm">
    <div class="section-header">
      <div class="icon"><i class="bi bi-fingerprint fs-5"></i></div>
      <h5 class="mb-0">Manual Attendance Entry</h5>
    </div>

    <div class="card-body">
      <form action="{{ route('attendance.manual') }}" method="POST">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Date</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
              <input type="date" name="date" class="form-control" required>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Clock In</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
              <input type="time" name="manual_clock_in" class="form-control" required>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Clock Out</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-clock"></i></span>
              <input type="time" name="manual_clock_out" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="mt-3">
          <button class="btn btn-slate w-100" type="submit">
            <i class="bi bi-save me-1"></i> Submit Attendance
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- =================== Attendance DataTable =================== -->
  <div class="card shadow-sm">
    <div class="section-header">
      <div class="icon"><i class="bi bi-journal-check fs-5"></i></div>
      <h5 class="mb-0">Manual Attendance Requests</h5>
    </div>

    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 legend">
        <small class="text-muted">Legend:</small>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-secondary">Pending</span>
          <span class="badge bg-success">Accepted</span>
          <span class="badge bg-danger">Rejected</span>
        </div>
      </div>

      <div class="table-responsive">
        <table id="attendanceTable" class="table table-bordered table-hover align-middle w-100">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>User</th>
              <th>Date</th>
              <th>Clock In</th>
              <th>Clock Out</th>
              <th>Status</th>
              {{-- <th>Action</th> --}}
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- =================== DataTables Initialization + Dropdown Handling =================== -->
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
      }
      // If you decide to re-enable actions, the existing delegate below already works.
    ],
    order: [[2, 'desc']] // sort by Date descending by default (optional visual tweak)
  });

  // Initialize dropdowns (if you add an Action column later)
  table.on('draw', function () {
    $('[data-bs-toggle="dropdown"]').each(function () {
      new bootstrap.Dropdown(this);
    });
  });

  // Handle dropdown actions with event delegation (kept as-is)
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

@extends('layouts.app')

@section('content')
<style>
  /* ===== Slate tokens ===== */
  :root{
    --bg:#f4f6f9;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#64748b;
    --brand:#475569;     /* slate */
    --brand-2:#334155;
    --border:#e5e7eb;
    --head:#f1f5f9;
    --hover:#eef2f7;
    --success:#16a34a;
    --primary:#2563eb;
  }

  body{ background:var(--bg); }

  /* ===== Card & header ===== */
  .rounded-4{ border-radius:16px!important; }
  .card{ border:1px solid var(--border); background:var(--card); }
  .card.shadow-sm{ box-shadow:0 10px 30px rgba(2,6,23,.06)!important; }

  .card-header{
    background: linear-gradient(135deg, rgba(71,85,105,.98), rgba(71,85,105,.78))!important; /* slate gradient */
    border-bottom:1px solid rgba(255,255,255,.15);
    padding:14px 16px;
  }
  .card-header h4{ font-weight:800; letter-spacing:.2px }
  .btn-light.btn-sm{ border-radius:10px }

  /* ===== Body ===== */
  .card-body{ background:var(--head); border-top:1px solid var(--border) }
  .form-label{ font-weight:600; color:var(--ink) }
  .form-control, .form-select{
    border:1px solid var(--border); border-radius:10px;
  }
  .form-control:focus, .form-select:focus{
    border-color:var(--brand);
    box-shadow:0 0 0 .25rem rgba(71,85,105,.15);
  }
  .input-group-text{
    background:var(--head); color:var(--brand-2); border:1px solid var(--border);
    border-right:0; border-radius:10px 0 0 10px;
  }
  .input-group .form-control{ border-left:0; border-radius:0 10px 10px 0; }

  /* ===== Buttons ===== */
  .btn-primary{
    background: linear-gradient(180deg, #2563eb, #1d4ed8);
    border-color:#1e40af; border-radius:12px; font-weight:700;
  }
  .btn-primary:hover{ background: linear-gradient(180deg, #1d4ed8, #1e40af); }
  .btn-primary[disabled]{ opacity:.85 }

  /* ===== Small helpers ===== */
  .hint{ color:var(--muted); font-size:.85rem }
  .alert{ border-radius:12px }
</style>

<div class="container py-4">
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-header text-white d-flex justify-content-between align-items-center rounded-top-4">
      <h4 class="mb-0">💼 Generate Employee Payment</h4>
      <a href="{{ route('payments.index') }}" class="btn btn-light btn-sm shadow-sm">
        📄 View All Payments
      </a>
    </div>

    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
      @endif

      <form method="POST" action="{{ route('payments.generate') }}" class="row g-4" id="generatePaymentForm">
        @csrf

        <!-- Employee -->
        <div class="col-md-4">
          <label for="user_id" class="form-label fw-semibold">Select Employee</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
            <select class="form-select" name="user_id" required>
              <option value="">-- Choose Employee --</option>
              @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="hint mt-1">Choose the employee for whom you want to generate salary.</div>
        </div>

        <!-- From Date -->
        <div class="col-md-4">
          <label for="from_date" class="form-label fw-semibold">From Date</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
            <input type="date" name="from_date" class="form-control" required>
          </div>
          <div class="hint mt-1">Start of the salary period.</div>
        </div>

        <!-- To Date -->
        <div class="col-md-4">
          <label for="to_date" class="form-label fw-semibold">To Date</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-calendar2-week"></i></span>
            <input type="date" name="to_date" class="form-control" required>
          </div>
          <div class="hint mt-1">End of the salary period.</div>
        </div>

        <div class="col-12 text-end mt-2">
          <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm" id="btnGenerate">
            <span class="btn-label">💰 Generate Payment</span>
            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Tiny UX: prevent double-submit, show spinner (no backend change) -->
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('generatePaymentForm');
    const btn  = document.getElementById('btnGenerate');
    const spin = btn.querySelector('.spinner-border');

    form.addEventListener('submit', function(){
      btn.setAttribute('disabled', 'disabled');
      spin.classList.remove('d-none');
    });
  });
</script>
@endsection

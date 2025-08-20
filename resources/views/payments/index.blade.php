@extends('layouts.app')

@section('content')
<style>
  /* ===== Slate tokens ===== */
  :root{
    --bg:#f4f6f9;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#64748b;
    --brand:#475569;    /* slate */
    --brand-2:#334155;
    --head-bg:#f1f5f9;
    --row-alt:#f8fafc;
    --hover:#eef2f7;
    --border:#e5e7eb;
    --success:#16a34a;
  }

  .card{ border:1px solid var(--border); border-radius:14px; background:var(--card); }
  .card.shadow-sm{ box-shadow:0 10px 30px rgba(2,6,23,.05)!important; }

  /* Top bar inside card */
  .section-header{
    background: linear-gradient(135deg, rgba(71,85,105,.95), rgba(71,85,105,.75));
    color:#fff; border-radius:12px; padding:16px 18px;
    display:flex; align-items:center; justify-content:space-between; gap:1rem;
    margin-bottom:1rem;
  }
  .section-header h2{
    margin:0; font-weight:800; letter-spacing:.2px; display:flex; align-items:center; gap:.6rem;
  }
  .btn-export{
    background:#22c55e; border-color:#16a34a; color:#fff; border-radius:10px;
  }
  .btn-export:hover{ background:#16a34a; color:#fff; }

  /* Table polish */
  .table-salary{ border-color:var(--border)!important; }
  .table-salary thead th{
    background:var(--head-bg)!important; color:var(--ink);
    border-bottom:1px solid var(--border)!important;
    position:sticky; top:0; z-index:1;
  }
  .table-salary tbody tr:nth-child(odd){ background:var(--row-alt); }
  .table-salary tbody tr:hover{ background:var(--hover); }
  .table-salary td, .table-salary th{ vertical-align:middle; }
  .num{ text-align:right; font-variant-numeric: tabular-nums; }
  .amount{ font-weight:600; }

  /* Subtle chip for dates */
  .chip{
    display:inline-block; padding:.15rem .5rem; border-radius:999px;
    background:#eef2f7; border:1px solid var(--border); color:var(--brand-2); font-size:.85rem;
  }

  /* Empty state row */
  .empty-row td{
    padding:2.25rem 1rem; text-align:center; color:var(--muted);
    background:linear-gradient(180deg, #ffffff, #fafcff);
  }
</style>

<div class="card p-3 p-md-4 shadow-sm">
  <div class="container">

    <!-- Header -->
    <div class="section-header">
      <h2 class="mb-0">
        <i class="bi bi-cash-stack"></i> Salary Payments
      </h2>
      <a href="{{ route('payments.export') }}" class="btn btn-export">
        ⬇️ Download CSV
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover table-salary">
        <thead>
          <tr>
            <th>Employee</th>
            <th>From</th>
            <th>To</th>
            <th class="num">Present Days</th>
            <th class="num">Gross Salary</th>
            <th class="num">Gross Payable</th>
            <th class="num">Deductions</th>
            <th class="num">Net Payable</th>
            <th>Generated On</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $payment)
            <tr>
              <td>{{ $payment->user->name }}</td>
              <td><span class="chip">{{ $payment->from_date }}</span></td>
              <td><span class="chip">{{ $payment->to_date }}</span></td>
              <td class="num">{{ $payment->present_days }}</td>
              <td class="num">{{ $payment->gross_salary }}</td>
              <td class="num">{{ $payment->gross_payable }}</td>
              <td class="num">{{ $payment->total_deduction }}</td>
              <td class="num amount">{{ $payment->net_payable }}</td>
              <td><span class="chip">{{ $payment->created_at->format('d M Y') }}</span></td>
            </tr>
          @empty
            <tr class="empty-row">
              <td colspan="9">
                <i class="bi bi-inbox"></i> No payments found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>
</div>
@endsection

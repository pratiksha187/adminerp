@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
  /* -------- Slate palette tokens -------- */
  :root{
    --bg:#f4f6f9;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#64748b;
    --border:#e5e7eb;
    --head:#f1f5f9;
    --row-alt:#f8fafc;
    --hover:#eef2f7;
    --brand:#475569;      /* slate */
    --brand-dark:#334155; /* slate deep */
  }
  body{ background:var(--bg); }

  /* -------- Shell -------- */
  .letterhead-container{ max-width:1100px; margin:30px auto; }
  .card-custom{
    background:var(--card); border-radius:14px; border:1px solid var(--border);
    box-shadow:0 10px 30px rgba(2,6,23,.08); padding:18px;
  }

  /* -------- Header bar -------- */
  .section-header{
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color:#fff; border-radius:12px; padding:14px 16px;
    display:flex; align-items:center; justify-content:space-between; gap:1rem;
    margin-bottom:1rem;
  }
  .section-header h3{ margin:0; font-weight:800; letter-spacing:.2px; display:flex; align-items:center; gap:.6rem; }

  /* Buttons */
  .btn-primary{
    background: linear-gradient(180deg, #475569, #334155);
    border-color:#334155; border-radius:10px;
  }
  .btn-primary:hover{ background: linear-gradient(180deg, #4b5563, #334155); border-color:#334155; }
  .btn-secondary{ border-radius:10px; }

  /* -------- Table polish -------- */
  .table{ border-color:var(--border)!important; }
  .table thead th{
    background:var(--head)!important; color:#495057;
    border-bottom:1px solid var(--border)!important;
    position:sticky; top:0; z-index:1;
  }
  .table tbody tr:nth-child(odd){ background:var(--row-alt); }
  .table tbody tr:hover{ background:var(--hover); }
  .table td, .table th{ vertical-align:middle; }

  /* Chips for date/ref */
  .chip{
    display:inline-block; padding:.2rem .55rem; border-radius:999px;
    background:#eef2f7; color:#334155; border:1px solid var(--border); font-size:.85rem;
  }

  /* Empty state */
  .empty-message{
    text-align:center; color:var(--muted); padding:40px;
    background:linear-gradient(180deg, #ffffff, #fbfdff);
    border-radius:10px;
  }

  /* -------- Modal polish -------- */
  .modal-content{ border-radius:14px; border:1px solid var(--border); }
  .modal-header{
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color:#fff; border-top-left-radius:14px; border-top-right-radius:14px;
  }
  .modal-body{ padding:24px; background:#fbfcfe; }
  .form-label{ font-weight:600; color:#495057 }
  .form-control, .form-select{
    border-radius:10px; border:1px solid #ced4da;
  }
  .form-control:focus, .form-select:focus{
    border-color:var(--brand); box-shadow:0 0 0 .25rem rgba(71,85,105,.15);
  }
  .modal-footer{ border-top:1px solid var(--border) }
</style>

<div class="letterhead-container">
  <div class="card card-custom">
    @if(session('success'))
      <div class="alert alert-success mb-3" role="alert">{{ session('success') }}</div>
    @endif

    <!-- Header -->
    <div class="section-header">
      <h3 class="mb-0"><i class="bi bi-file-earmark-text"></i> Letter Head List</h3>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#employeeModal">
        <i class="bi bi-plus-circle me-1"></i> Add Letter Head
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Ref No</th>
            <th>Description</th>
            <!-- <th class="text-center">Action</th> -->
          </tr>
        </thead>
        <tbody>
          @forelse ($letterHeads as $item)
            <tr>
              <td><span class="chip">{{ $item->date }}</span></td>
              <td>{{ $item->name }}</td>
              <td><span class="chip">{{ $item->ref_no }}</span></td>
              <td>{{ $item->description }}</td>
              <!-- <td class="text-center">
                <button class="btn btn-sm btn-danger">
                  <i class="bi bi-trash"></i> Delete
                </button>
              </td> -->
            </tr>
          @empty
            <tr>
              <td colspan="4" class="empty-message">
                <i class="bi bi-inbox me-2"></i>No letter heads added yet. Click <strong>“Add Letter Head”</strong> to start.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content shadow">
      <form action="{{ route('letterhead.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-file-plus me-2"></i>Add Letter Head</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Name of receiver</label>
            <input type="text" name="name" class="form-control" placeholder="Enter receiver name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" placeholder="Short description" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Assigned To</label>
            <select name="assigned_to" class="form-select" required>
              <option value="Pirlpl">Pirlpl</option>
              <option value="Shreeyash">Shreeyash</option>
              <option value="Apurva">Apurva</option>
              <option value="Swaraj">Swaraj</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

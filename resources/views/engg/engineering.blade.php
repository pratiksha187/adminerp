@extends('layouts.app')

@section('content')

<!-- ✅ Select2 + DataTables CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
  /* ===== Slate design tokens ===== */
  :root{
    --bg:#f4f6f9;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#64748b;
    --brand:#475569;          /* slate */
    --brand-2:#334155;
    --ring:#cbd5e1;           /* focus ring */
    --head-bg:#f1f5f9;        /* table head bg */
    --row-alt:#f8fafc;
    --hover:#eef2f7;
    --border:#e5e7eb;
    --success:#16a34a;
    --primary:#2563eb;
  }

  body{ background:var(--bg); }

  /* ===== Page card ===== */
  .card{
    border:1px solid var(--border);
    border-radius: 14px;
    background: var(--card);
  }
  .card.shadow-sm{ box-shadow: 0 10px 30px rgba(2,6,23,.05)!important; }

  /* ===== Header (top title) ===== */
  .header {
    display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;
    background: linear-gradient(135deg, rgba(71,85,105,.95), rgba(71,85,105,.75));
    color:#fff; padding:14px 16px; border-radius:12px;
  }
  .header-icon{
    background: rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
    width:48px; height:48px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
  }
  .header-icon svg{ width:26px; height:26px; fill:#fff; }
  .header-title, .header h4{ font-weight:800; letter-spacing:.2px; margin:0; }

  /* ===== Section titles ===== */
  .card-body > h5{
    font-weight:800; color:var(--brand-2); margin-bottom:.75rem;
    display:flex; align-items:center; gap:.5rem;
    position:relative; padding-left:.75rem;
  }
  .card-body > h5::before{
    content:""; position:absolute; left:0; width:4px; height:1.2em; border-radius:3px; background:var(--brand);
    top:50%; transform:translateY(-50%);
  }

  /* ===== Forms ===== */
  .form-label{ font-weight:600; color:var(--ink) }
  .form-control, .form-select{
    border:1px solid var(--border); border-radius:10px;
  }
  .form-control:focus, .form-select:focus{
    border-color: var(--brand); box-shadow: 0 0 0 .25rem rgba(71,85,105,.15);
  }
  input[readonly].bg-light{ background:#f8fafc!important; border-color:var(--border)!important; }

  /* Dimension fields align right */
  input[name="length"], input[name="breadth"], input[name="height"]{ text-align:right; }

  /* ===== Labour tiles ===== */
  .rounded-circle.bg-opacity-25{ box-shadow: inset 0 0 0 2px rgba(0,0,0,.04); }
  .d-flex.flex-column.align-items-center .form-control{
    border-radius: 12px; text-align:center; font-weight:700;
  }

  /* ===== Select2 (Bootstrap-ish) ===== */
  .select2-container .select2-selection--single{
    height: 38px; border:1px solid var(--border)!important; border-radius:10px!important;
    display:flex; align-items:center;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height:38px; padding-left:12px;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow{ height:36px; right:8px; }
  .select2-dropdown{ border-color:var(--border)!important; border-radius:10px!important; }
  .select2-results__option--highlighted{ background:var(--head-bg)!important; color:var(--brand-2)!important; }

  /* ===== DataTables ===== */
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
    background:var(--head-bg)!important; color:var(--ink); position:sticky; top:0; z-index:1;
    border-bottom:1px solid var(--border)!important;
  }
  .table tbody tr:nth-child(odd){ background:var(--row-alt); }
  .table tbody tr:hover{ background:var(--hover); }

  /* ===== Buttons ===== */
  .btn-primary{
    background: linear-gradient(180deg, #2563eb, #1d4ed8);
    border-color:#1e40af;
  }
  .btn-primary:hover{ background: linear-gradient(180deg, #1d4ed8, #1e40af); }
  .btn-outline-secondary{ border-radius:10px }
  .btn{ border-radius:10px }
</style>

<div class="card p-4 shadow-sm">
  <div class="container my-4">

    <!-- Header -->
    <div class="header mb-4">
      <div class="header-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path d="M12 2a7 7 0 0 0-7 7v2H5v2H3v2h18v-2h-2v-2h.001V9a7 7 0 0 0-7-7zm0 2a5 5 0 0 1 5 5v1H7v-1a5 5 0 0 1 5-5zm-7 9h14v4H5v-4z"/>
        </svg>
      </div>
      <h4 class="mb-0">📘 Daily Work Entry</h4>
    </div>

    <form method="POST" id="workEntryForm">
      @csrf

      <!-- Basic Info -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5>📋 Basic Information</h5>
          <div class="row g-3 mt-2">
            <div class="col-md-3">
              <label class="form-label">Date of Entry</label>
              <input type="date" class="form-control" name="date" value="{{ now()->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Chapter</label>
              <select class="form-select select2" id="chapterSelect" name="chapter_id" required>
                <option value="">Select Chapter</option>
                @foreach($chapters ?? [] as $chapter)
                  <option value="{{ $chapter->id }}">{{ $chapter->chapter_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Description & Quantity -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5>📏 Work Description & Measurements</h5>
          <div class="mb-3 mt-2">
            <label class="form-label">Description of the Item</label>
            <input type="text" name="description" id="description" class="form-control" required>
          </div>

          <div class="row g-3">
            <div class="col-md-2">
              <label class="form-label">Unit</label>
              <select class="form-select select2" name="unit" required>
                <option value="">Select unit</option>
                @foreach($unit ?? [] as $units)
                  <option value="{{ $units->id }}">{{ $units->unit }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Length (L)</label>
              <input type="number" step="0.01" name="length" class="form-control" value="0.00" min="0" required>
            </div>
            <div class="col-md-2">
              <label class="form-label">Breadth (B)</label>
              <input type="number" step="0.01" name="breadth" class="form-control" value="0.00" min="0" required>
            </div>
            <div class="col-md-2">
              <label class="form-label">Height (H)</label>
              <input type="number" step="0.01" name="height" class="form-control" value="0.00" min="0" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Total Quantity</label>
              <input type="text" name="total_quantity" class="form-control bg-light" placeholder="Auto-calculated" readonly>
              <small class="text-muted">Auto-calculated (L × B × H)</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Supervisor -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5>🧑‍🏭 Engginers/Supervision</h5>
          <div class="row mt-2">
            <div class="col-md-6">
              <label class="form-label">Person In-Charge</label>
              <select class="form-select" name="supervisor_id" required>
                <option value="">Select Supervisor</option>
                @foreach($users as $user)
                  <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Labour Count -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5>👷 Labour Count by Trade</h5>
          <div class="row text-center mt-3 g-4">
            @php
              $labourTypes = [
                ['label' => 'Mistry', 'icon' => 'bi-person-gear', 'color' => 'primary'],
                ['label' => 'Male Labour', 'icon' => 'bi-person-fill', 'color' => 'success'],
                ['label' => 'Female Labour', 'icon' => 'bi-person-fill', 'color' => 'danger'],
                ['label' => 'Carpenter', 'icon' => 'bi-hammer', 'color' => 'warning'],
                ['label' => 'Plumber', 'icon' => 'bi-wrench', 'color' => 'info'],
                ['label' => 'Fabricator', 'icon' => 'bi-tools', 'color' => 'danger'],
                ['label' => 'Steel Fitter', 'icon' => 'bi-building', 'color' => 'secondary'],
              ];
            @endphp
            @foreach ($labourTypes as $type)
              <div class="col-6 col-md-3 col-lg-2">
                <div class="d-flex flex-column align-items-center">
                  <div class="rounded-circle bg-{{ $type['color'] }} bg-opacity-25 p-3 mb-2" style="width: 60px; height: 60px;">
                    <i class="bi {{ $type['icon'] }} fs-4 text-{{ $type['color'] }}"></i>
                  </div>
                  <small class="mb-1 fw-semibold">{{ $type['label'] }}</small>
                  <input type="number" name="labour[{{ \Illuminate\Support\Str::slug($type['label'], '_') }}]" class="form-control text-center" min="0" value="0" style="max-width: 80px;">
                </div>
              </div>
            @endforeach
          </div>
          <div class="mt-4 text-end pe-2">
            <h6>Total Labour Count: <span class="fw-bold" id="totalLabour">0</span></h6>
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div class="text-end mb-5">
        <button type="reset" class="btn btn-outline-secondary me-2">🔄 Reset Form</button>
        <button type="submit" class="btn btn-primary">✅ Submit Entry</button>
      </div>
    </form>

    <!-- Recent Entries Table -->
    <div class="card shadow-sm">
      <div class="card-body">
        <h5>📜 Recent Entries</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-hover mt-3" id="entriesTable">
            <thead>
              <tr>
                <th>Sr. No.</th>
                <th>Date</th>
                <th>Chapter</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Labour</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ✅ JS CDN (assumes jQuery is already included in your layout) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function () {

    // Initialize Select2
    $('.select2').select2({
      placeholder: "Select option",
      allowClear: true,
      width: '100%'
    });

    // Auto-calc quantity (L*B*H) + tiny visual feedback
    $('input[name="length"], input[name="breadth"], input[name="height"]').on('input', function () {
      let l = parseFloat($('input[name="length"]').val()) || 0;
      let b = parseFloat($('input[name="breadth"]').val()) || 0;
      let h = parseFloat($('input[name="height"]').val()) || 0;
      const $qty = $('input[name="total_quantity"]');
      $qty.val((l * b * h).toFixed(2));
      $qty.addClass('is-valid'); setTimeout(()=> $qty.removeClass('is-valid'), 350);
    });

    // Total Labour Count
    $(document).on('input', 'input[name^="labour"]', function () {
      let total = 0;
      $('input[name^="labour"]').each(function () {
        total += parseInt($(this).val()) || 0;
      });
      $('#totalLabour').text(total);
    });

    // DataTable
    let table = $('#entriesTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('work-entry.data') }}",
      columns: [
        { data: null, searchable: false, orderable: false },
        { data: 'date' },
        { data: 'chapter_name' },
        { data: 'description' },
        { data: 'total_quantity' },
        { data: 'labour_count' }
      ],
      order: [[1, 'desc']],
      drawCallback: function (settings) {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
          cell.innerHTML = i + 1;
        });
      }
    });

    // Form Submit (same logic)
    $('#workEntryForm').submit(function (e) {
      e.preventDefault();
      let form = $(this);

      $.post("{{ route('work-entry.save') }}", form.serialize())
        .done(function (response) {
          if (response.success) {
            table.ajax.reload(null, false);
            form[0].reset();
            $('#totalLabour').text('0');
            $('input[name="total_quantity"]').val('');
            $('.select2').val('').trigger('change');
            alert('Entry saved successfully!');
          } else {
            alert('Failed to save entry.');
          }
        })
        .fail(function (xhr) {
          if (xhr.status === 422) {
            let errors = xhr.responseJSON.errors;
            alert(Object.values(errors).flat().join("\n"));
          } else {
            alert('An error occurred. Please try again.');
          }
        });
    });
  });
</script>

@endsection

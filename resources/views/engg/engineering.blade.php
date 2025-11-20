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
            <div class="col-md-2" id="tonnage-box" style="display:none;">
                <label class="form-label">Tonnage Value</label>
                <select id="tonnage" class="form-select">
                    <option value="">Select</option>
                    <option value="8">8</option>
                    <option value="10">10</option>
                    <option value="12">12</option>
                    <option value="16">16</option>
                    <option value="20">20</option>
                    <option value="25">25</option>
                </select>
            </div>
            
            <div class="col-md-2" id="length-box">
              <label class="form-label">Length (L)</label>
              <input type="number" step="0.01" id="length" name="length" class="form-control" value="0.00" min="0">
            </div>

            <div class="col-md-2" id="breadth-box">
              <label class="form-label">Breadth (B)</label>
              <input type="number" step="0.01" id="breadth" name="breadth" class="form-control" value="0.00" min="0">
            </div>

            <div class="col-md-2" id="height-box">
              <label class="form-label">Height (H)</label>
              <input type="number" step="0.01" id="height" name="height" class="form-control" value="0.00" min="0">
            </div>

            <div class="col-md-2" id="days-box" style="display:none;">
                <label class="form-label">Days</label>
                <input type="number" step="1" id="days" name="days" class="form-control" value="1" min="1">
            </div>

            <div class="col-md-2" id="intime-box" style="display:none;">
                <label class="form-label">In Time</label>
                <input type="time" id="in_time" name="in_time" class="form-control">
            </div>

            <div class="col-md-2" id="outtime-box" style="display:none;">
                <label class="form-label">Out Time</label>
                <input type="time" id="out_time" name="out_time" class="form-control">
            </div>

            

            <div class="col-md-4">
              <label class="form-label">Total Value</label>
              <input type="text" id="total_qty" name="total_quantity" class="form-control bg-light" readonly>
              <small class="text-muted">Auto-calculated</small>
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
      <div class="card shadow-sm mb-4">
        <div class="card-body">
        <div class="mb-3 mt-2">
              <label class="form-label">Description of work done</label>
              <input type="text" name="description_of_work_done" id="description_of_work_done" class="form-control" required>
      </div>
      </div>
      </div>

      <!-- Submit -->
      <div class="text-end mb-5">
        <button type="reset" class="btn btn-outline-secondary me-2">🔄 Reset Form</button>
        <button type="submit" class="btn btn-primary">✅ Submit Entry</button>
      </div>
    </form>

  

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

   
    // Total Labour Count
    $(document).on('input', 'input[name^="labour"]', function () {
      let total = 0;
      $('input[name^="labour"]').each(function () {
        total += parseInt($(this).val()) || 0;
      });
      $('#totalLabour').text(total);
    });


$("#workEntryForm").submit(function(e){
    e.preventDefault();

    $.post("{{ route('work-entry.save') }}", $(this).serialize())
        .done(res=>{
            if(res.success){
                window.location.href = "{{ route('allenggworkentry') }}";
            } else {
                alert("Failed to save entry.");
            }
        });
});
  });
</script>

<script>
$(document).ready(function () {

    function calculateQty() {

        let unit = $("select[name='unit'] option:selected").text().trim().toLowerCase();

        let L = parseFloat($("#length").val()) || 0;
        let B = parseFloat($("#breadth").val()) || 0;
        let H = parseFloat($("#height").val()) || 0;
        let D = parseFloat($("#days").val()) || 0;

        let inTime = $("#in_time").val();
        let outTime = $("#out_time").val();
        let X = parseFloat($("#tonnage").val()) || 0;

        let qty = 0;

        // ------- Square Metre -------
        if (unit.includes("square") && unit.includes("metre")) {
            qty = L * H;
        }

        // ------- Cubic Metre -------
        else if (unit.includes("cubic") && unit.includes("metre")) {
            qty = L * B * H;
        }

        // ------- Kilogramme -------
        else if (unit.includes("kilogram") || unit === "kg" || unit.includes("kg")) {
            qty = L;
        }

        // ------- Running Metre -------
        else if (unit.includes("running") || unit.includes("rm") || unit.includes("rmt")) {
            qty = L;
        }

        // ------- Day -------
        else if (unit.includes("day")) {
            qty = D;
        }

        // ------- Hour -------
        else if (unit.includes("hour")) {
            if (inTime && outTime) {
                let start = new Date("2000-01-01 " + inTime);
                let end = new Date("2000-01-01 " + outTime);
                let diff = (end - start) / (1000 * 60 * 60);
                qty = diff > 0 ? diff : 0;
            }
        }

        // ------- PER TEST -------
        else if (unit.includes("per test") || unit.includes("test")) {
            qty = L;
        }

        // ------- LITRE (L × B × H / 1000) -------
        else if (
            unit.includes("litre") ||
            unit.includes("liter") ||
            unit.includes("ltr") ||
            unit.includes("lt")
        ) {
            qty = (L * B * H) / 1000;
        }

        // ------- Metric Tonne -------
        else if (unit.includes("metric") || unit.includes("tonne") || unit.includes("mt")) {

            let Q = L;

            if (X > 0) {
                let A = (X * X) / 162;
                let Bval = A * 12;
                let Total = Q * Bval;

                qty = Math.floor((Total / 1000) * 10000) / 10000;
            }
        }

        // ------- Default -------
        else {
            qty = L * B * H;
        }

        $("#total_qty").val(qty.toFixed(3));
    }


    // ================= UNIT VISIBILITY =================
    $("select[name='unit']").on("change", function () {

        let unit = $("select[name='unit'] option:selected").text().trim().toLowerCase();

        $("#length-box, #breadth-box, #height-box, #days-box, #intime-box, #outtime-box, #tonnage-box").hide();


        // ------- Square Metre -------
        if (unit.includes("square") && unit.includes("metre")) {
            $("#length-box").show();
            $("#height-box").show();
        }

        // ------- Cubic Metre -------
        else if (unit.includes("cubic") && unit.includes("metre")) {
            $("#length-box").show();
            $("#breadth-box").show();
            $("#height-box").show();
        }

        // ------- Kilogramme -------
        else if (unit.includes("kilogram") || unit.includes("kg")) {
            $("#length-box").show();
        }

        // ------- Running Metre -------
        else if (unit.includes("running") || unit.includes("rm")) {
            $("#length-box").show();
        }

        // ------- PER TEST -------
        else if (unit.includes("per test") || unit.includes("test")) {
            $("#length-box").show();
        }

        // ------- LITRE (needs L, B, H) -------
        else if (
            unit.includes("litre") ||
            unit.includes("liter") ||
            unit.includes("ltr") ||
            unit.includes("lt")
        ) {
            $("#length-box").show();
            $("#breadth-box").show();
            $("#height-box").show();
        }

        // ------- Day -------
        else if (unit.includes("day")) {
            $("#days-box").show();
        }

        // ------- Hour -------
        else if (unit.includes("hour")) {
            $("#intime-box").show();
            $("#outtime-box").show();
        }

        // ------- Metric Tonne -------
        else if (unit.includes("metric") || unit.includes("tonne") || unit.includes("mt")) {
            $("#length-box").show();
            $("#tonnage-box").show();
        }

        // ------- Default -------
        else {
            $("#length-box").show();
            $("#breadth-box").show();
            $("#height-box").show();
        }

        calculateQty();
    });

    $("#length, #breadth, #height, #days, #in_time, #out_time, #tonnage").on("input change", function () {
        calculateQty();
    });

});
</script>

@endsection

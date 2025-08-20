@extends('layouts.app')

@section('content')
<style>
  /* ---------- Theme tokens (scoped by [data-table-theme]) ---------- */
  /* .table-skin[data-table-theme="gold"]   { --th-accent:#b78f3e; --th-head-bg:#fff7e0; --th-head-ink:#634b16; --th-row:#ffffff; --th-row-alt:#fffdf5; --th-hover:#fff8e8; --th-border:#e9e5d6; --th-chip:#fff3c4; }
  .table-skin[data-table-theme="emerald"]{ --th-accent:#0f766e; --th-head-bg:#ecfdf5; --th-head-ink:#064e3b; --th-row:#ffffff; --th-row-alt:#f6fffb; --th-hover:#ecfdf5; --th-border:#cce7e1; --th-chip:#d1fae5; }
  .table-skin[data-table-theme="indigo"] { --th-accent:#4f46e5; --th-head-bg:#eef2ff; --th-head-ink:#3730a3; --th-row:#ffffff; --th-row-alt:#f7f8ff; --th-hover:#eef2ff; --th-border:#dfe3ff; --th-chip:#e0e7ff; }
  .table-skin[data-table-theme="ruby"]   { --th-accent:#b91c1c; --th-head-bg:#fee2e2; --th-head-ink:#7f1d1d; --th-row:#ffffff; --th-row-alt:#fff7f7; --th-hover:#fee2e2; --th-border:#f5c2c2; --th-chip:#fecaca; }
  .table-skin[data-table-theme="slate"]  { --th-accent:#475569; --th-head-bg:#f1f5f9; --th-head-ink:#0f172a; --th-row:#ffffff; --th-row-alt:#f8fafc; --th-hover:#eef2f7; --th-border:#e5e7eb; --th-chip:#e2e8f0; } */
/* Fixed Slate tokens */
.table-skin{
  --th-accent:#475569;     /* slate foreground */
  --th-head-bg:#f1f5f9;    /* header bg */
  --th-head-ink:#0f172a;   /* header text */
  --th-row:#ffffff;        /* row bg */
  --th-row-alt:#f8fafc;    /* alt row */
  --th-hover:#eef2f7;      /* hover */
  --th-border:#e5e7eb;     /* borders */
  --th-chip:#e2e8f0;       /* chip bg */
}

  body{ background:#f4f6f9; font-family: Arial, sans-serif; }

  /* ---------- Card & header ---------- */
  .card{ border:1px solid var(--th-border, #e5e7eb); border-radius:14px; overflow:hidden; }
  .card-header {
    background: var(--th-accent, #b78f3e);
    border-bottom:1px solid var(--th-border, #e5e7eb);
  }

  /* ---------- Filters toolbar ---------- */
  .table-toolbar{ gap:.75rem; }
  .table-toolbar .form-control, .table-toolbar .form-select{
    border:1px solid var(--th-border); border-radius:10px; height:38px;
  }
  .btn-outline-theme{
    border:1px solid var(--th-accent); color:var(--th-accent);
  }
  .btn-outline-theme:hover{ background:var(--th-accent); color:#fff; }

  /* ---------- Table polish ---------- */
  .table-themed{ margin:0; }
  .table-themed thead th{
    background:var(--th-head-bg) !important;
    color:var(--th-head-ink);
    position:sticky; top:0; z-index:1;
    border-bottom:1px solid var(--th-border) !important;
  }
  .table-themed tbody tr{ background:var(--th-row); }
  .table-themed tbody tr:nth-child(odd){ background:var(--th-row-alt); }
  .table-themed tbody tr:hover{ background:var(--th-hover); }
  .table-themed td, .table-themed th{ padding:.85rem .9rem; border-color:var(--th-border) !important; }

  /* ---------- Chips ---------- */
  .chip{
    display:inline-block; padding:.2rem .5rem; border-radius:999px;
    background:var(--th-chip); border:1px solid var(--th-border); font-size:.82rem;
  }

  /* ---------- Challan print block (unchanged structure, just nicer) ---------- */
  .challan{
    width:420px; max-width:100%; background:#fff; border:1px dashed #111; border-radius:12px; padding:14px;
  }
  .challan-header{ text-align:center; font-weight:bold; font-size:1.05rem }
  .challan-header .company{ font-size:1.2rem; margin-top:4px }
  .challan-row{ display:flex; margin-top:10px }
  .challan-row label{ flex:0 0 120px; font-weight:700 }
  .challan-row .value{ flex:1; border-bottom:1px solid #000; padding-left:5px }
  .challan-footer{ margin-top:20px; display:flex; justify-content:space-between }
  .signature-box{ border-top:1px solid #000; width:140px; text-align:center; margin-top:5px }
</style>

<div class="container py-4 table-skin" id="tableSkin" >
  <!-- Header + Quick actions -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0 text-dark">
      <i class="bi bi-truck me-2"></i>Delivery Challan List
    </h3>
  
  </div>

  <!-- Filters toolbar (client-side only) -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="row table-toolbar">
        <div class="col-md-3">
          <input type="text" id="filterSearch" class="form-control" placeholder="Search challan, party, material, vehicle…">
        </div>
        <div class="col-md-2">
          <input type="date" id="filterFrom" class="form-control" placeholder="From">
        </div>
        <div class="col-md-2">
          <input type="date" id="filterTo" class="form-control" placeholder="To">
        </div>
        <div class="col-md-3">
          <select id="filterParty" class="form-select">
            <option value="">All Parties</option>
          </select>
        </div>
       
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card shadow-sm mb-4">
    <div class="card-header text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="bi bi-truck-front me-2"></i>Delivery Challans</h5>
      <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#challanModal">+ New Challan</button>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-themed">
          <thead>
          <tr>
            <th>Challan No</th>
            <th>Date</th>
            <th>Party</th>
            <th>Material</th>
            <th>Vehicle</th>
            <th style="width:120px;">Actions</th>
          </tr>
          </thead>
          <tbody id="challanTableBody"></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Pagination Links -->
  <div class="mt-3 d-flex justify-content-center" id="paginationLinks"></div>
</div>

<!-- ====== Your existing modals (UNCHANGED) ====== -->
{{-- Create/Edit Modal --}}
<div class="modal fade" id="challanModal" tabindex="-1" aria-labelledby="challanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="challanForm">
        @csrf
        <div class="modal-header text-white" style="background: var(--th-accent, #475569); >
          <h5 class="modal-title" id="challanModalLabel">🚚 Delivery Challan Form</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="alertBox"></div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="date" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Name of Party</label>
              <input type="text" class="form-control" name="party_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Driver Name</label>
              <input type="text" class="form-control" name="driver_name" required>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-12">
              <label class="form-label">Material / M/C</label>
              <div id="materialContainer">
                <div class="input-group mb-2">
                  <input type="text" name="material[]" class="form-control me-2" placeholder="Material" required>
                  <input type="number" name="quantity[]" class="form-control me-2" placeholder="Qty" min="0" step="any" required>
                  <select name="unit[]" id="unit" class="form-select me-2" required>
                    <option value="">Unit</option>
                    <option value="brass">Brass</option>
                    <option value="nos">Nos</option>
                    <option value="cum">CUM</option>
                    <option value="sqmtr">Sq.Mtr</option>
                    <option value="sqfit">Sq.Fit</option>
                    <option value="ltr">Ltr</option>
                    <option value="mt">MT</option>
                    <option value="kg">Kg</option>
                  </select>
                  <button type="button" class="btn btn-success add-material">+</button>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Vehicle No.</label>
              <input type="text" class="form-control" name="vehicle_no">
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <select name="location" id="location" class="form-select mb-2">
                <option value="">Select State</option>
                @foreach ($location as $locations)
                  <option value="{{ $locations->id }}">{{ $locations->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Time</label>
              <input type="text" class="form-control" name="time" placeholder="e.g., 03 to 6 pm">
            </div>
            <div class="col-md-6">
              <label class="form-label">Receiver Signature (Name)</label>
              <input type="text" class="form-control" name="receiver_sign">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Remark</label>
            <textarea type="text" class="form-control" name="remark" placeholder="remark"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-lg">💾 Save Challan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- View Modal (unchanged logic/ids) --}}
<div class="modal fade" id="viewChallanModal" tabindex="-1" aria-labelledby="viewChallanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content px-4 py-3" style="border:53px solid #ffffff; font-size:14px; font-family:'Segoe UI',sans-serif; max-width:500px; margin:auto;">
      <!-- (Your original challan markup retained) -->
      <div class="challan">
        <div class="challan-header text-center mb-3">
          <strong>DELIVERY CHALLAN</strong><br/>
          <div class="company fw-bold fs-5 mt-1">Shreeyash Construction</div>
          <div class="address">Khopoli, Tal- Khalapur, Dist - Raigad</div>
          <div class="contact">Contact No. 9923299301 / 9326216153</div>
        </div>
        <div class="challan-row"><label>Challan No. :</label><div class="value" id="viewChallanNo">—</div></div>
        <div class="challan-row"><label>Date :</label><div class="value" id="viewDate">—</div></div>
        <div class="challan-row"><label>Name Of Party :</label><div class="value" id="viewPartyName">—</div></div>
        <div class="challan-row"><label>Location :</label><div class="value" id="viewLocation">—</div></div>
        <div class="challan-row"><label>Vehicle No. :</label><div class="value" id="viewVehicleNo">—</div></div>
        <div class="challan-row"><label>Driver Name :</label><div class="value" id="viewDriverName">—</div></div>
        <div class="challan-row"><label>Time :</label><div class="value" id="viewTime">—</div></div>
        <div class="challan-row mt-3">
          <label>Materials :</label>
          <div class="value w-100">
            <table class="table table-bordered mt-2">
              <thead class="table-light">
                <tr>
                  <th style="width:40px;">Sr. No.</th>
                  <th>Description</th>
                  <th style="width:120px;">Qty (Unit)</th>
                </tr>
              </thead>
              <tbody id="viewMaterialTable"></tbody>
            </table>
          </div>
        </div>
        <div class="challan-row"><label>Remark :</label><div class="value" id="viewRemark">—</div></div>
        <div class="challan-footer mt-4 d-flex justify-content-between">
          <div><label>Receiver Name :</label><div class="signature-box" id="viewReceiverSign"></div></div>
          <div><label>Driver Name :</label><div class="signature-box" id="viewDriverSign"></div></div>
        </div>
      </div>

      <div class="mt-3">
        <div class="row g-2 align-items-center">
          <div class="col-8">
            <select class="form-select" id="userSelect">
              <option selected disabled>Select User</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}" data-mobile="{{ $user->mobile_no }}">{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-4 text-end">
            <button class="btn btn-primary" id="sendChallanBtn">Send</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- jQuery + Bootstrap JS (keep) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ========= Your original logic, plus client-side filtering/theme (no server changes) ========= */
$(document).ready(function() {
  let challanCache = []; // current page cache
  loadChallans();

  // Theme switcher
  $('#tableTheme').on('change', function(){
    $('#tableSkin').attr('data-table-theme', $(this).val());
  });

  // Filters
  $('#filterSearch, #filterFrom, #filterTo, #filterParty').on('input change', applyFilters);
  $('#btnResetFilters').on('click', function(e){
    e.preventDefault();
    $('#filterSearch').val('');
    $('#filterFrom').val('');
    $('#filterTo').val('');
    $('#filterParty').val('');
    applyFilters();
  });

  // Add material row
  $(document).on('click', '.add-material', function () {
    const tpl = `
      <div class="input-group mb-2">
        <input type="text" name="material[]" class="form-control me-2" placeholder="Material" required>
        <input type="number" name="quantity[]" class="form-control me-2" placeholder="Qty" min="0" step="any" required>
        <select name="unit[]" class="form-select me-2" required>
          <option value="">Unit</option>
          <option value="brass">Brass</option><option value="nos">Nos</option><option value="cum">CUM</option>
          <option value="sqmtr">Sq.Mtr</option><option value="sqfit">Sq.Fit</option><option value="ltr">Ltr</option>
          <option value="mt">MT</option><option value="kg">Kg</option>
        </select>
        <button type="button" class="btn btn-danger remove-material">−</button>
      </div>`;
    $('#materialContainer').append(tpl);
  });
  $(document).on('click', '.remove-material', function(){ $(this).closest('.input-group').remove(); });

  // Pagination click (unchanged)
  $(document).on('click', '.page-link', function(e) {
    e.preventDefault();
    const page = $(this).text();
    loadChallans(page);
  });

  // Original loader (kept) -> now stores cache then applies filters
  function loadChallans(page = 1) {
    $.get("{{ route('challan.list') }}", { page: page }, function(response) {
      challanCache = response.data || [];
      // Build Party filter options
      buildPartyOptions(challanCache);
      // Apply filters to current cache
      applyFilters();

      // Pagination buttons (unchanged)
      let pagination = '';
      if (response.last_page > 1) {
        pagination += '<ul class="pagination">';
        for(let i = 1; i <= response.last_page; i++) {
          pagination += `<li class="page-item ${i === response.current_page ? 'active' : ''}">
              <a href="#" class="page-link">${i}</a>
            </li>`;
        }
        pagination += '</ul>';
      }
      $('#paginationLinks').html(pagination);
    });
  }

  function buildPartyOptions(data){
    const set = new Set();
    data.forEach(x => { if (x.party_name) set.add(x.party_name); });
    const $sel = $('#filterParty');
    const current = $sel.val() || '';
    $sel.empty().append('<option value="">All Parties</option>');
    [...set].sort((a,b)=>a.localeCompare(b)).forEach(name => {
      $sel.append(`<option value="${escapeHtml(name)}">${escapeHtml(name)}</option>`);
    });
    $sel.val(current);
  }

  function applyFilters(){
    const q = ($('#filterSearch').val() || '').toLowerCase().trim();
    const f = $('#filterFrom').val(); // yyyy-mm-dd
    const t = $('#filterTo').val();
    const party = $('#filterParty').val() || '';

    let rows = challanCache.filter(item => {
      // Party filter
      if (party && (item.party_name||'') !== party) return false;

      // Date range filter (inclusive)
      if (f && (item.date||'') < f) return false;
      if (t && (item.date||'') > t) return false;

      // Search filter across multiple fields
      if (q) {
        const hay = [
          item.challan_no, item.party_name, item.material, item.vehicle_no
        ].map(v => (v||'').toString().toLowerCase()).join(' | ');
        if (!hay.includes(q)) return false;
      }
      return true;
    });

    renderTable(rows);
  }

  function renderTable(list){
    let html = '';
    list.forEach(item => {
      html += `
        <tr>
          <td><strong>${escapeHtml(item.challan_no||'')}</strong></td>
          <td>${escapeHtml(item.date||'')}</td>
          <td>${escapeHtml(item.party_name||'')}</td>
          <td>${escapeHtml(item.material||'')}</td>
          <td>${item.vehicle_no ? `<span class="chip">${escapeHtml(item.vehicle_no)}</span>` : '-'}</td>
          <td>
            <button class="btn btn-sm btn-outline-info view-btn" data-id="${item.id}">
              <i class="bi bi-eye"></i> View
            </button>
          </td>
        </tr>`;
    });
    $('#challanTableBody').html(html);
  }

  // Simple HTML escaper
  function escapeHtml(s){
    return String(s)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }

  // ======= Your existing form submit & view modal logic (unchanged) =======
  $('#challanForm').submit(function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    $.ajax({
      url: "{{ route('challan.save') }}", method: "POST", data: formData,
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      success: function(response) {
        $('#alertBox').html(`<div class="alert alert-success">${response.message}</div>`);
        $('#challanForm')[0].reset();
        setTimeout(() => { location.reload(); }, 1200);
      },
      error: function(xhr) {
        let errors = xhr.responseJSON?.errors, msgs='';
        if(errors){ Object.values(errors).forEach(arr => arr.forEach(m => msgs += `<div>${m}</div>`)); }
        else msgs = 'Something went wrong!';
        $('#alertBox').html(`<div class="alert alert-danger">${msgs}</div>`);
      }
    });
  });

  $(document).on('click', '.view-btn', function () {
    let challanId = $(this).data('id');
    $.get(`{{ url('challan') }}/${challanId}`, function (data) {
      $('#viewChallanNo').text(data.challan_no || '—');
      $('#viewDate').text(data.date || '—');
      $('#viewPartyName').text(data.party_name || '—');
      $('#viewVehicleNo').text(data.vehicle_no || '—');
      $('#viewDriverName').text(data.driver_name || '—');
      $('#viewLocation').text(data.location_name || '—');
      $('#viewTime').text(data.time || '—');
      $('#viewRemark').text(data.remark || '—');

      $('#viewReceiverSign').text(data.receiver_sign || 'Receiver Sign.');
      $('#viewDriverSign').text(data.driver_name || 'Driver Sign.');

      $('#sendChallanBtn').data('pdf_path', data.pdf_path);

      let materials = data.material ? data.material.split(',') : [];
      let quantities = data.quantity ? data.quantity.split(',') : [];
      let units = data.unit ? data.unit.split(',') : [];
      let tableRows = '';
      materials.forEach((m,i) => {
        tableRows += `
          <tr>
            <td>${i+1}</td>
            <td>${escapeHtml(m.trim())}</td>
            <td>${escapeHtml((quantities[i]||'—').toString().trim())} ${escapeHtml((units[i]||'').toString().trim())}</td>
          </tr>`;
      });
      $('#viewMaterialTable').html(tableRows);
      $('#viewChallanModal').modal('show');
    }).fail(() => { alert('Failed to fetch challan details.'); });
  });

  $('#sendChallanBtn').on('click', function () {
    const opt = $('#userSelect option:selected'), mob = opt.data('mobile');
    if (!mob){ alert('Please select a valid user.'); return; }
    const pdfPath = $(this).data('pdf_path'); if (!pdfPath){ alert('PDF not available.'); return; }
    const fullPdfUrl = `{{ url('storage') }}/${pdfPath}`;
    const encodedMsg = encodeURIComponent(`📎 *Download Challan PDF:*\n${fullPdfUrl}`);
    const waLink = `https://wa.me/91${mob}?text=${encodedMsg}`;
    window.open(waLink, '_blank');
  });
});
</script>
@endsection

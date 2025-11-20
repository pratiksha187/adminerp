@extends('layouts.app')

@section('content')

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
  :root{
    --bg:#f4f6f9;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#64748b;
    --brand:#475569;
    --brand-2:#334155;
    --head-bg:#f1f5f9;
    --row-alt:#f8fafc;
    --hover:#eef2f7;
    --border:#e5e7eb;
  }
  body{ background:var(--bg); }

  .card{
    border:1px solid var(--border);
    border-radius: 14px;
    background: var(--card);
  }
  .card.shadow-sm{
    box-shadow: 0 10px 30px rgba(2,6,23,.05)!important;
  }

  h5{
    font-weight:800;
    color:var(--brand-2);
    margin-bottom:.75rem;
    position:relative;
    padding-left:.75rem;
  }
  h5::before{
    content:"";
    position:absolute;
    left:0;
    width:4px;
    height:1.2em;
    border-radius:3px;
    background:var(--brand);
    top:50%;
    transform:translateY(-50%);
  }

  .table thead th{
    background:var(--head-bg)!important;
    color:var(--ink);
    border-bottom:1px solid var(--border)!important;
    position:sticky; top:0; z-index:10;
  }

  .table tbody tr:nth-child(odd){ background:var(--row-alt); }
  .table tbody tr:hover{ background:var(--hover); }

  .btn-info{
    background:#0ea5e9;
    border:none;
  }
  .btn-info:hover{
    background:#0284c7;
  }
</style>

<div class="card p-4 shadow-sm">
  <div class="container my-4">

    <div class="card shadow-sm">
      <div class="card-body">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5>📜 Recent Entries</h5>
          <a href="{{ route('work-entry.index') }}" class="btn btn-primary">
            ➕ Add New Entry
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-hover mt-3" id="entriesTable">
            <thead>
              <tr>
                <th>Sr. No.</th>
                <th>Date</th>
                <th>Chapter</th>
                <th>Description</th>
                <th>Quantity</th>
                
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Work Entry Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="modalContent">
        <p class="text-center text-muted">Loading...</p>
      </div>

    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function () {

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
       
        {
            data: 'id',
            render: function(id){
                return `
                    <button class="btn btn-sm btn-info viewEntry" data-id="${id}">
                      👁️ View
                    </button>
                `;
            }
        }
      ],
      order: [[1, 'desc']],
      drawCallback: function () {
        table.column(0).nodes().each(function (cell, i) {
          cell.innerHTML = i + 1;
        });
      }
    });

    // View button click
    $(document).on("click", ".viewEntry", function () {
        let id = $(this).data("id");

        $("#modalContent").html("<p class='text-center text-muted'>Loading...</p>");
        $("#viewModal").modal("show");

            $.get("{{ route('work-entry.view', '') }}/" + id, function (data) {

            $("#modalContent").html(data);
        });
    });

  });
</script>

@endsection

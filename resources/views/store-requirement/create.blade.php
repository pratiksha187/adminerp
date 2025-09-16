@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">📦 Material Requirement Form</h3>

    <form id="materialForm" action="{{ route('store-requirement.save') }}" method="POST">
        @csrf

        {{-- Requester Dropdown --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Select Requester</label>
            <select class="form-select" name="requester" id="requester">
                <option value="">-- Select Requester --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Add Material Rows --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title">➕ Add Material</h5>
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Material Name</label>
                        <input type="text" class="form-control" id="materialName">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control" id="materialQty">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit</label>
                        <select class="form-select" id="materialUnit">
                            <option value="">-- Select Unit --</option>
                            @foreach($unit as $units)
                                <option value="{{ $units->unit }}">{{ $units->unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Remarks</label>
                        <input type="text" class="form-control" id="materialRemark">
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="button" class="btn btn-primary" id="addMaterial">Add</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Materials Table --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">📋 Materials List</h5>
                <table class="table table-bordered table-striped" id="materialsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Material</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Dynamic rows will appear here --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="mt-3 text-end">
            <button type="submit" class="btn btn-success">💾 Save Requirement</button>
        </div>
    </form>
</div>

{{-- Select2 CSS & JS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for requester
    $('#requester').select2({
        placeholder: "-- Select Requester --",
        allowClear: true,
        width: '100%'
    });

    let count = 0;

    // Add material row
    $('#addMaterial').on('click', function() {
        const name = $('#materialName').val().trim();
        const qty = $('#materialQty').val().trim();
        const unit = $('#materialUnit').val();
        const remark = $('#materialRemark').val().trim();

        if (!name || !qty) {
            alert('Please enter material name and quantity.');
            return;
        }

        count++;
        const row = `
            <tr>
                <td>${count}</td>
                <td>
                    <input type="hidden" name="materials[${count}][name]" value="${name}">${name}
                </td>
                <td>
                    <input type="hidden" name="materials[${count}][qty]" value="${qty}">${qty}
                </td>
                <td>
                    <input type="hidden" name="materials[${count}][unit]" value="${unit}">${unit}
                </td>
                <td>
                    <input type="hidden" name="materials[${count}][remark]" value="${remark}">${remark}
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm removeRow">Delete</button>
                </td>
            </tr>`;
        $('#materialsTable tbody').append(row);

        // Reset inputs
        $('#materialName').val('');
        $('#materialQty').val('');
        $('#materialUnit').val('');
        $('#materialRemark').val('');
    });

    // Remove material row
    $(document).on('click', '.removeRow', function() {
        $(this).closest('tr').remove();
    });

    // Optional: Validate requester on form submit
    $('#materialForm').on('submit', function(e) {
        if (!$('#requester').val()) {
            alert('Please select a requester.');
            e.preventDefault();
        }
    });
});
</script>
@endsection

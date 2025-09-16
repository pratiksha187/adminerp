@extends('layouts.app')

@section('title', 'Add Store DPR')

@section('content')
<div class="container my-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Daily Progress Report (Store)</h3>
        <span class="text-muted">Date: <strong>{{ now()->format('d/m/Y') }}</strong></span>
    </div>

    <form action="{{ route('store-dpr.store') }}" method="POST">
        @csrf

        <!-- Store Info -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-primary mb-3"><i class="bi bi-shop me-2"></i>Store Information</h5>
                <div class="mb-3">
                    <label for="store_name" class="form-label">Store Name</label>
                    <!-- <input type="text" class="form-control" id="store_name" name="store_name" placeholder="Enter Store Name"> -->
                    <select class="form-select" name="store_name" id="store_name">
                        <option value="">-- Select Store Name --</option>
                        @foreach($site_name as $site_names)
                            <option value="{{ $site_names->id }}">{{ $site_names->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

    <!-- Inward Materials -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-success">
                    <i class="bi bi-box-arrow-in-down me-2"></i>Inward (Receipts)
                </h5>
                <div id="inward-items">
                    <div class="row g-2 mb-2">
                        <div class="col-md-3">
                            <select name="inward[0][item]" class="form-select" >
                                <option value="">Select Material</option>
                                @foreach($material_name as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                            <!-- <input type="text" name="inward[0][item]" class="form-control" placeholder="Material Name"> -->
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="inward[0][vendor]" class="form-control" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="inward[0][rate]" class="form-control" placeholder="Rate" >
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="inward[0][qty]" class="form-control" placeholder="Quantity" >
                        </div>
                        <div class="col-md-2">
                           
                             <select name="inward[0][type]" class="form-select" >
                                <option value="">Select Type</option>
                                @foreach($unit as $units)
                                    <option value="{{ $units->id }}">{{ $units->unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-success add-inward w-100">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outward Materials -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-danger"><i class="bi bi-box-arrow-up me-2"></i>Outward (Dispatches)</h5>
                <div id="outward-items">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <select name="outward[0][item]" class="form-select" >
                                <option value="">Select Material</option>
                                @foreach($material_name as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                            <!-- <input type="text" name="outward[0][item]" class="form-control" placeholder="Material Name"> -->
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="outward[0][qty]" class="form-control" placeholder="Quantity">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger add-outward w-100"><i class="bi bi-plus"></i> Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Issued Materials -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-warning"><i class="bi bi-truck me-2"></i>Material Issued</h5>
                <div id="issued-items">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <select name="issued[0][item]" class="form-select" >
                                <option value="">Select Material</option>
                                @foreach($material_name as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                            <!-- <input type="text" name="issued[0][item]" class="form-control" placeholder="Material Name"> -->
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="issued[0][qty]" class="form-control" placeholder="Quantity">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-warning add-issued w-100"><i class="bi bi-plus"></i> Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Completed -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-info"><i class="bi bi-check2-square me-2"></i>Tasks Completed</h5>
                <div id="tasks">
                    <div class="row g-2 mb-2">
                        <div class="col-md-9">
                            <input type="text" name="tasks[0][task]" class="form-control" placeholder="Enter task description">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info add-task w-100"><i class="bi bi-plus"></i> Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="text-end">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Save Report</button>
        </div>
    </form>
</div>

<!-- jQuery for dynamic fields -->
<script>
document.addEventListener('DOMContentLoaded', function () {
 
let inwardIndex = 1;
document.querySelector('.add-inward').addEventListener('click', function () {
    let materialOptions = `
        <option value="">Select Material</option>
        @foreach($material_name as $material)
            <option value="{{ $material->id }}">{{ $material->name }}</option>
        @endforeach
    `;

    let unitOptions = `
        <option value="">Select Type</option>
        @foreach($unit as $units)
            <option value="{{ $units->id }}">{{ $units->unit }}</option>
        @endforeach
    `;
    let row = `
    <div class="row g-2 mb-2">
        <div class="col-md-3">
            <select name="inward[${inwardIndex}][item]" class="form-select">
                ${materialOptions}
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="inward[${inwardIndex}][vendor]" class="form-control" placeholder="Vendor Name">
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="inward[${inwardIndex}][rate]" class="form-control" placeholder="Rate">
        </div>
        <div class="col-md-2">
            <input type="number" name="inward[${inwardIndex}][qty]" class="form-control" placeholder="Quantity">
        </div>
        <div class="col-md-2">
          

             <select name="inward[${inwardIndex}][type]" class="form-select">
                ${unitOptions}
            </select>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100 remove-row">Remove</button>
        </div>
    </div>`;
    document.getElementById('inward-items').insertAdjacentHTML('beforeend', row);
    inwardIndex++;
});


    // Add dynamic Outward row
    let outwardIndex = 1;
    document.querySelector('.add-outward').addEventListener('click', function () {
         let materialOptions = `
        <option value="">Select Material</option>
        @foreach($material_name as $material)
            <option value="{{ $material->id }}">{{ $material->name }}</option>
        @endforeach
    `;
        let row = `
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <select name="outward[${outwardIndex}][item]" class="form-select">

                ${materialOptions}
            </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="outward[${outwardIndex}][qty]" class="form-control" placeholder="Quantity">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-danger w-100 remove-row">Remove</button>
            </div>
        </div>`;
        document.getElementById('outward-items').insertAdjacentHTML('beforeend', row);
        outwardIndex++;
    });

    // Add dynamic Issued row
    let issuedIndex = 1;
    document.querySelector('.add-issued').addEventListener('click', function () {
         let materialOptions = `
        <option value="">Select Material</option>
        @foreach($material_name as $material)
            <option value="{{ $material->id }}">{{ $material->name }}</option>
        @endforeach
    `;
        let row = `
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                 <select name="issued[${issuedIndex}][item]" class="form-select">

                ${materialOptions}
            </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="issued[${issuedIndex}][qty]" class="form-control" placeholder="Quantity">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-danger w-100 remove-row">Remove</button>
            </div>
        </div>`;
        document.getElementById('issued-items').insertAdjacentHTML('beforeend', row);
        issuedIndex++;
    });

    // Add dynamic Task row
    let taskIndex = 1;
    document.querySelector('.add-task').addEventListener('click', function () {
        let row = `
        <div class="row g-2 mb-2">
            <div class="col-md-9">
                <input type="text" name="tasks[${taskIndex}][task]" class="form-control" placeholder="Enter task description">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-danger w-100 remove-row">Remove</button>
            </div>
        </div>`;
        document.getElementById('tasks').insertAdjacentHTML('beforeend', row);
        taskIndex++;
    });

    // Remove row handler
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.row').remove();
        }
    });
});


function calculateStock() {
    let inward = document.querySelectorAll('input[name*="inward"][name*="[qty]"]');
    let issued = document.querySelectorAll('input[name*="issued"][name*="[qty]"]');
    let outward = document.querySelectorAll('input[name*="outward"][name*="[qty]"]');

    let totalInward = 0, totalIssued = 0, totalOutward = 0;

    inward.forEach(i => totalInward += parseFloat(i.value || 0));
    issued.forEach(i => totalIssued += parseFloat(i.value || 0));
    outward.forEach(i => totalOutward += parseFloat(i.value || 0));

    let available = totalInward - (totalIssued + totalOutward);
    document.getElementById('stock-preview').innerText = "Available Stock: " + available;
}

document.addEventListener('input', function(e) {
    if (e.target.closest('#inward-items') || e.target.closest('#issued-items') || e.target.closest('#outward-items')) {
        calculateStock();
    }
});

</script>
@endsection

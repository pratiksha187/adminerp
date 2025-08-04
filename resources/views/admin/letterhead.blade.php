@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background-color: #f2f4f8;
    }

    .letterhead-container {
        max-width: 1100px;
        margin: 30px auto;
    }

    .card-custom {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(28, 44, 62, 0.1);
        padding: 20px;
    }

    .table thead {
        background-color: #f8f9fa;
        color: #495057;
    }

    .table tbody tr:hover {
        background-color: #f1f5f9;
    }

    .btn-primary {
        background-color: #f25c05;
        border-color: #f25c05;
    }

    .btn-primary:hover {
        background-color: #d84e04;
        border-color: #d84e04;
    }

    h3 {
        color: #1c2c3e;
        font-weight: bold;
        font-size: 1.5rem;
    }

    .empty-message {
        text-align: center;
        color: #6c757d;
        padding: 40px;
    }

    /* Modal styling */
    .modal-content {
        border-radius: 12px;
    }

    .modal-header {
        background-color: #f25c05;
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .modal-footer {
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .modal-body {
        padding: 30px;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
    }
</style>

<div class="">
    <div class="card card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Letter Head List</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#employeeModal">
                <i class="bi bi-plus-circle me-1"></i> Add Letter Head
            </button>
        </div>

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
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->name }}</td>

                            <td>{{ $item->ref_no }}</td>
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
                                No letter heads added yet. Click "Add Letter Head" to start.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('letterhead.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Letter Head</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name of receiver</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_to" class="form-control" required>
                            <option value="Pirlpl">Pirlpl</option>
                            <option value="Shreeyash">Shreeyash</option>
                            <option value="Apurva">Apurva</option>
                            <option value="Swaraj">Swaraj</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

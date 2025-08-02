@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background-color: #f2f4f8;
    }

    .letterhead-container {
        max-width: 1000px;
        margin: 30px auto;
    }

    .card-custom {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(28, 44, 62, 0.1);
        padding: 20px;
    }

    .table thead {
        background-color: #f8f9fa;
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
    }

    .empty-message {
        text-align: center;
        color: #6c757d;
        padding: 40px;
    }
</style>

<div class="letterhead-container">
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
                        <th>Description</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                {{-- <tbody id="employeeTableBody">
                    <tr>
                        <td colspan="4" class="empty-message">
                            No letter heads added yet. Click "Add Letter Head" to start.
                        </td>
                    </tr>
                </tbody> --}}
                <tbody>
                    @forelse ($letterHeads as $item)
                        <tr>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->ref_no }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-center">
                                {{-- You can add delete/edit buttons here --}}
                            </td>
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
{{-- <div class="modal fade" id="employeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background-color: #f25c05; color: white;">
                <h5 class="modal-title">Add Letter Head</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="employeeForm">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="saveEmployee">Save</button>
            </div>
        </div>
    </div>
</div> --}}
<!-- Modal -->
        <div class="modal fade" id="employeeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('letterhead.store') }}" method="POST">
                        @csrf
                        <div class="modal-header" style="background-color: #f25c05; color: white;">
                            <h5 class="modal-title">Add Letter Head</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let employees = [];

    document.getElementById('saveEmployee').addEventListener('click', function () {
        const date = document.getElementById('date').value;
        const refNo = document.getElementById('ref_no').value;
        const description = document.getElementById('description').value;

        if (date && refNo && description) {
            employees.push({
                id: Date.now(),
                date: date,
                ref_no: refNo,
                description: description
            });

            updateTable();

            const modal = bootstrap.Modal.getInstance(document.getElementById('employeeModal'));
            modal.hide();
            document.getElementById('employeeForm').reset();
        }
    });

    function updateTable() {
        const tbody = document.getElementById('employeeTableBody');

        if (employees.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="empty-message">
                        No letter heads added yet. Click "Add Letter Head" to start.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = employees.map(emp => `
            <tr>
                <td>${emp.date}</td>
                <td>${emp.ref_no}</td>
                <td>${emp.description}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger" onclick="deleteEmployee(${emp.id})">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function deleteEmployee(id) {
        employees = employees.filter(emp => emp.id !== id);
        updateTable();
    }
</script>
@endsection

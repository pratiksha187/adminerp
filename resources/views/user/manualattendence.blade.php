@extends('layouts.app')

@section('content')

<!-- ✅ Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- ✅ DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<!-- ✅ jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- ✅ Bootstrap Bundle JS (includes Popper.js for dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<div class="container py-4">

    <!-- ✅ Manual Attendance Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3 text-primary">Manual Attendance Entry</h5>
            <form action="{{ route('attendance.manual') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Clock In</label>
                        <input type="time" name="manual_clock_in" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Clock Out</label>
                        <input type="time" name="manual_clock_out" class="form-control" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-warning w-100" type="submit">
                        Submit Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ✅ Attendance DataTable -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Manual Attendance Requests</h5>
            <div class="table-responsive">
                <table id="attendanceTable" class="table table-bordered w-100">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Status</th>
                            {{-- <th>Action</th> --}}
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ✅ DataTables Initialization + Dropdown Handling -->
<script>
$(document).ready(function () {
    const table = $('#attendanceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("attendance.manual.data") }}',
        columns: [
            { data: 'id' },
              { data: 'user_id' },
            
            { data: 'date' },
            { data: 'clock_in' },
            { data: 'clock_out' },
            {
                data: 'status',
                render: function (data) {
                    let label = 'Pending', cls = 'secondary';
                    if (data == 1) { label = 'Accepted'; cls = 'success'; }
                    else if (data == 2) { label = 'Rejected'; cls = 'danger'; }
                    return `<span class="badge bg-${cls}">${label}</span>`;
                }
            }
        ]
    });

    // Initialize dropdowns after every DataTable reload
    table.on('draw', function () {
        // Ensure Bootstrap dropdown is initialized
        $('[data-bs-toggle="dropdown"]').each(function () {
            new bootstrap.Dropdown(this);
        });
    });

    // Handle dropdown actions with event delegation
    $('#attendanceTable').on('click', '.action-btn', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        let action = $(this).data('action');

        if (confirm(`Are you sure you want to ${action} this request?`)) {
            $.post('{{ route("attendance.manual.action") }}', {
                _token: '{{ csrf_token() }}',
                id: id,
                action: action
            }, function (response) {
                table.ajax.reload();
            }).fail(function () {
                alert('An error occurred. Please try again.');
            });
        }
    });
});
</script>

@endsection

@extends('layouts.app')

@section('content')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h4 class="mb-4">📋 Attendance Report</h4>

            <!-- Filter Form -->
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ $start }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ $end }}" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('attendance.export', ['start_date' => $start, 'end_date' => $end]) }}" class="btn btn-success">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            </form>

            <!-- Attendance Table -->
            <div class="table-responsive">
               
                <table id="attendanceTable" class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Worked Hours</th>
                              <th>Overtime</th> 
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>

        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#attendanceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("attendance.datatables") }}',
                data: {
                    start_date: '{{ $start }}',
                    end_date: '{{ $end }}'
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'user.name', name: 'user.name' },
                { data: 'date', name: 'date' },
                { data: 'clock_in', name: 'clock_in' },
                { data: 'clock_out', name: 'clock_out' },
                { data: 'worked_hours', name: 'worked_hours', orderable: false, searchable: false },
                { data: 'overtime', name: 'overtime', orderable: false, searchable: false }
            ]
        });

    });
</script>


@endsection

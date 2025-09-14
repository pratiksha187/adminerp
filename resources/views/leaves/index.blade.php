@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="card p-3 mb-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Apply Leave</h4>
        </div>

        <form action="{{ route('leaves.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" required>
                </div>
               
                <div class="col-md-3">
                    <label>Type</label>
                    <select name="type" class="form-control" {{ $disableType ? 'disabled' : '' }} required>
                        <option value="">Select Type</option>
                        <option value="Sick">Sick (SL)</option>
                        <option value="Casual">Casual (CL)</option>
                        <option value="Paid">Paid (EL)</option>
                    </select>
                    @if($disableType)
                        <small class="text-danger">Leave type selection is disabled for employees with less than 3 months of service.</small>
                    @endif
                </div>

                <div class="col-md-3">
                    <label>Reason</label>
                    <input type="text" name="reason" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Head of Department</label>
                    <input type="text" name="hod_name" class="form-control" placeholder="Enter HOD Name" required>
                </div>
                <div class="col-12 mt-2">
                    <button class="btn btn-primary">Apply Leave</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Applied Leaves Table -->
    <div class="card p-3 shadow-sm">
        <h5>My Applied Leaves</h5>
        <div class="table-responsive mt-2">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Head of Department</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        <td>{{ $leave->from_date }}</td>
                        <td>{{ $leave->to_date }}</td>
                        <td>{{ $leave->type }}</td>
                        <td>{{ $leave->reason ?? '—' }}</td>
                        <td>{{ $leave->hod_name ?? '—' }}</td>
                        <td>
                            @if($leave->status === 'Approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($leave->status === 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No leaves applied.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

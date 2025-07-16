<!-- 🛠️ Manual Attendance Entry -->
@extends('layouts.app')

@section('content')
<style>
    .btn-brand-orange {
    background-color: #f25c05 !important;
    border-color: #f25c05 !important;
    color: #fff !important;
}
.btn-brand-orange:hover {
    background-color: #d94e04 !important;
    border-color: #d94e04 !important;
}

    </style>
<div class="card shadow-sm border-0 mb-5">
    <div class="card-body">
        <h5 class="card-title mb-4 " style="color: #e5ad38;">
            <i class="bi bi-pencil-square me-2"></i> Manual Attendance Entry (Missed Log)
        </h5>

        <form action="{{ route('attendance.manual') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="date" class="form-label">Select Date</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label for="manual_clock_in" class="form-label">Clock In Time</label>
                    <input type="time" name="manual_clock_in" id="manual_clock_in" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label for="manual_clock_out" class="form-label">Clock Out Time</label>
                    <input type="time" name="manual_clock_out" id="manual_clock_out" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn w-100 text-white" style="background-color: #e5ad38; border-color: #f25c05;">
                <i class="bi bi-save me-1"></i> Save Manual Attendance
            </button>

        </form>
    </div>
</div>
@endsection
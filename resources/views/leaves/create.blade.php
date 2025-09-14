@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Apply Leave</h3>
    <form action="{{ route('leave.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>From Date</label>
            <input type="date" name="from_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>To Date</label>
            <input type="date" name="to_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Type</label>
            <select name="type" class="form-select" required>
                <option value="Sick">Sick (SL)</option>
                <option value="Casual">Casual (CL)</option>
                <option value="Paid">Paid (EL)</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Reason</label>
            <textarea name="reason" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Apply</button>
    </form>
</div>
@endsection

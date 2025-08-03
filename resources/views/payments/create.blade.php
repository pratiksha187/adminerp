@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow rounded-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
            <h4 class="mb-0">💼 Generate Employee Payment</h4>
            <a href="{{ route('payments.index') }}" class="btn btn-light btn-sm shadow-sm">
                📄 View All Payments
            </a>
        </div>

        <div class="card-body bg-light">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('payments.generate') }}" class="row g-4">
                @csrf

                <div class="col-md-4">
                    <label for="user_id" class="form-label fw-semibold">Select Employee</label>
                    <select class="form-select" name="user_id" required>
                        <option value="">-- Choose Employee --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="from_date" class="form-label fw-semibold">From Date</label>
                    <input type="date" name="from_date" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label for="to_date" class="form-label fw-semibold">To Date</label>
                    <input type="date" name="to_date" class="form-control" required>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm fw-bold">
                        💰 Generate Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

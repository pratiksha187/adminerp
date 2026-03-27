@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-white py-3 border-0">
            <h4 class="mb-0 fw-bold">Upload Payroll Excel</h4>
        </div>

        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('payroll.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Month</label>
                        <input type="text" name="month" class="form-control" placeholder="February" value="{{ old('month') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Year</label>
                        <input type="text" name="year" class="form-control" placeholder="2026" value="{{ old('year') }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Upload Excel File</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        Upload Excel & Generate Salary Slips
                    </button>

                    <a href="{{ route('payroll.index') }}" class="btn btn-dark px-4 ms-2">
                        View Payroll List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
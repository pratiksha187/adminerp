@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Payroll Records</h4>
        <a href="{{ route('payroll.upload.form') }}" class="btn btn-primary">Upload New Excel</a>
    </div>

    <div class="card shadow border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Employee Name</th>
                            <th>Designation</th>
                            <th>Month</th>
                            <th>Gross Earnings</th>
                            <th>Total Deduction</th>
                            <th>Net Payable</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->employee_name }}</td>
                                <td>{{ $payment->designation }}</td>
                                <td>{{ $payment->month }} {{ $payment->year }}</td>
                                <td>{{ number_format($payment->gross_earnings, 2) }}</td>
                                <td>{{ number_format($payment->total_deduction, 2) }}</td>
                                <td>{{ number_format($payment->net_payable, 2) }}</td>
                                <td>
                                    <a href="{{ route('payroll.show', $payment->id) }}" class="btn btn-info btn-sm text-white">View</a>
                                    <a href="{{ route('payroll.downloadSlip', $payment->id) }}" class="btn btn-success btn-sm">Download Slip</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No payroll data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
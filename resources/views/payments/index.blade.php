@extends('layouts.app')

@section('content')
<div class="card p-4 shadow-sm">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Salary Payments</h2>
        <a href="{{ route('payments.export') }}" class="btn btn-success">
            ⬇️ Download CSV
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Employee</th>
                <th>From</th>
                <th>To</th>
                <th>Present Days</th>
                <th>Gross Salary</th>
                <th>Gross Payable</th>
                <th>Deductions</th>
                <th>Net Payable</th>
                <th>Generated On</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->user->name }}</td>
                    <td>{{ $payment->from_date }}</td>
                    <td>{{ $payment->to_date }}</td>
                    <td>{{ $payment->present_days }}</td>
                    <td>{{ $payment->gross_salary }}</td>
                    <td>{{ $payment->gross_payable }}</td>
                    <td>{{ $payment->total_deduction }}</td>
                    <td><strong>{{ $payment->net_payable }}</strong></td>
                    <td>{{ $payment->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="9">No payments found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
@endsection

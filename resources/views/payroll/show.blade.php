@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0 fw-bold">Employee Salary Detail</h4>
            <a href="{{ route('payroll.downloadSlip', $payment->id) }}" class="btn btn-success">Download Slip</a>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Employee Name:</strong> {{ $payment->employee_name }}</div>
                <div class="col-md-4"><strong>Designation:</strong> {{ $payment->designation }}</div>
                <div class="col-md-4"><strong>Joining Date:</strong> {{ $payment->joining_date ? \Carbon\Carbon::parse($payment->joining_date)->format('d-m-Y') : '-' }}</div>

                <div class="col-md-3"><strong>Gross Salary:</strong> {{ number_format($payment->gross_salary, 2) }}</div>
                <div class="col-md-3"><strong>Per Day:</strong> {{ number_format($payment->per_day, 2) }}</div>
                <div class="col-md-3"><strong>Present Days:</strong> {{ number_format($payment->present_days, 2) }}</div>
                <div class="col-md-3"><strong>Total Present Days:</strong> {{ number_format($payment->total_present_days, 2) }}</div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h5 class="fw-bold">Earnings</h5>
                    <table class="table table-sm table-bordered">
                        <tr><td>Basic</td><td>{{ number_format($payment->basic, 2) }}</td></tr>
                        <tr><td>HRA</td><td>{{ number_format($payment->hra, 2) }}</td></tr>
                        <tr><td>Conveyance</td><td>{{ number_format($payment->conveyance, 2) }}</td></tr>
                        <tr><td>Other Allowance</td><td>{{ number_format($payment->other_allowance, 2) }}</td></tr>
                        <tr><td>OT / Arrears / Penalty</td><td>{{ number_format($payment->ot_arrears_penalty, 2) }}</td></tr>
                        <tr><th>Gross Payable</th><th>{{ number_format($payment->gross_payable, 2) }}</th></tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5 class="fw-bold">Deductions</h5>
                    <table class="table table-sm table-bordered">
                        <tr><td>PF</td><td>{{ number_format($payment->pf, 2) }}</td></tr>
                        <tr><td>Insurance</td><td>{{ number_format($payment->insurance, 2) }}</td></tr>
                        <tr><td>PT</td><td>{{ number_format($payment->pt, 2) }}</td></tr>
                        <tr><td>Advance</td><td>{{ number_format($payment->advance, 2) }}</td></tr>
                        <tr><td>Late Mark</td><td>{{ number_format($payment->late_mark, 2) }}</td></tr>
                        <tr><td>Loan Deduction</td><td>{{ number_format($payment->loan_deduction, 2) }}</td></tr>
                        <tr><th>Total Deduction</th><th>{{ number_format($payment->total_deduction, 2) }}</th></tr>
                    </table>
                </div>
            </div>

            <div class="alert alert-success mt-4">
                <h5 class="mb-0">Net Payable: ₹{{ number_format($payment->net_payable, 2) }}</h5>
            </div>
        </div>
    </div>
</div>
@endsection
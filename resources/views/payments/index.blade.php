@extends('layouts.app')

@section('content')
<style>
/* ===== General Styles ===== */
body { font-family: 'Poppins', sans-serif; color: #1e293b; background: #f8fafc; }

.card { border-radius: 14px; background: #fff; border: 1px solid #e5e7eb; }
.section-header {
    background: linear-gradient(135deg, rgba(71,85,105,.95), rgba(71,85,105,.75));
    color: #fff; border-radius: 12px; padding: 16px 18px;
    display: flex; justify-content: space-between; align-items: center;
}
.btn-export { background:#22c55e; color:#fff; border-radius:10px; }
.btn-export:hover { background:#16a34a; }
.num { text-align:right; font-variant-numeric:tabular-nums; }
.chip { display:inline-block; padding:.2rem .5rem; border-radius:999px; background:#eef2f7; border:1px solid #e5e7eb; }
.amount { font-weight:600; }

/* ===== Modal Payslip ===== */
#slipBody { min-height: 300px; font-size: 14px; }
#slipBody table { width: 100%; border-collapse: collapse; }
#slipBody table th, #slipBody table td { border: 1px solid #d1d5db; padding: 6px 8px; }
#slipBody table th { background: #f3f4f6; text-align: center; }
#slipBody .fw-bold td { font-weight: 700; }
#slipBody .text-end { text-align: right; }
#slipBody .text-muted { color: #6b7280; font-size: 0.85rem; }
#slipBody .badge { font-size: 0.85rem; }
</style>

<div class="card p-3 p-md-4 shadow-sm">
    <div class="container">

        <!-- Header -->
        <div class="section-header mb-3">
            <h2><i class="bi bi-cash-stack"></i> Salary Payments</h2>
            <a href="{{ route('payments.export') }}" class="btn btn-export">⬇️ Download CSV</a>
        </div>

        <!-- Salary Table -->
        <!-- <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>From</th>
                        <th>To</th>
                        <th class="num">Present</th>
                        <th class="num">Gross</th>
                        <th class="num">Net</th>
                        <th>Slip</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->user->name }}</td>
                        <td><span class="chip">{{ $payment->from_date }}</span></td>
                        <td><span class="chip">{{ $payment->to_date }}</span></td>
                        <td class="num">{{ $payment->present_days }}</td>
                        <td class="num">{{ $payment->gross_salary }}</td>
                        <td class="num amount">{{ $payment->net_payable }}</td>
                        <td>
                            <button type="button" 
                                    class="btn btn-sm btn-primary btn-slip"
                                    data-id="{{ $payment->id }}">
                                <i class="bi bi-file-earmark-text"></i> Slip
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox"></i> No payments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div> -->
@if(session('success'))
<div class="alert alert-success shadow-sm p-3">
    <h5>{{ session('success') }}</h5>
    @if(session('summary'))
        <ul class="mb-0 ps-3">
            @foreach(session('summary') as $label => $value)
                <li><strong>{{ $label }}:</strong> {{ $value }}</li>
            @endforeach
        </ul>
    @endif
</div>
@endif

<div class="table-responsive mt-3">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr class="text-center">
                <th rowspan="2">Employee</th>
                <th colspan="2">Period</th>
                <th colspan="6">Attendance Summary</th>
                <th colspan="4">Earnings (₹)</th>
                <th colspan="4">Deductions (₹)</th>
                <th rowspan="2">Net Payable (₹)</th>
            </tr>
            <tr class="text-center">
                <th>From</th>
                <th>To</th>
                <th>Present</th>
                <th>Week Off</th>
                <th>Holiday</th>
                <th>C.Off</th>
                <th>CL</th>
                <th>SL</th>
                <th>Basic</th>
                <th>HRA</th>
                <th>Conveyance</th>
                <th>Other</th>
                <th>PF</th>
                <th>PT</th>
                <th>Insurance</th>
                <th>Advance</th>
            </tr>
        </thead>

        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->user->name }}</td>
                    <td><span class="chip">{{ \Carbon\Carbon::parse($payment->from_date)->format('d-M-Y') }}</span></td>
                    <td><span class="chip">{{ \Carbon\Carbon::parse($payment->to_date)->format('d-M-Y') }}</span></td>

                    <!-- Attendance Summary -->
                    <td class="num">{{ $payment->present_days_in_month }}</td>
                    <td class="num">{{ $payment->weekoffCount }}</td>
                    <td class="num">{{ $payment->holidayCount }}</td>
                    <td class="num text-primary fw-bold">{{ $payment->cOffCount ?? 0 }}</td>
                    <td class="num">{{ $payment->leave_cl }}</td>
                    <td class="num">{{ $payment->leave_sl }}</td>

                    <!-- Earnings -->
                    <td class="num">{{ number_format($payment->basic_60, 2) }}</td>
                    <td class="num">{{ number_format($payment->hra_5, 2) }}</td>
                    <td class="num">{{ number_format($payment->conveyance_20, 2) }}</td>
                    <td class="num">{{ number_format($payment->other_allowance, 2) }}</td>

                    <!-- Deductions -->
                    <td class="num text-danger">{{ number_format($payment->pf_12, 2) }}</td>
                    <td class="num text-danger">{{ number_format($payment->pt, 2) }}</td>
                    <td class="num text-danger">{{ number_format($payment->insurance, 2) }}</td>
                    <td class="num text-danger">{{ number_format($payment->advance, 2) }}</td>

                    <!-- Net Payable -->
                    <td class="num amount fw-bold text-success">{{ number_format($payment->net_payable, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="18" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> No payments found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


    </div>
</div>

<!-- Payslip Modal -->
<div class="modal fade" id="slipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" id="slipContent">
            <div class="modal-header">
                <h5 class="modal-title">Salary Slip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="slipBody">
                <div class="text-center text-muted py-4">Loading...</div>
            </div>
            <div class="modal-footer">
                <button id="downloadPdf" class="btn btn-success">
                    <i class="bi bi-download"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- HTML2PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
$(function(){
    // Open slip modal
    $('.btn-slip').on('click', function(){
        let id = $(this).data('id');
        $('#slipBody').html('<div class="text-center text-muted py-4">Loading...</div>');
        $('#slipModal').modal('show');

        // Fetch payslip via AJAX
        $.get("{{ url('payments/slip') }}/" + id, function(res){
            $('#slipBody').html(res);
        });
    });

    // Download PDF
    $('#downloadPdf').on('click', function(){
        var element = document.getElementById('slipBody');
        html2pdf().from(element).set({
            margin: 10,
            filename: 'salary-slip.pdf',
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).save();
    });
});
</script>

@endsection

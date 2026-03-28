<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Slip</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .wrapper { border: 1px solid #ccc; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0 0; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table th, table td { border: 1px solid #ccc; padding: 8px; }
        .section-title { background: #f3f3f3; font-weight: bold; }
        .text-end { text-align: right; }
        .net-pay { margin-top: 18px; font-size: 16px; font-weight: bold; text-align: right; color: #0d6efd; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h2>SHREEYASH CONSTRUCTION</h2>
            <p>Salary Slip - {{ $payment->month }} {{ $payment->year }}</p>
        </div>

        <table>
            <tr>
                <td><strong>Employee Name</strong></td>
                <td>{{ $payment->employee_name }}</td>
                <td><strong>Designation</strong></td>
                <td>{{ $payment->designation }}</td>
            </tr>
            <tr>
                <td><strong>Joining Date</strong></td>
                <td>{{ $payment->joining_date ? \Carbon\Carbon::parse($payment->joining_date)->format('d-m-Y') : '-' }}</td>
                <td><strong>Per Day Salary</strong></td>
                <td>₹{{ number_format($payment->per_day_salary, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Present Days</strong></td>
                <td>{{ round($payment->present_days) }}</td>

                <td><strong>Total Days</strong></td>
                <td>{{ $daysInMonth }}</td>
            </tr>
        </table>

        <table>
            <tr class="section-title">
                <td>Earnings</td>
                <td class="text-end">Amount</td>
                <td>Deductions</td>
                <td class="text-end">Amount</td>
            </tr>

            <tr>
                <td>Basic Salary</td>
                <td class="text-end">₹{{ number_format($payment->basic_salary, 2) }}</td>
                <td>PF</td>
                <td class="text-end">₹{{ number_format($payment->pf, 2) }}</td>
            </tr>

            <tr>
                <td>HRA</td>
                <td class="text-end">₹{{ number_format($payment->hra, 2) }}</td>
                <td>ESIC</td>
                <td class="text-end">₹{{ number_format($payment->esic, 2) }}</td>
            </tr>

            <tr>
                <td>Conveyance</td>
                <td class="text-end">₹{{ number_format($payment->conveyance, 2) }}</td>
                <td>PT</td>
                <td class="text-end">₹{{ number_format($payment->pt, 2) }}</td>
            </tr>

            <tr>
                <td>Other Allowance</td>
                <td class="text-end">₹{{ number_format($payment->other_allowance, 2) }}</td>
                <td>Advance</td>
                <td class="text-end">₹{{ number_format($payment->advance_amount, 2) }}</td>
            </tr>

            <tr>
                <td>OT Amount</td>
                <td class="text-end">₹{{ number_format($payment->ot_amount, 2) }}</td>
                <td>Leave Deduction</td>
                <td class="text-end">₹{{ number_format($payment->leave_deduction, 2) }}</td>
            </tr>

            <tr>
                <th>Gross Earnings</th>
                <th class="text-end">₹{{ number_format($payment->gross_earnings, 2) }}</th>
                <th>Total Deduction</th>
                <th class="text-end">₹{{ number_format($payment->total_deduction, 2) }}</th>
            </tr>
        </table>

        <div class="net-pay">
            Net Payable: ₹{{ round($payment->net_payable) }}
        </div>
    </div>


    <p>NOTE: This document is electronically generated and does not require any physical signature or stamp.</p>
</body>
</html>
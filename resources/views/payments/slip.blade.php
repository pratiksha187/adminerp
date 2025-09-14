<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pay Slip</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #000; padding: 4px; text-align: left; }
        .no-border td { border: none; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>

    <h3 class="center">Shreeyash Construction</h3>
    <p class="center">Crescent pearl - B, B-G/1, Veena Nagar, Katrang, Road,, nr. St. Anthony Church, Khopoli, Maharashtra 410203</p>
    <!-- <h4 class="center">PAY SLIP FOR JUNE - 2025</h4> -->
    <h4 class="center">
        PAY SLIP FOR {{ \Carbon\Carbon::parse($payment->from_date)->format('F - Y') }}
    </h4>

    <!-- Employee Info -->
    <table>
        <tr>
            <td>Emp. UAN No:</td>
            <td>{{ $user->uan_no ?? '' }}</td>
            <td>Emp. Code:</td>
            <td>{{ $user->employee_code ?? '' }}</td>
        </tr>
        <tr>
            <td>Aadhar No:</td>
            <td>{{ $user->aadhaar ?? '' }}</td>
            <td>PAN:</td>
            <td>{{ $user->pan ?? '' }}</td>
        </tr>
        <tr>
            <td>Name:</td>
            <td>{{ $user->name ?? '' }}</td>
            <td>Designation:</td>
            <td>{{ $user->role ?? '' }}</td>
        </tr>
        <tr>
            <td>DOJ:</td>
            <td>{{ $user->join_date ?? '' }}</td>
          
        </tr>
    </table>
    <br>

    <!-- Attendance Info -->
    <table>
        <tr>
            <td>Cal Days</td>
            <td>{{ $daysInMonth ?? 30 }}</td>
            <td>Present</td>
            <td>{{ $payment->present_days_in_month ?? 0 }}</td>
            <td>Paid Holiday</td>
            <td>{{ $payment->holidayCount ?? 0 }}</td>
            <td>Weekly Off</td>
            <td>{{ $payment->weekoffCount ?? 0 }}</td>
            <td>Paid Days</td>
            <td>{{ $payment->present_days_act ?? 0 }}</td>
        </tr>
    </table>
    <br>

    <!-- Earnings & Deductions -->
    <table>
        <tr>
            <th colspan="2" class="center">Earnings</th>
            <th colspan="2" class="center">Deductions</th>
        </tr>
        <tr>
            <td>BASIC</td>
            <td class="right">{{ $payment->basic_60 }}</td>
            <td>PF</td>
            <td class="right">{{ $payment->pf_12 }}</td>
        </tr>
        <tr>
            <td>HRA</td>
            <td class="right">{{ $payment->hra_5 }}</td>
            <td>Insurance</td>
            <td class="right">{{ $payment->insurance }}</td>
        </tr>
        <tr>
            <td>Conveyance</td>
            <td class="right">{{ $payment->conveyance_20 }}</td>
            <td>PT</td>
            <td class="right">{{ $payment->pt }}</td>
        </tr>
        <tr>
            <td>Other Allowances</td>
            <td class="right">{{ $payment->other_allowance }}</td>
            <td>Advance</td>
            <td class="right">{{ $payment->advance }}</td>
        </tr>
        <tr>
            <td><b>Gross Earnings</b></td>
            <td class="right"><b>{{ $payment->gross_payable }}</b></td>
            <td><b>Total Deductions</b></td>
            <td class="right"><b>{{ $payment->total_deduction }}</b></td>
        </tr>
    </table>
    <br>

    <!-- Net Pay -->
    <h3 class="center">Net Amount Paid: Rs {{ $payment->net_payable }}</h3>

    <!-- Leave Balance -->
    <!-- <table>
        <tr>
            <th>Leave Type</th>
            <th>Opening</th>
            <th>Availed</th>
            <th>Closing</th>
        </tr>
        <tr>
            <td>CL</td>
            <td>{{ $leave->cl_open ?? 0 }}</td>
            <td>{{ $leave->cl_availed ?? 0 }}</td>
            <td>{{ $leave->cl_close ?? 0 }}</td>
        </tr>
        <tr>
            <td>SL</td>
            <td>{{ $leave->sl_open ?? 0 }}</td>
            <td>{{ $leave->sl_availed ?? 0 }}</td>
            <td>{{ $leave->sl_close ?? 0 }}</td>
        </tr>
        <tr>
            <td>PL</td>
            <td>{{ $leave->pl_open ?? 0 }}</td>
            <td>{{ $leave->pl_availed ?? 0 }}</td>
            <td>{{ $leave->pl_close ?? 0 }}</td>
        </tr>
    </table> -->
    <br>

    <p class="right">Prepared By: <b>HR</b></p>

</body>
</html>

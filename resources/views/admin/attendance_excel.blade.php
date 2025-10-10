<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
</head>
<body>
    <h3 style="text-align: center;">User Attendance Report</h3>
    <br>
<table>
    <thead>
        <tr>
            <th>Employee Name</th>
            <th>Date</th>
            <th>Clock In</th>
            <th>Clock Out</th>
            <th>Worked Hours</th>
            <th>Overtime (Extra Hrs)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $att)
            <tr>
                <td>{{ $att->user->name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($att->clock_in)->format('Y-m-d') }}</td>
                <td>{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('h:i A') : '—' }}</td>
                <td>{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('h:i A') : '—' }}</td>
                <td>{{ $att->worked_hours }}</td>
                <td>{{ $att->overtime }}</td> <!-- ✅ Only numeric value -->
            </tr>
        @endforeach
    </tbody>
</table>


    <!-- <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>#</th>
                <th>User Name</th>
                <th>Date</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Hours Worked</th>
                <th>Status</th> {{-- New column --}}
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $row)
                @php
                    $clockIn = \Carbon\Carbon::parse($row->clock_in);
                    $clockOut = $row->clock_out ? \Carbon\Carbon::parse($row->clock_out) : null;
                    $workedHours = $clockOut ? $clockIn->diffInHours($clockOut) : null;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->user->name }}</td>
                    <td>{{ $clockIn->format('Y-m-d') }}</td>
                    <td>{{ $clockIn->format('h:i A') }}</td>
                    <td>{{ $clockOut ? $clockOut->format('h:i A') : '—' }}</td>
                    <td>{{ $workedHours ? $workedHours . ' hrs' : '—' }}</td>
                    <td>
                        @if($workedHours !== null)
                            {{ $workedHours <= 4 ? 'Half Day' : 'Full Day' }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table> -->
</body>
</html>

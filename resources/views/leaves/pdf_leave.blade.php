<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Leave Application</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    td, th { border: 1px solid #444; padding: 8px; text-align: left; }
  </style>
</head>
<body>

  <h2 style="text-align:center;">Leave Approval Report</h2>

  <table>
      <tr><th>Employee Name</th><td>{{ $leave->user->name }}</td></tr>
      <tr><th>Leave Type</th><td>{{ $leave->type }}</td></tr>
      <tr><th>From</th><td>{{ $leave->from_date }}</td></tr>
      <tr><th>To</th><td>{{ $leave->to_date }}</td></tr>
      <tr><th>Reason</th><td>{{ $leave->reason }}</td></tr>
      <tr><th>HOD Status</th><td>{{ $leave->hod_name }}</td></tr>
      <tr><th>HR Status</th><td>{{ $leave->status }}</td></tr>
      <tr><th>HR Remark</th><td>{{ $leave->hr_reason }}</td></tr>
  </table>

</body>
</html>

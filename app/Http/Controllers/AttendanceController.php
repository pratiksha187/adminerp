<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Yajra\DataTables\DataTables;
use App\Models\ManualAttendance;


class AttendanceController extends Controller
{
   
    public function clockIn(Request $request)
    {
        $time = $request->device_time ?? now(); // fallback to server time

        Attendance::create([
            'user_id' => auth()->id(),
            'clock_in' => $time,
        ]);

        return redirect()->back()->with('success', 'Clock In successful!');
    }


    
    public function clockOut(Request $request)
    {
        $time = $request->device_time ?? now();

        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('clock_in', now()->toDateString())
            ->first();

        if ($attendance) {
            $attendance->update(['clock_out' => $time]);
        }

        return redirect()->back()->with('success', 'Clock Out successful!');
    }

    public function report(Request $request)
    {
        $start = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $attendances = Attendance::with('user')
            ->whereBetween('clock_in', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->orderBy('clock_in', 'desc')
            ->get();

        return view('admin.attendance_report', compact('attendances', 'start', 'end'));
    }

    public function export(Request $request)
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end = $request->end_date ?? now()->endOfMonth()->toDateString();

        return Excel::download(new AttendanceExport($start, $end), 'AttendanceReport.xlsx');
    }



    public function attendanceDatatable(Request $request)
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end = $request->end_date ?? now()->endOfMonth()->toDateString();

        $data = \App\Models\Attendance::with('user')
            ->whereBetween('clock_in', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->orderBy('clock_in', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('date', function($row) {
                return \Carbon\Carbon::parse($row->clock_in)->format('Y-m-d');
            })
            ->editColumn('clock_in', function($row) {
                return \Carbon\Carbon::parse($row->clock_in)->format('h:i A');
            })
            ->editColumn('clock_out', function($row) {
                return $row->clock_out 
                    ? \Carbon\Carbon::parse($row->clock_out)->format('h:i A') 
                    : '—';
            })
            ->addColumn('worked_hours', function($row) {
                if ($row->clock_in && $row->clock_out) {
                    return \Carbon\Carbon::parse($row->clock_in)
                            ->diff(\Carbon\Carbon::parse($row->clock_out))
                            ->format('%h hrs %i min');
                }
                return '—';
            })
            ->make(true);
    }


    public function  manualattendence(){
        return view('user.manualattendence');
    }

    public function acceptattendence(){
        return view('admin.acceptattendence');

    }
   public function manualEntry(Request $request)
{
    // dd($request->all());
    $request->validate([
        'date' => 'required|date',
        'manual_clock_in' => 'required|date_format:H:i',
        'manual_clock_out' => 'required|date_format:H:i',
    ]);

    $clockIn = Carbon::parse($request->date . ' ' . $request->manual_clock_in);
    $clockOut = Carbon::parse($request->date . ' ' . $request->manual_clock_out);

    if ($clockOut->lessThanOrEqualTo($clockIn)) {
        return back()->with('error', 'Clock out time must be after clock in time.');
    }

    $user = auth()->user();

    $existing = ManualAttendance::where('user_id', $user->id)
        ->whereDate('clock_in', $clockIn->toDateString())
        ->first();

    if ($existing) {
        return back()->with('error', 'Manual attendance for this date already exists.');
    }

    ManualAttendance::create([
        'date' => $user->date,
        'user_id' => $user->id,
        'clock_in' => $clockIn,
        'clock_out' => $clockOut,
        'status' => '0'
    ]);

    return back()->with('success', 'Manual attendance entry saved successfully.');
}

    public function getManualData(Request $request)
    {
        if ($request->ajax()) {
            $data = ManualAttendance::query();
            // echo"<pre>";
            // print_r($data);
            return DataTables::of($data)->make(true);
        }
    }

    public function handleManualAction(Request $request)
{
    $request->validate([
        'id' => 'required|exists:manual_attendances,id',
        'action' => 'required|in:accept,reject',
    ]);

    $manual = ManualAttendance::findOrFail($request->id);

    if ($request->action == 'accept') {
        Attendance::create([
            'user_id' => $manual->user_id,
            'clock_in' => $manual->clock_in,
            'clock_out' => $manual->clock_out,
        ]);
        $manual->status = '1';
    } else {
        $manual->status = '2';
    }

    $manual->save();

    return response()->json(['message' => 'Manual attendance ' . $request->action . 'ed']);
}



}


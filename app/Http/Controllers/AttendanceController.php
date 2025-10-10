<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Yajra\DataTables\DataTables;
use App\Models\ManualAttendance;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $maxDistanceMeters = 500;
        $userLat = (float) $request->input('latitude');
        $userLng = (float) $request->input('longitude');

        // ✅ STEP 1: Check if already clocked in today
        $existing = Attendance::where('user_id', auth()->id())
            ->whereDate('clock_in', now('Asia/Kolkata')->toDateString())
            ->first();

        if ($existing) {
            return back()->with('error', '⚠️ You have already clocked in today.');
        }

        // ✅ STEP 2: Location validation
        $locations = \App\Models\Location::select('latitude', 'longitude')->get();
        $withinAllowedLocation = false;

        foreach ($locations as $location) {
            if ($this->distance(
                (float) $location->latitude,
                (float) $location->longitude,
                $userLat,
                $userLng
            ) <= $maxDistanceMeters) {
                $withinAllowedLocation = true;
                break;
            }
        }

        if (!$withinAllowedLocation) {
            return back()->with('error', '❌ You are outside the allowed clock-in zone.');
        }

        // ✅ STEP 3: Authoritative IST clock-in time
        $nowIst = \Carbon\Carbon::now('Asia/Kolkata');

        // ✅ STEP 4: Insert clean record
        Attendance::create([
            'user_id'   => auth()->id(),
            'clock_in'  => $nowIst,
            'latitude'  => round($userLat, 8),
            'longitude' => round($userLng, 8),
        ]);

        return back()->with('success', '✅ Clocked in successfully!');
    }

    // public function clockIn(Request $request)
    // {
    //     // 1) Validate cleanly (no device_time needed)
    //     $request->validate([
    //         'latitude'  => 'required|numeric|between:-90,90',
    //         'longitude' => 'required|numeric|between:-180,180',
    //     ]);

    //     $maxDistanceMeters = 500;

    //     $userLat = (float) $request->input('latitude');
    //     $userLng = (float) $request->input('longitude');

    //     // 2) Geofence check against saved Locations
    //     $locations = Location::query()->select('latitude', 'longitude')->get();

    //     $withinAllowedLocation = false;
    //     foreach ($locations as $location) {
    //         if ($this->distance(
    //             (float) $location->latitude,
    //             (float) $location->longitude,
    //             $userLat,
    //             $userLng
    //         ) <= $maxDistanceMeters) {
    //             $withinAllowedLocation = true;
    //             break;
    //         }
    //     }

    //     if (!$withinAllowedLocation) {
    //         return back()->with('error', 'You are outside the allowed clock-in zones.');
    //     }

    //     // 3) Authoritative clock-in: server time in IST
    //     $nowIst = Carbon::now('Asia/Kolkata');

    //     // 4) Save (round to 8 dp if your DB is DECIMAL(12,8))
    //     Attendance::create([
    //         'user_id'   => auth()->id(),
    //         'clock_in'  => $nowIst,                   // authoritative IST time
    //         'latitude'  => round($userLat, 8),
    //         'longitude' => round($userLng, 8),
    //         // 'clock_out' left null by default
    //     ]);

    //     return back()->with('success', 'Clocked in successfully!');
    // }

    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2
           + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
    
    // public function clockOut(Request $request)
    // {
    //     // $time = $request->device_time ?? now();
    //     $time = $request->device_time ? Carbon::parse($request->device_time)->timezone('Asia/Kolkata') : now(); 

    //     $attendance = Attendance::where('user_id', auth()->id())
    //         ->whereDate('clock_in', now()->toDateString())
    //         ->first();

    //     if ($attendance) {
    //         $attendance->update(['clock_out' => $time]);
    //     }

    //     return redirect()->back()->with('success', 'Clock Out successful!');
    // }

    public function clockOut(Request $request)
    {
        // ✅ 1) Validate location input
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $maxDistanceMeters = 500;
        $userLat = (float) $request->input('latitude');
        $userLng = (float) $request->input('longitude');

        // ✅ 2) Geofence validation (same as ClockIn)
        $locations = \App\Models\Location::select('latitude', 'longitude')->get();
        $withinAllowedLocation = false;

        foreach ($locations as $location) {
            if ($this->distance(
                (float) $location->latitude,
                (float) $location->longitude,
                $userLat,
                $userLng
            ) <= $maxDistanceMeters) {
                $withinAllowedLocation = true;
                break;
            }
        }

        if (!$withinAllowedLocation) {
            return back()->with('error', '❌ You are outside the allowed clock-out zones.');
        }

        // ✅ 3) Find today’s attendance record
        $attendance = \App\Models\Attendance::where('user_id', auth()->id())
            ->whereDate('clock_in', now('Asia/Kolkata')->toDateString())
            ->first();

        if (!$attendance) {
            return back()->with('error', '⚠️ You have not clocked in today.');
        }

        // ✅ 4) Prevent multiple clock-outs
        if ($attendance->clock_out) {
            return back()->with('error', '⚠️ You have already clocked out today.');
        }

        // ✅ 5) Use authoritative server time (IST)
        $nowIst = \Carbon\Carbon::now('Asia/Kolkata');

        // ✅ 6) Update attendance with clock-out + location data
        $attendance->update([
            'clock_out' => $nowIst,
            'out_latitude'  => round($userLat, 8),
            'out_longitude' => round($userLng, 8),
        ]);

        return redirect()->back()->with('success', '✅ Clock Out successful!');
    }


    // public function report(Request $request)
    // {
    //     $userId = Auth::id();
    //     $userDetails = DB::table('users')
    //                 ->select('role')
    //                 ->where('id', $userId)   // ✅ match by id, not role
    //                 ->first();

    //     $role = $userDetails->role;
    //     $start = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
    //     $end = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

    //     $attendances = Attendance::with('user')
    //         ->whereBetween('clock_in', [$start . ' 00:00:00', $end . ' 23:59:59'])
    //         ->orderBy('clock_in', 'desc')
    //         ->get();

    //     return view('admin.attendance_report', compact('attendances', 'start', 'end','role'));
    // }
    public function report(Request $request)
{
    $userId = Auth::id();
    $userDetails = DB::table('users')
        ->select('role')
        ->where('id', $userId)
        ->first();

    $role = $userDetails->role;
    $start = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
    $end = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

    // ✅ Fetch attendances
    $attendances = Attendance::with('user')
        ->whereBetween('clock_in', [$start . ' 00:00:00', $end . ' 23:59:59'])
        ->orderBy('clock_in', 'desc')
        ->get();

    // ✅ Define normal working hours
    $normalWorkingHours = 9; // 9 hours per day

    // ✅ Calculate total hours and overtime for each entry
    foreach ($attendances as $attendance) {
        if ($attendance->clock_in && $attendance->clock_out) {
            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = Carbon::parse($attendance->clock_out);

            // Total working hours
            $totalHours = $clockOut->diffInHours($clockIn);
            $attendance->total_hours = $totalHours;

            // Overtime calculation
            $attendance->overtime_hours = $totalHours > $normalWorkingHours 
                ? $totalHours - $normalWorkingHours 
                : 0;
        } else {
            $attendance->total_hours = 0;
            $attendance->overtime_hours = 0;
        }
    }

    return view('admin.attendance_report', compact('attendances', 'start', 'end', 'role'));
}


    public function export(Request $request)
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end = $request->end_date ?? now()->endOfMonth()->toDateString();
// dd($end ); 
        return Excel::download(new AttendanceExport($start, $end), 'AttendanceReport.xlsx');
    }



    // public function attendanceDatatable(Request $request)
    // {
    //     $start = $request->start_date ?? now()->startOfMonth()->toDateString();
    //     $end = $request->end_date ?? now()->endOfMonth()->toDateString();

    //     $data = \App\Models\Attendance::with('user')
    //         ->whereBetween('clock_in', [$start . ' 00:00:00', $end . ' 23:59:59'])
    //         ->orderBy('clock_in', 'desc');

    //     return DataTables::of($data)
    //         ->addIndexColumn()
    //         ->editColumn('date', function($row) {
    //             return \Carbon\Carbon::parse($row->clock_in)->format('Y-m-d');
    //         })
    //         ->editColumn('clock_in', function($row) {
    //             return \Carbon\Carbon::parse($row->clock_in)->format('h:i A');
    //         })
    //         ->editColumn('clock_out', function($row) {
    //             return $row->clock_out 
    //                 ? \Carbon\Carbon::parse($row->clock_out)->format('h:i A') 
    //                 : '—';
    //         })
    //         ->addColumn('worked_hours', function($row) {
    //             if ($row->clock_in && $row->clock_out) {
    //                 return \Carbon\Carbon::parse($row->clock_in)
    //                         ->diff(\Carbon\Carbon::parse($row->clock_out))
    //                         ->format('%h hrs %i min');
    //             }
    //             return '—';
    //         })
    //         ->make(true);
    // }

public function attendanceDatatable(Request $request)
{
    $start = $request->start_date ?? now()->startOfMonth()->toDateString();
    $end = $request->end_date ?? now()->endOfMonth()->toDateString();

    $data = \App\Models\Attendance::with('user')
        ->whereBetween('clock_in', [$start . ' 00:00:00', $end . ' 23:59:59'])
        ->orderBy('clock_in', 'desc');

    return DataTables::of($data)
        ->addIndexColumn()

        // ✅ Format Date
        ->editColumn('date', function ($row) {
            return $row->clock_in 
                ? \Carbon\Carbon::parse($row->clock_in)->format('Y-m-d') 
                : '—';
        })

        // ✅ Format Clock In
        ->editColumn('clock_in', function ($row) {
            return $row->clock_in 
                ? \Carbon\Carbon::parse($row->clock_in)->format('h:i A') 
                : '—';
        })

        // ✅ Format Clock Out
        ->editColumn('clock_out', function ($row) {
            return $row->clock_out 
                ? \Carbon\Carbon::parse($row->clock_out)->format('h:i A') 
                : '—';
        })

        // ✅ Calculate Worked Hours
        ->addColumn('worked_hours', function ($row) {
            if ($row->clock_in && $row->clock_out) {
                $in = \Carbon\Carbon::parse($row->clock_in);
                $out = \Carbon\Carbon::parse($row->clock_out);

                $hours = $in->diffInHours($out);
                $minutes = $in->diffInMinutes($out) % 60;

                return sprintf('%02d hrs %02d min', $hours, $minutes);
            }
            return '—';
        })

        // ✅ Calculate Overtime (beyond 9 hours)
        ->addColumn('overtime', function ($row) {
            $normalHours = 9;

            if ($row->clock_in && $row->clock_out) {
                $in = \Carbon\Carbon::parse($row->clock_in);
                $out = \Carbon\Carbon::parse($row->clock_out);

                $workedHours = $in->diffInHours($out);

                if ($workedHours > $normalHours) {
                    return ($workedHours - $normalHours) . ' hrs';
                }
                return '0 hrs';
            }

            return '—';
        })

        ->make(true);
}

    public function  manualattendence(){
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   // ✅ match by id, not role
                    ->first();

       $role = $userDetails->role;
        return view('user.manualattendence',compact('role'));
    }

    public function acceptattendence(){
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   // ✅ match by id, not role
                    ->first();

        $role = $userDetails->role;
        return view('admin.acceptattendence',compact('role'));

    }
   public function manualEntry(Request $request)
{
    
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
// dd($user->id);


    // $existing = ManualAttendance::where('user_id', $user->id)
    //     ->whereDate('clock_in', $clockIn->toDateString())
    //     ->first();
// dd($existing());
    // if ($existing) {
    //     return back()->with('error', 'Manual attendance for this date already exists.');
    // }

    ManualAttendance::create([
        'date' => $request->date,
        'user_id' => $user->id,
        'clock_in' => $clockIn,
        'clock_out' => $clockOut,
        'status' => '0'
    ]);

    return back()->with('success', 'Manual attendance entry saved successfully.');
}

    // public function getManualData(Request $request)
    // {
    //     $userId = Auth::id();
    //     if ($request->ajax()) {
    //         $data = ManualAttendance::query();
           
    //         // dd($data);
    //         return DataTables::of($data)->make(true);
    //     }
    // }
public function getManualData(Request $request)
{
    $userId = Auth::id();

    if ($request->ajax()) {
        $data = ManualAttendance::query()
                  ->where('user_id', $userId);  // ✅ filter by logged-in user

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


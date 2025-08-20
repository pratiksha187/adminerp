<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceCalendarController extends Controller
{
    // Page with the calendar
    public function view()
    {
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   // ✅ match by id, not role
                    ->first();

        $role = $userDetails->role;
        return view('admin.calendar',compact('role')); // resources/views/attendance/calendar.blade.php
    }

//    public function events(Request $request)
// {
//     $userId = Auth::id();

//     $start = $request->query('start');
//     $end   = $request->query('end');

//     $query = Attendance::where('user_id', $userId);

//     if ($start && $end) {
//         $query->whereBetween('clock_in', [
//             \Carbon\Carbon::parse($start)->startOfDay(),
//             \Carbon\Carbon::parse($end)->endOfDay(),
//         ]);
//     }

//     $rows = $query->orderBy('clock_in')->get();

//     $presentDays = [];
//     $events = $rows->map(function ($row) use (&$presentDays) {
//         $in  = $row->clock_in ? \Carbon\Carbon::parse($row->clock_in) : null;
//         $out = $row->clock_out ? \Carbon\Carbon::parse($row->clock_out) : null;

//         if (!$in) {
//             return null; // skip if no clock_in
//         }

//         // Default: open shift
//         $title = 'In ' . $in->format('h:i A') . ' – Open';
//         $color = '#f59e0b'; // amber for open
//         $endForCal = null;

//         if ($out) {
//             // Fix bad data where OUT < IN (clamp to end of IN day)
//             if ($out->lt($in)) {
//                 $adjustedOut = (clone $in)->setTime(23, 59, 0);
//                 $mins = $in->diffInMinutes($adjustedOut);
//                 $title = 'In ' . $in->format('h:i A') . ' – Out ' . $adjustedOut->format('h:i A')
//                        . ' (' . floor($mins/60) . 'h' . str_pad($mins%60, 2, '0', STR_PAD_LEFT) . 'm)';
//                 $endForCal = $adjustedOut;
//             } else {
//                 $mins = $in->diffInMinutes($out);
//                 $title = 'In ' . $in->format('h:i A') . ' – Out ' . $out->format('h:i A')
//                        . ' (' . floor($mins/60) . 'h' . str_pad($mins%60, 2, '0', STR_PAD_LEFT) . 'm)';
//                 $endForCal = $out;
//             }

//             $color = '#16a34a'; // green for complete
//             $presentDays[] = $in->toDateString(); // mark this date as "present"
//         }

//         return [
//             'id'    => $row->id,
//             'title' => $title,
//             'start' => $in->toIso8601String(),
//             'end'   => $endForCal ? $endForCal->toIso8601String() : null,
//             'allDay'=> false,
//             'color' => $color,
//             'extendedProps' => [
//                 'lat' => $row->latitude,
//                 'lng' => $row->longitude,
//             ],
//         ];
//     })->filter(); // remove nulls

//     // Soft green background for present days
//     $bgEvents = collect(array_unique($presentDays))->map(function ($day) {
//         return [
//             'start'   => $day,
//             'end'     => \Carbon\Carbon::parse($day)->addDay()->toDateString(),
//             'display' => 'background',
//             'color'   => '#ECFDF5',
//             'allDay'  => true,
//             'groupId' => 'present-bg',
//         ];
//     });

//     return response()->json($events->concat($bgEvents)->values());
// }
 public function events(Request $request)
    {
        $userId = Auth::id();

        // FullCalendar sends ?start=YYYY-MM-DD&end=YYYY-MM-DD
        $start = $request->query('start');
        $end   = $request->query('end');

        $query = Attendance::where('user_id', $userId);

        if ($start && $end) {
            $query->whereBetween('clock_in', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay(),
            ]);
        }

        $rows = $query->orderBy('clock_in')->get();

        $presentDays = [];
        $events = $rows->map(function ($row) use (&$presentDays) {
            $in  = $row->clock_in ? Carbon::parse($row->clock_in) : null;
            $out = $row->clock_out ? Carbon::parse($row->clock_out) : null;

            if (!$in) return null; // skip bad row

            $status = 'open';
            $endForCal = null;
            $durText = null;

            if ($out) {
                // If bad data (out < in), clamp to 23:59 of the in-day
                if ($out->lt($in)) {
                    $out = (clone $in)->setTime(23, 59, 0);
                }
                $mins = $in->diffInMinutes($out);
                $durText = sprintf('%dh%02dm', intdiv($mins, 60), $mins % 60);
                $endForCal = $out;
                $status = 'complete';
                $presentDays[] = $in->toDateString();
            }

            return [
                'id'    => $row->id,
                'title' => '', // we'll render custom HTML via eventContent
                'start' => $in->toIso8601String(),
                'end'   => $endForCal ? $endForCal->toIso8601String() : null,
                'allDay'=> false,
                'color' => $status === 'complete' ? '#16a34a' : '#f59e0b', // dot color
                'extendedProps' => [
                    'status'  => $status,
                    'inText'  => $in->format('h:i A'),                // 12-hr
                    'outText' => $endForCal ? $endForCal->format('h:i A') : '—',
                    'durText' => $durText,                            // e.g., 8h30m or null
                    'lat'     => $row->latitude,
                    'lng'     => $row->longitude,
                ],
            ];
        })->filter()->values();

        // Soft green background for present days
        $bgEvents = collect(array_unique($presentDays))->map(function ($day) {
            return [
                'start'   => $day,
                'end'     => Carbon::parse($day)->addDay()->toDateString(),
                'display' => 'background',
                'color'   => '#ECFDF5',
                'allDay'  => true,
                'groupId' => 'present-bg',
            ];
        });

        return response()->json($events->concat($bgEvents)->values());
    }

}

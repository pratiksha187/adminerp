<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Holiday;
use Carbon\CarbonPeriod;
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
// app/Http/Controllers/AttendanceCalendarController.php


    public function events(Request $request)
    {
        $userId  = Auth::id();
        $start   = $request->query('start');
        $end     = $request->query('end');

        $startAt = $start ? Carbon::parse($start)->startOfDay() : now()->startOfMonth();
        $endAt   = $end   ? Carbon::parse($end)->endOfDay()     : now()->endOfMonth();

        /* ===================================================
         * SHIFT POLICY (adjust to your office rules)
         * =================================================== */
        $SHIFT_START   = '09:00';     // fixed office start time (e.g., '09:00' or '09:30')
        $REQUIRED_MINS = 9 * 60;      // 9 hours required in a day
        $GRACE_IN      = 10;          // minutes allowed after shift start before "Late In"
        $GRACE_END     = 5;           // tolerance around expected end for Early/OT checks

        /* ---------------------------
         * 1) ATTENDANCE EVENTS
         * --------------------------- */
        $rows = Attendance::where('user_id', $userId)
            ->whereBetween('clock_in', [$startAt, $endAt])
            ->orderBy('clock_in')
            ->get();

        $presentDays = [];
        $attendanceEvents = $rows->map(function ($row) use (&$presentDays, $SHIFT_START, $REQUIRED_MINS, $GRACE_IN, $GRACE_END) {
            $in  = $row->clock_in ? Carbon::parse($row->clock_in) : null;
            $out = $row->clock_out ? Carbon::parse($row->clock_out) : null;
            if (!$in) return null;

            // Fix data: if OUT < IN, clamp to 23:59 same day
            if ($out && $out->lt($in)) {
                $out = (clone $in)->setTime(23, 59, 0);
            }

            // Build shift bounds for THIS day
            $shiftIn     = (clone $in)->setTimeFromTimeString($SHIFT_START);
            $expectedEnd = (clone $shiftIn)->addMinutes($REQUIRED_MINS);

            // Duration
            $status  = $out ? 'complete' : 'open';
            $durMins = null;
            $durText = null;
            if ($out) {
                $durMins = $in->diffInMinutes($out);
                $durText = sprintf('%dh%02dm', intdiv($durMins,60), $durMins%60);
                $presentDays[] = $in->toDateString();
            }

            // Late In
            $lateMins = 0;
            $lateThreshold = (clone $shiftIn)->addMinutes($GRACE_IN);
            if ($in->gt($lateThreshold)) {
                $lateMins = $lateThreshold->diffInMinutes($in);
            }

            // Early Leave / OT
            $earlyMins = 0;
            $otMins    = 0;
            if ($out) {
                $earlyThreshold = (clone $expectedEnd)->subMinutes($GRACE_END);
                $otThreshold    = (clone $expectedEnd)->addMinutes($GRACE_END);

                if ($out->lt($earlyThreshold)) {
                    $earlyMins = $out->diffInMinutes($earlyThreshold);
                } elseif ($out->gt($otThreshold)) {
                    $otMins = $otThreshold->diffInMinutes($out);
                }
            }

            return [
                'id'    => $row->id,
                'title' => '', // custom HTML rendered on the client
                'start' => $in->toIso8601String(),
                'end'   => $out ? $out->toIso8601String() : null,
                'allDay'=> false,
                'color' => $status === 'complete' ? '#16a34a' : '#f59e0b', // dot color
                'extendedProps' => [
                    'kind'      => 'attendance',
                    'status'    => $status,
                    'inText'    => $in->format('h:i A'),
                    'outText'   => $out ? $out->format('h:i A') : '—',
                    'durText'   => $durText,
                    'durMins'   => $durMins,
                    'lateMins'  => $lateMins,
                    'earlyMins' => $earlyMins,
                    'otMins'    => $otMins,
                    'lat'       => $row->latitude,
                    'lng'       => $row->longitude,
                ],
            ];
        })->filter()->values();

        /* ---------------------------
         * 2) HOLIDAYS (optional, if table exists)
         * --------------------------- */
        $holidayBg = collect();
        $holidayLabels = collect();
        if (class_exists(Holiday::class)) {
            $holidays = Holiday::whereBetween('date', [$startAt->toDateString(), $endAt->toDateString()])->get();

            $holidayBg = $holidays->map(function ($h) {
                return [
                    'start'   => $h->date->toDateString(),
                    'end'     => $h->date->copy()->addDay()->toDateString(), // exclusive
                    'display' => 'background',
                    'allDay'  => true,
                    'color'   => $h->color ?: '#FEE2E2', // soft red
                    'groupId' => 'holiday-bg',
                    'extendedProps' => ['kind' => 'holiday-bg'],
                ];
            });

            $holidayLabels = $holidays->map(function ($h) {
                return [
                    'title' => $h->title,
                    'start' => $h->date->toDateString(),
                    'allDay'=> true,
                    'display' => 'block',
                    'color' => '#ef4444',
                    'textColor' => '#ffffff',
                    'extendedProps' => ['kind' => 'holiday-label', 'title' => $h->title],
                ];
            });
        }

        /* ---------------------------
         * 3) WEEKLY OFFS (e.g., Sundays)
         * --------------------------- */
        $weeklyOffDOW = [0]; // 0=Sun. Add 6 for Sat: [0,6]
        $weeklyOffDates = collect();
        foreach (CarbonPeriod::create($startAt, '1 day', $endAt) as $d) {
            if (in_array($d->dayOfWeek, $weeklyOffDOW)) {
                $weeklyOffDates->push($d->toDateString());
            }
        }
        $weeklyOffBg = $weeklyOffDates->map(fn($day) => [
            'start'   => $day,
            'end'     => Carbon::parse($day)->addDay()->toDateString(),
            'display' => 'background',
            'allDay'  => true,
            'color'   => '#FFEFE3', // soft orange
            'groupId' => 'weeklyoff-bg',
            'extendedProps' => ['kind' => 'weeklyoff-bg'],
        ]);
        $weeklyOffLabels = $weeklyOffDates->map(fn($day) => [
            'title' => 'Weekly Off',
            'start' => $day,
            'allDay'=> true,
            'display' => 'block',
            'color' => '#fb923c',
            'textColor' => '#ffffff',
            'extendedProps' => ['kind' => 'weeklyoff-label', 'title' => 'Weekly Off'],
        ]);

        /* ---------------------------
         * 4) PRESENT-DAY BACKGROUND
         * --------------------------- */
        $presentBg = collect(array_unique($presentDays))->map(function ($day) {
            return [
                'start'   => $day,
                'end'     => Carbon::parse($day)->addDay()->toDateString(),
                'display' => 'background',
                'allDay'  => true,
                'color'   => '#ECFDF5', // soft green
                'groupId' => 'present-bg',
                'extendedProps' => ['kind' => 'present-bg'],
            ];
        });

        // Order matters: backgrounds → labels → attendance
        return response()->json(
            $presentBg
                ->concat($holidayBg)->concat($weeklyOffBg)
                ->concat($holidayLabels)->concat($weeklyOffLabels)
                ->concat($attendanceEvents)
                ->values()
        );
    }



}

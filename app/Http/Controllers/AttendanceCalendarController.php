<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;

use App\Models\Holiday;
use App\Models\Leave;
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

// public function events(Request $request)
// {
//     $userId  = Auth::id();
//     $start   = $request->query('start');
//     $end     = $request->query('end');

//     $startAt = $start ? Carbon::parse($start)->startOfDay() : now()->startOfMonth();
//     $endAt   = $end   ? Carbon::parse($end)->endOfDay()     : now()->endOfMonth();

//     $SHIFT_START   = '09:00';     
//     $REQUIRED_MINS = 9 * 60;      
//     $GRACE_IN      = 10;         
//     $GRACE_END     = 5;       

//     $fmtHM = function (?int $mins): ?string {
//         if (!$mins || $mins <= 0) return null;
//         $h = intdiv($mins, 60);
//         $m = $mins % 60;
//         return $h . 'h' . ($m ? ' ' . str_pad((string)$m, 2, '0', STR_PAD_LEFT) . 'm' : '');
//     };

//     $rows = Attendance::where('user_id', $userId)
//             ->whereBetween('clock_in', [$startAt, $endAt])
//             ->orderBy('clock_in')
//             ->get();

//     $presentDays = [];

//     // Holidays
//     $holidays = collect();
//     if (class_exists(Holiday::class)) {
//         $holidays = Holiday::whereBetween('date', [$startAt->toDateString(), $endAt->toDateString()])->get();
//     }
//     $holidayDates = $holidays->pluck('date')->map(fn($d) => $d->toDateString())->toArray();

//     // Weekly off (Sunday)
//     $weeklyOffDOW = [0];
//     $weeklyOffDates = collect();
//     foreach (CarbonPeriod::create($startAt, '1 day', $endAt) as $d) {
//         if (in_array($d->dayOfWeek, $weeklyOffDOW)) {
//             $weeklyOffDates->push($d->toDateString());
//         }
//     }

//     $attendanceEvents = $rows->map(function ($row) use (
//         &$presentDays, $SHIFT_START, $REQUIRED_MINS, $GRACE_IN, $GRACE_END, $fmtHM, 
//         $holidayDates, $weeklyOffDates
//     ) {
//         $in  = $row->clock_in ? Carbon::parse($row->clock_in) : null;
//         $out = $row->clock_out ? Carbon::parse($row->clock_out) : null;
//         if (!$in) return null;

//         if ($out && $out->lt($in)) {
//             $out = (clone $in)->setTime(23, 59, 0);
//         }

//         $shiftIn     = (clone $in)->setTimeFromTimeString($SHIFT_START);
//         $expectedEnd = (clone $shiftIn)->addMinutes($REQUIRED_MINS);

//         $status  = $out ? 'complete' : 'open';
//         $durMins = $out ? $in->diffInMinutes($out) : null;
//         $durText = $durMins ? sprintf('%dh%02dm', intdiv($durMins,60), $durMins%60) : null;

//         $presentDays[] = $in->toDateString();

//         $lateMins = 0;
//         $lateThreshold = (clone $shiftIn)->addMinutes($GRACE_IN);
//         if ($in->gt($lateThreshold)) {
//             $lateMins = $lateThreshold->diffInMinutes($in);
//         }

//         $earlyMins = 0;
//         $otMins    = 0;
//         if ($out) {
//             $earlyThreshold = (clone $expectedEnd)->subMinutes($GRACE_END);
//             $otThreshold    = (clone $expectedEnd)->addMinutes($GRACE_END);

//             if ($out->lt($earlyThreshold)) {
//                 $earlyMins = $out->diffInMinutes($earlyThreshold);
//             } elseif ($out->gt($otThreshold)) {
//                 $otMins = $otThreshold->diffInMinutes($out);
//             }
//         }

//         // ✅ Comp Off: if attendance on holiday or weekly off
//         $day = $in->toDateString();
//         $isCOff = in_array($day, $holidayDates) || in_array($day, $weeklyOffDates->toArray());

//         return [
//             'id'    => $row->id,
//             'title' => $isCOff ? 'C.Off' : '',
//             'start' => $in->toIso8601String(),
//             'end'   => $out ? $out->toIso8601String() : null,
//             'allDay'=> false,
//             'color' => $isCOff ? '#2563EB' : ($status === 'complete' ? '#16a34a' : '#f59e0b'), // Blue for C.Off
//             'extendedProps' => [
//                 'kind'      => 'attendance',
//                 'status'    => $status,
//                 'c_off'     => $isCOff,
//                 'inText'    => $in->format('h:i A'),
//                 'outText'   => $out ? $out->format('h:i A') : '—',
//                 'durText'   => $durText,
//                 'durMins'   => $durMins,
//                 'lateMins'  => $lateMins,
//                 'earlyMins' => $earlyMins,
//                 'otMins'    => $otMins,
//                 'lateText'  => $fmtHM($lateMins),
//                 'earlyText' => $fmtHM($earlyMins),
//                 'otText'    => $fmtHM($otMins),
//                 'lat'       => $row->latitude,
//                 'lng'       => $row->longitude,
//             ],
//         ];
//     })->filter()->values();

//     // --- Holidays / Weekly Off Background & Labels ---
//     $holidayBg = $holidays->map(fn($h) => [
//         'start' => $h->date->toDateString(),
//         'end'   => $h->date->copy()->addDay()->toDateString(),
//         'display' => 'background',
//         'allDay' => true,
//         'color' => $h->color ?: '#FEE2E2',
//         'groupId' => 'holiday-bg',
//         'extendedProps' => ['kind'=>'holiday-bg'],
//     ]);

//     $holidayLabels = $holidays->map(fn($h) => [
//         'title' => $h->title,
//         'start' => $h->date->toDateString(),
//         'allDay'=> true,
//         'display' => 'block',
//         'color' => '#ef4444',
//         'textColor' => '#ffffff',
//         'extendedProps'=>['kind'=>'holiday-label','title'=>$h->title],
//     ]);

//     $weeklyOffBg = $weeklyOffDates->map(fn($d) => [
//         'start' => $d,
//         'end' => Carbon::parse($d)->addDay()->toDateString(),
//         'display' => 'background',
//         'allDay' => true,
//         'color' => '#FFEFE3',
//         'groupId'=>'weeklyoff-bg',
//         'extendedProps'=>['kind'=>'weeklyoff-bg'],
//     ]);

//     $weeklyOffLabels = $weeklyOffDates->map(fn($d)=>[
//         'title'=>'Weekly Off',
//         'start'=>$d,
//         'allDay'=>true,
//         'display'=>'block',
//         'color'=>'#fb923c',
//         'textColor'=>'#fff',
//         'extendedProps'=>['kind'=>'weeklyoff-label','title'=>'Weekly Off']
//     ]);

//     // --- Present Day Background ---
//     $presentBg = collect(array_unique($presentDays))->map(fn($d)=>[
//         'start'=>$d,
//         'end'=>Carbon::parse($d)->addDay()->toDateString(),
//         'display'=>'background',
//         'allDay'=>true,
//         'color'=>'#ECFDF5',
//         'groupId'=>'present-bg',
//         'extendedProps'=>['kind'=>'present-bg']
//     ]);

//     // --- Absent Background ---
//     $today = now()->toDateString();
//     $absentBg = collect();
//     $absentLabels = collect();

//     foreach (CarbonPeriod::create($startAt, '1 day', $endAt) as $d) {
//         $day = $d->toDateString();
//         if ($day >= $today) continue;
//         if (in_array($day, $presentDays)) continue;
//         if (in_array($day, $holidayDates)) continue;
//         if (in_array($day, $weeklyOffDates->toArray())) continue;

//         $absentBg->push([
//             'start'=>$day,
//             'end'=>Carbon::parse($day)->addDay()->toDateString(),
//             'display'=>'background',
//             'allDay'=>true,
//             'color'=>'#FDE2E4',
//             'groupId'=>'absent-bg',
//             'extendedProps'=>['kind'=>'absent-bg'],
//         ]);

//         $absentLabels->push([
//             'title'=>'Absent',
//             'start'=>$day,
//             'allDay'=>true,
//             'display'=>'block',
//             'color'=>'#dc2626',
//             'textColor'=>'#fff',
//             'extendedProps'=>['kind'=>'absent-label','title'=>'Absent'],
//         ]);
//     }

//     return response()->json(
//         $presentBg
//         ->concat($holidayBg)->concat($weeklyOffBg)->concat($absentBg)
//         ->concat($holidayLabels)->concat($weeklyOffLabels)->concat($absentLabels)
//         ->concat($attendanceEvents)
//         ->values()
//     );
// }

public function events(Request $request)
{
    $userId  = Auth::id();
    $start   = $request->query('start');
    $end     = $request->query('end');

    $startAt = $start ? Carbon::parse($start)->startOfDay() : now()->startOfMonth();
    $endAt   = $end   ? Carbon::parse($end)->endOfDay()     : now()->endOfMonth();

    $SHIFT_START   = '09:00';     
    $REQUIRED_MINS = 9 * 60;      
    $GRACE_IN      = 10;         
    $GRACE_END     = 5;       

    $fmtHM = function (?int $mins): ?string {
        if (!$mins || $mins <= 0) return null;
        $h = intdiv($mins, 60);
        $m = $mins % 60;
        return $h . 'h' . ($m ? ' ' . str_pad((string)$m, 2, '0', STR_PAD_LEFT) . 'm' : '');
    };

    $rows = Attendance::where('user_id', $userId)
            ->whereBetween('clock_in', [$startAt, $endAt])
            ->orderBy('clock_in')
            ->get();

    $presentDays = [];

    // Holidays
    $holidays = collect();
    if (class_exists(Holiday::class)) {
        $holidays = Holiday::whereBetween('date', [$startAt->toDateString(), $endAt->toDateString()])->get();
    }
    $holidayDates = $holidays->pluck('date')->map(fn($d) => $d->toDateString())->toArray();

    // Weekly off (Sunday)
    $weeklyOffDOW = [0];
    $weeklyOffDates = collect();
    foreach (CarbonPeriod::create($startAt, '1 day', $endAt) as $d) {
        if (in_array($d->dayOfWeek, $weeklyOffDOW)) {
            $weeklyOffDates->push($d->toDateString());
        }
    }
// dd($userId);
    // ✅ Leaves (approved)
    $leaves = collect();
    if (class_exists(Leave::class)) {
        $leaves = Leave::where('user_id', $userId)
      
            ->where('status', 'approved')
            ->where(function ($q) use ($startAt, $endAt) {
                $q->whereBetween('from_date', [$startAt, $endAt])
                  ->orWhereBetween('to_date', [$startAt, $endAt])
                  ->orWhere(function ($q2) use ($startAt, $endAt) {
                      $q2->where('from_date', '<=', $startAt)
                         ->where('to_date', '>=', $endAt);
                  });
            })
            ->get();

    }

    // Attendance events
    $attendanceEvents = $rows->map(function ($row) use (
        &$presentDays, $SHIFT_START, $REQUIRED_MINS, $GRACE_IN, $GRACE_END, $fmtHM, 
        $holidayDates, $weeklyOffDates
    ) {
        $in  = $row->clock_in ? Carbon::parse($row->clock_in) : null;
        $out = $row->clock_out ? Carbon::parse($row->clock_out) : null;
        if (!$in) return null;

        if ($out && $out->lt($in)) {
            $out = (clone $in)->setTime(23, 59, 0);
        }

        $shiftIn     = (clone $in)->setTimeFromTimeString($SHIFT_START);
        $expectedEnd = (clone $shiftIn)->addMinutes($REQUIRED_MINS);

        $status  = $out ? 'complete' : 'open';
        $durMins = $out ? $in->diffInMinutes($out) : null;
        $durText = $durMins ? sprintf('%dh%02dm', intdiv($durMins,60), $durMins%60) : null;

        $presentDays[] = $in->toDateString();

        $lateMins = 0;
        $lateThreshold = (clone $shiftIn)->addMinutes($GRACE_IN);
        if ($in->gt($lateThreshold)) {
            $lateMins = $lateThreshold->diffInMinutes($in);
        }

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

        // ✅ Comp Off: if attendance on holiday or weekly off
        $day = $in->toDateString();
        $isCOff = in_array($day, $holidayDates) || in_array($day, $weeklyOffDates->toArray());

        return [
            'id'    => $row->id,
            'title' => $isCOff ? 'C.Off' : '',
            'start' => $in->toIso8601String(),
            'end'   => $out ? $out->toIso8601String() : null,
            'allDay'=> false,
            'color' => $isCOff ? '#2563EB' : ($status === 'complete' ? '#16a34a' : '#f59e0b'),
            'extendedProps' => [
                'kind'      => 'attendance',
                'status'    => $status,
                'c_off'     => $isCOff,
                'inText'    => $in->format('h:i A'),
                'outText'   => $out ? $out->format('h:i A') : '—',
                'durText'   => $durText,
                'durMins'   => $durMins,
                'lateMins'  => $lateMins,
                'earlyMins' => $earlyMins,
                'otMins'    => $otMins,
                'lateText'  => $fmtHM($lateMins),
                'earlyText' => $fmtHM($earlyMins),
                'otText'    => $fmtHM($otMins),
                'lat'       => $row->latitude,
                'lng'       => $row->longitude,
            ],
        ];
    })->filter()->values();

    // Holiday / weeklyoff bg + labels
    $holidayBg = $holidays->map(fn($h) => [
        'start' => $h->date->toDateString(),
        'end'   => $h->date->copy()->addDay()->toDateString(),
        'display' => 'background',
        'allDay' => true,
        'color' => $h->color ?: '#FEE2E2',
        'groupId' => 'holiday-bg',
        'extendedProps' => ['kind'=>'holiday-bg'],
    ]);

    $holidayLabels = $holidays->map(fn($h) => [
        'title' => $h->title,
        'start' => $h->date->toDateString(),
        'allDay'=> true,
        'display' => 'block',
        'color' => '#ef4444',
        'textColor' => '#ffffff',
        'extendedProps'=>['kind'=>'holiday-label','title'=>$h->title],
    ]);

    $weeklyOffBg = $weeklyOffDates->map(fn($d) => [
        'start' => $d,
        'end' => Carbon::parse($d)->addDay()->toDateString(),
        'display' => 'background',
        'allDay' => true,
        'color' => '#FFEFE3',
        'groupId'=>'weeklyoff-bg',
        'extendedProps'=>['kind'=>'weeklyoff-bg'],
    ]);

    $weeklyOffLabels = $weeklyOffDates->map(fn($d)=>[
        'title'=>'Weekly Off',
        'start'=>$d,
        'allDay'=>true,
        'display'=>'block',
        'color'=>'#fb923c',
        'textColor'=>'#fff',
        'extendedProps'=>['kind'=>'weeklyoff-label','title'=>'Weekly Off']
    ]);

    // ✅ Leave events
   // ✅ Leave events
    $leaveEvents = collect();
    foreach ($leaves as $leave) {
        foreach (CarbonPeriod::create($leave->from_date, '1 day', $leave->to_date) as $d) {
            $day = $d->toDateString();
            $leaveEvents->push([
                'title' => 'Leave',
                'start' => $day,
                'allDay'=> true,
                'display'=>'block',
                'color' => '#3b82f6',   // blue
                'textColor'=>'#fff',
                'extendedProps'=>[
                    'kind'=>'leave-label',
                    'title'=>'Leave (Approved)',
                    'leave_id'=>$leave->id,
                    'type'=>$leave->type ?? null,
                ]
            ]);
        }
    }


    // Present day background
    $presentBg = collect(array_unique($presentDays))->map(fn($d)=>[
        'start'=>$d,
        'end'=>Carbon::parse($d)->addDay()->toDateString(),
        'display'=>'background',
        'allDay'=>true,
        'color'=>'#ECFDF5',
        'groupId'=>'present-bg',
        'extendedProps'=>['kind'=>'present-bg']
    ]);

    // Absent backgrounds
    $today = now()->toDateString();
    $absentBg = collect();
    $absentLabels = collect();

    foreach (CarbonPeriod::create($startAt, '1 day', $endAt) as $d) {
        $day = $d->toDateString();
        if ($day >= $today) continue;
        if (in_array($day, $presentDays)) continue;
        if (in_array($day, $holidayDates)) continue;
        if (in_array($day, $weeklyOffDates->toArray())) continue;

        // ✅ Skip absent if leave approved
        // $leaveDay = $leaves->filter(fn($lv)=>$day >= $lv->start_date && $day <= $lv->end_date)->count();
        // if ($leaveDay) continue;
        // ✅ Skip absent if leave approved
        $leaveDay = $leaves->first(function ($lv) use ($day) {
            return $day >= $lv->from_date && $day <= $lv->to_date;
        });
        if ($leaveDay) continue;

        $absentBg->push([
            'start'=>$day,
            'end'=>Carbon::parse($day)->addDay()->toDateString(),
            'display'=>'background',
            'allDay'=>true,
            'color'=>'#FDE2E4',
            'groupId'=>'absent-bg',
            'extendedProps'=>['kind'=>'absent-bg'],
        ]);

        $absentLabels->push([
            'title'=>'Absent',
            'start'=>$day,
            'allDay'=>true,
            'display'=>'block',
            'color'=>'#dc2626',
            'textColor'=>'#fff',
            'extendedProps'=>['kind'=>'absent-label','title'=>'Absent'],
        ]);
    }
// dd($leaveEvents);
    return response()->json(
        $presentBg
        ->concat($holidayBg)->concat($weeklyOffBg)->concat($absentBg)
        ->concat($holidayLabels)->concat($weeklyOffLabels)->concat($absentLabels)
        ->concat($leaveEvents) // ✅ new leaves
        ->concat($attendanceEvents)
        ->values()
    );
}

    // public function events(Request $request)
    // {
    //     $userId  = Auth::id();
    //     $start   = $request->query('start');
    //     $end     = $request->query('end');

    //     $startAt = $start ? Carbon::parse($start)->startOfDay() : now()->startOfMonth();
    //     $endAt   = $end   ? Carbon::parse($end)->endOfDay()     : now()->endOfMonth();

    //     $SHIFT_START   = '09:00';     
    //     $REQUIRED_MINS = 9 * 60;      
    //     $GRACE_IN      = 10;         
    //     $GRACE_END     = 5;       

    //     $fmtHM = function (?int $mins): ?string {
    //         if (!$mins || $mins <= 0) return null;
    //         $h = intdiv($mins, 60);
    //         $m = $mins % 60;
    //         return $h . 'h' . ($m ? ' ' . str_pad((string)$m, 2, '0', STR_PAD_LEFT) . 'm' : '');
    //     };

    //     $rows = Attendance::where('user_id', $userId)
    //             ->whereBetween('clock_in', [$startAt, $endAt])
    //             ->orderBy('clock_in')
    //             ->get();

    //     $presentDays = [];
    //     $attendanceEvents = $rows->map(function ($row) use (&$presentDays, $SHIFT_START, $REQUIRED_MINS, $GRACE_IN, $GRACE_END, $fmtHM) {
    //         $in  = $row->clock_in ? Carbon::parse($row->clock_in) : null;
    //         $out = $row->clock_out ? Carbon::parse($row->clock_out) : null;
    //         if (!$in) return null;

    //         if ($out && $out->lt($in)) {
    //             $out = (clone $in)->setTime(23, 59, 0);
    //         }

    //         $shiftIn     = (clone $in)->setTimeFromTimeString($SHIFT_START);
    //         $expectedEnd = (clone $shiftIn)->addMinutes($REQUIRED_MINS);

    //         $status  = $out ? 'complete' : 'open';
    //         $durMins = null;
    //         $durText = null;
    //         if ($out) {
    //             $durMins = $in->diffInMinutes($out);
    //             $durText = sprintf('%dh%02dm', intdiv($durMins,60), $durMins%60);
    //             $presentDays[] = $in->toDateString();
    //         }

    //         $lateMins = 0;
    //         $lateThreshold = (clone $shiftIn)->addMinutes($GRACE_IN);
    //         if ($in->gt($lateThreshold)) {
    //             $lateMins = $lateThreshold->diffInMinutes($in);
    //         }

    //         $earlyMins = 0;
    //         $otMins    = 0;
    //         if ($out) {
    //             $earlyThreshold = (clone $expectedEnd)->subMinutes($GRACE_END);
    //             $otThreshold    = (clone $expectedEnd)->addMinutes($GRACE_END);

    //             if ($out->lt($earlyThreshold)) {
    //                 $earlyMins = $out->diffInMinutes($earlyThreshold);
    //             } elseif ($out->gt($otThreshold)) {
    //                 $otMins = $otThreshold->diffInMinutes($out);
    //             }
    //         }

    //         return [
    //             'id'    => $row->id,
    //             'title' => '',
    //             'start' => $in->toIso8601String(),
    //             'end'   => $out ? $out->toIso8601String() : null,
    //             'allDay'=> false,
    //             'color' => $status === 'complete' ? '#16a34a' : '#f59e0b',
    //             'extendedProps' => [
    //                 'kind'      => 'attendance',
    //                 'status'    => $status,
    //                 'inText'    => $in->format('h:i A'),
    //                 'outText'   => $out ? $out->format('h:i A') : '—',
    //                 'durText'   => $durText,
    //                 'durMins'   => $durMins,
    //                 'lateMins'  => $lateMins,
    //                 'earlyMins' => $earlyMins,
    //                 'otMins'    => $otMins,
    //                 'lateText'  => $fmtHM($lateMins),
    //                 'earlyText' => $fmtHM($earlyMins),
    //                 'otText'    => $fmtHM($otMins),
    //                 'lat'       => $row->latitude,
    //                 'lng'       => $row->longitude,
    //             ],
    //         ];
    //     })->filter()->values();

    //     // Holidays
    //     $holidayBg = collect();
    //     $holidayLabels = collect();
    //     $holidays = collect();
    //     if (class_exists(Holiday::class)) {
    //         $holidays = Holiday::whereBetween('date', [$startAt->toDateString(), $endAt->toDateString()])->get();

    //         $holidayBg = $holidays->map(function ($h) {
    //             return [
    //                 'start'   => $h->date->toDateString(),
    //                 'end'     => $h->date->copy()->addDay()->toDateString(), 
    //                 'display' => 'background',
    //                 'allDay'  => true,
    //                 'color'   => $h->color ?: '#FEE2E2', 
    //                 'groupId' => 'holiday-bg',
    //                 'extendedProps' => ['kind' => 'holiday-bg'],
    //             ];
    //         });

    //         $holidayLabels = $holidays->map(function ($h) {
    //             return [
    //                 'title' => $h->title,
    //                 'start' => $h->date->toDateString(),
    //                 'allDay'=> true,
    //                 'display' => 'block',
    //                 'color' => '#ef4444',
    //                 'textColor' => '#ffffff',
    //                 'extendedProps' => ['kind' => 'holiday-label', 'title' => $h->title],
    //             ];
    //         });
    //     }

    //     // Weekly off (Sunday)
    //     $weeklyOffDOW = [0]; 
    //     $weeklyOffDates = collect();
    //     foreach (CarbonPeriod::create($startAt, '1 day', $endAt) as $d) {
    //         if (in_array($d->dayOfWeek, $weeklyOffDOW)) {
    //             $weeklyOffDates->push($d->toDateString());
    //         }
    //     }

    //     $weeklyOffBg = $weeklyOffDates->map(fn($day) => [
    //         'start'   => $day,
    //         'end'     => Carbon::parse($day)->addDay()->toDateString(),
    //         'display' => 'background',
    //         'allDay'  => true,
    //         'color'   => '#FFEFE3', 
    //         'groupId' => 'weeklyoff-bg',
    //         'extendedProps' => ['kind' => 'weeklyoff-bg'],
    //     ]);

    //     $weeklyOffLabels = $weeklyOffDates->map(fn($day) => [
    //         'title' => 'Weekly Off',
    //         'start' => $day,
    //         'allDay'=> true,
    //         'display' => 'block',
    //         'color' => '#fb923c',
    //         'textColor' => '#ffffff',
    //         'extendedProps' => ['kind' => 'weeklyoff-label', 'title' => 'Weekly Off'],
    //     ]);

    //     // Present day background
    //     $presentBg = collect(array_unique($presentDays))->map(function ($day) {
    //         return [
    //             'start'   => $day,
    //             'end'     => Carbon::parse($day)->addDay()->toDateString(),
    //             'display' => 'background',
    //             'allDay'  => true,
    //             'color'   => '#ECFDF5',
    //             'groupId' => 'present-bg',
    //             'extendedProps' => ['kind' => 'present-bg'],
    //         ];
    //     });

    //     // Absent (past working days with no punch)
    //     $today = now()->toDateString();
    //     $holidayDates = $holidays->pluck('date')->map(fn($d) => $d->toDateString())->toArray();
    //     $absentBg = collect();
    //     $absentLabels = collect();

    //     foreach (CarbonPeriod::create($startAt, '1 day', $endAt) as $d) {
    //         $day = $d->toDateString();
    //         if ($day >= $today) continue;
    //         if (in_array($day, $presentDays)) continue;
    //         if (in_array($day, $holidayDates)) continue;
    //         if (in_array($day, $weeklyOffDates->toArray())) continue;

    //         $absentBg->push([
    //             'start'   => $day,
    //             'end'     => Carbon::parse($day)->addDay()->toDateString(),
    //             'display' => 'background',
    //             'allDay'  => true,
    //             'color'   => '#FDE2E4',
    //             'groupId' => 'absent-bg',
    //             'extendedProps' => ['kind' => 'absent-bg'],
    //         ]);

    //         $absentLabels->push([
    //             'title' => 'Absent',
    //             'start' => $day,
    //             'allDay'=> true,
    //             'display' => 'block',
    //             'color' => '#dc2626',
    //             'textColor' => '#ffffff',
    //             'extendedProps' => ['kind' => 'absent-label', 'title' => 'Absent'],
    //         ]);
    //     }

    //     return response()->json(
    //         $presentBg
    //             ->concat($holidayBg)->concat($weeklyOffBg)->concat($absentBg)
    //             ->concat($holidayLabels)->concat($weeklyOffLabels)->concat($absentLabels)
    //             ->concat($attendanceEvents)
    //             ->values()
    //     );
    // }

}

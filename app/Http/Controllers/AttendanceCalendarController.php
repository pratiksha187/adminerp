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

public function events(Request $request)
{
    $userId  = Auth::id();
    $start   = $request->query('start');
    // dd($start);
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
    $attendanceEvents = $rows->map(function ($row) use (&$presentDays, $SHIFT_START, $REQUIRED_MINS, $GRACE_IN, $GRACE_END, $fmtHM) {
        $in  = $row->clock_in ? Carbon::parse($row->clock_in) : null;
        $out = $row->clock_out ? Carbon::parse($row->clock_out) : null;
        if (!$in) return null;

        if ($out && $out->lt($in)) {
            $out = (clone $in)->setTime(23, 59, 0);
        }

        $shiftIn     = (clone $in)->setTimeFromTimeString($SHIFT_START);
        $expectedEnd = (clone $shiftIn)->addMinutes($REQUIRED_MINS);

        $status  = $out ? 'complete' : 'open';
        $durMins = null;
        $durText = null;
        if ($out) {
            $durMins = $in->diffInMinutes($out);
            $durText = sprintf('%dh%02dm', intdiv($durMins,60), $durMins%60);
            $presentDays[] = $in->toDateString();
        }

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

        // formatted text for badges/tooltips
        $lateText  = $fmtHM($lateMins);
        $earlyText = $fmtHM($earlyMins);
        $otText    = $fmtHM($otMins);

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

                // formatted strings used in UI
                'lateText'  => $lateText,
                'earlyText' => $earlyText,
                'otText'    => $otText,

                'lat'       => $row->latitude,
                'lng'       => $row->longitude,
            ],
        ];
    })->filter()->values();

    $holidayBg = collect();
    $holidayLabels = collect();
    if (class_exists(Holiday::class)) {
        $holidays = Holiday::whereBetween('date', [$startAt->toDateString(), $endAt->toDateString()])->get();

        $holidayBg = $holidays->map(function ($h) {
            return [
                'start'   => $h->date->toDateString(),
                'end'     => $h->date->copy()->addDay()->toDateString(), 
                'display' => 'background',
                'allDay'  => true,
                'color'   => $h->color ?: '#FEE2E2', 
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

  
    $weeklyOffDOW = [0]; 
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
        'color'   => '#FFEFE3', 
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

    /* ---------------------------
 * 5) ABSENT (only past working days with no punch)
 * --------------------------- */
$today = now()->toDateString();

// Get all holiday dates as strings
$holidayDates = isset($holidays) ? $holidays->pluck('date')->map(fn($d) => $d->toDateString())->toArray() : [];

$absentBg = collect();
$absentLabels = collect();

foreach (CarbonPeriod::create($startAt, '1 day', $endAt) as $d) {
    $day = $d->toDateString();

    // Skip today and future dates
    if ($day >= $today) continue;

    // Skip if present
    if (in_array($day, $presentDays)) continue;

    // Skip if holiday
    if (in_array($day, $holidayDates)) continue;

    // Skip if weekly off
    if (in_array($day, $weeklyOffDates->toArray())) continue;

    // Mark as absent
    $absentBg->push([
        'start'   => $day,
        'end'     => Carbon::parse($day)->addDay()->toDateString(),
        'display' => 'background',
        'allDay'  => true,
        'color'   => '#FDE2E4', // light red
        'groupId' => 'absent-bg',
        'extendedProps' => ['kind' => 'absent-bg'],
    ]);

    $absentLabels->push([
        'title' => 'Absent',
        'start' => $day,
        'allDay'=> true,
        'display' => 'block',
        'color' => '#dc2626', // strong red
        'textColor' => '#ffffff',
        'extendedProps' => ['kind' => 'absent-label', 'title' => 'Absent'],
    ]);
}


    // Order matters: backgrounds → labels → attendance
   return response()->json(
    $presentBg
        ->concat($holidayBg)->concat($weeklyOffBg)->concat($absentBg)
        ->concat($holidayLabels)->concat($weeklyOffLabels)->concat($absentLabels)
        ->concat($attendanceEvents)
        ->values()
);




}



}

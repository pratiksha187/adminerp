<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonPeriod;
use App\Models\Leave;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;


class PaymentController extends Controller
{

public function index()
{
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;
        $payments = Payment::with('user')->latest()->get();
    return view('payments.index', compact('payments','role'));
}
public function create()
{
    $userId = Auth::id();
    $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)  
                    ->first();

    $role = $userDetails->role;
    $users = \App\Models\User::all(); // fetch employees for dropdown
    return view('payments.create', compact('users','role'));
}

// public function generatePayment(Request $request)
// {
//     $request->validate([
//         'user_id'   => 'required|exists:users,id',
//         'from_date' => 'required|date',
//         'to_date'   => 'required|date|after_or_equal:from_date',
//     ]);

//     $userid = $request->user_id;

//     // Prevent duplicate payment
//     $exists = Payment::where('user_id', $userid)
//         ->whereDate('from_date', $request->from_date)
//         ->whereDate('to_date', $request->to_date)
//         ->exists();

//     if ($exists) {
//         return back()->with('error', 'Payment is already generated for this period!');
//     }

//     $user = User::findOrFail($userid);
//     $gross_salary = (int) ($user->salary ?? 0);

//     $from = Carbon::parse($request->from_date)->startOfDay();
//     $to   = Carbon::parse($request->to_date)->endOfDay();

//     // ✅ Ensure full end date included (up to 23:59:59)
//     if ($to->lt($from)) {
//         return back()->with('error', 'Invalid date range!');
//     }

//     $daysInMonth  = $from->daysInMonth;
//     $per_day_rate = $daysInMonth > 0 ? round($gross_salary / $daysInMonth) : 0;

//     /* ----------------------------------
//        FETCH ATTENDANCE + LEAVES + HOLIDAYS
//     ----------------------------------- */

//     // ✅ Attendance grouped by date
//     $attendances = Attendance::where('user_id', $user->id)
//         ->whereBetween('clock_in', [$from, $to])
//         ->get()
//         ->groupBy(fn($a) => Carbon::parse($a->clock_in)->toDateString());

//     // ✅ Holidays
//     $holidays = DB::table('holidays')
//         ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
//         ->pluck('date')
//         ->map(fn($d) => Carbon::parse($d)->toDateString())
//         ->toArray();

//     // ✅ Weekly Offs (Always include full range)
//     $weeklyOffDates = collect();
//     foreach (CarbonPeriod::create($from->copy()->startOfMonth(), '1 day', $to->copy()->endOfMonth()) as $d) {
//         if ($d->isSunday()) {
//             $weeklyOffDates->push($d->toDateString());
//         }
//     }

//     // ✅ Convert once for speed
//     $weeklyOffArray = $weeklyOffDates->toArray();

//     // ✅ Approved Leaves
//     $leaves = Leave::where('user_id', $user->id)
//         ->where('status', 'Approved')
//         ->where(function ($q) use ($from, $to) {
//             $q->whereBetween('from_date', [$from, $to])
//               ->orWhereBetween('to_date', [$from, $to])
//               ->orWhere(function ($q2) use ($from, $to) {
//                   $q2->where('from_date', '<=', $from)
//                      ->where('to_date', '>=', $to);
//               });
//         })
//         ->get();

//     // ✅ Map leave days
//     $leaveMap = [];
//     foreach ($leaves as $lv) {
//         $period = CarbonPeriod::create($lv->from_date, $lv->to_date);
//         foreach ($period as $d) {
//             $leaveMap[$d->toDateString()] = strtoupper(substr($lv->type, 0, 2));
//         }
//     }

//     /* ----------------------------------
//        DAILY STATUS EVALUATION
//     ----------------------------------- */

//     $present_days = 0;
//     $weekoffCount = 0;
//     $holidayCount = 0;
//     $leave_cl = 0;
//     $leave_sl = 0;
//     $leave_el = 0;
//     $cOffCount = 0;

//     foreach (CarbonPeriod::create($from, '1 day', $to) as $day) {
//         $date = $day->toDateString();

//         $hasAttendance = isset($attendances[$date]);
//         $isHoliday = in_array($date, $holidays, true);
//         $isWeeklyOff = in_array($date, $weeklyOffArray, true);
//         $isLeave = isset($leaveMap[$date]);

//         // ✅ If worked on Sunday or Holiday => mark as Comp Off
//         if ($hasAttendance && ($isWeeklyOff || $isHoliday)) {
//             $cOffCount++;
//             continue;
//         }

//         // ✅ Present
//         if ($hasAttendance) {
//             $present_days++;
//         }
//         // ✅ Holiday (not attended)
//         elseif ($isHoliday) {
//             $holidayCount++;
//         }
//         // ✅ Weekly Off (not attended)
//         elseif ($isWeeklyOff) {
//             $weekoffCount++;
//         }
//         // ✅ Leave
//         elseif ($isLeave) {
//             $type = $leaveMap[$date];
//             if ($type === 'CL') $leave_cl++;
//             elseif ($type === 'SL') $leave_sl++;
//             elseif ($type === 'EL') $leave_el++;
//         }
//     }

//     // Debug check (optional)
//     // dd(['weeklyOffArray' => $weeklyOffArray, 'weekoffCount' => $weekoffCount, 'cOffCount' => $cOffCount]);

//     $leaveDays = $leave_cl + $leave_sl + $leave_el;

//     // ✅ Total payable days
//     $present_days_act = $present_days + $weekoffCount + $holidayCount + $leaveDays;

//     /* ----------------------------------
//        SALARY CALCULATION
//     ----------------------------------- */
//     $gross_payable = round($per_day_rate * $present_days_act);

//     $basic_60        = round($gross_payable * 0.6);
//     $hra_5           = round($gross_payable * 0.05);
//     $conveyance_20   = round($gross_payable * 0.2);
//     $other_allowance = $gross_payable - $basic_60 - $hra_5 - $conveyance_20;

//     $pf        = (int) ($user->pf ?? 0);
//     $insurance = (int) ($user->insurance ?? 0);
//     $pt        = (int) ($user->pt ?? 0);
//     $advance   = (int) ($user->advance ?? 0);

//     $total_deduction = $pf + $insurance + $pt + $advance;
//     $net_payable     = $gross_payable - $total_deduction;

//     /* ----------------------------------
//        SAVE PAYMENT
//     ----------------------------------- */
//     Payment::create([
//         'user_id'              => $user->id,
//         'from_date'            => $from,
//         'to_date'              => $to,
//         'present_days'         => $present_days_act,
//         'present_days_in_month'=> $present_days,
//         'weekoffCount'         => $weekoffCount,
//         'holidayCount'         => $holidayCount,
//         'cOffCount'            => $cOffCount,
//         'leave_cl'             => $leave_cl,
//         'leave_sl'             => $leave_sl,
//         'leave_el'             => $leave_el,
//         'gross_salary'         => $gross_salary,
//         'per_day_rate'         => $per_day_rate,
//         'basic_60'             => $basic_60,
//         'hra_5'                => $hra_5,
//         'conveyance_20'        => $conveyance_20,
//         'other_allowance'      => $other_allowance,
//         'gross_payable'        => $gross_payable,
//         'pf_12'                => $pf,
//         'insurance'            => $insurance,
//         'pt'                   => $pt,
//         'advance'              => $advance,
//         'total_deduction'      => $total_deduction,
//         'net_payable'          => $net_payable,
//     ]);

//     return back()->with('success', 'Payment generated successfully with correct weekly offs and C.Off logic.');
// }
public function generatePayment(Request $request)
{
    $request->validate([
        'user_id'   => 'required|exists:users,id',
        'from_date' => 'required|date',
        'to_date'   => 'required|date|after_or_equal:from_date',
    ]);

    $userid = $request->user_id;

    // Prevent duplicate payment
    $exists = Payment::where('user_id', $userid)
        ->whereDate('from_date', $request->from_date)
        ->whereDate('to_date', $request->to_date)
        ->exists();

    if ($exists) {
        return back()->with('error', 'Payment is already generated for this period!');
    }

    $user = User::findOrFail($userid);
    $gross_salary = (int) ($user->salary ?? 0);

    $from = Carbon::parse($request->from_date)->startOfDay();
    $to   = Carbon::parse($request->to_date)->endOfDay();

    if ($to->lt($from)) {
        return back()->with('error', 'Invalid date range!');
    }

    $daysInMonth  = $from->daysInMonth;
    $per_day_rate = $daysInMonth > 0 ? round($gross_salary / $daysInMonth) : 0;

    /* ----------------------------------
       FETCH ATTENDANCE + LEAVES + HOLIDAYS
    ----------------------------------- */

    // Attendance grouped by date
    $attendances = Attendance::where('user_id', $user->id)
        ->whereBetween('clock_in', [$from, $to])
        ->get()
        ->groupBy(fn($a) => Carbon::parse($a->clock_in)->toDateString());

    // Holidays
    $holidays = DB::table('holidays')
        ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
        ->pluck('date')
        ->map(fn($d) => Carbon::parse($d)->toDateString())
        ->toArray();

    // Weekly Offs (Sundays for full month)
    $weeklyOffDates = collect();
    foreach (CarbonPeriod::create($from->copy()->startOfMonth(), '1 day', $to->copy()->endOfMonth()) as $d) {
        if ($d->isSunday()) {
            $weeklyOffDates->push($d->toDateString());
        }
    }
    $weeklyOffArray = $weeklyOffDates->toArray();

    // Approved Leaves
    $leaves = Leave::where('user_id', $user->id)
        ->where('status', 'Approved')
        ->where(function ($q) use ($from, $to) {
            $q->whereBetween('from_date', [$from, $to])
              ->orWhereBetween('to_date', [$from, $to])
              ->orWhere(function ($q2) use ($from, $to) {
                  $q2->where('from_date', '<=', $from)
                     ->where('to_date', '>=', $to);
              });
        })
        ->get();

    // Map leave days by type
    $leaveMap = [];
    foreach ($leaves as $lv) {
        $period = CarbonPeriod::create($lv->from_date, $lv->to_date);
        foreach ($period as $d) {
            $leaveMap[$d->toDateString()] = strtoupper(substr($lv->type, 0, 2)); // CL/SL/EL
        }
    }

    /* ----------------------------------
       DAILY STATUS EVALUATION
    ----------------------------------- */

    $present_days = 0;
    $weekoffCount = 0;
    $holidayCount = 0;
    $leave_cl = 0;
    $leave_sl = 0;
    $leave_el = 0;
    $cOffCount = 0;

    foreach (CarbonPeriod::create($from, '1 day', $to) as $day) {
        $date = $day->toDateString();

        $hasAttendance = isset($attendances[$date]);
        $isHoliday = in_array($date, $holidays, true);
        $isWeeklyOff = in_array($date, $weeklyOffArray, true);
        $isLeave = isset($leaveMap[$date]);

        // If worked on a Weekly Off or Holiday => mark as C.Off
        if ($hasAttendance && ($isWeeklyOff || $isHoliday)) {
            $cOffCount++;
            continue;
        }

        if ($hasAttendance) {
            $present_days++;
        } elseif ($isHoliday) {
            $holidayCount++;
        } elseif ($isWeeklyOff) {
            $weekoffCount++;
        } elseif ($isLeave) {
            $type = $leaveMap[$date];
            if ($type === 'CL') $leave_cl++;
            elseif ($type === 'SL') $leave_sl++;
            elseif ($type === 'EL') $leave_el++;
        }
    }
// dd($cOffCount);
    $leaveDays = $leave_cl + $leave_sl + $leave_el;

    // Total payable days (excluding C.Off)
    $present_days_act = $present_days + $weekoffCount + $holidayCount + $leaveDays;

    /* ----------------------------------
       SALARY CALCULATION
    ----------------------------------- */
    $gross_payable = round($per_day_rate * $present_days_act);
    $basic_60        = round($gross_payable * 0.6);
    $hra_5           = round($gross_payable * 0.05);
    $conveyance_20   = round($gross_payable * 0.2);
    $other_allowance = $gross_payable - $basic_60 - $hra_5 - $conveyance_20;

    $pf        = (int) ($user->pf ?? 0);
    $insurance = (int) ($user->insurance ?? 0);
    $pt        = (int) ($user->pt ?? 0);
    $advance   = (int) ($user->advance ?? 0);

    $total_deduction = $pf + $insurance + $pt + $advance;
    $net_payable     = $gross_payable - $total_deduction;

    /* ----------------------------------
       SAVE PAYMENT
    ----------------------------------- */
    Payment::create([
        'user_id'              => $user->id,
        'from_date'            => $from,
        'to_date'              => $to,
        'present_days'         => $present_days_act,
        'present_days_in_month'=> $present_days,
        'weekoffCount'         => $weekoffCount,
        'holidayCount'         => $holidayCount,
        'cOffCount'            => $cOffCount,
        'leave_cl'             => $leave_cl,
        'leave_sl'             => $leave_sl,
        'leave_el'             => $leave_el,
        'gross_salary'         => $gross_salary,
        'per_day_rate'         => $per_day_rate,
        'basic_60'             => $basic_60,
        'hra_5'                => $hra_5,
        'conveyance_20'        => $conveyance_20,
        'other_allowance'      => $other_allowance,
        'gross_payable'        => $gross_payable,
        'pf_12'                => $pf,
        'insurance'            => $insurance,
        'pt'                   => $pt,
        'advance'              => $advance,
        'total_deduction'      => $total_deduction,
        'net_payable'          => $net_payable,
    ]);

    /* ----------------------------------
       OUTPUT SUCCESS WITH SUMMARY
    ----------------------------------- */
    return back()->with([
        'success' => '✅ Payment generated successfully!',
        'summary' => [
            'Week Offs' => $weekoffCount,
            'C.Offs'    => $cOffCount,
            'Holidays'  => $holidayCount,
            'Presents'  => $present_days,
            'Leaves'    => $leaveDays,
        ]
    ]);
}

// public function generatePayment(Request $request)
// {
//     $request->validate([
//         'user_id'   => 'required|exists:users,id',
//         'from_date' => 'required|date',
//         'to_date'   => 'required|date|after_or_equal:from_date',
//     ]);

//     $userid = $request->user_id;

//     // Prevent duplicate payment
//     $exists = Payment::where('user_id', $userid)
//         ->whereDate('from_date', $request->from_date)
//         ->whereDate('to_date', $request->to_date)
//         ->exists();

//     if ($exists) {
//         return back()->with('error', 'Payment is already generated for this period!');
//     }

//     $user = User::findOrFail($userid);
//     $gross_salary = (int) ($user->salary ?? 0);

//     $from = Carbon::parse($request->from_date)->startOfDay();
//     $to   = Carbon::parse($request->to_date)->endOfDay();

//     $daysInMonth  = $from->daysInMonth;
//     $per_day_rate = $daysInMonth > 0 ? round($gross_salary / $daysInMonth) : 0;

//     /* ----------------------------------
//        FETCH ATTENDANCE + LEAVES + HOLIDAYS
//     ----------------------------------- */

//     // ✅ Attendance grouped by date
//     $attendances = Attendance::where('user_id', $user->id)
//         ->whereBetween('clock_in', [$from, $to])
//         ->get()
//         ->groupBy(fn($a) => Carbon::parse($a->clock_in)->toDateString());
// // dd($attendances);
//     // ✅ Holidays (no type column — treat all as holidays)
//     $holidays = DB::table('holidays')
//         ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
//         ->pluck('date')
//         ->map(fn($d) => Carbon::parse($d)->toDateString())
//         ->toArray();

//     $companyHolidayDates = $holidays;  // treat all as company/public holidays
//     $publicHolidayDates  = [];         // not used, but defined for consistency

//     // ✅ Weekly Offs (Sundays)
//     $weeklyOffDates = [];
//     foreach (CarbonPeriod::create($from, '1 day', $to) as $d) {
//         if ($d->isSunday()) {
//             $weeklyOffDates[] = $d->toDateString();
//         }
//     }

//     // ✅ Approved Leaves
//     $leaves = Leave::where('user_id', $user->id)
//         ->where('status', 'Approved')
//         ->where(function ($q) use ($from, $to) {
//             $q->whereBetween('from_date', [$from, $to])
//               ->orWhereBetween('to_date', [$from, $to])
//               ->orWhere(function ($q2) use ($from, $to) {
//                   $q2->where('from_date', '<=', $from)
//                      ->where('to_date', '>=', $to);
//               });
//         })
//         ->get();

//     // ✅ Map leave days by type (CL/SL/EL)
//     $leaveMap = [];
//     foreach ($leaves as $lv) {
//         $period = CarbonPeriod::create($lv->from_date, $lv->to_date);
//         foreach ($period as $d) {
//             $leaveMap[$d->toDateString()] = strtoupper(substr($lv->type, 0, 2)); // CL / SL / EL
//         }
//     }

//     /* ----------------------------------
//        DAILY STATUS EVALUATION
//     ----------------------------------- */

//     $present_days = 0;
//     $weekoffCount = 0;
//     $holidayCount = 0;
//     $leave_cl = 0;
//     $leave_sl = 0;
//     $leave_el = 0;

//     foreach (CarbonPeriod::create($from, '1 day', $to) as $day) {
//         $date = $day->toDateString();

//         if (isset($attendances[$date])) {
//             $present_days++;
//         } elseif (in_array($date, $companyHolidayDates, true)) {
//             $holidayCount++;
//         } elseif (in_array($date, $weeklyOffDates, true)) {
//             $weekoffCount++;
//         } elseif (isset($leaveMap[$date])) {
//             $type = $leaveMap[$date];
//             if ($type === 'CL') $leave_cl++;
//             elseif ($type === 'SL') $leave_sl++;
//             elseif ($type === 'EL') $leave_el++;
//         }
//     }

//     $leaveDays = $leave_cl + $leave_sl + $leave_el;

//     // ✅ Total payable days
//     $present_days_act = $present_days + $weekoffCount + $holidayCount + $leaveDays;

//     /* ----------------------------------
//        SALARY CALCULATION
//     ----------------------------------- */
//     $gross_payable = round($per_day_rate * $present_days_act);

//     $basic_60        = round($gross_payable * 0.6);
//     $hra_5           = round($gross_payable * 0.05);
//     $conveyance_20   = round($gross_payable * 0.2);
//     $other_allowance = $gross_payable - $basic_60 - $hra_5 - $conveyance_20;

//     $pf        = (int) ($user->pf ?? 0);
//     $insurance = (int) ($user->insurance ?? 0);
//     $pt        = (int) ($user->pt ?? 0);
//     $advance   = (int) ($user->advance ?? 0);

//     $total_deduction = $pf + $insurance + $pt + $advance;
//     $net_payable     = $gross_payable - $total_deduction;

//     /* ----------------------------------
//        SAVE PAYMENT
//     ----------------------------------- */
//     Payment::create([
//         'user_id'              => $user->id,
//         'from_date'            => $from,
//         'to_date'              => $to,
//         'present_days'         => $present_days_act,
//         'present_days_in_month'=> $present_days,
//         'weekoffCount'         => $weekoffCount,
//         'holidayCount'         => $holidayCount,
//         'publicHolidayCount'   => 0, // not used
//         'leave_cl'             => $leave_cl,
//         'leave_sl'             => $leave_sl,
//         'leave_el'             => $leave_el,
//         'gross_salary'         => $gross_salary,
//         'per_day_rate'         => $per_day_rate,
//         'basic_60'             => $basic_60,
//         'hra_5'                => $hra_5,
//         'conveyance_20'        => $conveyance_20,
//         'other_allowance'      => $other_allowance,
//         'gross_payable'        => $gross_payable,
//         'pf_12'                => $pf,
//         'insurance'            => $insurance,
//         'pt'                   => $pt,
//         'advance'              => $advance,
//         'total_deduction'      => $total_deduction,
//         'net_payable'          => $net_payable,
//     ]);

//     return back()->with('success', 'Payment generated successfully with holidays, weekoffs, and leave details.');
// }


public function slip($id)
{
    $payment = Payment::with('user')->findOrFail($id);
    // dd($payment);
    $month = \Carbon\Carbon::parse($payment->from_date)->format('F'); // August
    $year  = \Carbon\Carbon::parse($payment->from_date)->format('Y'); // 2025
    $from = Carbon::parse($payment->from_date);
   
    $daysInMonth = $from->daysInMonth;
    //  dd($daysInMonth); 
    $user = $payment->user;
    // dd($user);
    // $leave = LeaveBalance::where('user_id', $user->id)->first(); // optional

    return view('payments.slip', compact('payment','user','daysInMonth'));
}

public function export(): StreamedResponse
{
    $fileName = 'salary_payments_with_attendance.csv';
    $payments = \App\Models\Payment::with('user')->get();

    $fromDate = \Carbon\Carbon::parse($payments->first()?->from_date ?? now()->startOfMonth());
    $toDate   = \Carbon\Carbon::parse($payments->first()?->to_date ?? now()->endOfMonth());

    $dateRange = [];
    $current = $fromDate->copy();
    while ($current->lte($toDate)) {
        $dateRange[] = $current->format('j/n');
        $current->addDay();
    }

    $columns = array_merge([
        'Employee Name', 'From Date', 'To Date'
    ], $dateRange, [
        'Present Days', 'Weekly Offs', 'Holidays',
        'CL', 'SL', 'EL',
        'Gross Salary', 'Per Day Rate', 'Gross Payable',
        'Total Deduction', 'Net Payable'
    ]);

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename={$fileName}",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate',
        'Expires' => '0',
    ];

    $callback = function () use ($payments, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($payments as $p) {
            // ✅ Parse full date range properly
            $from = \Carbon\Carbon::parse($p->from_date)->startOfDay();
            $to   = \Carbon\Carbon::parse($p->to_date)->endOfDay();

            // ✅ Attendance (include full end of day)
            $attendance = \App\Models\Attendance::where('user_id', $p->user_id)
                ->whereBetween('clock_in', [$from, $to])
                ->get()
                ->groupBy(fn($att) => \Carbon\Carbon::parse($att->clock_in)->toDateString());

            // ✅ Leaves
            $leaves = \App\Models\Leave::where('user_id', $p->user_id)
                ->where('status', 'Approved')
                ->where(function ($q) use ($from, $to) {
                    $q->whereBetween('from_date', [$from, $to])
                      ->orWhereBetween('to_date', [$from, $to])
                      ->orWhere(function ($q2) use ($from, $to) {
                          $q2->where('from_date', '<=', $from)
                             ->where('to_date', '>=', $to);
                      });
                })
                ->get();

            $leaveDays = [];
            foreach ($leaves as $leave) {
                $leaveFrom = \Carbon\Carbon::parse($leave->from_date);
                $leaveTo   = \Carbon\Carbon::parse($leave->to_date);
                for ($d = $leaveFrom->copy(); $d->lte($leaveTo); $d->addDay()) {
                    $leaveDays[$d->format('Y-m-d')] = strtoupper(substr($leave->type, 0, 2));
                }
            }

            // ✅ Holidays (include full end of day)
            $holidays = DB::table('holidays')
                ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                ->pluck('date')
                ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
                ->toArray();

            // ✅ Build daily status
            $dailyStatus = [];
            $current = $from->copy();
            while ($current->lte($to)) {
                $date = $current->format('Y-m-d');

                if (isset($attendance[$date])) {
                    $dailyStatus[] = 'P';
                } elseif (in_array($date, $holidays, true)) {
                    $dailyStatus[] = 'H';
                } elseif (isset($leaveDays[$date])) {
                    $dailyStatus[] = $leaveDays[$date];
                } elseif ($current->isSunday()) {
                    $dailyStatus[] = 'WO';
                } else {
                    $dailyStatus[] = 'A';
                }

                $current->addDay();
            }

            // ✅ Write row to CSV
            fputcsv($file, array_merge([
                $p->user->name,
                $p->from_date,
                $p->to_date,
            ], $dailyStatus, [
                $p->present_days,
                $p->weekoffCount,
                $p->holidayCount,
                $p->leave_cl ?? 0,
                $p->leave_sl ?? 0,
                $p->leave_el ?? 0,
                $p->gross_salary,
                $p->per_day_rate,
                $p->gross_payable,
                $p->total_deduction,
                $p->net_payable,
            ]));
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}

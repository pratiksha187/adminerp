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


public function generatePayment(Request $request)
{
    $request->validate([
        'user_id'   => 'required|exists:users,id',
        'from_date' => 'required|date',
        'to_date'   => 'required|date|after_or_equal:from_date',
    ]);

    $userid = $request->user_id;

    // ✅ Prevent duplicate payment
    $exists = Payment::where('user_id', $userid)
        ->whereDate('from_date', $request->from_date)
        ->whereDate('to_date', $request->to_date)
        ->exists();

    if ($exists) {
        return back()->with('error', 'Payment is already generated for this period!');
    }

    $user = User::findOrFail($userid);
    $gross_salary = (int) ($user->salary ?? 0);

    $from = Carbon::parse($request->from_date);
    $to   = Carbon::parse($request->to_date);

    $daysInMonth  = $from->daysInMonth;
    $per_day_rate = $daysInMonth > 0 ? round($gross_salary / $daysInMonth) : 0;

    // ✅ Attendance count
    $attendances = Attendance::where('user_id', $user->id)
        ->whereBetween('clock_in', [$from, $to])
        ->get();

    $present_days = 0;
    foreach ($attendances as $attendance) {
        if (!$attendance->clock_in || !$attendance->clock_out) continue;

        $clockIn  = Carbon::parse($attendance->clock_in);
        $clockOut = Carbon::parse($attendance->clock_out);

        $workedMinutes = $clockIn->diffInMinutes($clockOut);

        if ($workedMinutes >= 270) {
            $present_days += 1;      // Full day
        } elseif ($workedMinutes > 0) {
            $present_days += 0.5;    // Half day
        }
    }

    // ✅ Weekly offs (Sundays)
    $weekoffCount = 0;
    for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
        if ($date->isSunday()) $weekoffCount++;
    }

    // ✅ Holidays
    $holidayCount = DB::table('holidays')
        ->whereBetween('date', [$from, $to])
        ->count();

    // ✅ Approved Leaves (EL/CL/SL)
    $leaveDays = 0;
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

    foreach ($leaves as $lv) {
        $period = CarbonPeriod::create($lv->from_date, $lv->to_date);
        foreach ($period as $d) {
            if ($d->between($from, $to)) {
                $leaveDays++;
            }
        }

        // Deduct from user leave balance
        $col = match ($lv->type) {
            'Sick'   => 'sl',
            'Casual' => 'cl',
            'Paid'   => 'el',
            default  => null,
        };

        if ($col && (int) $user->$col > 0) {
            $used   = Carbon::parse($lv->from_date)->diffInDays(Carbon::parse($lv->to_date)) + 1;
            $deduct = min($used, (int) $user->$col); // avoid negative
            $user->$col = (int) $user->$col - $deduct;
        }
    }
    $user->save();

    // ✅ Final Present Days
    $present_days_act = $present_days + $weekoffCount + $holidayCount + $leaveDays;

    // ✅ Gross Payable
    $gross_payable = round($per_day_rate * $present_days_act);

    // Salary Components
    $basic_60        = round($gross_payable * 0.6);
    $hra_5           = round($gross_payable * 0.05);
    $conveyance_20   = round($gross_payable * 0.2);
    $other_allowance = $gross_payable - $basic_60 - $hra_5 - $conveyance_20;

    // ✅ Deductions (cast to int to avoid int+string bug)
    $pf        = (int) ($user->pf ?? 0);
    $insurance = (int) ($user->insurance ?? 0);
    $pt        = (int) ($user->pt ?? 0);
    $advance   = (int) ($user->advance ?? 0);

    $total_deduction = $pf + $insurance + $pt + $advance;
    $net_payable     = $gross_payable - $total_deduction;

    // ✅ Save Payment
    Payment::create([
        'user_id'              => $user->id,
        'from_date'            => $from,
        'to_date'              => $to,
        'present_days'         => $present_days_act,
        'gross_salary'         => $gross_salary,
        'per_day_rate'         => $per_day_rate,
        'basic_60'             => $basic_60,
        'hra_5'                => $hra_5,
        'conveyance_20'        => $conveyance_20,
        'other_allowance'      => $other_allowance,
        'ot_arrears'           => 0,
        'gross_payable'        => $gross_payable,
        'pf_12'                => $pf,
        'insurance'            => $insurance,
        'pt'                   => $pt,
        'weekoffCount'         => $weekoffCount,
        'advance'              => $advance,
        'total_deduction'      => $total_deduction,
        'net_payable'          => $net_payable,
        'holidayCount'         => $holidayCount,
        'present_days_in_month'=> $present_days,
    ]);

    return back()->with('success', 'Payment generated successfully with leave adjustment!');
}

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

    // Set date range from first record (or fallback to current month)
    $fromDate = \Carbon\Carbon::parse($payments->first()?->from_date ?? now()->startOfMonth());
    $toDate = \Carbon\Carbon::parse($payments->first()?->to_date ?? now()->endOfMonth());

    // Generate date headers: 1/7, 2/7, ...
    $dateRange = [];
    $current = $fromDate->copy();
    while ($current->lte($toDate)) {
        $dateRange[] = $current->format('j/n');
        $current->addDay();
    }

    // Define header row
    $columns = array_merge([
        'Employee', 'From Date', 'To Date',
    ], $dateRange, [
        'Present Days', 'Gross Salary', 'Gross Payable', 'Total Deductions', 'Net Payable'
    ]);

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename={$fileName}",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate',
        'Expires' => '0',
    ];

    $callback = function () use ($payments, $columns, $fromDate, $toDate) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($payments as $p) {
            $attendance = \App\Models\Attendance::where('user_id', $p->user_id)
                ->whereBetween('clock_in', [$fromDate, $toDate])
                ->get()
                ->groupBy(function ($att) {
                    return \Carbon\Carbon::parse($att->clock_in)->format('Y-m-d');
                });

            // Fetch leave records for the same period
            $leaves = \App\Models\Leave::where('user_id', $p->user_id)
                ->where('status', 'Approved')
                ->where(function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('from_date', [$fromDate, $toDate])
                      ->orWhereBetween('to_date', [$fromDate, $toDate]);
                })
                ->get();

            // Map leave days
            $leaveDays = [];
            foreach ($leaves as $leave) {
                $leaveFrom = \Carbon\Carbon::parse($leave->from_date);
                $leaveTo = \Carbon\Carbon::parse($leave->to_date);

                for ($d = $leaveFrom->copy(); $d->lte($leaveTo); $d->addDay()) {
                    $leaveDays[$d->format('Y-m-d')] = strtoupper(substr($leave->type, 0, 1)); // e.g. P/L/S
                }
            }

            // Build daily status array
            $dailyStatus = [];
            $current = $fromDate->copy();
            while ($current->lte($toDate)) {
                $key = $current->format('Y-m-d');

                if (isset($attendance[$key])) {
                    $dailyStatus[] = 'P'; // Present
                } elseif (isset($leaveDays[$key])) {
                    $dailyStatus[] = $leaveDays[$key]; // PL, SL, EL
                } else {
                    $dailyStatus[] = 'A'; // Absent
                }

                $current->addDay();
            }

            fputcsv($file, array_merge([
                $p->user->name,
                $p->from_date,
                $p->to_date,
            ], $dailyStatus, [
                $p->present_days,
                $p->gross_salary,
                $p->gross_payable,
                $p->total_deduction,
                $p->net_payable
            ]));
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


// public function export(): StreamedResponse
// {
//     $fileName = 'salary_payments_with_attendance.csv';
//     $payments = \App\Models\Payment::with('user')->get();

//     // Set date range from first record (or fallback to current month)
//     $fromDate = \Carbon\Carbon::parse($payments->first()?->from_date ?? now()->startOfMonth());
//     $toDate = \Carbon\Carbon::parse($payments->first()?->to_date ?? now()->endOfMonth());

//     // Generate date headers: 1/7, 2/7, ...
//     $dateRange = [];
//     $current = $fromDate->copy();
//     while ($current->lte($toDate)) {
//         $dateRange[] = $current->format('j/n');
//         $current->addDay();
//     }

//     // Define header row (dates before totals)
//     $columns = array_merge([
//         'Employee', 'From Date', 'To Date',
//     ], $dateRange, [
//         'Present Days', 'Gross Salary', 'Gross Payable', 'Total Deductions', 'Net Payable'
//     ]);

//     $headers = [
//         'Content-Type' => 'text/csv',
//         'Content-Disposition' => "attachment; filename={$fileName}",
//         'Pragma' => 'no-cache',
//         'Cache-Control' => 'must-revalidate',
//         'Expires' => '0',
//     ];

//     $callback = function () use ($payments, $columns, $fromDate, $toDate) {
//         $file = fopen('php://output', 'w');
//         fputcsv($file, $columns);

//         foreach ($payments as $p) {
//             $attendance = \App\Models\Attendance::where('user_id', $p->user_id)
//                 ->whereBetween('clock_in', [$fromDate, $toDate])
//                 ->get()
//                 ->groupBy(function ($att) {
//                     return \Carbon\Carbon::parse($att->clock_in)->format('Y-m-d');
//                 });

//             // Build daily status array
//             $dailyStatus = [];
//             $current = $fromDate->copy();
//             while ($current->lte($toDate)) {
//                 $key = $current->format('Y-m-d');
//                 $dailyStatus[] = isset($attendance[$key]) ? 'P' : 'A';
//                 $current->addDay();
//             }

//             fputcsv($file, array_merge([
//                 $p->user->name,
//                 $p->from_date,
//                 $p->to_date,
//             ], $dailyStatus, [
//                 $p->present_days,
//                 $p->gross_salary,
//                 $p->gross_payable,
//                 $p->total_deduction,
//                 $p->net_payable
//             ]));
//         }

//         fclose($file);
//     };

//     return response()->stream($callback, 200, $headers);
// }

}

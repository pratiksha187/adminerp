<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\User;
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
        'user_id' => 'required|exists:users,id',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
    ]);

    $userid = $request->user_id;

    // Check if payment already exists
    $exists = Payment::where('user_id', $userid)
            ->whereDate('from_date', $request->from_date)
            ->whereDate('to_date', $request->to_date)
            ->exists();

    if ($exists) {
        return back()->with('error', 'Payment is already generated for this period!');
    }

    $user = User::findOrFail($userid);
    $user_details = DB::table('users')->where('id', $userid)->first();
    $gross_salary = $user->salary ?? 0;

    $from = Carbon::parse($request->from_date);
    $to = Carbon::parse($request->to_date);

    $daysInMonth = $from->daysInMonth;
    $per_day_rate = round($gross_salary / $daysInMonth, 2);

    // Get attendance records
    $attendances = Attendance::where('user_id', $user->id)
                ->whereBetween('clock_in', [$from, $to])
                ->get();
    // dd($attendances);
    $present_days = 0;

    foreach ($attendances as $attendance) {
        if (!$attendance->clock_out || !$attendance->clock_in) continue;

        $clockIn = Carbon::parse($attendance->clock_in);
        $clockOut = Carbon::parse($attendance->clock_out);

        $workedMinutes = $clockIn->diffInMinutes($clockOut);

        if ($workedMinutes >= 270) { // 4.5 hours or more → full day
            $present_days += 1;
        } elseif ($workedMinutes > 0 && $workedMinutes < 270) { // less than 4.5h → half day
            $present_days += 0.5;
        }
    }
// dd($workedMinutes);
    // Count weekly offs
    $weekoffCount = 0;
    for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
        if ($date->isSunday()) $weekoffCount++;
    }

    // Count holidays
    $holidayCount = DB::table('holidays')
                    ->whereBetween('date', [$from, $to])
                    ->count();

    // Total days considered present
    $present_days_act = $present_days + $weekoffCount + $holidayCount;

    // Gross payable
    $gross_payable = round($per_day_rate * $present_days_act, 2);

    // Salary components
    $basic_60 = round($gross_salary * 0.6, 2);
    $hra_5 = round($gross_salary * 0.05, 2);
    $conveyance_20 = round($gross_salary * 0.2, 2);
    $other_allowance = $gross_salary - $basic_60 - $hra_5 - $conveyance_20;

    // Deductions
    $pf = $user_details->pf ?? 0;
    $insurance = $user_details->insurance ?? 0;
    $pt = $user_details->pt ?? 0;
    $advance = $user_details->advance ?? 0;
    $total_deduction = $pf + $insurance + $pt + $advance;

    $net_payable = $gross_payable - $total_deduction;

    // Save Payment
    Payment::create([
        'user_id' => $user->id,
        'from_date' => $from,
        'to_date' => $to,
        'present_days' => $present_days_act,
        'gross_salary' => $gross_salary,
        'per_day_rate' => $per_day_rate,
        'basic_60' => $basic_60,
        'hra_5' => $hra_5,
        'conveyance_20' => $conveyance_20,
        'other_allowance' => $other_allowance,
        'ot_arrears' => 0,
        'gross_payable' => $gross_payable,
        'pf_12' => $pf,
        'insurance' => $insurance,
        'pt' => $pt,
        'weekoffCount' => $weekoffCount,
        'advance' => $advance,
        'total_deduction' => $total_deduction,
        'net_payable' => $net_payable,
        'holidayCount' => $holidayCount,
        'present_days_in_month' => $present_days,
    ]);

    return back()->with('success', 'Payment generated successfully!');
}


// public function generatePayment(Request $request)
// {
//     $request->validate([
//         'user_id' => 'required|exists:users,id',
//         'from_date' => 'required|date',
//         'to_date' => 'required|date|after_or_equal:from_date',
//     ]);
//     $weekoffCount = 0;
//     $userid = $request->user_id;

//     $exists = Payment::where('user_id', $userid)
//             ->whereDate('from_date', $request->from_date)
//             ->whereDate('to_date', $request->to_date)
//             ->exists();

//     if ($exists) {
//         return back()->with('error', 'Payment is already generated for this period!');
//     }
//     $user = User::findOrFail($request->user_id);
//     $ot_arrears = 0;
//     $user_details =  DB::table('users')
//                     ->where('id', $userid)  
//                     ->first();
//                     // dd($user_details);
//     $from = Carbon::parse($request->from_date);
//     $to = Carbon::parse($request->to_date);
//     $gross_salary = $user->salary ?? 0;
  
//     $daysInMonth = $from->daysInMonth; 
   
//     $per_day_rate = round($gross_salary / $daysInMonth);

//     $present_days_in_month = Attendance::where('user_id', $user->id)
//         ->whereBetween('clock_in', [$from, $to])
//         ->selectRaw('DATE(clock_in) as day')
//         ->distinct()
//         ->count();
// dd($present_days_in_month);
//     for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
//         if ($date->isSunday()) {   
//             $weekoffCount++;
//         }
//     }  
    
//     $holidayCount = DB::table('holidays')
//                     ->whereBetween('date', [$from, $to])
//                     ->count();

//     $present_days_act = $present_days_in_month + $weekoffCount + $holidayCount;
//     $gross_payable = round($per_day_rate * $present_days_act);

//     $basic_60 = round($gross_salary * 0.6, 2);
//     $hra_5 = round($gross_salary * 0.05, 2);
//     $conveyance_20 = round($gross_salary * 0.2, 2);
//     $other_allowance = $gross_salary - $basic_60 - $hra_5 - $conveyance_20;

//     $pf = $user_details->pf;
//     $insurance = $user_details->insurance;
//     $pt = $user_details->pt;
//     $advance = $user_details->advance;
//     $total_deduction = $pf + $insurance + $pt + $advance;

//     $net_payable = $gross_payable - $pf - $insurance - $pt - $advance;


//     $payment = Payment::create([
//         'user_id' => $user->id,
//         'from_date' => $from,
//         'to_date' => $to,
//         'present_days' => $present_days_act,
//         'gross_salary' => $gross_salary,
//         'per_day_rate' => $per_day_rate,
//         'basic_60' => $basic_60,
//         'hra_5' => $hra_5,
//         'conveyance_20' => $conveyance_20,
//         'other_allowance' => $other_allowance,
//         'ot_arrears' => $ot_arrears,
//         'gross_payable' => $gross_payable,
//         'pf_12' => $pf,
//         'insurance' => $insurance,
//         'pt' => $pt,
//         'weekoffCount' =>$weekoffCount,
//         'advance' => $advance,
//         'total_deduction' => $total_deduction,
//         'net_payable' => $net_payable,
//         'holidayCount' => $holidayCount,
//         'present_days_in_month' => $present_days_in_month,
//     ]);

//     return back()->with('success', 'Payment generated successfully!');
    
   
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

    // Define header row (dates before totals)
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

            // Build daily status array
            $dailyStatus = [];
            $current = $fromDate->copy();
            while ($current->lte($toDate)) {
                $key = $current->format('Y-m-d');
                $dailyStatus[] = isset($attendance[$key]) ? 'P' : 'A';
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

}

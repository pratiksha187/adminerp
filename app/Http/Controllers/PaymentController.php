<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;


class PaymentController extends Controller
{

   public function index()
{
    $payments = \App\Models\Payment::with('user')->latest()->get();
    return view('payments.index', compact('payments'));
}
public function create()
{
    $users = \App\Models\User::all(); // fetch employees for dropdown
    return view('payments.create', compact('users'));
}


   public function generatePayment(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
    ]);

    $user = User::findOrFail($request->user_id);
    $from = Carbon::parse($request->from_date);
    $to = Carbon::parse($request->to_date);

    $gross_salary = $user->gross_salary ?? 0;
    $per_day_rate = round($gross_salary / 31, 2);

    // Get present days
    $present_days = Attendance::where('user_id', $user->id)
        ->whereBetween('clock_in', [$from, $to])
        ->selectRaw('DATE(clock_in) as day')
        ->distinct()
        ->count();

    // Payment logic
    $basic_60 = round($gross_salary * 0.6, 2);
    $hra_5 = round($basic_60 * 0.05, 2);
    $conveyance_20 = round($basic_60 * 0.2, 2);
    $other_allowance = 2000; // example static
    $ot_arrears = 0;         // can be set dynamically

    $gross_payable = round($per_day_rate * $present_days + $hra_5 + $conveyance_20 + $other_allowance + $ot_arrears, 2);
    $pf = round($gross_payable * 0.12, 2);
    $insurance = 500; // fixed
    $pt = 200;        // fixed
    $advance = 0;     // from advance table if needed

    $total_deduction = $pf + $insurance + $pt + $advance;
    $net_payable = $gross_payable - $total_deduction;

    $payment = Payment::create([
        'user_id' => $user->id,
        'from_date' => $from,
        'to_date' => $to,
        'present_days' => $present_days,
        'gross_salary' => $gross_salary,
        'per_day_rate' => $per_day_rate,
        'basic_60' => $basic_60,
        'hra_5' => $hra_5,
        'conveyance_20' => $conveyance_20,
        'other_allowance' => $other_allowance,
        'ot_arrears' => $ot_arrears,
        'gross_payable' => $gross_payable,
        'pf_12' => $pf,
        'insurance' => $insurance,
        'pt' => $pt,
        'advance' => $advance,
        'total_deduction' => $total_deduction,
        'net_payable' => $net_payable,
    ]);

    return back()->with('success', 'Payment generated successfully!');
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

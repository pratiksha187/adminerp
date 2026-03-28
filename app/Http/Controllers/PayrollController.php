<?php

namespace App\Http\Controllers;

use App\Imports\PayrollImport;
use App\Models\EmployeePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Auth;


class PayrollController extends Controller
{
    public function uploadForm()
    {
        $userId = Auth::id();
        //   dd($userId);
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)
                    ->first();
                     $role = $userDetails->role;
        return view('payroll.upload',compact('role'));
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'month' => 'nullable|string|max:20',
            'year' => 'nullable|string|max:10',
        ]);

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $storedPath = $file->store('payroll_excels', 'public');
            $fullPath = storage_path('app/public/' . $storedPath);

            if ($request->filled('month') && $request->filled('year')) {
                EmployeePayment::where('month', $request->month)
                    ->where('year', $request->year)
                    ->delete();
            }

            Excel::import(
                new PayrollImport(
                    basename($storedPath),
                    $request->month,
                    $request->year
                ),
                $fullPath
            );

            $payments = EmployeePayment::where('excel_file_name', basename($storedPath))->get();

            // foreach ($payments as $payment) {
            //     $pdf = Pdf::loadView('payroll.slip_pdf', compact('payment'))->setPaper('a4', 'portrait');

            //     $pdfPath = 'payment_slips/' . $payment->id . '_' . time() . '.pdf';
            //     Storage::disk('public')->put($pdfPath, $pdf->output());

            //     $payment->update([
            //         'pdf_path' => $pdfPath
            //     ]);
            // }
            foreach ($payments as $payment) {

                $from = \Carbon\Carbon::parse($payment->month . ' ' . $payment->year);
                $daysInMonth = $from->daysInMonth;

                $pdf = Pdf::loadView('payroll.slip_pdf', compact('payment', 'daysInMonth'))
                    ->setPaper('a4', 'portrait');

                $pdfPath = 'payment_slips/' . $payment->id . '_' . time() . '.pdf';
                Storage::disk('public')->put($pdfPath, $pdf->output());

                $payment->update([
                    'pdf_path' => $pdfPath
                ]);
            }

            DB::commit();

            return redirect()->route('payroll.index')
                ->with('success', 'Excel uploaded, data imported and payment slips generated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function index()
    {
          $userId = Auth::id();
    //   dd($userId);
      $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)
                    ->first();
                     $role = $userDetails->role;
        $payments = EmployeePayment::latest()->paginate(20);
        return view('payroll.index', compact('payments','role'));
    }

    public function show($id)
    {
          $userId = Auth::id();
    //   dd($userId);
      $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)
                    ->first();
                     $role = $userDetails->role;
        $payment = EmployeePayment::findOrFail($id);
        return view('payroll.show', compact('payment','role'));
    }

    public function downloadSlip($id)
    {
        $payment = EmployeePayment::findOrFail($id);

        if (!$payment->pdf_path || !Storage::disk('public')->exists($payment->pdf_path)) {
            $pdf = Pdf::loadView('payroll.slip_pdf', compact('payment'))->setPaper('a4', 'portrait');

            $pdfPath = 'payment_slips/' . $payment->id . '_' . time() . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());

            $payment->update([
                'pdf_path' => $pdfPath
            ]);
        }

        return Storage::disk('public')->download(
            $payment->pdf_path,
            str_replace(' ', '_', $payment->employee_name) . '_salary_slip.pdf'
        );
    }
}
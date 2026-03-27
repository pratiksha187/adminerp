<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * ✅ Get logged-in user role
     */
    private function getUserRole()
    {
        $userId = Auth::id();

        $user = DB::table('users')
            ->select('role')
            ->where('id', $userId)
            ->first();

        return $user->role ?? null;
    }

    /**
     * 📄 Show Form
     */
    public function form()
    {
        $roleId = $this->getUserRole();
        return view('invoice.form', compact('roleId'));
    }

    /**
     * 📋 List Page
     */
    public function list()
    {
        $roleId = $this->getUserRole();
        $invoices = session('invoices', []);

        return view('invoice.list', compact('invoices', 'roleId'));
    }

    /**
     * ⚙️ Generate Invoice
     */
    public function generate(Request $request)
    {
        $roleId = $this->getUserRole();

        // ✅ VALIDATION (IMPORTANT)
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $mainAmount = $request->amount;

        // ✅ 1% calculation
        $amount = $mainAmount * 0.01;

        // ✅ GST
        $cgst = $amount * 0.09;
        $sgst = $amount * 0.09;

        $total = $amount + $cgst + $sgst;

        // ✅ FINAL DATA STRUCTURE
        $invoice = [
            'invoice_no' => 'INV-' . rand(1000, 9999),
            'date'       => date('d-m-Y'),

            // 🔹 Vendor
            'vendor' => [
                'name'    => $request->vendor_name ?? '',
                'address' => $request->vendor_address ?? '',
                'phone'   => $request->vendor_phone ?? '',
                'email'   => $request->vendor_email ?? '',
                'gstin'   => $request->vendor_gstin ?? '',
            ],

            // 🔹 Bill To
            'bill' => [
                'name'    => $request->bill_name ?? '',
                'address' => $request->bill_address ?? '',
                'phone'   => $request->bill_phone ?? '',
                'email'   => $request->bill_email ?? '',
                'gstin'   => $request->bill_gstin ?? '',
            ],

            // 🔹 Item
            'description' => $request->description ?? '',

            // 🔹 Amounts
            'main_amount' => $mainAmount,
            'amount'      => $amount,
            'cgst'        => $cgst,
            'sgst'        => $sgst,
            'total'       => $total,
        ];
// dd($invoice);
        // ✅ STORE IN SESSION
        $invoices = session('invoices', []);
        $invoices[] = $invoice;

        session([
            'invoices' => $invoices,
            'invoice'  => $invoice
        ]);

        return view('invoice.show', compact('invoice', 'roleId'));
    }

    /**
     * 📥 Download PDF
     */
    public function download()
    {
        $roleId = $this->getUserRole();
        $invoice = session('invoice');

        if (!$invoice) {
            return redirect()->back()->with('error', 'No invoice found');
        }

        // $pdf = Pdf::loadView('invoice.pdf', compact('invoice', 'roleId'))
        //     ->setPaper('A4', 'portrait')
        //     ->setOptions([
        //         'isRemoteEnabled' => true
        //     ]);
        $pdf = Pdf::loadView('invoice.pdf', compact('invoice'))
    ->setPaper('A4', 'portrait')
    ->setOptions([
        'isRemoteEnabled' => true,
        'fontDir' => storage_path('fonts/'),
        'fontCache' => storage_path('fonts/'),
    ]);

        return $pdf->download('TaxInvoice.pdf');
    }
}
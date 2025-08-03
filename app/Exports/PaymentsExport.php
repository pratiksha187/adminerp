<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Payment::with('user')
            ->get()
            ->map(function ($payment) {
                return [
                    'Employee Name' => $payment->user->name,
                    'From Date' => $payment->from_date,
                    'To Date' => $payment->to_date,
                    'Per Day Rate' => $payment->per_day_rate,
                    'Present Days' => $payment->total_present_days,
                    'Total Amount' => $payment->total_amount,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'From Date',
            'To Date',
            'Per Day Rate',
            'Present Days',
            'Total Amount',
        ];
    }
}


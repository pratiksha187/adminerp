<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayment extends Model
{
    protected $fillable = [
        'month',
        'year',
        'employee_name',
        'designation',
        'joining_date',
        'gross_salary_monthly',
        'per_day_salary',
        'half_days',
        'present_days',
        'weekly_off',
        'paid_leave',
        'extra_days',
        'total_days',
        'basic_salary',
        'hra',
        'conveyance',
        'other_allowance',
        'gross_earnings',
        'pf',
        'esic',
        'pt',
        'advance_amount',
        'ot_amount',
        'leave_deduction',
        'total_deduction',
        'net_payable',
        'excel_file_name',
        'pdf_path',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];
}
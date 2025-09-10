<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table ='emppayments';
    protected $fillable = [
        'user_id', 'from_date', 'to_date', 'present_days',
        'gross_salary', 'per_day_rate', 'basic_60', 'hra_5',
        'conveyance_20', 'other_allowance', 'ot_arrears', 'gross_payable',
        'pf_12', 'insurance', 'pt', 'advance', 'total_deduction', 'net_payable','present_days_in_month','holidayCount','weekoffCount'
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

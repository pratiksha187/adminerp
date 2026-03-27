<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'invoice_no',
        'client_name',
        'client_address',
        'amount',
        'gst',
        'total'
    ];
}
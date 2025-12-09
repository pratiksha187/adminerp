<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $table = 'po_items';
    protected $fillable = [
        'purchase_order_id', 'description', 'hsn', 'qty', 'unit', 'rate', 'amount'
    ];
}

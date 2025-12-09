<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderTerm extends Model
{
    
protected $table = 'po_terms';
    protected $fillable = ['purchase_order_id', 'term'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table ='purchase_orders';
    protected $fillable = [
        'ref_no', 'po_no', 'po_date', 'supplier_ref', 'dispatch_through', 'destination','consignee_name','consignee_address','consignee_phone','consignee_email','consignee_gstin','buyer_name',
        'buyer_address','buyer_phone','buyer_email','buyer_gstin',
        'subtotal','gst_type', 'cgst_percent', 'cgst_amount', 'sgst_percent', 'sgst_amount', 'grand_total','grandTotalWords','forpo'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function terms()
    {
        return $this->hasMany(PurchaseOrderTerm::class);
    }
}

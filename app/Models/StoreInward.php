<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInward extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_dpr_id',
        'item',       // currently stores site_id
        'vendor',
        'rate',
        'qty',
        'type',
    ];

    public function dpr()
    {
        return $this->belongsTo(StoreDpr::class,'store_dpr_id');
    }

    // Add this to get Site Name
   public function site()
{
    return $this->belongsTo(SiteName::class, 'item'); // 'item' stores site ID
}
 public function itemName()
    {
        return $this->belongsTo(StoreRequirement::class, 'item', 'id');
    }
}

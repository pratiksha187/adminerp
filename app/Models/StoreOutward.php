<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOutward extends Model
{
    use HasFactory;

    protected $fillable = ['store_dpr_id', 'item', 'qty'];

    public function dpr()
    {
        return $this->belongsTo(StoreDpr::class,'store_dpr_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTask extends Model
{
    use HasFactory;

    protected $fillable = ['store_dpr_id', 'task'];

    public function dpr()
    {
        return $this->belongsTo(StoreDpr::class,'store_dpr_id');
    }
}

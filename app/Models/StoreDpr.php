<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreDpr extends Model
{
    use HasFactory;

    protected $fillable = ['store_name'];

    public function inwards()  { return $this->hasMany(StoreInward::class); }
    public function outwards() { return $this->hasMany(StoreOutward::class); }
    public function issued()   { return $this->hasMany(StoreIssued::class); }
    public function tasks()    { return $this->hasMany(StoreTask::class); }

    // ✅ Calculate stock for this DPR
    public function getStock()
    {
        $stocks = [];
// dd($this->inwards);
        foreach ($this->inwards as $inward) {
            $stocks[$inward->item] = ($stocks[$inward->item] ?? 0) + $inward->qty;
        }
        foreach ($this->outwards as $outward) {
            $stocks[$outward->item] = ($stocks[$outward->item] ?? 0) - $outward->qty;
        }
        foreach ($this->issued as $issued) {
            $stocks[$issued->item] = ($stocks[$issued->item] ?? 0) - $issued->qty;
        }

        return $stocks;
    }

    // app/Models/StoreDpr.php
public function requirements()
{
    return $this->hasMany(StoreRequirement::class, 'store_dpr_id');
}

}

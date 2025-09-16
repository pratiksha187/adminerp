<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreReport extends Model
{
    protected $fillable = [
        'date',
        'store_name',
        'inward_material',
        'outward_material',
        'tasks_completed',
    ];

    protected $casts = [
        'inward_material' => 'array',
        'outward_material' => 'array',
        'tasks_completed' => 'array',
    ];
}

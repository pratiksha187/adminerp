<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkEntry extends Model
{
    protected $fillable = [
        'date',
        'chapter_id',
        'supervisor_id',
        'description',
        'unit',
        'length',
        'breadth',
        'height',
        'days',
        'in_time',
        'out_time',
        'tonnage',
        'total_quantity',
        'labour',
        'description_of_work_done',
    ];

    protected $casts = [
         'labour' => 'array',
        'date' => 'date',
        'in_time' => 'datetime:H:i',
        'out_time' => 'datetime:H:i',
    ];

    
}


<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkEntry extends Model
{
    protected $fillable = [
        'date', 'chapter_id', 'description', 'unit', 'length', 'breadth', 'height',
        'total_quantity', 'supervisor_id', 'labour',
    ];

    protected $casts = [
        'labour' => 'array',
        'date' => 'date',
    ];

    
}


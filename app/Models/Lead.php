<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    
    protected $fillable = [
        'full_name','phone','email','source','stage','owner','notes','next_activity_at'
    ];

    protected $casts = ['next_activity_at' => 'datetime'];
}

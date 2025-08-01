<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'clock_in', 'clock_out'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

}


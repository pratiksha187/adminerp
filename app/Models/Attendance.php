<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
     protected $table = 'attendances';
    protected $fillable = ['user_id', 'clock_in', 'clock_out','latitude','longitude','out_latitude','out_longitude'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

}


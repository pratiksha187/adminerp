<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualAttendance extends Model
{
    protected $table ='manual_attendances';
    protected $fillable = ['user_id', 'clock_in', 'clock_out'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class Leave extends Model
{
      use HasFactory;

    protected $fillable = [
        'user_id', 'from_date', 'to_date', 'type', 'reason', 'status','hod_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

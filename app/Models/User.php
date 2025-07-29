<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table ='users';
    protected $fillable = [
        'name', 'email', 'password', 'employee_code', 'mobile_no', 'gender', 'marital_status',
        'dob', 'join_date', 'confirmation_date', 'probation_months', 'aadhaar', 
        'hours_day', 'days_week', 'role'
    ];

    

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // For Laravel 10+, you can add 'password' => 'hashed',
    ];
}

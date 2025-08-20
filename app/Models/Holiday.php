<?php

// app/Models/Holiday.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['date','title','type','color','notes'];
    public $casts = ['date' => 'date'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterHead extends Model
{
    protected $table ='letter_heads';
     protected $fillable = ['date', 'name','ref_no', 'description'];
}

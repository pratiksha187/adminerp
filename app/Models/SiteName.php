<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteName extends Model
{
    use HasFactory;

    protected $table = 'site_name'; // your table name
    protected $fillable = ['name']; // the columns you need
}

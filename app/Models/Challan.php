<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challan extends Model
{
    protected $table ='challans';
    protected $fillable = [
    'challan_no', 'date', 'material','party_name',
    'vehicle_no', 'measurement', 'location', 'time','driver_name','quantity',
    'receiver_sign', 'driver_sign','pdf_path','party_add','party_contact_person','remark'
];

}

<?php
// app/Models/StoreRequirement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreRequirement extends Model
{
    protected $fillable = ['requester_id'];

    public function items()
    {
        return $this->hasMany(StoreRequirementItem::class);
    }

    
}

class StoreRequirementItem extends Model
{
    protected $fillable = ['store_requirement_id', 'name', 'qty', 'unit', 'remark'];
}


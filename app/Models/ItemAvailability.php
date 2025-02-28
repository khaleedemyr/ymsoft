<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemAvailability extends Model
{
    protected $fillable = [
        'item_id',
        'availability_type',
        'region_id',
        'outlet_id'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }
} 
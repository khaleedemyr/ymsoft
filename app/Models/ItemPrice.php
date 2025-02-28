<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPrice extends Model
{
    protected $fillable = [
        'item_id',
        'region_id',
        'price'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
} 
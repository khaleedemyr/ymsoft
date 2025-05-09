<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    protected $table = 'item_images';
    
    protected $fillable = ['item_id', 'path'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
} 
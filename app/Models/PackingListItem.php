<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'packing_list_id',
        'item_id',
        'quantity'
    ];

    public function packingList()
    {
        return $this->belongsTo(PackingList::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
} 
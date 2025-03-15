<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloorOrderItem extends Model
{
    protected $table = 'floor_order_items';

    protected $fillable = [
        'item_id',
        'qty',
        'price',
        'total'
    ];

    protected $casts = [
        'qty' => 'decimal:2'
    ];

    /**
     * Get the floor order that owns the item.
     */
    public function floorOrder(): BelongsTo
    {
        return $this->belongsTo(FloorOrder::class, 'floor_order_id');
    }

    /**
     * Get the item details.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
} 
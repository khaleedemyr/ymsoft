<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'warehouse_id',
        'item_id',
        'stock_on_hand',
        'stock_available',
        'moving_average_cost',
        'last_purchase_price',
        'lowest_purchase_price',
        'highest_purchase_price',
        'total_value'
    ];

    protected $casts = [
        'stock_on_hand' => 'decimal:2',
        'stock_available' => 'decimal:2',
        'moving_average_cost' => 'decimal:2',
        'last_purchase_price' => 'decimal:2',
        'lowest_purchase_price' => 'decimal:2',
        'highest_purchase_price' => 'decimal:2',
        'total_value' => 'decimal:2'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
} 
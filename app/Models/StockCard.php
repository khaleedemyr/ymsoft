<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'item_id',
        'date',
        'reference_type',
        'reference_id',
        'reference_number',
        'qty_in',
        'qty_out',
        'stock_balance',
        'notes',
        'created_by',
        'unit_price',
        'total_value',
        'moving_average_cost',
        'old_stock_value',
        'new_stock_value'
    ];

    protected $casts = [
        'date' => 'datetime',
        'qty_in' => 'decimal:2',
        'qty_out' => 'decimal:2',
        'stock_balance' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
        'moving_average_cost' => 'decimal:2',
        'old_stock_value' => 'decimal:2',
        'new_stock_value' => 'decimal:2'
    ];

    // Relationship dengan Warehouse
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Relationship dengan Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relationship dengan User (creator)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Method untuk mendapatkan reference model
    public function reference()
    {
        return $this->morphTo();
    }

    public function smallUnit()
    {
        return $this->belongsTo(Unit::class, 'small_unit_id');
    }

    public function mediumUnit()
    {
        return $this->belongsTo(Unit::class, 'medium_unit_id');
    }

    public function largeUnit()
    {
        return $this->belongsTo(Unit::class, 'large_unit_id');
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodReceiveItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'good_receive_id',
        'purchase_order_item_id',
        'quantity',
        'unit_id',
        'price',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function goodReceive()
    {
        return $this->belongsTo(GoodReceive::class, 'good_receive_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->subtotal = $item->quantity * $item->price;
        });

        static::updating(function ($item) {
            $item->subtotal = $item->quantity * $item->price;
        });
    }
} 
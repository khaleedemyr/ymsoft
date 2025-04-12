<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_invoice_id',
        'item_id',
        'quantity',
        'unit_id',
        'original_price',
        'invoice_price',
        'discount_type',
        'discount_value',
        'discount_amount',
        'price',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'original_price' => 'decimal:2',
        'invoice_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the purchase invoice that owns the item.
     */
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id', 'id');
    }

    /**
     * Get the item associated with the purchase invoice item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    /**
     * Get the unit associated with the purchase invoice item.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
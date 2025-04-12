<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenancePurchaseOrderItem extends Model
{
    protected $table = 'maintenance_purchase_order_items';

    protected $fillable = [
        'po_id',
        'supplier_id',
        'item_name',
        'description',
        'specifications',
        'quantity',
        'unit_id',
        'price',
        'supplier_price',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'supplier_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(MaintenancePurchaseOrder::class, 'po_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    // Accessor untuk memastikan subtotal selalu dihitung dengan benar
    public function getSubtotalAttribute($value)
    {
        // Jika supplier_price ada, gunakan itu, jika tidak gunakan price
        $price = $this->supplier_price ?? $this->price;
        return $this->quantity * $price;
    }

    // Mutator untuk mengupdate subtotal saat quantity atau price berubah
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->updateSubtotal();
    }

    public function setSupplierPriceAttribute($value)
    {
        $this->attributes['supplier_price'] = $value;
        $this->updateSubtotal();
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value;
        $this->updateSubtotal();
    }

    // Helper method untuk update subtotal
    private function updateSubtotal()
    {
        $price = $this->supplier_price ?? $this->price;
        $this->attributes['subtotal'] = $this->quantity * $price;
    }
}

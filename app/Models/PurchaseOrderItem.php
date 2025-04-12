<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'supplier_id',
        'quantity',
        'uom_id',
        'price',
        'total',
        'purchase_requisition_item_id'
    ];
    
    // Relationship dengan Purchase Order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
    
    // Relationship dengan Item
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    
    // Relationship dengan Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    
    // Relationship dengan Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'uom_id');
    }
    
    // Relationship dengan Purchase Requisition Item
    public function purchaseRequisitionItem()
    {
        return $this->belongsTo(PurchaseRequisitionItem::class, 'purchase_requisition_item_id');
    }

    public function goodReceiveItems()
    {
        return $this->hasMany(GoodReceiveItem::class, 'purchase_order_item_id');
    }
} 
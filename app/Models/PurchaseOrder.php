<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'po_number',
        'po_date',
        'purchase_requisition_id',
        'supplier_id',
        'status',
        'notes',
        'total',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at'
    ];
    
    protected $casts = [
        'po_date' => 'date',
        'total' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];
    
    // Relationship dengan Purchase Requisition
    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }
    
    // Relationship dengan Items
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    
    // Relationship dengan User
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // Relationship dengan Approver
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    // Relationship dengan Rejector
    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
    
    // Relationship dengan Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

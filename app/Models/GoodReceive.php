<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodReceive extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gr_number',
        'po_id',
        'receive_date',
        'notes',
        'status',
        'total_amount',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at'
    ];

    protected $casts = [
        'receive_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function items()
    {
        return $this->hasMany(GoodReceiveItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function canEdit()
    {
        return $this->status === 'draft';
    }

    public function canDelete()
    {
        return $this->status === 'draft';
    }

    public function canApprove()
    {
        return $this->status === 'draft';
    }

    public function canReject()
    {
        return $this->status === 'draft';
    }

    /**
     * Get the purchase invoice associated with the good receive.
     */
    public function purchaseInvoice()
    {
        return $this->hasOne(PurchaseInvoice::class);
    }

    /**
     * Get the supplier associated with the good receive.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    /**
     * Get the warehouse associated with the good receive.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function goodReceiveItems()
    {
        return $this->hasMany(GoodReceiveItem::class, 'good_receive_id');
    }

} 
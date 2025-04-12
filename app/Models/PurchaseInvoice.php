<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'supplier_id',
        'warehouse_id',
        'good_receive_id',
        'payment_days',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'vat_type',
        'vat_percentage',
        'vat_amount',
        'grand_total',
        'payment_status',
        'contra_bon_number',
        'contra_bon_date',
        'notes',
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at'
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    protected $attributes = [
        'payment_status' => 'unpaid',
        'status' => 'draft'
    ];

    protected $dates = [
        'invoice_date',
        'due_date'
    ];

    /**
     * Get the good receive associated with the purchase invoice.
     */
    public function goodReceive()
    {
        return $this->belongsTo(GoodReceive::class, 'good_receive_id');
    }

    /**
     * Get the supplier associated with the purchase invoice.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    /**
     * Get the warehouse associated with the purchase invoice.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    /**
     * Get the creator of the purchase invoice.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the updater of the purchase invoice.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Get the approver of the purchase invoice.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * Get the rejector of the purchase invoice.
     */
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }

    /**
     * Get the items for the purchase invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    /**
     * Check if the purchase invoice can be edited
     */
    public function canEdit(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the purchase invoice can be deleted
     */
    public function canDelete(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the purchase invoice can be approved
     */
    public function canApprove(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the purchase invoice can be rejected
     */
    public function canReject(): bool
    {
        return $this->status === 'draft';
    }

    public function contraBons()
    {
        return $this->belongsToMany(ContraBon::class, 'contra_bon_purchase_invoice');
    }
}
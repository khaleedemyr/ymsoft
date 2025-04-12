<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class ContraBon extends Model
{
    protected $fillable = [
        'contra_bon_number',
        'supplier_id',
        'issue_date',
        'due_date',
        'total_amount',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at'
    ];

    protected $dates = [
        'issue_date',
        'due_date',
        'approved_at',
        'paid_at'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(PurchaseInvoice::class, 'contra_bon_invoices')
                    ->withPivot('amount')
                    ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function purchaseInvoices()
    {
        return $this->belongsToMany(PurchaseInvoice::class, 'contra_bon_purchase_invoice');
    }
} 
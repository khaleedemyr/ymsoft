<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContraBonInvoice extends Model
{
    protected $fillable = [
        'contra_bon_id',
        'purchase_invoice_id',
        'amount'
    ];

    public function contraBon()
    {
        return $this->belongsTo(ContraBon::class);
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }
} 
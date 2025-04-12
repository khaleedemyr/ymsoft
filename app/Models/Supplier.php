<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'npwp',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'payment_term',
        'payment_days',
        'status'
    ];

    // Relasi dengan purchase orders (jika diperlukan nanti)
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
} 
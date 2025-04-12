<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePurchaseRequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_id',
        'item_name',
        'description',
        'specifications',
        'quantity',
        'unit_id',
        'price',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'float',
        'price' => 'float',
        'subtotal' => 'float',
    ];

    /**
     * Get the PR that owns the item.
     */
    public function pr()
    {
        return $this->belongsTo(MaintenancePurchaseRequisition::class, 'pr_id');
    }

    /**
     * Get the unit that owns the item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}

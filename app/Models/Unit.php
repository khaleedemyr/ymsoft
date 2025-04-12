<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogActivity;

class Unit extends Model
{
    use LogActivity;

    protected $fillable = [
        'code',
        'name',
        'status'
    ];

    protected $logAttributes = [
        'code',
        'name',
        'status'
    ];

    protected $logName = 'unit';

    public function purchaseRequisitions()
    {
        return $this->hasMany(MaintenancePurchaseRequisition::class, 'unit_id');
    }
} 
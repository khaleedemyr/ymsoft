<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    protected $fillable = [
        'pr_number',
        'date',
        'warehouse_id',
        'department',
        'notes',
        'status',
        'created_by',
        'requested_by',
        'updated_by',
        'approved_ssd_by',
        'approved_ssd_at',
        'approved_cc_by',
        'approved_cc_at'
    ];

    protected $table = 'purchase_requisitions';

    protected $dates = [
        'date',
        'approved_ssd_at',
        'approved_cc_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date' => 'date',
        'approved_ssd_at' => 'datetime',
        'approved_cc_at' => 'datetime'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseRequisitionItem::class, 'purchase_requisition_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function ssdApprover()
    {
        return $this->belongsTo(User::class, 'approved_ssd_by');
    }

    public function ccApprover()
    {
        return $this->belongsTo(User::class, 'approved_cc_by');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::retrieved(function ($model) {
            \Log::info('PR Retrieved:', [
                'id' => $model->id,
                'pr_number' => $model->pr_number
            ]);
        });
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ContraBon;

class Payment extends Model
{
    protected $fillable = [
        'payment_number',
        'contra_bon_id',
        'payment_method',
        'amount',
        'payment_proof',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at'
    ];

    protected $dates = [
        'approved_at',
        'rejected_at'
    ];

    public function contraBon()
    {
        return $this->belongsTo(ContraBon::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
} 
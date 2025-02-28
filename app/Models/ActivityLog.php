<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'old_data',
        'new_data'
    ];

    const UPDATED_AT = null;

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyCheckHeader extends Model
{
    use SoftDeletes;

    protected $table = 'daily_check_headers';

    protected $fillable = [
        'no_daily_check',
        'date',
        'id_outlet',
        'user_id',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(DailyCheckDetail::class, 'daily_check_id');
    }

    public function photos()
    {
        return $this->hasMany(DailyCheckPhoto::class, 'daily_check_id');
    }
} 
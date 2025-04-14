<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'daily_check_areas';

    protected $fillable = [
        'name',
        'sort_order'
    ];

    public function items()
    {
        return $this->hasMany(DailyCheckItem::class, 'area_id');
    }
} 
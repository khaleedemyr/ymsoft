<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyCheckItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daily_check_items';

    protected $fillable = [
        'area_id',
        'name',
        'code',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function details()
    {
        return $this->hasMany(DailyCheckDetail::class, 'item_id');
    }
} 
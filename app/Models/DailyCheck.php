<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'item_id',
        'condition',
        'other_issue',
        'checked_by',
        'time',
        'remark'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function item()
    {
        return $this->belongsTo(DailyCheckItem::class, 'item_id');
    }

    public function photos()
    {
        return $this->hasMany(DailyCheckPhoto::class);
    }
} 
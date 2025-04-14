<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyCheckDetail extends Model
{
    use SoftDeletes;

    protected $table = 'daily_check_details';

    protected $fillable = [
        'daily_check_id',
        'item_id',
        'condition',
        'other_issue',
        'time',
        'remark'
    ];

    protected $attributes = [
        'condition' => 'NA',
        'time' => '00:00:00',
        'other_issue' => '',
        'remark' => ''
    ];

    public function header()
    {
        return $this->belongsTo(DailyCheckHeader::class, 'daily_check_id');
    }

    public function photos()
    {
        return $this->hasMany(DailyCheckPhoto::class, 'daily_check_id')
            ->where('item_id', $this->item_id);
    }

    public function item()
    {
        return $this->belongsTo(DailyCheckItem::class, 'item_id');
    }
} 
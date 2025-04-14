<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCheckPhoto extends Model
{
    use HasFactory;

    protected $table = 'daily_check_photos';

    protected $fillable = [
        'daily_check_id',
        'item_id',
        'photo_path',
        'photo_size',
        'original_name'
    ];

    public function header()
    {
        return $this->belongsTo(DailyCheckHeader::class, 'daily_check_id');
    }

    public function detail()
    {
        return $this->belongsTo(DailyCheckDetail::class, 'daily_check_id')
            ->where('item_id', $this->item_id);
    }

    public function dailyCheck()
    {
        return $this->belongsTo(DailyCheck::class);
    }

    public function item()
    {
        return $this->belongsTo(DailyCheckItem::class, 'item_id');
    }
} 
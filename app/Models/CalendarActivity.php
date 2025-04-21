<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarActivity extends Model
{
    use HasFactory;

    protected $table = 'calendar_activities';
    
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'event_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke event
    public function event()
    {
        return $this->belongsTo(CalendarEvent::class, 'event_id');
    }
} 
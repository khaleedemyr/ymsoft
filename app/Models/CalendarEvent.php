<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CalendarEvent extends Model
{
    use HasFactory;
    
    protected $table = 'calendar_events';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'outlet_id',
        'pic_name',
        'pic_phone',
        'pic_position',
        'status',
        'company_name',
        'segment',
        'area',
        'pax',
        'event_type',
        'estimation_revenue'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relasi ke outlet
    public function outlet()
    {
        return $this->belongsTo(DataOutlet::class, 'outlet_id', 'id_outlet');
    }
    
    // Relasi ke activities
    public function activities()
    {
        return $this->hasMany(CalendarActivity::class, 'event_id');
    }
} 
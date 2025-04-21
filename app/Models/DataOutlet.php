<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataOutlet extends Model
{
    use HasFactory;

    protected $table = 'tbl_data_outlet';

    protected $fillable = [
        'nama_outlet',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'status'
    ];

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'outlet_id');
    }
} 
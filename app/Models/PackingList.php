<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'pl_number',
        'warehouse_id',
        'status',
        'created_by',
        'notes'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PackingListItem::class);
    }

    public function getStatusLabelAttribute()
    {
        return [
            'draft' => 'Draft',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger'
        ][$this->status] ?? 'secondary';
    }
} 
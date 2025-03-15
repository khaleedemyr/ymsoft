<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloorOrder extends Model
{
    protected $table = 'floor_orders';

    protected $fillable = [
        'fo_number',
        'id_outlet',
        'warehouse_id',
        'created_by',
        'order_date',
        'arrival_date',
        'notes',
        'status',
        'total_amount'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'order_date' => 'datetime',
        'arrival_date' => 'date'
    ];

    protected $attributes = [
        'status' => 'draft'
    ];

    // Jika menggunakan enum di database, tambahkan konstanta untuk status yang valid
    const STATUS_DRAFT = 'draft';
    const STATUS_SAVED = 'saved';

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge bg-warning">Draft</span>',
            'saved' => '<span class="badge bg-success">Tersimpan</span>',
            'completed' => '<span class="badge bg-info">Selesai</span>',
            'cancelled' => '<span class="badge bg-danger">Dibatalkan</span>'
        ];

        return $badges[$this->status] ?? $this->status;
    }

    /**
     * Get the items for the floor order.
     */
    public function items()
    {
        return $this->hasMany(FloorOrderItem::class, 'floor_order_id');
    }

    /**
     * Get the warehouse that owns the floor order.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the outlet that owns the floor order.
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    /**
     * Get the user that created the floor order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function floorOrderItems()
    {
        return $this->hasMany(FloorOrderItem::class);
    }

    protected static function booted()
    {
        static::creating(function ($floorOrder) {
            // Generate FO number: FO/YYYYMMDD/XXXX
            $today = now()->format('Ymd');
            $lastOrder = static::where('fo_number', 'like', "FO/{$today}/%")
                ->orderBy('fo_number', 'desc')
                ->first();
            
            if ($lastOrder) {
                $lastNumber = intval(substr($lastOrder->fo_number, -4));
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }
            
            $floorOrder->fo_number = "FO/{$today}/{$newNumber}";
        });
    }

    // Jika perlu, tambahkan validasi untuk status
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            if (!in_array($model->status, ['draft', 'saved'])) {
                throw new \Exception('Invalid status value');
            }
        });
    }
} 
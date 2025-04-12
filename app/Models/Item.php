<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogActivity;

class Item extends Model
{
    use LogActivity;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'sku',
        'name',
        'description',
        'specification',
        'small_unit_id',
        'medium_unit_id',
        'large_unit_id',
        'medium_conversion_qty',
        'small_conversion_qty',
        'status'
    ];

    protected $logAttributes = [
        'category_id',
        'sub_category_id',
        'sku',
        'name',
        'description',
        'small_unit_id',
        'medium_unit_id',
        'large_unit_id',
        'medium_conversion_qty',
        'small_conversion_qty',
        'status'
    ];

    protected $logName = 'item';

    protected $table = 'items';  // Pastikan nama tabel benar

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function smallUnit()
    {
        return $this->belongsTo(Unit::class, 'small_unit_id');
    }

    public function mediumUnit()
    {
        return $this->belongsTo(Unit::class, 'medium_unit_id');
    }

    public function largeUnit()
    {
        return $this->belongsTo(Unit::class, 'large_unit_id');
    }

    public function prices()
    {
        return $this->hasMany(ItemPrice::class);
    }

    public function availabilities()
    {
        return $this->hasMany(ItemAvailability::class);
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'uom_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    // Untuk mendapatkan semua unit yang tersedia
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'item_units', 'item_id', 'unit_id')
            ->withPivot(['conversion']);
    }

    /**
     * Mendapatkan nilai konversi ke satuan terkecil berdasarkan unit yang diberikan
     * @param int $unitId ID unit yang akan dikonversi
     * @return float Nilai pengali untuk konversi ke satuan terkecil
     */
    public function getConversionMultiplier($unitId)
    {
        if ($unitId == $this->small_unit_id) {
            return 1;
        } elseif ($unitId == $this->medium_unit_id) {
            return $this->small_conversion_qty;
        } elseif ($unitId == $this->large_unit_id) {
            return $this->medium_conversion_qty * $this->small_conversion_qty;
        }
        return 1;
    }
} 
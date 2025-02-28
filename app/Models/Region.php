<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogActivity;

class Region extends Model
{
    use LogActivity;

    protected $fillable = [
        'code',
        'name',
        'status'
    ];

    protected $logAttributes = [
        'code',
        'name',
        'status'
    ];

    protected $logName = 'region';

    public function itemPrices()
    {
        return $this->hasMany(ItemPrice::class);
    }
} 
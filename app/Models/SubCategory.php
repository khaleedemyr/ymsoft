<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogActivity;

class SubCategory extends Model
{
    use LogActivity;
    
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'status'
    ];

    protected $logAttributes = [
        'category_id',
        'name', 
        'description',
        'status'
    ];

    protected $logName = 'sub_category';

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['code', 'name', 'description', 'status'];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
} 
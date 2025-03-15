<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function hasPermission($menuSlug, $permission)
    {
        return $this->permissions()
            ->whereHas('menu', function($query) use ($menuSlug) {
                $query->where('slug', $menuSlug);
            })
            ->where("can_{$permission}", true)
            ->exists();
    }
} 
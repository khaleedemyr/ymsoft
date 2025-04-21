<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'id_jabatan',
        'division_id',
        'id_outlet',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function outlet()
    {
        return $this->belongsTo('App\Models\Outlet', 'id_outlet', 'id_outlet');
    }

    public function divisi()
    {
        return $this->belongsTo('App\Models\Divisi', 'division_id', 'id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Jabatan', 'id_jabatan', 'id_jabatan');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole($roleId)
    {
        // Jika menggunakan relasi
        if (method_exists($this, 'roles')) {
            return $this->roles()->where('role_id', $roleId)->exists();
        }
        
        // Jika menggunakan tabel user_roles langsung
        return DB::table('user_roles')
            ->where('user_id', $this->id)
            ->where('role_id', $roleId)
            ->exists();
    }

    public function hasPermission($menuSlug, $permission)
    {
        return $this->roles()->get()->contains(function ($role) use ($menuSlug, $permission) {
            return $role->hasPermission($menuSlug, $permission);
        });
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        
        if (!$this->roles()->where('role_id', $role->id)->exists()) {
            $this->roles()->attach($role->id);
        }
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        
        $this->roles()->detach($role->id);
    }

    public function getNameAttribute()
    {
        return $this->nama_lengkap;
    }

    public function createdPurchaseRequisitions()
    {
        return $this->hasMany(PurchaseRequisition::class, 'created_by');
    }

    public function requestedPurchaseRequisitions()
    {
        return $this->hasMany(PurchaseRequisition::class, 'requested_by');
    }

    public function user_roles()
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    /**
     * Get the PRs created by the user.
     */
    public function purchaseRequisitions()
    {
        return $this->hasMany(MaintenancePurchaseRequisition::class, 'created_by');
    }

    /**
     * Get the PRs approved by the user as Chief Engineering.
     */
    public function chiefEngineeringApprovedPrs()
    {
        return $this->hasMany(MaintenancePurchaseRequisition::class, 'chief_engineering_approval_by');
    }

    /**
     * Get the PRs approved by the user as Purchasing Manager.
     */
    public function purchasingManagerApprovedPrs()
    {
        return $this->hasMany(MaintenancePurchaseRequisition::class, 'purchasing_manager_approval_by');
    }

    /**
     * Get the PRs approved by the user as COO.
     */
    public function cooApprovedPrs()
    {
        return $this->hasMany(MaintenancePurchaseRequisition::class, 'coo_approval_by');
    }

    public function approvedPoAsGmFinance()
    {
        return $this->hasMany(MaintenancePurchaseOrder::class, 'gm_finance_approval_by');
    }

    public function approvedPoAsManagingDirector()
    {
        return $this->hasMany(MaintenancePurchaseOrder::class, 'managing_director_approval_by');
    }

    public function approvedPoAsPresidentDirector()
    {
        return $this->hasMany(MaintenancePurchaseOrder::class, 'president_director_approval_by');
    }

    public function createdPurchaseOrders()
    {
        return $this->hasMany(MaintenancePurchaseOrder::class, 'created_by');
    }

    public function updatedPurchaseOrders()
    {
        return $this->hasMany(MaintenancePurchaseOrder::class, 'updated_by');
    }

    public function calendarActivities()
    {
        return $this->hasMany(CalendarActivity::class, 'user_id');
    }

    // Avatar untuk user, default jika tidak ada
    public function getAvatarAttribute()
    {
        // Di sini bisa tambahkan logika untuk mendapatkan avatar dari storage jika ada
        return 'build/images/users/avatar-default.jpg';
    }
}

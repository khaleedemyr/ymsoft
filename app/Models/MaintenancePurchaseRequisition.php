<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePurchaseRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_number',
        'task_id',
        'total_amount',
        'status',
        'notes',
        'created_by',
        'chief_engineering_approval',
        'chief_engineering_approval_date',
        'chief_engineering_approval_by',
        'purchasing_manager_approval',
        'purchasing_manager_approval_date',
        'purchasing_manager_approval_by',
        'coo_approval',
        'coo_approval_date',
        'coo_approval_by',
        'rejection_notes'
    ];

    protected $casts = [
        'chief_engineering_approval_date' => 'datetime',
        'purchasing_manager_approval_date' => 'datetime',
        'coo_approval_date' => 'datetime',
        'total_amount' => 'float',
    ];

    /**
     * Get the task that owns the PR.
     */
    public function task()
    {
        return $this->belongsTo(MaintenanceTask::class, 'task_id');
    }

    /**
     * Get the items for the PR.
     */
    public function items()
    {
        return $this->hasMany(MaintenancePurchaseRequisitionItem::class, 'pr_id');
    }

    /**
     * Get the user who created the PR.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the PR as Chief Engineering.
     */
    public function chiefEngineeringApprover()
    {
        return $this->belongsTo(User::class, 'chief_engineering_approval_by');
    }

    /**
     * Get the user who approved the PR as Purchasing Manager.
     */
    public function purchasingManagerApprover()
    {
        return $this->belongsTo(User::class, 'purchasing_manager_approval_by');
    }

    /**
     * Get the user who approved the PR as COO.
     */
    public function cooApprover()
    {
        return $this->belongsTo(User::class, 'coo_approval_by');
    }

    /**
     * Scope a query to only include draft PRs.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'DRAFT');
    }

    /**
     * Scope a query to only include approved PRs.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Scope a query to only include rejected PRs.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'REJECTED');
    }

    /**
     * Scope a query to only include PRs that need approval from Chief Engineering.
     */
    public function scopeNeedChiefEngineeringApproval($query)
    {
        return $query->where('chief_engineering_approval', 'PENDING')
                     ->where('status', '!=', 'REJECTED');
    }

    /**
     * Scope a query to only include PRs that need approval from Purchasing Manager.
     */
    public function scopeNeedPurchasingManagerApproval($query)
    {
        return $query->where('chief_engineering_approval', 'APPROVED')
                     ->where('purchasing_manager_approval', 'PENDING')
                     ->where('status', '!=', 'REJECTED');
    }

    /**
     * Scope a query to only include PRs that need approval from COO.
     */
    public function scopeNeedCooApproval($query)
    {
        return $query->where('chief_engineering_approval', 'APPROVED')
                     ->where('purchasing_manager_approval', 'APPROVED')
                     ->where('coo_approval', 'PENDING')
                     ->where('status', '!=', 'REJECTED');
    }
}

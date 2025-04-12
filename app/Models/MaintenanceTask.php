<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceTask extends Model
{
    const PRIORITY_IMPORTANT_URGENT = 'IMPORTANT VS URGENT';
    const PRIORITY_IMPORTANT_NOT_URGENT = 'IMPORTANT VS NOT URGENT';
    const PRIORITY_NOT_IMPORTANT_URGENT = 'NOT IMPORTANT VS URGENT';

    protected $table = 'maintenance_tasks';

    const STATUS_TASK = 'TASK';
    const STATUS_PR = 'PR';
    const STATUS_PO = 'PO';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_IN_REVIEW = 'IN_REVIEW';
    const STATUS_DONE = 'DONE';

    protected $fillable = [
        'task_number',
        'title',
        'description',
        'status',
        'priority_id',
        'label_id',
        'id_outlet',
        'id_ruko',
        'created_by',
        'due_date',
        'completed_at'
    ];

    protected $attributes = [
        'status' => 'TASK' // Set default value
    ];

    protected $dates = [
        'due_date',
        'completed_at',
        'created_at',
        'updated_at'
    ];

    const STATUSES = [
        'Open' => 'Open',
        'PR' => 'PR',
        'PO' => 'PO',
        'In Progress' => 'In Progress',
        'In Review' => 'In Review',
        'Done' => 'Done'
    ];

    const PRIORITIES = [
        'IMPORTANT VS URGENT' => 'Important vs Urgent',
        'IMPORTANT VS NOT URGENT' => 'Important vs Not Urgent',
        'NOT IMPORTANT VS URGENT' => 'Not Important vs Urgent'
    ];

    const LABELS = [
        'Heater' => 'Heater',
        'Refrigeration' => 'Refrigeration',
        'Civil' => 'Civil',
        'Gas' => 'Gas',
        'Machinary' => 'Machinary',
        'Others' => 'Others'
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function ruko()
    {
        return $this->belongsTo(Ruko::class, 'ruko_id', 'id_ruko');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function media()
    {
        return $this->hasMany(MaintenanceMedia::class);
    }

    public function documents()
    {
        return $this->hasMany(MaintenanceDocument::class, 'task_id');
    }

    public function comments()
    {
        return $this->hasMany(MaintenanceComment::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(MaintenanceActivityLog::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'maintenance_task_members', 'task_id', 'user_id')
                    ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Tambahkan validasi untuk priority
    public static function getPriorityValues()
    {
        return [
            self::PRIORITY_IMPORTANT_URGENT,
            self::PRIORITY_IMPORTANT_NOT_URGENT,
            self::PRIORITY_NOT_IMPORTANT_URGENT
        ];
    }

    public static function getValidStatuses()
    {
        return [
            self::STATUS_TASK,
            self::STATUS_PR,
            self::STATUS_PO,
            self::STATUS_IN_PROGRESS,
            self::STATUS_IN_REVIEW,
            self::STATUS_DONE
        ];
    }

    // Mutator untuk memastikan status selalu dalam format yang benar
    public function setStatusAttribute($value)
    {
        $validStatuses = ['TASK', 'PR', 'PO', 'IN_PROGRESS', 'IN_REVIEW', 'DONE'];
        $this->attributes['status'] = in_array($value, $validStatuses) ? $value : 'TASK';
    }

    // Pastikan semua field yang required memiliki nilai
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->status) {
                $model->status = 'TASK';
            }
            if (!$model->created_by) {
                $model->created_by = auth()->id();
            }
        });
    }

    /**
     * Get the PRs for the task.
     */
    public function purchaseRequisitions()
    {
        return $this->hasMany(MaintenancePurchaseRequisition::class, 'task_id');
    }
}

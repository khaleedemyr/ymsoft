<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceActivityLog extends Model
{
    protected $table = 'maintenance_activity_logs';

    protected $fillable = [
        'task_id',
        'user_id',
        'action',
        'description'
    ];

    protected $with = ['user'];

    public function task()
    {
        return $this->belongsTo(MaintenanceTask::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

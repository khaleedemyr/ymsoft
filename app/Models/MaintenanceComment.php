<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceComment extends Model
{
    protected $table = 'maintenance_comments';

    protected $fillable = [
        'task_id',
        'user_id',
        'comment'
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

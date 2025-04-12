<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceDocument extends Model
{
    protected $table = 'maintenance_documents';

    protected $fillable = [
        'task_id',
        'file_name',
        'file_path',
        'file_type'
    ];

    public function task()
    {
        return $this->belongsTo(MaintenanceTask::class, 'task_id');
    }
}

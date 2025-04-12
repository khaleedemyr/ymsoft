<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceEvidence extends Model
{
    use HasFactory;

    protected $table = 'maintenance_evidence';
    
    protected $fillable = [
        'task_id',
        'created_by',
        'notes'
    ];
    
    /**
     * Relasi ke task
     */
    public function task()
    {
        return $this->belongsTo(MaintenanceTask::class, 'task_id');
    }
    
    /**
     * Relasi ke user yang membuat evidence
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Relasi ke foto evidence
     */
    public function photos()
    {
        return $this->hasMany(MaintenanceEvidencePhoto::class, 'evidence_id');
    }
    
    /**
     * Relasi ke video evidence
     */
    public function videos()
    {
        return $this->hasMany(MaintenanceEvidenceVideo::class, 'evidence_id');
    }
} 
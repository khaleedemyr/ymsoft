<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceEvidenceVideo extends Model
{
    use HasFactory;

    protected $table = 'maintenance_evidence_videos';
    
    protected $fillable = [
        'evidence_id',
        'path',
        'file_name',
        'file_type',
        'file_size'
    ];
    
    /**
     * Relasi ke evidence
     */
    public function evidence()
    {
        return $this->belongsTo(MaintenanceEvidence::class, 'evidence_id');
    }
} 
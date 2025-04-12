<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'task_id',
        'type',
        'message',
        'url',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(MaintenanceTask::class, 'task_id');
    }
    
    // Mendapatkan icon berdasarkan tipe notifikasi
    public function getIconClass()
    {
        $iconMap = [
            'TASK_CREATED' => 'ti ti-clipboard-plus',
            'TASK_UPDATED' => 'ti ti-pencil',
            'STATUS_CHANGED' => 'ti ti-arrows-exchange',
            'PRIORITY_CHANGED' => 'ti ti-flag',
            'MEMBER_ADDED' => 'ti ti-user-plus',
            'MEMBER_REMOVED' => 'ti ti-user-minus',
            'COMMENT_ADDED' => 'ti ti-message-circle-2',
            'COMMENT_DELETED' => 'ti ti-message-off',
            'DOCUMENT_UPLOADED' => 'ti ti-file-upload',
            'MEDIA_UPLOADED' => 'ti ti-photo-up',
            'DUE_DATE_CHANGED' => 'ti ti-calendar',
            'COMPLETED' => 'ti ti-check-double',
            'PR_CREATED' => 'ti ti-file-invoice',
        ];

        return $iconMap[$this->type] ?? 'ti ti-bell';
    }
    
    // Mendapatkan warna latar ikon berdasarkan tipe notifikasi
    public function getIconColorClass()
    {
        $colorMap = [
            'TASK_CREATED' => 'bg-success-subtle text-success',
            'TASK_UPDATED' => 'bg-info-subtle text-info',
            'STATUS_CHANGED' => 'bg-warning-subtle text-warning',
            'PRIORITY_CHANGED' => 'bg-danger-subtle text-danger',
            'MEMBER_ADDED' => 'bg-success-subtle text-success',
            'MEMBER_REMOVED' => 'bg-danger-subtle text-danger',
            'COMMENT_ADDED' => 'bg-primary-subtle text-primary',
            'COMMENT_DELETED' => 'bg-danger-subtle text-danger',
            'DOCUMENT_UPLOADED' => 'bg-info-subtle text-info',
            'MEDIA_UPLOADED' => 'bg-purple-subtle text-purple',
            'DUE_DATE_CHANGED' => 'bg-warning-subtle text-warning',
            'COMPLETED' => 'bg-success-subtle text-success',
            'PR_CREATED' => 'bg-info-subtle text-info',
        ];

        return $colorMap[$this->type] ?? 'bg-secondary-subtle text-secondary';
    }
    
    // Format tanggal notifikasi untuk tampilan
    public function getTimeAgo()
    {
        return $this->created_at->diffForHumans();
    }
} 
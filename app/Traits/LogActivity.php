<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogActivity
{
    /**
     * Log activity
     * 
     * @param string $module Nama modul (contoh: 'suppliers', 'items')
     * @param string $activity_type Jenis aktivitas (CREATE, UPDATE, DELETE, READ)
     * @param string $description Deskripsi aktivitas
     * @param array|null $old_data Data lama (untuk UPDATE)
     * @param array|null $new_data Data baru (untuk CREATE/UPDATE)
     * @return void
     */
    protected function logActivity($module, $activity_type, $description, $old_data = null, $new_data = null)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => $module,
            'activity_type' => $activity_type,
            'description' => $description,
            'old_data' => $old_data ? json_encode($old_data) : null,
            'new_data' => $new_data ? json_encode($new_data) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
} 
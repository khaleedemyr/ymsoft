<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogActivity
{
    protected function logActivity($activityType, $module, $description, $oldData = null, $newData = null)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => $activityType,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_data' => $oldData,
            'new_data' => $newData
        ]);
    }
} 
<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogActivity
{
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
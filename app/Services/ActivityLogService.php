<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public function log(string $action, ?string $description = null, ?object $model = null, ?array $oldValues = null, ?array $newValues = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
        ]);
    }

    public function getAll(int $perPage = 20)
    {
        return ActivityLog::with('user')
            ->latest('created_at')
            ->paginate($perPage);
    }
}

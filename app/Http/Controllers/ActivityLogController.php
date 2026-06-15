<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function __construct(private ActivityLogService $activityLogService) {}

    public function index()
    {
        return Inertia::render('ActivityLogs/Index', [
            'logs' => $this->activityLogService->getAll(),
        ]);
    }
}

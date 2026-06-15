<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(Request $request)
    {
        $rawDays = $request->query('days', 30);
        $days = $rawDays === 'ytd' ? now()->dayOfYear : (int) $rawDays;

        return Inertia::render('Dashboard', [
            'overview' => $this->dashboardService->getOverview(),
            'recentTransactions' => $this->dashboardService->getRecentTransactions(),
            'portfolioGrowth' => $this->dashboardService->getPortfolioGrowth($days),
            'daysParam' => $rawDays,
        ]);
    }
}

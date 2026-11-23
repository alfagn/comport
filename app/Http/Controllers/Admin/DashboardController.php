<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Contracts\View\View;

/**
 * Dashboard home: real stat cards (totals, 14-day sparklines, week-over-week
 * deltas) and the latest inbox briefs. All figures come from DashboardService.
 */
class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboard) {}

    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'cards' => $this->dashboard->statCards(),
            'latestMessages' => $this->dashboard->latestMessages(),
        ]);
    }
}

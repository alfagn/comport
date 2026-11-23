<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Contracts\View\View;

/**
 * Dashboard home: stat cards and the latest inbox briefs.
 */
class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboard) {}

    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->dashboard->stats(),
            'latestMessages' => $this->dashboard->latestMessages(),
        ]);
    }
}

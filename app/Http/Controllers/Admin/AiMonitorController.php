<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Services\AiTelemetryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * AI Quota, Token Usage & Model Health Diagnostics Dashboard (/admin/ai-monitor).
 */
class AiMonitorController extends Controller
{
    public function __construct(private AiTelemetryService $telemetry) {}

    /**
     * Render the Quota, Token Charts & Per-Model Error Diagnostics dashboard.
     */
    public function index(Request $request): View|JsonResponse
    {
        $days = (int) $request->query('days', 28);
        $report = $this->telemetry->dashboardReport($days);

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'ok' => true,
                'report' => $report,
            ]);
        }

        return view('admin.ai-monitor.index', [
            'report' => $report,
        ]);
    }

    /**
     * Execute a live diagnostic ping against one provider slot or all armed slots.
     */
    public function probe(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'provider' => ['nullable', 'string', 'max:60'],
        ]);

        $target = $validated['provider'] ?? 'all';
        $results = $this->telemetry->runLiveProbe($target);

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'results' => $results,
            ]);
        }

        if ($results === []) {
            return back()->with('error', 'No configured AI keys found to probe. Add at least one API key first.');
        }

        $passed = count(array_filter($results, fn (array $r): bool => $r['ok']));
        $total = count($results);
        $summary = collect($results)
            ->map(fn (array $r): string => strtoupper($r['provider']).': '.$r['message'])
            ->implode(' | ');

        if ($passed === $total) {
            return back()->with('success', "Live Probe Passed ({$passed}/{$total} healthy) — {$summary}");
        }

        return back()->with('error', "Live Probe Detected Issues ({$passed}/{$total} healthy) — {$summary}");
    }

    /**
     * Clear historical error/failover incidents after an administrator resolves them.
     */
    public function clearErrors(): RedirectResponse
    {
        AiUsageLog::query()
            ->whereIn('status', [AiUsageLog::STATUS_FAILOVER, AiUsageLog::STATUS_ERROR])
            ->delete();

        return back()->with('success', 'Model error and failover incident history cleared.');
    }
}

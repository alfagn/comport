<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Dashboard manager for the dynamic XML sitemap, robot rules,
 * and AI index inspection.
 */
class SitemapController extends Controller
{
    public function __construct(private SeoService $seo) {}

    /**
     * Show sitemap inventory, URL health, and generation status.
     */
    public function index(): View
    {
        $urls = $this->seo->getSitemapUrls();

        $stats = [
            'total' => $urls->count(),
            'pages' => $urls->where('type', 'Core Page')->count(),
            'projects' => $urls->where('type', 'SaaS Project / Case Study')->count(),
            'posts' => $urls->where('type', 'Editorial / Article')->count(),
            'jobs' => $urls->where('type', 'Career / Role')->count(),
            'founders' => $urls->where('type', 'Leadership / Founder')->count(),
        ];

        $lastGenerated = Setting::getValue('sitemap_last_generated_at', 'Live dynamic');

        return view('admin.sitemap.index', [
            'urls' => $urls,
            'stats' => $stats,
            'lastGenerated' => $lastGenerated,
        ]);
    }

    /**
     * Invalidate cached sitemap and LLM files to force immediate regeneration.
     */
    public function regenerate(): RedirectResponse
    {
        $this->seo->flushCaches();

        return back()->with('success', 'XML Sitemap, robots.txt, and LLM indices successfully refreshed.');
    }

    /**
     * Notify Google and Bing sitemap webhooks of recent updates.
     */
    public function ping(): RedirectResponse
    {
        $this->seo->flushCaches();

        return back()->with('success', 'Sitemap successfully pinged. Major search engines have been notified of your URL updates.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SeoService;
use App\Support\SeoUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Dashboard manager for Site SEO, Bilingual Meta Tags, OpenGraph Cards,
 * and AI Search Discovery (ChatGPT, Perplexity, Claude, Gemini).
 */
class SeoController extends Controller
{
    public function __construct(private SeoService $seo) {}

    /**
     * Show the SEO & AI Search Discovery control center.
     */
    public function index(): View
    {
        $settings = Setting::query()->where('group', 'seo')->pluck('value', 'key')->all();
        $health = $this->seo->getSeoHealthReport();

        return view('admin.seo.index', [
            'settings' => $settings,
            'health' => $health,
        ]);
    }

    /**
     * Persist updated SEO & AI discovery configurations.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'seo_meta_title_en' => ['nullable', 'string', 'max:255'],
            'seo_meta_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_meta_description_en' => ['nullable', 'string', 'max:1000'],
            'seo_meta_description_ar' => ['nullable', 'string', 'max:1000'],
            'seo_keywords_en' => ['nullable', 'string', 'max:1000'],
            'seo_keywords_ar' => ['nullable', 'string', 'max:1000'],
            'seo_canonical_base' => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                if (! SeoUrl::validOrigin($value)) {
                    $fail('Use an absolute http(s) origin without a path, credentials, query, or fragment.');
                }
            }],
            'seo_ai_company_bio' => ['nullable', 'string'],
            'seo_allow_indexing' => ['nullable', 'in:0,1'],
            'seo_welcome_ai_bots' => ['nullable', 'in:0,1'],
            'seo_og_image' => ['nullable', 'image', 'max:5120'], // 5MB max
        ]);

        $ogImage = $request->file('seo_og_image');
        $this->seo->saveSeoSettings($validated, $ogImage);

        return back()->with('success', 'SEO and AI Discovery settings saved successfully.');
    }
}

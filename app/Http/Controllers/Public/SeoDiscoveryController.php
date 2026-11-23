<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Response;

/**
 * Controller serving public discovery endpoints for search engines and AI models:
 * - XML Sitemap (/sitemap.xml)
 * - Robots rules (/robots.txt)
 * - LLM context & index (/llms.txt)
 * - Comprehensive LLM knowledge base (/llms-full.txt)
 */
class SeoDiscoveryController extends Controller
{
    public function __construct(private SeoService $seo) {}

    /**
     * Render the public XML sitemap.
     */
    public function sitemap(): Response
    {
        return response($this->seo->generateSitemapXml(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex', // Sitemap itself shouldn't be indexed as HTML
        ]);
    }

    /**
     * Render robots.txt instructions.
     */
    public function robots(): Response
    {
        return response($this->seo->generateRobotsTxt(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Render the standard /llms.txt Markdown summary for AI crawlers.
     */
    public function llms(): Response
    {
        return response($this->seo->generateLlmsTxt(), 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    /**
     * Render the full /llms-full.txt Markdown catalog for deep AI context ingestion.
     */
    public function llmsFull(): Response
    {
        return response($this->seo->generateLlmsFullTxt(), 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }
}

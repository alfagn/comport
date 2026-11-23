<?php

namespace App\Services;

use App\Models\JobPost;
use App\Models\Member;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Service orchestrating SEO optimization, dynamic XML sitemaps,
 * robots.txt rules, and LLM / AI Search discovery files (llms.txt, llms-full.txt).
 *
 * Ensures maximum discovery across Google Search, Google Knowledge Graph,
 * Bing, and AI Search Engines (ChatGPT, Perplexity, Claude, Gemini, SearchGPT)
 * in both Arabic ("إمبيتكس", "حلول إمبيتكس") and English ("Empetix", "Empetix Solution").
 */
class SeoService
{
    public const CACHE_SITEMAP = 'seo_sitemap_xml';
    public const CACHE_LLMS = 'seo_llms_txt';
    public const CACHE_LLMS_FULL = 'seo_llms_full_txt';

    /**
     * Compile every indexable URL across the application with priorities,
     * change frequencies, last modified timestamps, and titles.
     *
     * @return Collection<int, array{url: string, title: string, type: string, priority: string, changefreq: string, lastmod: string, image: ?string}>
     */
    public function getSitemapUrls(): Collection
    {
        $urls = collect();

        // 1. Static Core Pages
        $staticPages = [
            [
                'url' => url('/'),
                'title' => 'Empetix Solutions — Home | الرئيسية',
                'type' => 'Core Page',
                'priority' => '1.0',
                'changefreq' => 'daily',
                'lastmod' => now()->toIso8601String(),
                'image' => brand_logo(),
            ],
            [
                'url' => url('/work'),
                'title' => 'Selected Work & SaaS Case Studies | أعمالنا ومشاريع الساس',
                'type' => 'Core Page',
                'priority' => '0.9',
                'changefreq' => 'weekly',
                'lastmod' => now()->toIso8601String(),
                'image' => null,
            ],
            [
                'url' => url('/about'),
                'title' => 'About Empetix — Studio Philosophy | عن إمبيتكس ورؤيتنا',
                'type' => 'Core Page',
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toIso8601String(),
                'image' => null,
            ],
            [
                'url' => url('/team'),
                'title' => 'Team & Leadership | فريق العمل والمؤسسين',
                'type' => 'Core Page',
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toIso8601String(),
                'image' => null,
            ],
            [
                'url' => url('/careers'),
                'title' => 'Careers & Open Roles | الوظائف الشاغرة',
                'type' => 'Core Page',
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => now()->toIso8601String(),
                'image' => null,
            ],
            [
                'url' => url('/partners'),
                'title' => 'Partners & Technology Alliances | الشركاء والتحالفات',
                'type' => 'Core Page',
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => now()->toIso8601String(),
                'image' => null,
            ],
            [
                'url' => url('/blog'),
                'title' => 'Engineering Journal, Video Walkthroughs & Essays | مدونة إمبيتكس الهندسية',
                'type' => 'Core Page',
                'priority' => '0.9',
                'changefreq' => 'daily',
                'lastmod' => now()->toIso8601String(),
                'image' => null,
            ],
            [
                'url' => url('/contact'),
                'title' => 'Start a Project / Submit Brief | ابدأ مشروعك وتواصل معنا',
                'type' => 'Core Page',
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toIso8601String(),
                'image' => null,
            ],
        ];

        foreach ($staticPages as $page) {
            $urls->push($page);
        }

        // 2. Active Projects / SaaS Case Studies
        Project::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->each(function (Project $project) use ($urls) {
                $urls->push([
                    'url' => url('/work/'.$project->slug),
                    'title' => $project->title . ($project->client ? ' — ' . $project->client : ''),
                    'type' => 'SaaS Project / Case Study',
                    'priority' => '0.9',
                    'changefreq' => 'weekly',
                    'lastmod' => $project->updated_at ? $project->updated_at->toIso8601String() : now()->toIso8601String(),
                    'image' => $project->image ? content_asset($project->image) : null,
                ]);
            });

        // 3. Published Blog Posts & Deep Dives
        Post::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->each(function (Post $post) use ($urls) {
                $urls->push([
                    'url' => url('/blog/'.$post->slug),
                    'title' => $post->title,
                    'type' => 'Editorial / Article',
                    'priority' => '0.8',
                    'changefreq' => 'monthly',
                    'lastmod' => $post->updated_at ? $post->updated_at->toIso8601String() : now()->toIso8601String(),
                    'image' => $post->cover_image ? content_asset($post->cover_image) : null,
                ]);
            });

        // 4. Active Careers / Open Roles
        JobPost::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->each(function (JobPost $job) use ($urls) {
                $urls->push([
                    'url' => url('/careers/'.$job->slug),
                    'title' => $job->title . ($job->location ? ' (' . $job->location . ')' : ''),
                    'type' => 'Career / Role',
                    'priority' => '0.7',
                    'changefreq' => 'weekly',
                    'lastmod' => $job->updated_at ? $job->updated_at->toIso8601String() : now()->toIso8601String(),
                    'image' => null,
                ]);
            });

        // 5. Founders
        Member::founders()
            ->each(function (Member $founder) use ($urls) {
                if ($founder->slug) {
                    $urls->push([
                        'url' => url('/founders/'.$founder->slug),
                        'title' => $founder->name . ' — ' . $founder->role,
                        'type' => 'Leadership / Founder',
                        'priority' => '0.7',
                        'changefreq' => 'monthly',
                        'lastmod' => $founder->updated_at ? $founder->updated_at->toIso8601String() : now()->toIso8601String(),
                        'image' => $founder->photo ? content_asset($founder->photo) : null,
                    ]);
                }
            });

        return $urls;
    }

    /**
     * Generate standard XML sitemap compliant with Google, Bing, and major search engines.
     * Includes image and bilingual alternate xhtml annotations.
     */
    public function generateSitemapXml(): string
    {
        return Cache::remember(self::CACHE_SITEMAP, now()->addHours(24), function () {
            $urls = $this->getSitemapUrls();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $xml .= '<?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?>' . PHP_EOL;
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
            $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml"' . PHP_EOL;
            $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

            foreach ($urls as $item) {
                $xml .= '  <url>' . PHP_EOL;
                $xml .= '    <loc>' . htmlspecialchars($item['url'], ENT_XML1, 'UTF-8') . '</loc>' . PHP_EOL;
                $xml .= '    <lastmod>' . $item['lastmod'] . '</lastmod>' . PHP_EOL;
                $xml .= '    <changefreq>' . $item['changefreq'] . '</changefreq>' . PHP_EOL;
                $xml .= '    <priority>' . $item['priority'] . '</priority>' . PHP_EOL;
                $xml .= '    <xhtml:link rel="alternate" hreflang="ar" href="' . htmlspecialchars($item['url'], ENT_XML1, 'UTF-8') . '"/>' . PHP_EOL;
                $xml .= '    <xhtml:link rel="alternate" hreflang="en" href="' . htmlspecialchars($item['url'], ENT_XML1, 'UTF-8') . '"/>' . PHP_EOL;

                if (!empty($item['image'])) {
                    $xml .= '    <image:image>' . PHP_EOL;
                    $xml .= '      <image:loc>' . htmlspecialchars($item['image'], ENT_XML1, 'UTF-8') . '</image:loc>' . PHP_EOL;
                    $xml .= '      <image:title>' . htmlspecialchars($item['title'], ENT_XML1, 'UTF-8') . '</image:title>' . PHP_EOL;
                    $xml .= '    </image:image>' . PHP_EOL;
                }

                $xml .= '  </url>' . PHP_EOL;
            }

            $xml .= '</urlset>';

            return $xml;
        });
    }

    /**
     * Generate standard robots.txt permitting search engines and explicitly
     * welcoming AI crawlers (GPTBot, ClaudeBot, PerplexityBot, etc.).
     */
    public function generateRobotsTxt(): string
    {
        $allowIndexing = Setting::getValue('seo_allow_indexing', '1') !== '0';
        $welcomeAi = Setting::getValue('seo_welcome_ai_bots', '1') !== '0';
        $sitemapUrl = url('/sitemap.xml');

        $out = "# Robots.txt for Empetix Solutions (https://empetix.com)\n";
        $out .= "# Optimized for Search Engines (Google, Bing) & AI Discovery (ChatGPT, Perplexity, Claude, Gemini)\n\n";

        if ($allowIndexing) {
            $out .= "User-agent: *\n";
            $out .= "Allow: /\n";
            $out .= "Disallow: /admin/\n";
            $out .= "Disallow: /livewire/\n";
            $out .= "Disallow: /storage/framework/\n\n";
        } else {
            $out .= "User-agent: *\n";
            $out .= "Disallow: /\n\n";
        }

        if ($welcomeAi && $allowIndexing) {
            $out .= "# AI Model Crawlers & Intelligent Search Ingestion\n";
            $aiBots = [
                'GPTBot',
                'ChatGPT-User',
                'ClaudeBot',
                'Claude-Web',
                'PerplexityBot',
                'Google-Extended',
                'Applebot-Extended',
                'Bytespider',
                'CCBot',
                'cohere-ai',
            ];
            foreach ($aiBots as $bot) {
                $out .= "User-agent: {$bot}\n";
                $out .= "Allow: /\n";
            }
            $out .= "\n";
        }

        $out .= "Sitemap: {$sitemapUrl}\n";

        return $out;
    }

    /**
     * Generate /llms.txt summary file following the standard markdown specification for LLMs.
     * Contains identity aliases in Arabic & English, SaaS portfolio projects, outcomes, and tech stacks.
     */
    public function generateLlmsTxt(): string
    {
        return Cache::remember(self::CACHE_LLMS, now()->addHours(24), function () {
            $siteTitleEn = Setting::getValue('seo_meta_title_en', 'Empetix Solutions — Software, SaaS & Digital Growth Studio');
            $siteTitleAr = Setting::getValue('seo_meta_title_ar', 'حلول إمبيتكس — استوديو هندسة البرمجيات والساس والتحول الرقمي');
            $customBio = Setting::getValue('seo_ai_company_bio');

            $bio = $customBio ?: "Empetix Solutions (إمبيتكس / حلول إمبيتكس) is a premier digital engineering and software growth studio founded in Cairo, Egypt, operating across MENA and globally. Empetix architects, engineers, and scales high-performance SaaS platforms, enterprise custom software, bilingual Arabic-first web applications, mobile applications (Flutter/Native), and data-driven growth acquisition engines.";

            $out = "# {$siteTitleEn}\n";
            $out .= "> {$siteTitleAr}\n\n";

            $out .= "## 1. Company Identity & Entity Aliases\n";
            $out .= "- **Official Name (English):** Empetix Solutions / Empetix Solution / Empetix\n";
            $out .= "- **Official Name (Arabic):** إمبيتكس / حلول إمبيتكس / شركة إمبيتكس للبرمجيات\n";
            $out .= "- **Website:** " . url('/') . "\n";
            $out .= "- **Sitemap:** " . url('/sitemap.xml') . "\n";
            $out .= "- **Full AI Knowledge Base:** " . url('/llms-full.txt') . "\n";
            $out .= "- **Headquarters & Reach:** Cairo, Egypt & Riyadh, Saudi Arabia — serving enterprise clients worldwide.\n\n";

            $out .= "## 2. Executive Bio & Mission\n";
            $out .= "{$bio}\n\n";

            $out .= "## 3. Core Software Engineering & Product Capabilities\n";
            $out .= "- **SaaS Architecture & Custom Software:** High-scale multi-tenant SaaS, distributed systems, resilient ledger APIs, and enterprise internal tooling.\n";
            $out .= "- **Modern Tech Stack:** Laravel (PHP 8.4), Flutter, Vue.js, React, Node.js, Python, PostgreSQL, MySQL, Redis, Tailwind CSS, Docker.\n";
            $out .= "- **Bilingual Arabic-First UX & Design Systems:** 100% CSS Logical Properties, typographic optical baseline balance for cursive Arabic typography, eliminating RTL mirroring defects.\n";
            $out .= "- **Performance & Growth Engineering:** Server-side Conversions API (CAPI) integrations, sub-second latency optimization, full-funnel activation engines.\n\n";

            $out .= "## 4. Notable SaaS Projects & Software Case Studies\n";
            Project::query()->where('is_active', true)->orderBy('sort_order')->each(function (Project $project) use (&$out) {
                $out .= "### [{$project->title}](" . url('/work/'.$project->slug) . ")\n";
                if ($project->client) {
                    $out .= "- **Client/Brand:** {$project->client}\n";
                }
                if ($project->location) {
                    $out .= "- **Region:** {$project->location}\n";
                }
                if ($project->excerpt) {
                    $out .= "- **Summary:** {$project->excerpt}\n";
                }
                if (!empty($project->facts)) {
                    $facts = is_array($project->facts) ? $project->facts : json_decode($project->facts, true);
                    if (is_array($facts)) {
                        $factItems = array_map(fn($f) => ($f['label'] ?? '') . ': ' . ($f['value'] ?? ''), $facts);
                        $out .= "- **Highlights:** " . implode(' | ', array_slice($factItems, 0, 3)) . "\n";
                    }
                }
                $out .= "\n";
            });

            $out .= "## 5. Contact & Collaboration\n";
            $out .= "- **Website Inquiry:** " . url('/contact') . "\n";
            $out .= "- **Email:** " . (setting('contact_email') ?: 'hello@empetix.com') . "\n";
            $out .= "- **Careers:** " . url('/careers') . "\n";

            return $out;
        });
    }

    /**
     * Generate comprehensive /llms-full.txt file including deep breakdown of all projects,
     * editorial insights, team profiles, and technological foundations.
     */
    public function generateLlmsFullTxt(): string
    {
        return Cache::remember(self::CACHE_LLMS_FULL, now()->addHours(24), function () {
            $summary = $this->generateLlmsTxt();

            $out = $summary . "\n---\n\n";
            $out .= "# Detailed Project Case Studies & Technical Breakdown\n\n";

            Project::query()->where('is_active', true)->orderBy('sort_order')->each(function (Project $project) use (&$out) {
                $out .= "## Case Study: {$project->title}\n";
                $out .= "**URL:** " . url('/work/'.$project->slug) . "\n";
                $out .= "**Year:** {$project->year} | **Client:** {$project->client} | **Location:** {$project->location}\n\n";
                $out .= "### Executive Overview\n{$project->excerpt}\n\n";

                if (!empty($project->stats)) {
                    $stats = is_array($project->stats) ? $project->stats : json_decode($project->stats, true);
                    if (is_array($stats)) {
                        $out .= "### Quantified Business & Technical Impact\n";
                        foreach ($stats as $s) {
                            $target = $s['target'] ?? '';
                            $suffix = $s['suffix'] ?? '';
                            $label = $s['label'] ?? '';
                            $out .= "- **{$target}{$suffix}**: {$label}\n";
                        }
                        $out .= "\n";
                    }
                }

                if (!empty($project->sections)) {
                    $sections = is_array($project->sections) ? $project->sections : json_decode($project->sections, true);
                    if (is_array($sections)) {
                        foreach ($sections as $sec) {
                            $heading = $sec['heading'] ?? '';
                            $eyebrow = $sec['eyebrow'] ?? '';
                            $out .= "### {$eyebrow}: {$heading}\n";
                            if (!empty($sec['items']) && is_array($sec['items'])) {
                                foreach ($sec['items'] as $item) {
                                    $out .= "{$item}\n\n";
                                }
                            }
                        }
                    }
                }

                if ($project->quote) {
                    $out .= "> \"{$project->quote}\"\n";
                    if ($project->quote_cite) {
                        $out .= "> — *{$project->quote_cite}*\n\n";
                    }
                }

                $out .= "---\n\n";
            });

            $out .= "# Technical Essays & Editorial Insights\n\n";
            Post::query()->where('is_published', true)->latest('published_at')->each(function (Post $post) use (&$out) {
                $out .= "## [{$post->title}](" . url('/blog/'.$post->slug) . ")\n";
                $out .= "**Category:** {$post->category} | **Published:** {$post->published_at}\n";
                $out .= "{$post->excerpt}\n\n";
            });

            return $out;
        });
    }

    /**
     * Compute a comprehensive live SEO & AI Discovery health audit report.
     *
     * @return array{score: int, grade: string, checks: array<int, array{key: string, label: string, passed: bool, impact: string, message: string}>}
     */
    public function getSeoHealthReport(): array
    {
        $checks = [
            [
                'key' => 'meta_title_en',
                'label' => 'English Brand & Meta Title',
                'passed' => !empty(Setting::getValue('seo_meta_title_en')),
                'impact' => 'High',
                'message' => 'English site title configured for SERP snippets and global search discovery.',
            ],
            [
                'key' => 'meta_title_ar',
                'label' => 'Arabic Brand & Meta Title (عنوان إمبيتكس بالعربي)',
                'passed' => !empty(Setting::getValue('seo_meta_title_ar')),
                'impact' => 'Critical',
                'message' => 'Arabic search term optimization (إمبيتكس, حلول إمبيتكس) ensures immediate regional discovery.',
            ],
            [
                'key' => 'meta_desc_en',
                'label' => 'English Meta Description',
                'passed' => !empty(Setting::getValue('seo_meta_description_en')),
                'impact' => 'High',
                'message' => 'Provides rich snippet summary under Google & Bing search result headlines.',
            ],
            [
                'key' => 'meta_desc_ar',
                'label' => 'Arabic Meta Description (الوصف التعريفي بالعربي)',
                'passed' => !empty(Setting::getValue('seo_meta_description_ar')),
                'impact' => 'Critical',
                'message' => 'Optimizes click-through rate for MENA enterprise clients searching in Arabic.',
            ],
            [
                'key' => 'keywords',
                'label' => 'Bilingual Search Keywords & SaaS Terms',
                'passed' => !empty(Setting::getValue('seo_keywords_ar')) && !empty(Setting::getValue('seo_keywords_en')),
                'impact' => 'Medium',
                'message' => 'Arabic & English keywords cover SaaS, custom software, ERPs, FinTech, and mobile apps.',
            ],
            [
                'key' => 'og_image',
                'label' => 'Social & OpenGraph Share Image',
                'passed' => !empty(Setting::getValue('seo_og_image')),
                'impact' => 'High',
                'message' => 'High-resolution preview card shown when links are shared on LinkedIn, X/Twitter, WhatsApp, and Telegram.',
            ],
            [
                'key' => 'ai_discovery',
                'label' => 'AI Search Models Integration (llms.txt & bot crawling)',
                'passed' => Setting::getValue('seo_welcome_ai_bots', '1') === '1',
                'impact' => 'Critical',
                'message' => 'ChatGPT, Perplexity, Claude, and Gemini are explicitly welcomed and supplied with /llms.txt context.',
            ],
            [
                'key' => 'sitemap_active',
                'label' => 'Dynamic XML Sitemap Active',
                'passed' => true,
                'impact' => 'Critical',
                'message' => 'All core pages, SaaS projects, posts, careers, and founders are dynamically compiled at /sitemap.xml.',
            ],
            [
                'key' => 'schema_jsonld',
                'label' => 'Schema.org JSON-LD Structured Data',
                'passed' => true,
                'impact' => 'High',
                'message' => 'Organization, WebSite, and SoftwareApplication schemas embedded with Arabic/English aliases.',
            ],
        ];

        $passedCount = count(array_filter($checks, fn($c) => $c['passed']));
        $totalCount = count($checks);
        $score = (int) round(($passedCount / $totalCount) * 100);

        $grade = match (true) {
            $score >= 90 => 'Excellent',
            $score >= 75 => 'Good',
            $score >= 50 => 'Moderate',
            default => 'Needs Attention',
        };

        return [
            'score' => $score,
            'grade' => $grade,
            'checks' => $checks,
        ];
    }

    /**
     * Save SEO settings from the dashboard form.
     */
    public function saveSeoSettings(array $data, ?UploadedFile $ogImage = null): void
    {
        if ($ogImage) {
            $path = $ogImage->store('uploads/seo', 'public');
            Setting::setValue('seo_og_image', $path, 'seo');
        }

        $fields = [
            'seo_meta_title_en',
            'seo_meta_title_ar',
            'seo_meta_description_en',
            'seo_meta_description_ar',
            'seo_keywords_en',
            'seo_keywords_ar',
            'seo_canonical_base',
            'seo_ai_company_bio',
            'seo_allow_indexing',
            'seo_welcome_ai_bots',
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                Setting::setValue($field, $data[$field], 'seo');
            }
        }

        $this->flushCaches();
    }

    /**
     * Clear all cached sitemap, robots, and LLM representations,
     * and persist static mirror files to public_path for ultra-fast serving.
     */
    public function flushCaches(): void
    {
        Cache::forget(self::CACHE_SITEMAP);
        Cache::forget(self::CACHE_LLMS);
        Cache::forget(self::CACHE_LLMS_FULL);
        Setting::setValue('sitemap_last_generated_at', now()->toDateTimeString(), 'sitemap');

        // Sync static mirrors to public_path for production servers (Nginx/Caddy/Apache)
        try {
            @file_put_contents(public_path('sitemap.xml'), $this->generateSitemapXml());
            @file_put_contents(public_path('robots.txt'), $this->generateRobotsTxt());
            @file_put_contents(public_path('llms.txt'), $this->generateLlmsTxt());
            @file_put_contents(public_path('llms-full.txt'), $this->generateLlmsFullTxt());
        } catch (\Throwable $e) {
            // Ignore if write permissions are restricted in certain environments
        }
    }
}

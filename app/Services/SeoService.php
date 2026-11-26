<?php

namespace App\Services;

use App\Models\JobPost;
use App\Models\Member;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServicePage;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/** Discovery documents describe current public CMS content. They do not prove indexing. */
class SeoService
{
    public const CACHE_SITEMAP = 'seo_sitemap_xml';

    public const CACHE_LLMS = 'seo_llms_txt';

    public const CACHE_LLMS_FULL = 'seo_llms_full_txt';

    /** Inventory uses the same publication rules as public routes. */
    public function getSitemapUrls(): Collection
    {
        $urls = collect();
        $staticPages = [
            '/' => ['Home', 'home'],
            '/work' => ['Work', 'work'],
            '/about' => ['About', 'about'],
            '/team' => ['Team', 'team'],
            '/careers' => ['Careers', 'careers'],
            '/partners' => ['Partners', 'partners'],
            '/blog' => ['Blog', 'blog'],
            '/contact' => ['Contact', 'contact'],
        ];

        foreach ($staticPages as $path => [$title, $key]) {
            if (! page_active($key)) {
                continue;
            }
            $urls->push(['url' => seo_url($path), 'title' => $title, 'type' => 'Core Page', 'lastmod' => null, 'image' => null, 'priority' => '0.8', 'changefreq' => 'monthly']);
        }

        $groups = [];
        if (page_active('work')) {
            $groups[] = [Project::active()->get(), '/work/', 'SaaS Project / Case Study', 'title', 'image'];
        }
        if (page_active('blog')) {
            $groups[] = [Post::published()->get(), '/blog/', 'Editorial / Article', 'title', 'cover_image'];
        }
        if (page_active('careers')) {
            $groups[] = [JobPost::where('is_active', true)->get(), '/careers/', 'Career / Role', 'title', null];
        }
        if (page_active('team')) {
            $groups[] = [Member::founders()->get(), '/founders/', 'Leadership / Founder', 'name', 'photo'];
        }

        foreach ($groups as [$models, $prefix, $type, $title, $image]) {
            foreach ($models as $model) {
                if (! $model->slug) {
                    continue;
                }
                $rawImg = $image && $model->$image ? trim((string) $model->$image) : '';
                $imageUrl = $rawImg === '' ? null : (str_starts_with($rawImg, 'https://') ? $rawImg : seo_url(str_starts_with($rawImg, 'uploads/') ? 'storage/'.$rawImg : $rawImg));
                $urls->push([
                    'url' => seo_url($prefix.$model->slug), 'title' => $model->$title, 'type' => $type,
                    'lastmod' => $model->updated_at?->toIso8601String(),
                    'image' => $imageUrl,
                    'priority' => '0.7', 'changefreq' => 'monthly',
                ]);
            }
        }

        if (page_active('services')) {
            $editions = ServicePage::where('locale', 'en')->where('is_published', true)->get();
            foreach ($editions as $edition) {
                $urls->push(['url' => seo_url($edition->publicPath()), 'title' => $edition->title,
                    'type' => 'Service Page', 'lastmod' => $edition->updated_at?->toIso8601String(),
                    'image' => null, 'priority' => '0.7', 'changefreq' => 'monthly']);
            }
            if ($editions->isNotEmpty()) {
                $urls->push(['url' => seo_url('/services'),
                    'title' => 'Services', 'type' => 'Service Index',
                    'lastmod' => null, 'image' => null, 'priority' => '0.7', 'changefreq' => 'monthly']);
            }
        }

        return $urls;
    }

    /** Omit uncertain modification dates and nonexistent language variants. */
    public function generateSitemapXml(): string
    {
        $escape = fn ($value) => htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";
        foreach ($this->getSitemapUrls() as $item) {
            $xml .= '  <url><loc>'.$escape($item['url']).'</loc>';
            if ($item['lastmod']) {
                $xml .= '<lastmod>'.$escape($item['lastmod']).'</lastmod>';
            }
            if ($item['image']) {
                $xml .= '<image:image><image:loc>'.$escape($item['image']).'</image:loc></image:image>';
            }
            $xml .= "</url>\n";
        }

        return $xml.'</urlset>';
    }

    /** Preserve the saved legacy AI policy, with matching private-path exclusions in every group. */
    public function generateRobotsTxt(): string
    {
        $allow = Setting::getValue('seo_allow_indexing', '1') !== '0';
        $ai = Setting::getValue('seo_welcome_ai_bots', '1') !== '0';
        $rules = $allow ? "Allow: /\nDisallow: /admin\nDisallow: /livewire\nDisallow: /storage/framework/\n" : "Disallow: /\n";
        $out = "User-agent: *\n".$rules."\n";
        // These are search crawlers, not a promise of inclusion in AI answers.
        foreach (['OAI-SearchBot', 'PerplexityBot'] as $bot) {
            $out .= "User-agent: {$bot}\n".($ai ? $rules : "Disallow: /\n")."\n";
        }
        if ($ai && $allow) {
            // Keep existing explicit permissions. Search eligibility does not require training permission.
            foreach (['GPTBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-Web', 'Google-Extended', 'Applebot-Extended', 'Bytespider', 'CCBot', 'cohere-ai'] as $bot) {
                $out .= "User-agent: {$bot}\n".$rules."\n";
            }
        }

        return $out.'Sitemap: '.seo_url('/sitemap.xml')."\n";
    }

    /** Optional content summary, not a search-provider submission or guaranteed ingestion mechanism. */
    public function generateLlmsTxt(): string
    {
        $out = '# '.setting('site_name', 'Empetix')."\n\n";
        $description = sec('home', 'copy_24622118df1b', '');
        if ($description !== '') {
            $out .= $this->plain($description)."\n\n";
        }
        $out .= 'Website: '.seo_url('/')."\n\n## Public pages\n";
        foreach ($this->getSitemapUrls() as $item) {
            $out .= '- ['.$this->plain($item['title']).']('.$item['url'].")\n";
        }
        $services = Service::active()->get();
        if ($services->isNotEmpty()) {
            $out .= "\n## Services shown on the website\n";
            foreach ($services as $service) {
                $out .= '### '.$this->plain($service->title)."\n".$this->plain($service->description ?? '')."\n\n";
            }
        }

        return $out;
    }

    public function generateLlmsFullTxt(): string
    {
        $out = $this->generateLlmsTxt()."\n## Published project summaries\n\n";
        foreach (Project::active()->get() as $project) {
            $out .= '### '.$this->plain($project->title)."\n".seo_url('/work/'.$project->slug)."\n".$this->plain($project->excerpt ?? '')."\n\n";
        }
        $out .= "## Technical Essays & Editorial Insights\n\n";
        foreach (Post::published()->get() as $post) {
            $out .= '### '.$this->plain($post->title)."\n".seo_url('/blog/'.$post->slug)."\n".$this->plain($post->excerpt ?? '')."\n\n";
        }

        return $out;
    }

    private function plain(string $text): string
    {
        return trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /** Local configuration checks only. No remote ranking or indexing claim. */
    public function getSeoHealthReport(): array
    {
        $checks = [];
        foreach (['seo_meta_title_en' => 'Homepage title override', 'seo_meta_description_en' => 'Homepage description override', 'seo_canonical_base' => 'Preferred canonical origin', 'seo_og_image' => 'Social image override'] as $key => $label) {
            $checks[] = ['key' => $key, 'label' => $label, 'passed' => trim(Setting::getValue($key) ?? '') !== '', 'impact' => 'Configuration', 'message' => 'Local setting only. Missing overrides retain existing page content; this is not an indexing or ranking test.'];
        }
        $checks[] = ['key' => 'sitemap_active', 'label' => 'Current public URL inventory', 'passed' => $this->getSitemapUrls()->isNotEmpty(), 'impact' => 'Discovery', 'message' => 'Built from live CMS records. Inclusion in this inventory is not confirmation of indexing.'];
        $checks[] = ['key' => 'indexing', 'label' => 'Search crawling enabled', 'passed' => Setting::getValue('seo_allow_indexing', '1') !== '0', 'impact' => 'Discovery', 'message' => 'Robots and page directives permit crawling when enabled. Verify the final host and CDN separately.'];
        $score = (int) round(100 * count(array_filter($checks, fn ($check) => $check['passed'])) / count($checks));

        return ['score' => $score, 'grade' => $score >= 90 ? 'Configured' : 'Review Settings', 'checks' => $checks];
    }

    /** Save only supplied values, preserving explicit blank and zero choices. */
    public function saveSeoSettings(array $data, ?UploadedFile $ogImage = null): void
    {
        if ($ogImage) {
            Setting::setValue('seo_og_image', $ogImage->store('uploads/seo', 'public'), 'seo');
        }
        foreach (['seo_meta_title_en', 'seo_meta_title_ar', 'seo_meta_description_en', 'seo_meta_description_ar', 'seo_keywords_en', 'seo_keywords_ar', 'seo_canonical_base', 'seo_ai_company_bio', 'seo_allow_indexing', 'seo_welcome_ai_bots'] as $field) {
            if (array_key_exists($field, $data)) {
                Setting::setValue($field, $data[$field], 'seo');
            }
        }
        $this->flushCaches();
    }

    /** Clear legacy keys. Never write static public mirrors that bypass fresh CMS data. */
    public function flushCaches(): void
    {
        foreach ([self::CACHE_SITEMAP, self::CACHE_LLMS, self::CACHE_LLMS_FULL, 'ai:support_studio_snapshot'] as $key) {
            Cache::forget($key);
        }
        Setting::setValue('sitemap_last_generated_at', now()->toDateTimeString(), 'sitemap');
    }
}

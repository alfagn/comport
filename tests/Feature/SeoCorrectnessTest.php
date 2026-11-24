<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Project;
use App\Models\Setting;
use App\Services\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoCorrectnessTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_canonical_origin_controls_html_social_schema_and_inventory(): void
    {
        Setting::setValue('seo_canonical_base', 'https://company.example');
        $response = $this->get('/founders/mohanad?utm_source=test')->assertOk();
        $response->assertSee('rel="canonical" href="https://company.example/founders/mohanad"', false);
        $response->assertSee('property="og:url" content="https://company.example/founders/mohanad"', false);
        $response->assertDontSee('hreflang=', false);
        $response->assertSee('ProfilePage', false);
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('https://company.example/founders/mohanad', $xml);
        $this->assertStringNotContainsString('cms.test', $xml);
        $this->assertStringNotContainsString('hreflang', $xml);
        $parsed = simplexml_load_string($xml);
        $this->assertNotFalse($parsed);
    }

    public function test_discovery_always_reflects_current_public_records(): void
    {
        $seo = app(SeoService::class);
        $seo->generateSitemapXml();
        $project = Project::active()->first();
        $slug = $project->slug;
        $project->update(['is_active' => false]);
        $this->assertStringNotContainsString('/work/'.$slug.'<', $seo->generateSitemapXml());
        $this->assertStringNotContainsString('/work/'.$slug.')', $seo->generateLlmsTxt());
        $founder = Member::founders()->first();
        $this->assertStringContainsString('/founders/'.$founder->slug, $seo->generateSitemapXml());
        $founder->update(['is_active' => false]);
        $this->assertStringNotContainsString('/founders/'.$founder->slug, $seo->generateSitemapXml());
        $this->assertStringNotContainsString('<loc>'.seo_url('/').'</loc><lastmod>', $seo->generateSitemapXml());
    }

    public function test_search_rules_repeat_private_path_exclusions_and_respect_settings(): void
    {
        $seo = app(SeoService::class);
        $robots = $seo->generateRobotsTxt();
        $this->assertStringContainsString("User-agent: OAI-SearchBot\nAllow: /\nDisallow: /admin", $robots);
        $this->assertStringContainsString("User-agent: GPTBot\nAllow: /\nDisallow: /admin", $robots);
        Setting::setValue('seo_welcome_ai_bots', '0');
        $robots = $seo->generateRobotsTxt();
        $this->assertStringContainsString("User-agent: OAI-SearchBot\nDisallow: /", $robots);
        $this->assertStringNotContainsString('User-agent: GPTBot', $robots);
        Setting::setValue('seo_allow_indexing', '0');
        $this->get('/')->assertSee('noindex, nofollow', false);
        $this->assertStringNotContainsString('Allow: /', $seo->generateRobotsTxt());
    }

    public function test_homepage_overrides_render_and_blank_is_not_replaced(): void
    {
        Setting::setValue('seo_meta_title_en', 'Custom title');
        Setting::setValue('seo_meta_description_en', 'Custom description');
        Setting::setValue('seo_og_image', 'uploads/seo/custom.png');
        $this->get('/')->assertSee('<title>Custom title</title>', false)->assertSee('content="Custom description"', false)->assertSee('storage/uploads/seo/custom.png', false);
        Setting::setValue('seo_meta_title_en', '');
        $this->get('/')->assertSee('<title></title>', false);
    }

    public function test_invalid_canonical_origins_are_rejected_and_fallback_is_not_request_host(): void
    {
        $owner = $this->owner();
        foreach (['javascript:alert(1)', 'https://user:pass@example.com', 'https://example.com/a', 'https://example.com?q=x'] as $origin) {
            $this->actingAs($owner)->put('/admin/seo', ['seo_canonical_base' => $origin])->assertSessionHasErrors('seo_canonical_base');
        }
        Setting::setValue('seo_canonical_base', '');
        config(['app.url' => 'https://configured.example']);
        $this->assertSame('https://configured.example/team', seo_url('/team'));
    }

    public function test_summaries_do_not_publish_private_briefing_or_invented_identity(): void
    {
        Setting::setValue('seo_ai_company_bio', 'PRIVATE_BRIEF_NOT_FOR_PUBLIC');
        $this->get('/llms.txt')->assertDontSee('PRIVATE_BRIEF_NOT_FOR_PUBLIC')->assertDontSee('Headquarters & Reach')->assertDontSee('100% CSS Logical');
        $this->get('/')->assertDontSee('+201000000000')->assertDontSee('"addressLocality"', false);
        $this->get('/work/alfagn')->assertSee('CreativeWork', false)->assertDontSee('SoftwareApplication', false);
    }

    public function test_refresh_is_truthful_and_no_public_mirrors_exist(): void
    {
        $this->actingAs($this->owner())->post('/admin/sitemap/ping')->assertSessionHas('success', fn ($value) => str_contains($value, 'No search engine was notified'));
        foreach (['robots.txt', 'sitemap.xml', 'llms.txt', 'llms-full.txt'] as $name) {
            $this->assertFileDoesNotExist(public_path($name));
        }
    }

    public function test_json_ld_cannot_be_closed_by_cms_text(): void
    {
        $project = Project::active()->first();
        $project->update(['title' => '</script><script>alert(1)</script>']);
        $html = $this->get('/work/'.$project->slug)->assertOk()->getContent();
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $match);
        $data = json_decode($match[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('</script><script>alert(1)</script>', $data['@graph'][2]['name']);
    }

    public function test_canonical_upgrade_does_not_replace_custom_or_blank_values(): void
    {
        $migration = require database_path('migrations/2026_11_24_062000_set_confirmed_canonical_origin_if_missing.php');
        foreach (['https://custom.example', ''] as $value) {
            Setting::setValue('seo_canonical_base', $value);
            $migration->up();
            $this->assertSame($value, Setting::getValue('seo_canonical_base'));
        }
        Setting::where('key', 'seo_canonical_base')->delete();
        $migration->up();
        $this->assertSame('https://empetix.com', Setting::getValue('seo_canonical_base'));
    }
}

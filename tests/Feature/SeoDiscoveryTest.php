<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for SEO, Dynamic XML Sitemap, Robots.txt, and
 * LLM / AI Search Discovery (llms.txt, llms-full.txt).
 */
class SeoDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        User::first()?->assignRole(Permissions::OWNER_ROLE);
    }

    /** Public /sitemap.xml generates a valid XML urlset with projects and articles. */
    public function test_public_sitemap_xml_renders_correctly(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $content = $response->getContent();

        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString(url('/'), $content);
        $this->assertStringContainsString(url('/work/alfagn'), $content);
        $this->assertStringContainsString(url('/blog/arabic-first-design-systems'), $content);
        $this->assertStringContainsString('hreflang="ar"', $content);
        $this->assertStringContainsString('hreflang="en"', $content);
    }

    /** Public /robots.txt allows search crawlers and explicitly welcomes AI model bots. */
    public function test_public_robots_txt_welcomes_search_and_ai_bots(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $content = $response->getContent();

        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('User-agent: GPTBot', $content);
        $this->assertStringContainsString('User-agent: ClaudeBot', $content);
        $this->assertStringContainsString('User-agent: PerplexityBot', $content);
        $this->assertStringContainsString('Sitemap: ' . url('/sitemap.xml'), $content);
    }

    /** Public /llms.txt provides standard Markdown context for AI models with Arabic & English identity. */
    public function test_public_llms_txt_renders_bilingual_studio_context(): void
    {
        $response = $this->get('/llms.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $content = $response->getContent();

        $this->assertStringContainsString('Empetix Solutions', $content);
        $this->assertStringContainsString('إمبيتكس', $content);
        $this->assertStringContainsString('حلول إمبيتكس', $content);
        $this->assertStringContainsString('Alfagn', $content);
        $this->assertStringContainsString(url('/work/alfagn'), $content);
    }

    /** Public /llms-full.txt supplies detailed case studies and technical breakdowns. */
    public function test_public_llms_full_txt_renders_deep_catalog(): void
    {
        $response = $this->get('/llms-full.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $content = $response->getContent();

        $this->assertStringContainsString('Case Study:', $content);
        $this->assertStringContainsString('Technical Essays & Editorial Insights', $content);
    }

    /** Public layout injects JSON-LD Organization schema with Arabic/English aliases. */
    public function test_public_pages_embed_schema_org_json_ld(): void
    {
        $homeResponse = $this->get('/');
        $homeResponse->assertOk();
        $homeResponse->assertSee('application/ld+json', false);
        $homeResponse->assertSee('Empetix Solutions', false);
        $homeResponse->assertSee('إمبيتكس', false);
        $homeResponse->assertSee('حلول إمبيتكس', false);

        // Project detail embeds SoftwareApplication schema
        $projectResponse = $this->get('/work/alfagn');
        $projectResponse->assertOk();
        $projectResponse->assertSee('SoftwareApplication', false);
        $projectResponse->assertSee('Alfagn', false);
    }

    /** Guests cannot open the admin SEO or Sitemap control panels. */
    public function test_guests_cannot_access_seo_or_sitemap(): void
    {
        $this->get('/admin/seo')->assertRedirect('/admin/login');
        $this->get('/admin/sitemap')->assertRedirect('/admin/login');
    }

    /** Authenticated admins can view the SEO dashboard and save changes. */
    public function test_admin_can_view_and_update_seo_settings(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $indexResponse = $this->get('/admin/seo');
        $indexResponse->assertOk();
        $indexResponse->assertSee('SEO &amp; AI Search Discovery', false);
        $indexResponse->assertSee('Discovery &amp; Search Readiness', false);

        $updateResponse = $this->put('/admin/seo', [
            'seo_meta_title_en' => 'Empetix Solutions — Elite Software Studio',
            'seo_meta_title_ar' => 'حلول إمبيتكس — استوديو البرمجيات والأنظمة الرائد',
            'seo_meta_description_en' => 'Custom enterprise software and SaaS applications.',
            'seo_meta_description_ar' => 'بناء وتطوير أنظمة الساس وبرمجيات الشركات عالية الأداء.',
            'seo_keywords_en' => 'Empetix, SaaS, Enterprise Software',
            'seo_keywords_ar' => 'إمبيتكس, حلول إمبيتكس, برمجة ساس',
            'seo_welcome_ai_bots' => '1',
            'seo_allow_indexing' => '1',
            'seo_ai_company_bio' => 'Empetix is an Egyptian/MENA digital software engineering powerhouse.',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertEquals('Empetix Solutions — Elite Software Studio', Setting::getValue('seo_meta_title_en'));
        $this->assertEquals('حلول إمبيتكس — استوديو البرمجيات والأنظمة الرائد', Setting::getValue('seo_meta_title_ar'));
    }

    /** Authenticated admins can view sitemap inventory and trigger regeneration/ping. */
    public function test_admin_can_view_and_regenerate_sitemap(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $indexResponse = $this->get('/admin/sitemap');
        $indexResponse->assertOk();
        $indexResponse->assertSee('XML Sitemap &amp; URL Inventory', false);
        $indexResponse->assertSee('Total URLs Indexed', false);
        $indexResponse->assertSee('/work/alfagn', false);

        // Regenerate action
        $regenResponse = $this->post('/admin/sitemap/regenerate');
        $regenResponse->assertRedirect();
        $regenResponse->assertSessionHas('success');

        // Ping action
        $pingResponse = $this->post('/admin/sitemap/ping');
        $pingResponse->assertRedirect();
        $pingResponse->assertSessionHas('success');
    }
}

<?php

namespace Tests\Feature;

use App\Models\ServicePage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePageTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_migration_adds_six_unpublished_editions_and_no_public_exposure(): void
    {
        $this->assertSame(6, ServicePage::count());
        $this->assertSame(0, ServicePage::where('is_published', true)->count());
        foreach (['/services/saas-development', '/ar/services/saas-development', '/services/erp-systems', '/ar/services/performance-marketing'] as $path) {
            $this->get($path)->assertNotFound();
        }
        $this->get('/sitemap.xml')->assertDontSee('/services/');
        $this->get('/llms.txt')->assertDontSee('/services/');
        $this->get('/')->assertDontSee('class="published-service-links"', false);
        $this->get('/ar/services')->assertOk()->assertSee('noindex, nofollow', false)->assertSee('lang="ar" dir="rtl"', false);
    }

    public function test_previews_are_private_permission_checked_and_noindex(): void
    {
        $page = ServicePage::first();
        $this->get('/admin/service-pages/'.$page->id.'/preview')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/service-pages/'.$page->id.'/preview')->assertForbidden();
        $this->actingAs($this->owner())->get('/admin/service-pages/'.$page->id.'/preview')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertSee('Private editorial preview')->assertSee('noindex, nofollow', false);
    }

    public function test_publishing_pair_enables_real_language_urls_and_discovery(): void
    {
        ServicePage::where('slug', 'saas-development')->update(['is_published' => true]);
        foreach (['en' => '/services/saas-development', 'ar' => '/ar/services/saas-development'] as $locale => $path) {
            $this->get($path)->assertOk()->assertSee('lang="'.$locale.'"', false)
                ->assertSee('hreflang="en" href="https://empetix.com/services/saas-development"', false)
                ->assertSee('hreflang="ar" href="https://empetix.com/ar/services/saas-development"', false)
                ->assertSee('WebPage', false)->assertDontSee('Private editorial preview');
        }
        $this->get('/sitemap.xml')->assertSee('https://empetix.com/services/saas-development', false)->assertSee('https://empetix.com/ar/services/saas-development', false);
        $this->get('/llms.txt')->assertSee('/ar/services/saas-development');
        $this->get('/')->assertSee('class="published-service-links"', false);
        ServicePage::where('locale', 'ar')->update(['is_published' => false]);
        $this->get('/services/saas-development')->assertDontSee('hreflang="ar"', false);
        $this->get('/ar/services/saas-development')->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee('/ar/services');
    }

    public function test_owner_can_edit_publish_and_unpublish_without_unsafe_html(): void
    {
        $page = ServicePage::where('locale', 'ar')->first();
        $this->actingAs($this->owner())->get('/admin/service-pages/'.$page->id.'/edit')->assertOk()->assertSee('dir="rtl"', false);
        $payload = ['title' => 'عنوان جديد', 'summary' => 'ملخص', 'body' => "قسم\n<script>alert(1)</script>", 'meta_title' => '', 'meta_description' => '', 'is_published' => '1'];
        $this->put('/admin/service-pages/'.$page->id, $payload)->assertSessionHasNoErrors()->assertRedirect();
        $this->get($page->publicPath())->assertOk()->assertSee('عنوان جديد')->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
        $payload['is_published'] = '0';
        $this->put('/admin/service-pages/'.$page->id, $payload)->assertSessionHasNoErrors();
        $this->get($page->publicPath())->assertNotFound();
    }

    public function test_favicon_uses_square_raster_fallback_and_custom_choice_is_kept(): void
    {
        Setting::where('key', 'brand_favicon')->delete();
        $this->get('/')->assertSee('/assets/img/favicon.png', false);
        [$width, $height] = getimagesize(public_path('assets/img/favicon.png'));
        $this->assertSame($width, $height);
        $this->assertGreaterThan(48, $width);
        Setting::setValue('brand_favicon', 'uploads/brand/custom.png');
        $this->get('/')->assertSee('/storage/uploads/brand/custom.png', false);
    }
}

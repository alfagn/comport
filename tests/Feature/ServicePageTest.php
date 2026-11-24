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
        $this->get('/ar/services')->assertNotFound();
        $this->get('/services')->assertOk()->assertSee('lang="en" dir="ltr"', false);
    }

    public function test_previews_are_private_permission_checked_and_noindex(): void
    {
        $page = ServicePage::first();
        $this->get('/admin/service-pages/'.$page->id.'/preview')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/service-pages/'.$page->id.'/preview')->assertForbidden();
        $this->actingAs($this->owner())->get('/admin/service-pages/'.$page->id.'/preview')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertSee('Private editorial preview')->assertSee('noindex, nofollow', false);
    }

    public function test_only_english_is_public_even_when_an_archived_arabic_row_is_marked_published(): void
    {
        ServicePage::where('slug', 'saas-development')->update(['is_published' => true]);
        $this->get('/services/saas-development')->assertOk()->assertSee('lang="en" dir="ltr"', false)
            ->assertSee('WebPage', false)->assertDontSee('Private editorial preview')->assertDontSee('hreflang=', false);
        $this->get('/services')->assertOk()->assertDontSee('/ar/services');
        $this->get('/ar/services/saas-development')->assertNotFound();
        $this->get('/ar/services')->assertNotFound();
        $this->get('/sitemap.xml')->assertSee('https://empetix.com/services/saas-development', false)->assertDontSee('/ar/services');
        $this->get('/llms.txt')->assertSee('/services/saas-development')->assertDontSee('/ar/services');
        $this->get('/llms-full.txt')->assertDontSee('/ar/services');
        $this->get('/')->assertSee('class="published-service-links"', false)->assertDontSee('/ar/services');
        ServicePage::where('locale', 'en')->update(['is_published' => false]);
        $this->get('/')->assertDontSee('class="published-service-links"', false);
        $this->get('/sitemap.xml')->assertDontSee('/services');
    }

    public function test_archived_arabic_records_are_preserved_but_cannot_be_edited_published_or_previewed(): void
    {
        $page = ServicePage::where('locale', 'ar')->first();
        $original = $page->toArray();
        $this->actingAs($this->owner())->get('/admin/service-pages')->assertOk()
            ->assertDontSee($page->title)->assertSee('English drafts');
        foreach (['edit', 'preview'] as $action) {
            $this->get('/admin/service-pages/'.$page->id.'/'.$action)->assertNotFound();
        }
        $this->put('/admin/service-pages/'.$page->id, ['title' => 'Must not save', 'summary' => 'Must not save', 'body' => 'Must not save', 'is_published' => '1'])->assertNotFound();
        $this->assertSame($original, $page->fresh()->toArray());
        $this->assertSame(3, ServicePage::where('locale', 'ar')->count());
    }

    public function test_english_seo_form_keeps_archived_metadata_on_save(): void
    {
        Setting::setValue('seo_meta_title_ar', 'Archived title');
        $this->actingAs($this->owner())->get('/admin/seo')->assertOk()->assertDontSee('name="seo_meta_title_ar"', false)->assertDontSee('google-ar');
        $this->put('/admin/seo', ['seo_meta_title_en' => 'Empetix services'])->assertSessionHasNoErrors();
        $this->assertSame('Archived title', Setting::getValue('seo_meta_title_ar'));
    }

    public function test_owner_can_edit_publish_and_unpublish_without_unsafe_html(): void
    {
        $page = ServicePage::where('locale', 'en')->first();
        $this->actingAs($this->owner())->get('/admin/service-pages/'.$page->id.'/edit')->assertOk()->assertSee('dir="ltr"', false);
        $payload = ['title' => 'New service heading', 'summary' => 'Summary', 'body' => "Section\n<script>alert(1)</script>", 'meta_title' => '', 'meta_description' => '', 'is_published' => '1'];
        $this->put('/admin/service-pages/'.$page->id, $payload)->assertSessionHasNoErrors()->assertRedirect();
        $this->get($page->publicPath())->assertOk()->assertSee('New service heading')->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
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

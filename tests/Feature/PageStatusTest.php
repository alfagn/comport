<?php

namespace Tests\Feature;

use App\Models\JobPost;
use App\Models\Project;
use App\Models\User;
use App\Services\PageStatusService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageStatusTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return $this->owner();
    }

    public function test_public_pages_are_live_by_default(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/about')->assertOk();
        $this->get('/work')->assertOk();
        $this->get('/team')->assertOk();
        $this->get('/careers')->assertOk();
        $this->get('/partners')->assertOk();
        $this->get('/blog')->assertOk();
        $this->get('/contact')->assertOk();
    }

    public function test_admin_can_toggle_page_status(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = $this->admin();
        $service = app(PageStatusService::class);

        $this->assertTrue($service->isActive('careers'));

        // Toggle to hidden
        $this->actingAs($admin)
            ->patchJson(route('admin.status.toggle', ['entity' => 'pages', 'id' => 'careers']))
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'active' => false,
                'label' => 'Hidden',
            ]);

        $this->assertFalse($service->isActive('careers'));

        // Toggle back to live
        $this->actingAs($admin)
            ->patchJson(route('admin.status.toggle', ['entity' => 'pages', 'id' => 'careers']))
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'active' => true,
                'label' => 'Live',
            ]);

        $this->assertTrue($service->isActive('careers'));
    }

    public function test_hidden_page_aborts_with_404_on_direct_url_and_subroutes(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = $this->admin();

        $job = JobPost::create([
            'title' => 'Security Architect',
            'slug' => 'security-architect',
            'department' => 'Cloud',
            'location' => 'Remote',
            'type' => 'Full-time',
            'is_active' => true,
        ]);

        // Hide careers page
        $this->actingAs($admin)
            ->patchJson(route('admin.status.toggle', ['entity' => 'pages', 'id' => 'careers']))
            ->assertOk();

        // Direct page access returns 404
        $this->get('/careers')->assertNotFound();

        // Subroutes return 404 too
        $this->get('/careers/'.$job->slug)->assertNotFound();
        $this->post('/careers/'.$job->slug.'/apply', ['name' => 'John', 'email' => 'j@ex.com'])->assertNotFound();
    }

    public function test_hidden_work_page_blocks_index_and_project_pages(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = $this->admin();

        $project = Project::firstOrFail();

        // Live before toggle
        $this->get('/work')->assertOk();
        $this->get('/work/'.$project->slug)->assertOk();

        // Hide work page
        $this->actingAs($admin)
            ->patchJson(route('admin.status.toggle', ['entity' => 'pages', 'id' => 'work']))
            ->assertOk();

        $this->get('/work')->assertNotFound();
        $this->get('/work/'.$project->slug)->assertNotFound();
    }

    public function test_hidden_page_is_omitted_from_sitemap(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = $this->admin();

        // Initially present
        $sitemapBefore = $this->get('/sitemap.xml')->getContent();
        $this->assertStringContainsString('/about', $sitemapBefore);

        // Hide about page
        $this->actingAs($admin)
            ->patchJson(route('admin.status.toggle', ['entity' => 'pages', 'id' => 'about']))
            ->assertOk();

        $sitemapAfter = $this->get('/sitemap.xml')->getContent();
        $this->assertStringNotContainsString(seo_url('/about'), $sitemapAfter);
    }

    public function test_unprivileged_user_cannot_toggle_page_status(): void
    {
        $this->seed(DatabaseSeeder::class);
        $unprivileged = User::factory()->create();

        $this->actingAs($unprivileged)
            ->patchJson(route('admin.status.toggle', ['entity' => 'pages', 'id' => 'about']))
            ->assertForbidden();
    }
}

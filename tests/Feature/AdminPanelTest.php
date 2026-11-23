<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Dashboard tests: the guest wall holds, login works, each index screen
 * renders, and a full create/update/delete cycle persists through the
 * service layer for representative entities.
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    /** Seed the real content + admin user for every test. */
    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        // Explicit owner grant for the seeded admin (access is never implicit).
        \App\Models\User::first()?->assignRole(\App\Support\Permissions::OWNER_ROLE);
    }

    /** Guests are redirected to the login screen. */
    public function test_guests_cannot_open_the_dashboard(): void
    {
        foreach (['/admin', '/admin/projects', '/admin/settings', '/admin/pages'] as $route) {
            $this->get($route)->assertRedirect('/admin/login');
        }
    }

    /** The seeded admin signs in and lands on the dashboard. */
    public function test_admin_can_log_in(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@empetix.com',
            'password' => 'test-only-admin-password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticated();
    }

    /** Wrong credentials bounce back with errors. */
    public function test_login_rejects_bad_password(): void
    {
        $this->post('/admin/login', [
            'email' => 'admin@empetix.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** Every admin index screen renders for a signed-in admin. */
    public function test_admin_index_screens_render(): void
    {
        $admin = User::first();

        foreach ([
            '/admin', '/admin/hero-slides', '/admin/services', '/admin/industries',
            '/admin/projects', '/admin/members', '/admin/jobs', '/admin/posts',
            '/admin/partners', '/admin/pages', '/admin/pages/home', '/admin/inbox', '/admin/settings',
        ] as $route) {
            $this->actingAs($admin)->get($route)->assertOk();
        }
    }

    /** Create, update and delete a project through the dashboard. */
    public function test_project_crud_cycle(): void
    {
        $admin = User::first();

        $this->actingAs($admin)->post('/admin/projects', [
            'title' => 'Dashboard-made Project',
            'client' => 'Test Client',
            'year' => '2026',
            'excerpt' => 'Created from a feature test.',
            'is_active' => '1',
        ])->assertRedirect();

        $project = Project::where('title', 'Dashboard-made Project')->firstOrFail();

        $this->actingAs($admin)->put("/admin/projects/{$project->id}", [
            'title' => 'Dashboard-made Project (edited)',
            'client' => 'Test Client',
            'year' => '2026',
            'excerpt' => 'Updated from a feature test.',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('projects', ['title' => 'Dashboard-made Project (edited)']);

        $this->actingAs($admin)->delete("/admin/projects/{$project->id}")->assertRedirect();
        $this->assertDatabaseMissing('projects', ['title' => 'Dashboard-made Project (edited)']);
    }

    /** Update a service through the dashboard form payload shape. */
    public function test_service_update(): void
    {
        $admin = User::first();
        $service = Service::firstOrFail();

        $this->actingAs($admin)->put("/admin/services/{$service->id}", [
            'num' => $service->num,
            'title' => $service->title,
            'heading' => 'Edited heading from test',
            'description' => $service->description,
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('services', ['heading' => 'Edited heading from test']);
    }

    /** Post edit screens render without array conversion errors and handle updates safely. */
    public function test_post_edit_screen_renders_and_updates(): void
    {
        $admin = User::first();
        $post = Post::whereNotNull('meta')->firstOrFail();

        // Ensure edit screen renders with 200 OK
        $this->actingAs($admin)->get("/admin/posts/{$post->id}/edit")->assertOk();

        // Update post metadata
        $this->actingAs($admin)->put("/admin/posts/{$post->id}", [
            'title' => $post->title,
            'type' => $post->type,
            'category' => $post->category,
            'excerpt' => $post->excerpt,
            'body' => $post->body,
            'meta' => "UPDATED CHIP\n5 MIN READ",
            'is_published' => '1',
        ])->assertRedirect();

        $fresh = $post->fresh();
        $this->assertEquals('UPDATED CHIP', $fresh->meta['card_label'] ?? null);
        $this->assertEquals('5 MIN READ', $fresh->meta['read_time'] ?? null);
    }

    /** Settings save from the settings screen. */
    public function test_settings_save(): void
    {
        $admin = User::first();

        $this->actingAs($admin)->put('/admin/settings', [
            'settings' => ['email' => 'new@empetix.com'],
        ])->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'email', 'value' => 'new@empetix.com']);
    }

    /** Page sections save from the pages editor. */
    public function test_page_section_save(): void
    {
        $admin = User::first();

        $this->actingAs($admin)->put('/admin/pages/home', [
            'sections' => ['hero_kicker' => 'EDITED KICKER'],
        ])->assertRedirect();

        $this->assertDatabaseHas('page_sections', ['page' => 'home', 'key' => 'hero_kicker', 'value' => 'EDITED KICKER']);
    }
}

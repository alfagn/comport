<?php

namespace Tests\Feature;

use App\Models\ContentListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Content lists + mega menu: persisted CRUD, public rendering, validation.
 */
class ContentListTest extends TestCase
{
    use RefreshDatabase;

    /** Seeded principles render on the about page. */
    public function test_seeded_lists_render_publicly(): void
    {
        $this->seed(\Database\Seeders\ContentListSeeder::class);

        $this->get('/about')->assertOk()->assertSee('Code and campaigns share one scoreboard.');
        $this->get('/careers')->assertOk()->assertSee('Protected deep-work mornings');
    }

    /** A card created in the dashboard appears on the public site (cache flushed). */
    public function test_created_card_renders_publicly(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.content-lists.store'), [
            'group' => 'about.principles',
            'title' => 'Shipped beats perfect.',
            'body' => 'A new principle added from the dashboard.',
            'sort_order' => 9,
            'is_active' => '1',
        ])->assertRedirect(route('admin.content-lists.index'));

        $this->assertDatabaseHas('content_list_items', ['title' => 'Shipped beats perfect.', 'group' => 'about.principles']);
        $this->get('/about')->assertSee('Shipped beats perfect.');
    }

    /** Unknown groups are rejected and nothing is persisted. */
    public function test_invalid_group_is_rejected(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.content-lists.store'), [
            'group' => 'home.hero',
            'title' => 'Bad',
        ])->assertSessionHasErrors('group');

        $this->assertDatabaseMissing('content_list_items', ['title' => 'Bad']);
    }

    /** Guests cannot reach the content-list dashboard. */
    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.content-lists.index'))->assertRedirect(route('admin.login'));
    }

    /** Seeded mega-menu columns and links render in the public header. */
    public function test_mega_menu_renders_from_database(): void
    {
        $this->seed(\Database\Seeders\NavItemSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Software Engineering')
            ->assertSee('AI Automation &amp; Data Pipelines', false)
            ->assertSee('Behind the Lens', false);
    }

    /** A mega link created in the dashboard appears in the header (cache flushed). */
    public function test_created_mega_link_renders_publicly(): void
    {
        $this->seed(\Database\Seeders\NavItemSeeder::class);
        $admin = User::factory()->create();
        $column = \App\Models\NavItem::where('menu', 'mega-capabilities')->whereNull('parent_id')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.navigation.store'), [
            'menu' => 'mega-capabilities',
            'parent_id' => $column->id,
            'label' => 'Quantum Readiness Audits',
            'url' => '/work',
            'sub' => 'Because 2030 is coming.',
            'sort_order' => 9,
            'is_active' => '1',
        ])->assertRedirect(route('admin.navigation.index'));

        $this->get('/')->assertSee('Quantum Readiness Audits');
    }
}

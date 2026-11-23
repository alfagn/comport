<?php

namespace Tests\Feature;

use App\Models\NavItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Navigation CMS: dashboard CRUD, validation, and public rendering from DB.
 */
class NavigationTest extends TestCase
{
    use RefreshDatabase;

    /** The seeded utility links render in the public header. */
    public function test_utility_menu_renders_on_public_pages(): void
    {
        $this->seed(\Database\Seeders\NavItemSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('utility__link', false)
            ->assertSee('/careers', false);
    }

    /** A link created in the dashboard appears on the public site (cache flushed). */
    public function test_created_link_renders_publicly(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.navigation.store'), [
            'menu' => 'utility',
            'label' => 'Manifesto',
            'url' => '/manifesto',
            'sort_order' => 9,
            'is_active' => '1',
        ])->assertRedirect(route('admin.navigation.index'));

        $this->assertDatabaseHas('nav_items', ['label' => 'Manifesto', 'menu' => 'utility']);

        $this->get('/')->assertSee('Manifesto');
    }

    /** Hidden links disappear from the public header. */
    public function test_inactive_link_is_not_rendered(): void
    {
        $admin = User::factory()->create();
        $item = NavItem::create(['menu' => 'utility', 'label' => 'Secret Page', 'url' => '/secret', 'sort_order' => 1, 'is_active' => true]);

        $this->get('/')->assertSee('Secret Page');

        $this->actingAs($admin)->put(route('admin.navigation.update', $item->id), [
            'menu' => 'utility',
            'label' => 'Secret Page',
            'url' => '/secret',
            'sort_order' => 1,
            'is_active' => '0',
        ])->assertRedirect(route('admin.navigation.index'));

        $this->get('/')->assertDontSee('Secret Page');
    }

    /** Validation rejects bad input and nothing is persisted. */
    public function test_validation_blocks_invalid_menu(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.navigation.store'), [
            'menu' => 'sidebar',
            'label' => 'Bad',
            'url' => '/bad',
        ])->assertSessionHasErrors('menu');

        $this->assertDatabaseMissing('nav_items', ['label' => 'Bad']);
    }

    /** Guests cannot reach the navigation dashboard. */
    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.navigation.index'))->assertRedirect(route('admin.login'));
    }
}

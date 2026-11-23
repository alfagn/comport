<?php

namespace Tests\Feature;

use App\Models\PageSection;
use App\Models\User;
use App\Support\EditorialDefaults;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EditorialUpgradeRepairTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    public function test_unregistered_upgrade_rows_render_original_copy_then_migration_stores_it(): void
    {
        // Reproduce an existing installation whose new editorial seeder did not run.
        PageSection::where('key', 'like', 'copy_%')->delete();
        PageSection::where('page', 'home')->where('key', 'like', 'impact_value_%')->delete();
        Cache::forget('page_sections.map');
        $this->get('/')->assertOk()->assertSee('LARAVEL &amp; DISTRIBUTED CLOUD APIS', false)
            ->assertSee('Projects &amp; Platforms', false)->assertSee('64', false)->assertSee('Production uptime across client SaaS and FinTech applications');
        $migration = require database_path('migrations/2026_11_23_060000_materialize_editorial_defaults.php');
        $migration->up();
        foreach (EditorialDefaults::rows() as $row) {
            $this->assertDatabaseHas('page_sections', ['page' => $row['page'], 'key' => $row['key']]);
        }
        $this->assertDatabaseHas('page_sections', ['page' => 'home', 'key' => 'impact_value_1', 'value' => '64']);
        $owner = User::first(); $owner->assignRole(Permissions::OWNER_ROLE);
        $this->actingAs($owner)->get('/admin/pages/home')->assertOk()->assertSee('LARAVEL &amp; DISTRIBUTED CLOUD APIS', false);
    }

    public function test_repair_preserves_saved_edits_zero_and_explicit_blanks_and_materializes_legacy_null_defaults(): void
    {
        PageSection::where('page', 'home')->where('key', 'hero_kicker')->update(['value' => null]);
        PageSection::where('page', 'home')->where('key', 'hero_secondary_cta')->update(['value' => '']);
        PageSection::where('page', 'home')->where('key', 'impact_value_1')->update(['value' => '0']);
        PageSection::where('page', 'home')->where('key', 'impact_heading')->update(['value' => 'My own impact heading']);
        $this->seed(\Database\Seeders\EditorialDefaultsBackfillSeeder::class);
        $this->assertDatabaseHas('page_sections', ['page' => 'home', 'key' => 'hero_kicker', 'value' => 'NEW CAIRO, EGYPT']);
        $this->assertDatabaseHas('page_sections', ['page' => 'home', 'key' => 'hero_secondary_cta', 'value' => '']);
        $this->assertDatabaseHas('page_sections', ['page' => 'home', 'key' => 'impact_value_1', 'value' => '0']);
        $this->assertDatabaseHas('page_sections', ['page' => 'home', 'key' => 'impact_heading', 'value' => 'My own impact heading']);
        $this->get('/')->assertOk()->assertSee('My own impact heading');
    }

    public function test_intentionally_cleared_form_value_stays_blank_after_future_repairs(): void
    {
        $owner = User::first(); $owner->assignRole(Permissions::OWNER_ROLE);
        $this->actingAs($owner)->put('/admin/pages/home', ['sections' => ['hero_kicker' => '']])->assertRedirect();
        $this->seed(\Database\Seeders\EditorialDefaultsBackfillSeeder::class);
        $this->assertDatabaseHas('page_sections', ['page' => 'home', 'key' => 'hero_kicker', 'value' => '']);
        $this->assertSame('', sec('home', 'hero_kicker', 'NEW CAIRO, EGYPT'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\ContentListItem;
use App\Models\PageSection;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Source-to-dashboard registration, edits and safe upgrade regressions. */
class EditorialCoverageTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    public function test_editorial_catalog_is_registered_and_upgrade_preserves_edits_and_deletions(): void
    {
        $rows = json_decode(file_get_contents(database_path('seeders/data/editorial-content.json')), true);
        foreach ($rows as $row) {
            $this->assertDatabaseHas('page_sections', ['page' => $row['page'], 'key' => $row['key'], 'value' => $row['value']]);
        }
        PageSection::where('page', 'about')->where('key', 'hero_lead')->update(['value' => 'Keep edited introduction']);
        ContentListItem::where('group', 'about.directory')->delete();
        $this->seed(\Database\Seeders\PageSectionSeeder::class);
        $this->assertDatabaseHas('page_sections', ['page' => 'about', 'key' => 'hero_lead', 'value' => 'Keep edited introduction']);
        $this->assertDatabaseMissing('content_list_items', ['group' => 'about.directory']);
    }

    public function test_each_public_page_renders_an_actual_dashboard_editorial_edit(): void
    {
        $owner = User::first();
        $owner->assignRole(Permissions::OWNER_ROLE);
        foreach (['home' => '/', 'about' => '/about', 'work' => '/work', 'team' => '/team', 'careers' => '/careers', 'partners' => '/partners', 'blog' => '/blog', 'contact' => '/contact', 'founder' => '/founders/mohanad', 'project' => '/work/nilepay', 'job' => '/careers/laravel', 'post' => '/blog/arabic-first-design-systems', 'global' => '/'] as $page => $url) {
            $section = PageSection::where('page', $page)->where('key', 'like', 'copy_%')->where('type', 'text')->firstOrFail();
            $marker = 'EDIT-'.strtoupper($page).'-VERIFIED';
            $this->actingAs($owner)->put('/admin/pages/'.$page, ['sections' => [$section->key => $marker]])->assertRedirect();
            $this->get($url)->assertOk()->assertSee($marker);
        }
    }

    public function test_about_intro_media_directory_and_shared_founders_are_editable(): void
    {
        $owner = User::first();
        $owner->assignRole(Permissions::OWNER_ROLE);
        $this->actingAs($owner)->put('/admin/pages/about', ['sections' => [
            'hero_lead' => 'Changed company introduction', 'overview_lead' => 'Changed overview introduction',
            'overview_video' => 'https://example.com/studio.mp4', 'overview_poster' => 'uploads/pages/poster.jpg',
        ]])->assertRedirect();
        $this->actingAs($owner)->put('/admin/pages/founders', ['sections' => ['lead' => 'Changed founders introduction']])->assertRedirect();
        $this->actingAs($owner)->post('/admin/content-lists', [
            'group' => 'about.directory', 'title' => 'New Practice Head', 'body' => 'Engineering Director', 'is_active' => true,
        ])->assertRedirect();
        $this->get('/about')->assertOk()->assertSee('Changed company introduction')->assertSee('Changed overview introduction')
            ->assertSee('https://example.com/studio.mp4', false)->assertSee('/storage/uploads/pages/poster.jpg', false)
            ->assertSee('New Practice Head')->assertSee('Engineering Director')->assertSee('Changed founders introduction');
    }
    public function test_about_poster_upload_and_existing_values_are_visible_in_editor(): void
    {
        $owner = User::first(); $owner->assignRole(Permissions::OWNER_ROLE);
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->actingAs($owner)->get('/admin/pages/about')->assertOk()->assertSee('Built by engineers');
        $this->actingAs($owner)->put('/admin/pages/about', [
            'sections' => ['hero_lead' => 'Company introduction preserved'],
            'images' => ['overview_poster' => \Illuminate\Http\UploadedFile::fake()->image('poster.png')],
        ])->assertRedirect();
        $path = PageSection::where('page', 'about')->where('key', 'overview_poster')->value('value');
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($path);
        $this->get('/about')->assertOk()->assertSee('/storage/'.$path, false);
    }

    public function test_every_literal_template_section_has_a_registered_editor_control(): void
    {
        $paths = array_merge(glob(resource_path('views/pages/*.blade.php')), glob(resource_path('views/partials/*.blade.php')), [resource_path('views/layouts/public.blade.php')]);
        foreach ($paths as $path) {
            preg_match_all("/sec\\('([^']+)', '([^']+)'/", file_get_contents($path), $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $this->assertDatabaseHas('page_sections', ['page' => $match[1], 'key' => $match[2]]);
            }
        }
    }

}

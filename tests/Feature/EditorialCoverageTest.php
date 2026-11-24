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
        foreach (['home' => '/', 'about' => '/about', 'work' => '/work', 'team' => '/team', 'careers' => '/careers', 'partners' => '/partners', 'blog' => '/blog', 'contact' => '/contact', 'founder' => '/founders/mohanad', 'project' => '/work/alfagn', 'job' => '/careers/laravel', 'post' => '/blog/arabic-first-design-systems', 'global' => '/'] as $page => $url) {
            $section = PageSection::where('page', $page)->where('key', 'like', 'copy_%')->where('type', 'text')->firstOrFail();
            $marker = 'EDIT-'.strtoupper($page).'-VERIFIED';
            $this->actingAs($owner)->put('/admin/pages/'.$page, ['sections' => [$section->key => $marker]])->assertRedirect();
            $this->get($url)->assertOk()->assertSee($marker);
        }
    }

    public function test_about_intro_media_cards_private_video_and_shared_founders_are_editable(): void
    {
        $owner = User::first();
        $owner->assignRole(Permissions::OWNER_ROLE);

        // 1. Direct HTTPS link + inline editable Principles & Timeline cards
        $this->actingAs($owner)->put('/admin/pages/about', [
            'sections' => [
                'hero_lead' => 'Changed company introduction',
                'overview_lead' => 'Changed overview introduction',
                'overview_video' => 'https://example.com/studio.mp4',
                'overview_poster' => 'uploads/pages/poster.jpg',
            ],
            'lists' => [
                'about.principles' => [
                    ['title' => 'Unified Engineering & Growth Card', 'body' => 'Edited directly from About content blocks.'],
                ],
                'about.timeline' => [
                    ['meta' => '2025', 'title' => 'Started Software & Marketing Studio', 'body' => 'Launched in 2025 uniting code and growth.'],
                ],
            ],
        ])->assertRedirect();

        $this->actingAs($owner)->put('/admin/pages/founders', ['sections' => ['lead' => 'Changed founders introduction']])->assertRedirect();

        $this->get('/about')->assertOk()
            ->assertSee('Changed company introduction')
            ->assertSee('Changed overview introduction')
            ->assertSee('https://example.com/studio.mp4', false)
            ->assertSee('/storage/uploads/pages/poster.jpg', false)
            ->assertSee('Unified Engineering &amp; Growth Card', false)
            ->assertSee('Started Software &amp; Marketing Studio', false)
            ->assertSee('Changed founders introduction')
            ->assertDontSee('Senior Principals &amp; Discipline Heads', false);

        // 2. Private Storage video file upload + streaming endpoint
        \Illuminate\Support\Facades\Storage::fake('local');
        $fakeVideo = \Illuminate\Http\UploadedFile::fake()->create('overview.mp4', 512, 'video/mp4');

        $this->actingAs($owner)->put('/admin/pages/about', [
            'sections' => ['hero_lead' => 'Video uploaded to private disk'],
            'videos' => ['overview_video' => $fakeVideo],
        ])->assertRedirect();

        $storedValue = PageSection::where('page', 'about')->where('key', 'overview_video')->value('value');
        $this->assertStringStartsWith('private:videos/pages/', (string) $storedValue);
        $diskPath = substr((string) $storedValue, strlen('private:'));
        \Illuminate\Support\Facades\Storage::disk('local')->assertExists($diskPath);

        $this->get('/about')->assertOk()->assertSee('/media/video/'.$diskPath, false);
        $this->get('/media/video/'.$diskPath)->assertOk();
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

    public function test_projects_and_platforms_listing_and_detailed_case_study_controls_render(): void
    {
        $owner = User::first(); $owner->assignRole(Permissions::OWNER_ROLE);
        $project = \App\Models\Project::where('slug', 'alfagn')->firstOrFail();
        $this->actingAs($owner)->put('/admin/pages/work', ['sections' => ['hero_title' => 'Updated Projects and Platforms introduction']])->assertRedirect();
        $this->actingAs($owner)->put('/admin/projects/'.$project->id, [
            'title' => 'Edited Platform Case Study', 'slug' => $project->slug,
            'client' => 'Platform Client', 'location' => 'Cairo Platform Studio', 'year' => '2026',
            'kicker' => 'Platform Delivery', 'excerpt' => 'Platform card introduction',
            'facts' => 'Scope | Platform scope verified',
            'stats' => '42 | 0 | % | Platform outcome verified',
            'sections' => 'Delivery || Platform narrative heading || Platform narrative paragraph',
            'quote' => 'Platform client quotation', 'quote_cite' => 'Platform client leader',
            'body' => '<p>Platform detailed body</p>',
            'video_src' => 'https://example.com/platform.mp4', 'video_poster' => 'assets/img/work/alfagn/alfagn_course1.png',
            'tags' => 'Platform, Engineering', 'is_active' => true, 'is_featured' => true,
        ])->assertRedirect();
        $this->get('/work')->assertOk()->assertSee('Updated Projects and Platforms introduction')->assertSee('Edited Platform Case Study')->assertSee('Platform Delivery');
        $this->get('/work/'.$project->slug)->assertOk()->assertSee('Edited Platform Case Study')->assertSee('Cairo Platform Studio')
            ->assertSee('Platform scope verified')->assertSee('Platform outcome verified')->assertSee('Platform narrative heading')
            ->assertSee('Platform narrative paragraph')->assertSee('Platform client quotation')->assertSee('Platform client leader')
            ->assertSee('Platform detailed body')->assertSee('https://example.com/platform.mp4', false);
    }

}

<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use App\Services\SectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamAndErrorPageTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_team_scope_and_live_department_counts(): void
    {
        Member::query()->where('kind', 'member')->update(['is_active' => false]);
        Member::create(['name' => 'Design person', 'slug' => 'design-person', 'kind' => 'member', 'department' => 'design', 'is_active' => true]);
        Member::create(['name' => 'General person', 'slug' => 'general-person', 'kind' => 'member', 'is_active' => true]);
        $this->get('/team')->assertOk()->assertSee('class="team-page"', false)
            ->assertSee('All teams (2)')->assertSee('Product design (1)')
            ->assertSee('data-team-group="design"', false)->assertSee('data-team-group="general"', false)
            ->assertDontSee('data-team-group="engineering"', false)->assertSee('Design person');
    }

    public function test_department_saves_through_member_editor_and_filters_render_it(): void
    {
        $user = User::first();
        $user->assignRole('owner');
        $member = Member::where('kind', 'member')->first();
        $this->actingAs($user)->get('/admin/members/'.$member->id.'/edit')->assertOk()->assertSee('Department (team directory)');
        $this->actingAs($user)->put('/admin/members/'.$member->id, [
            'name' => $member->name, 'slug' => $member->slug, 'kind' => 'member',
            'department' => 'design', 'is_active' => true,
        ])->assertRedirect('/admin/members');
        $this->assertDatabaseHas('members', ['id' => $member->id, 'department' => 'design']);
        $this->get('/team')->assertOk()->assertSee('data-team-group="design"', false)->assertSee($member->name);
    }

    public function test_html_404_is_branded_for_routes_and_entities_with_real_status(): void
    {
        // Laravel's exception renderer uses the same view for missing routes and models.
        foreach (['/missing-route', '/work/missing', '/founders/missing', '/careers/missing', '/blog/missing'] as $url) {
            $this->get($url)->assertNotFound()->assertSee('This page is off the map.')
                ->assertSee('Back to home')->assertSee('Explore our work')
                ->assertSee('noindex, follow')->assertDontSee('Stack trace');
        }
        $this->get('/work')->assertOk();
        $this->get('/')->assertOk();
    }

    public function test_json_and_api_404_stay_json_not_the_html_error_page(): void
    {
        foreach (['/missing-route', '/work/missing', '/api/missing'] as $url) {
            $this->getJson($url)->assertNotFound()->assertHeader('Content-Type', 'application/json')
                ->assertJsonStructure(['message'])->assertDontSee('<!DOCTYPE html>', false);
        }
        $this->get('/api/missing')->assertNotFound()->assertHeader('Content-Type', 'application/json');
    }

    public function test_error_copy_is_stored_and_editable_without_affecting_json(): void
    {
        $this->assertDatabaseHas('page_sections', ['page' => 'error', 'key' => 'title', 'value' => 'This page is off the map.']);
        app(SectionService::class)->savePage('error', ['title' => 'Edited recovery heading']);
        $this->get('/missing')->assertNotFound()->assertSee('Edited recovery heading');
        $this->getJson('/missing')->assertNotFound()->assertDontSee('Edited recovery heading');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'owner'], ['guard_name' => 'web']);
    }

    public function test_projects_archive_page_displays_cards_and_live_url(): void
    {
        $project = Project::create([
            'title' => 'Alfagn High-Concurrency Platform',
            'slug' => 'alfagn-platform',
            'live_url' => 'https://alfagn.com',
            'kicker' => 'EDTECH & SAAS',
            'excerpt' => 'High throughput LMS with forensic video streaming.',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get('/work');
        $response->assertOk();
        $response->assertSee('Alfagn High-Concurrency Platform');
        $response->assertSee('https://alfagn.com');
        $response->assertSee('Live Platform');
        $response->assertSee('Visit Live Site');
    }

    public function test_project_detail_page_displays_live_url_html_body_and_gallery(): void
    {
        $project = Project::create([
            'title' => 'Westvale Learning Platform',
            'slug' => 'westvale-platform',
            'live_url' => 'http://westvalelearning.academy/',
            'kicker' => 'ENTERPRISE LMS',
            'excerpt' => 'WebRTC classrooms and AI proctoring.',
            'body' => '<div class="custom-arch-block"><h4>Distributed Microservices Core</h4><p>High availability setup.</p></div>',
            'gallery' => ['assets/img/work/westvale/westvale_preview_1.png', 'assets/img/work/westvale/westvale_preview_2.png'],
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get('/work/westvale-platform');
        $response->assertOk();
        $response->assertSee('Westvale Learning Platform');
        $response->assertSee('http://westvalelearning.academy/');
        $response->assertSee('Visit Live Platform');
        $response->assertSee('Distributed Microservices Core', false);
        $response->assertSee('assets/img/work/westvale/westvale_preview_1.png');
        $response->assertSee('youtube-nocookie.com/embed', false);
    }

    public function test_user_can_access_and_update_personal_and_team_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Mohanad Ahmed',
            'email' => 'mohand@example.com',
        ]);
        $user->assignRole('owner');

        $response = $this->actingAs($user)->get('/admin/profile');
        $response->assertOk();
        $response->assertSee('Account &amp; Team Profile', false);
        $response->assertSee('mohand@example.com');

        $updateResponse = $this->actingAs($user)->put('/admin/profile', [
            'name' => 'Mohanad Ahmed Updated',
            'email' => 'mohand.updated@example.com',
            'role' => 'Founder & Lead Systems Architect',
            'department' => 'Software Engineering',
            'focus' => 'High-Concurrency Cloud Engines',
            'bio' => "Engineering leader with over a decade of systems craft.\n\nDesigning resilient cloud platforms.",
        ]);

        $updateResponse->assertRedirect('/admin/profile');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Mohanad Ahmed Updated',
            'email' => 'mohand.updated@example.com',
        ]);
        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'name' => 'Mohanad Ahmed Updated',
            'role' => 'Founder & Lead Systems Architect',
            'department' => 'Software Engineering',
        ]);
    }

    public function test_artisan_user_create_command(): void
    {
        $this->artisan('user:create', [
            'name' => 'Lead Engineer',
            'email' => 'lead.eng@example.com',
            'password' => 'secret123456',
            '--role' => 'owner',
            '--founder' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'lead.eng@example.com',
            'name' => 'Lead Engineer',
        ]);
        $this->assertDatabaseHas('members', [
            'email' => 'lead.eng@example.com',
            'name' => 'Lead Engineer',
            'kind' => 'founder',
            'is_active' => true,
        ]);
    }
}

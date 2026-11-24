<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\TeamUsersSyncSeeder;
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
        Role::firstOrCreate(['name' => 'editor'], ['guard_name' => 'web']);
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

    public function test_project_can_be_updated_without_fileinfo_exception(): void
    {
        $user = User::factory()->create();
        $user->assignRole('owner');

        $project = Project::create([
            'title' => 'Initial Title',
            'slug' => 'initial-title',
            'live_url' => 'https://example.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put("/admin/projects/{$project->id}", [
            'title' => 'Updated Project Title',
            'slug' => 'updated-project-title',
            'live_url' => 'https://alfagn.com',
            'kicker' => 'EDTECH CORE',
            'body' => '<p>Updated description</p>',
        ]);

        $response->assertRedirect('/admin/projects');
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project Title',
            'live_url' => 'https://alfagn.com',
        ]);
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
        $response->assertSee('Login &amp; Security Credentials', false);
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

    public function test_user_does_not_see_themselves_in_team_and_cannot_delete_themselves(): void
    {
        $user = User::factory()->create(['name' => 'Current User', 'email' => 'current@empetix.com']);
        $user->assignRole('owner');

        $myMember = Member::create([
            'user_id' => $user->id,
            'name' => 'Current User',
            'slug' => 'current-user',
            'email' => 'current@empetix.com',
            'kind' => 'founder',
        ]);

        $otherMember = Member::create([
            'name' => 'Other Member',
            'slug' => 'other-member',
            'email' => 'other@empetix.com',
            'kind' => 'member',
        ]);

        // In DataTables JSON, current user's member is filtered out
        $response = $this->actingAs($user)->getJson('/admin/members', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk();
        $json = $response->json();
        $dataStr = json_encode($json['data'] ?? []);
        $this->assertStringNotContainsString('Current User', $dataStr);
        $this->assertStringContainsString('Other Member', $dataStr);

        // Attempting to delete own member record is blocked
        $deleteResponse = $this->actingAs($user)->delete("/admin/members/{$myMember->id}");
        $deleteResponse->assertRedirect('/admin/members');
        $deleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('members', ['id' => $myMember->id]);
    }

    public function test_owner_does_not_see_themselves_in_admins_table_and_peer_owner_is_protected(): void
    {
        $owner1 = User::factory()->create(['name' => 'Owner One', 'email' => 'owner1@empetix.com']);
        $owner1->assignRole('owner');

        $owner2 = User::factory()->create(['name' => 'Owner Two', 'email' => 'owner2@empetix.com']);
        $owner2->assignRole('owner');

        // Owner1 cannot see themselves in admins list
        $response = $this->actingAs($owner1)->getJson('/admin/admins', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk();
        $dataStr = json_encode($response->json()['data'] ?? []);
        $this->assertStringNotContainsString('owner1@empetix.com', $dataStr);
        $this->assertStringContainsString('owner2@empetix.com', $dataStr);
        $this->assertStringContainsString('Peer Owner', $dataStr);

        // Owner1 cannot delete Owner2
        $deleteResponse = $this->actingAs($owner1)->delete("/admin/admins/{$owner2->id}");
        $deleteResponse->assertSessionHasErrors();
        $this->assertDatabaseHas('users', ['id' => $owner2->id]);
    }

    public function test_team_users_sync_seeder_assigns_owners_correctly(): void
    {
        $this->seed(TeamUsersSyncSeeder::class);

        $mohanad = User::where('email', 'mohand@empetix.com')->first();
        $this->assertNotNull($mohanad);
        $this->assertTrue($mohanad->hasRole('owner'));
        $this->assertEquals('founder', $mohanad->member?->kind);

        $ayman = User::where('email', 'ayman@empetix.com')->first();
        $this->assertNotNull($ayman);
        $this->assertTrue($ayman->hasRole('owner'));
        $this->assertEquals('founder', $ayman->member?->kind);

        $bishoy = User::where('email', 'bishoy@empetix.com')->first();
        $this->assertNotNull($bishoy);
        $this->assertTrue($bishoy->hasRole('owner'));
        $this->assertEquals('founder', $bishoy->member?->kind);
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

    public function test_artisan_user_delete_command_deletes_peer_owner_and_linked_member(): void
    {
        User::role('owner')->get()->each(fn (User $u) => $u->delete());

        $primaryOwner = User::factory()->create(['name' => 'Primary Owner', 'email' => 'mohand@empetix.com']);
        $primaryOwner->assignRole('owner');

        $duplicateOwner = User::factory()->create(['name' => 'Mohanad Ahemd Shehata', 'email' => 'mohanad@empetix.com']);
        $duplicateOwner->assignRole('owner');

        Member::create([
            'user_id' => $duplicateOwner->id,
            'name' => 'Mohanad Ahemd Shehata',
            'slug' => 'mohanad-ahemd-shehata',
            'email' => 'mohanad@empetix.com',
            'kind' => 'founder',
        ]);

        $this->artisan('user:delete', [
            'identifier' => 'mohanad@empetix.com',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('users', ['email' => 'mohanad@empetix.com']);
        $this->assertDatabaseMissing('members', ['email' => 'mohanad@empetix.com']);
        $this->assertDatabaseHas('users', ['email' => 'mohand@empetix.com']);

        // Cannot delete the last remaining owner
        $this->artisan('user:delete', [
            'identifier' => 'mohand@empetix.com',
            '--force' => true,
        ])->assertFailed();

        $this->assertDatabaseHas('users', ['email' => 'mohand@empetix.com']);
    }

    public function test_project_detail_page_renders_animated_emblem_logo_stage(): void
    {
        $project = Project::updateOrCreate(
            ['slug' => 'western-elearning-academy'],
            [
                'title' => 'Western eLearning Academy',
                'logo' => 'assets/img/work/westvale/logo.png',
                'live_url' => 'http://westvalelearning.academy/',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $response = $this->get('/work/western-elearning-academy');
        $response->assertOk();
        $response->assertSee('project-emblem-stage', false);
        $response->assertSee('assets/img/work/westvale/logo.png');
        $response->assertSee('VERIFIED PRODUCTION SYSTEM');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\TeamUsersSyncSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\File\File;
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
        $response->assertSee('westvalelearning.academy');
        $response->assertSee('EMPETIX.COM');
    }

    public function test_bulk_delete_members_succeeds_without_cache_exception_and_renders_admin_photos(): void
    {
        $this->seed(TeamUsersSyncSeeder::class);

        $mohanad = User::where('email', 'mohand@empetix.com')->firstOrFail();

        // Topbar & Sidebar render Mohanad's real photo instead of initial letter
        $dashResponse = $this->actingAs($mohanad)->get('/admin/profile');
        $dashResponse->assertOk();
        $dashResponse->assertSee('assets/img/team/mohanad-shehata.jpg');

        // Admins DataTable renders Ayman & Bishoy real photos
        $adminsJson = $this->actingAs($mohanad)->getJson('/admin/admins', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $adminsJson->assertOk();
        $adminsPayload = json_encode($adminsJson->json()['data'] ?? []);
        $this->assertStringContainsString('ayman-ibrahim.jpg', $adminsPayload);
        $this->assertStringContainsString('bishoy-magdy.jpg', $adminsPayload);

        // Bulk delete staff member returns 200 OK JSON (no SeoService method exception)
        $staffMember = Member::create([
            'name' => 'Temp Staff Engineer',
            'slug' => 'temp-staff-engineer',
            'email' => 'temp-staff@empetix.com',
            'kind' => 'member',
        ]);

        Permission::firstOrCreate(['name' => 'manage members', 'guard_name' => 'web']);

        $bulkRes = $this->actingAs($mohanad)->postJson('/admin/bulk-delete/members', [
            'ids' => [$staffMember->id],
        ]);

        $bulkRes->assertOk()->assertJson(['ok' => true, 'count' => 1]);
        $this->assertDatabaseMissing('members', ['id' => $staffMember->id]);
    }

    public function test_creating_admin_by_selecting_team_member_email_and_revoking_keeps_member(): void
    {
        $owner = User::factory()->create(['name' => 'Owner User', 'email' => 'owner.test@empetix.com']);
        $owner->assignRole('owner');
        Permission::firstOrCreate(['name' => 'manage admins', 'guard_name' => 'web']);

        $teamPerson = Member::create([
            'name' => 'Tarek El-Shenawy',
            'slug' => 'tarek-el-shenawy-test',
            'email' => 'tarek.test@empetix.com',
            'role' => 'Principal Backend Engineer',
            'photo' => 'assets/img/team/tarek-elshenawy.jpg',
            'kind' => 'member',
            'is_active' => true,
        ]);

        // 1. Create form displays team member's email in the dropdown
        $createPage = $this->actingAs($owner)->get('/admin/admins/create');
        $createPage->assertOk();
        $createPage->assertSee('Select Team Member (Email)');
        $createPage->assertSee('tarek.test@empetix.com');

        // 2. Submitting member_id + password + roles creates Admin User linked to Member
        $storeRes = $this->actingAs($owner)->post('/admin/admins', [
            'member_id' => $teamPerson->id,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'roles' => ['editor'],
        ]);
        $storeRes->assertRedirect('/admin/admins');

        $createdAdmin = User::where('email', 'tarek.test@empetix.com')->first();
        $this->assertNotNull($createdAdmin);
        $this->assertEquals($teamPerson->id, $createdAdmin->member_id);
        $this->assertEquals($createdAdmin->id, $teamPerson->fresh()->user_id);
        $this->assertTrue($createdAdmin->hasRole('editor'));

        // 3. Deleting Admin revokes login while keeping the person in Team & Founders
        $delRes = $this->actingAs($owner)->delete("/admin/admins/{$createdAdmin->id}");
        $delRes->assertRedirect('/admin/admins');

        $this->assertDatabaseMissing('users', ['id' => $createdAdmin->id]);
        $this->assertDatabaseHas('members', ['id' => $teamPerson->id, 'user_id' => null]);
    }

    public function test_mega_menu_and_footer_render_live_dynamic_services_projects_and_posts_without_marketing(): void
    {
        Service::create([
            'num' => '01',
            'title' => 'Real-Time Distributed WebRTC Engines',
            'slug' => 'webrtc-engines',
            'heading' => 'Sub-150ms virtual classrooms & encrypted media relays',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Project::updateOrCreate(
            ['slug' => 'alfagn-live-test'],
            [
                'title' => 'Alfagn Live Dynamic Mega Card',
                'client' => 'Alfagn EdTech',
                'year' => '2026',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Post::create([
            'title' => 'Scaling PostgreSQL Under 45k Exam Submissions',
            'slug' => 'scaling-postgresql-exams',
            'category' => 'DATABASE ARCHITECTURE',
            'is_published' => true,
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee('Real-Time Distributed WebRTC Engines');
        $home->assertSee('Alfagn Live Dynamic Mega Card');
        $home->assertSee('Scaling PostgreSQL Under 45k Exam Submissions');
        $home->assertDontSee('MARKETING & GROWTH');
        $home->assertDontSee('Performance & Paid Acquisition');
    }

    public function test_extensionless_temp_upload_guesses_mime_from_binary_header_without_type_error(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $project = Project::updateOrCreate(
            ['slug' => 'mime-test-project'],
            ['title' => 'Mime Test Project', 'is_active' => true]
        );

        // Create an extensionless temporary file (just like PHP's /tmp/phpXXXXXX on Linux) containing valid 1x1 PNG bytes
        $tmpPath = tempnam(sys_get_temp_dir(), 'php');
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        file_put_contents($tmpPath, $pngBytes);

        try {
            $symfonyFile = new File($tmpPath);
            $this->assertEquals('image/png', $symfonyFile->getMimeType());
            $this->assertEquals('png', $symfonyFile->guessExtension());

            $uploadedLogo = new UploadedFile($tmpPath, 'logo.png', 'image/png', null, true);

            $res = $this->actingAs($owner)->put("/admin/projects/{$project->id}", [
                'title' => 'Mime Test Project Updated',
                'slug' => 'mime-test-project',
                'logo' => $uploadedLogo,
            ]);

            $res->assertSessionHasNoErrors()->assertRedirect('/admin/projects');
            $this->assertNotNull($project->fresh()->logo);
            $this->assertStringEndsWith('.png', (string) $project->fresh()->logo);
        } finally {
            if (is_file($tmpPath)) {
                @unlink($tmpPath);
            }
        }
    }

    public function test_team_members_get_auto_unique_id_and_enforce_unique_email(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');
        Permission::firstOrCreate(['name' => 'manage members', 'guard_name' => 'web']);

        // 1. Auto-generates sequential unique member_code (EMP-XXXXX) when omitted
        $this->actingAs($owner)->post('/admin/members', [
            'name' => 'Karim Adel',
            'email' => 'karim.adel@empetix.com',
            'kind' => 'member',
            'department' => 'engineering',
            'role' => 'Senior Backend Engineer',
            'is_active' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect('/admin/members');

        $karim = Member::where('email', 'karim.adel@empetix.com')->first();
        $this->assertNotNull($karim);
        $this->assertMatchesRegularExpression('/^EMP-\d{5,}$/', (string) $karim->member_code);

        // 2. Rejects duplicate email on another team member
        $dupEmailRes = $this->actingAs($owner)->post('/admin/members', [
            'name' => 'Another Karim',
            'email' => 'KARIM.ADEL@empetix.com',
            'kind' => 'member',
        ]);
        $dupEmailRes->assertSessionHasErrors('email');

        // 3. Rejects duplicate member_code on another team member
        $dupCodeRes = $this->actingAs($owner)->post('/admin/members', [
            'member_code' => $karim->member_code,
            'name' => 'Unique Email Same Code',
            'email' => 'unique.person@empetix.com',
            'kind' => 'member',
        ]);
        $dupCodeRes->assertSessionHasErrors('member_code');

        // 4. Next created member gets a distinct incremented EMP-XXX ID
        $second = Member::create([
            'name' => 'Salma Nabil',
            'slug' => 'salma-nabil-unique',
            'email' => 'salma.nabil@empetix.com',
            'kind' => 'member',
        ]);
        $this->assertNotNull($second->member_code);
        $this->assertNotEquals($karim->member_code, $second->member_code);
    }

    public function test_profile_edit_with_duplicate_name_generates_unique_member_slug(): void
    {
        Member::query()->firstOrCreate(
            ['slug' => 'mohanad'],
            [
                'name' => 'Mohanad Ahmed Shehata',
                'email' => 'mohand@gmail.com',
                'kind' => 'founder',
                'is_active' => true,
            ]
        );

        $secondMohanad = User::factory()->create([
            'name' => 'Mohanad',
            'email' => 'admin@emp.com',
        ]);
        $secondMohanad->assignRole('owner');

        $this->actingAs($secondMohanad)->get('/admin/profile')->assertOk();

        $linked = Member::query()->where('email', 'admin@emp.com')->firstOrFail();
        $this->assertSame('mohanad-2', $linked->slug);
        $this->assertSame($linked->id, $secondMohanad->fresh()->member_id);
    }
}

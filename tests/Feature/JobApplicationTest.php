<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Job applications: the public careers form persists real submissions with
 * CVs on the private disk, and only an authenticated admin can review,
 * download or delete them. No applicant data is ever seeded.
 */
class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        // Explicit owner grant for the seeded admin (access is never implicit).
        \App\Models\User::first()?->assignRole(\App\Support\Permissions::OWNER_ROLE);
    }

    /** Valid payload factory for the careers form. */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Test Candidate',
            'email' => 'candidate@example.com',
            'phone' => '+201000000000',
            'location' => 'Cairo, Egypt',
            'linkedin' => 'https://linkedin.com/in/test',
            'portfolio' => 'https://github.com/test',
            'experience_years' => '5-8',
            'notice_period' => '30-days',
            'achievement' => 'Shipped a multi-tenant billing platform serving 200k users.',
            'cv_file' => UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf'),
        ], $overrides);
    }

    /** A real submission stores the row and parks the CV on the private disk. */
    public function test_public_application_is_stored_with_private_cv(): void
    {
        Storage::fake('local');

        $response = $this->post('/careers/laravel/apply', $this->payload());

        $response->assertRedirect();
        $this->assertDatabaseHas('job_applications', [
            'email' => 'candidate@example.com',
            'status' => 'new',
        ]);

        $application = JobApplication::where('email', 'candidate@example.com')->firstOrFail();
        Storage::disk('local')->assertExists($application->cv_path);
        // The private path must never be a public-disk URL.
        $this->assertStringNotContainsString('public', $application->cv_path);
    }

    /** Validation rejects non-document uploads and missing required fields. */
    public function test_validation_rejects_bad_cv_and_missing_fields(): void
    {
        Storage::fake('local');

        $this->post('/careers/laravel/apply', $this->payload([
            'cv_file' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php'),
        ]))->assertSessionHasErrors('cv_file');

        $this->post('/careers/laravel/apply', $this->payload(['full_name' => '', 'achievement' => '']))
            ->assertSessionHasErrors(['full_name', 'achievement']);

        $this->assertDatabaseCount('job_applications', 0);
    }

    /** Applying to a closed or unknown opening 404s. */
    public function test_unknown_opening_404s(): void
    {
        Storage::fake('local');
        $this->post('/careers/nothing/apply', $this->payload())->assertNotFound();
    }

    /** Guests are walled off the applications screens and CV downloads. */
    public function test_guests_cannot_review_applications(): void
    {
        Storage::fake('local');
        $this->post('/careers/laravel/apply', $this->payload());
        $application = JobApplication::firstOrFail();

        $this->get('/admin/applications')->assertRedirect('/admin/login');
        $this->get("/admin/applications/{$application->id}")->assertRedirect('/admin/login');
        $this->get("/admin/applications/{$application->id}/cv")->assertRedirect('/admin/login');
        $this->get('/admin/applications?draw=1', ['X-Requested-With' => 'XMLHttpRequest'])->assertRedirect('/admin/login');
    }

    /** The admin can list (Yajra JSON), open (marks reviewed) and download the CV. */
    public function test_admin_reviews_and_downloads(): void
    {
        Storage::fake('local');
        $this->post('/careers/laravel/apply', $this->payload());
        $application = JobApplication::firstOrFail();
        $admin = User::firstOrFail();

        // Yajra JSON carries the applicant for the server-side table.
        $json = $this->actingAs($admin)->getJson('/admin/applications?draw=1&start=0&length=15', ['X-Requested-With' => 'XMLHttpRequest']);
        $json->assertOk()->assertJsonPath('recordsTotal', 1);
        $this->assertStringContainsString('Test Candidate', $json->json('data.0.full_name'));

        // Opening marks the application reviewed.
        $this->actingAs($admin)->get("/admin/applications/{$application->id}")
            ->assertOk()->assertSee('Test Candidate');
        $this->assertSame('reviewed', $application->fresh()->status);

        // The CV streams only through this authenticated route.
        $download = $this->actingAs($admin)->get("/admin/applications/{$application->id}/cv");
        $download->assertOk();
        $this->assertSame('application/pdf', $download->headers->get('content-type'));
    }

    /** Deleting an application removes its CV file too. */
    public function test_delete_removes_cv(): void
    {
        Storage::fake('local');
        $this->post('/careers/laravel/apply', $this->payload());
        $application = JobApplication::firstOrFail();
        $path = $application->cv_path;

        $this->actingAs(User::firstOrFail())->delete("/admin/applications/{$application->id}")
            ->assertRedirect(route('admin.applications.index'));

        $this->assertDatabaseMissing('job_applications', ['id' => $application->id]);
        Storage::disk('local')->assertMissing($path);
    }

    /** The job page renders the real requisition code and hiring manager. */
    public function test_job_page_uses_record_data(): void
    {
        $job = JobPost::where('slug', 'laravel')->firstOrFail();

        $this->get('/careers/laravel')
            ->assertOk()
            ->assertSee($job->ref_code)
            ->assertSee($job->hiringManager->name)
            ->assertSee(route('jobs.apply', 'laravel'), false);
    }
}

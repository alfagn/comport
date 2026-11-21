<?php

declare(strict_types=1);

use App\Modules\CMS\Livewire\ContactPage;
use App\Modules\CMS\Livewire\JobDetailPage;
use App\Modules\CMS\Models\ContactMessage;
use App\Modules\CMS\Models\JobApplication;
use App\Modules\CMS\Models\JobPosting;
use App\Modules\CMS\Models\PortfolioProject;
use App\Modules\CMS\Models\TeamMember;
use Livewire\Livewire;

test('public website home page loads successfully with livewire', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Empetix');
});

test('public about and team pages load with founders', function () {
    $this->get('/about')->assertOk()->assertSee('Built by engineers and storytellers');
    $this->get('/team')->assertOk()->assertSee('The people behind Empetix');
});

test('public work and project detail pages load with case studies', function () {
    $project = PortfolioProject::query()->where('is_published', true)->first();
    if ($project) {
        $this->get('/work')->assertOk()->assertSee($project->title);
        $this->get('/work/'.$project->slug)->assertOk()->assertSee($project->title);
    } else {
        $this->get('/work')->assertOk();
    }
});

test('founder profile pages load successfully', function () {
    $founder = TeamMember::query()->where('is_founder', true)->first();
    if ($founder) {
        $this->get('/founder/'.$founder->slug)->assertOk()->assertSee($founder->name);
    }
});

test('careers directory loads open roles', function () {
    $this->get('/careers')->assertOk()->assertSee('Empetix Talent Portal');
});

test('contact page livewire form submits message to landlord database', function () {
    Livewire::test(ContactPage::class)
        ->set('name', 'Tariq Al-Mansoor')
        ->set('email', 'tariq@cliententerprise.com')
        ->set('phone', '+201099887766')
        ->set('subject', 'FinTech Mobile Core & SaaS Architecture')
        ->set('message', 'We are looking to migrate our regional payments system to high-concurrency Laravel and Flutter.')
        ->call('submitMessage')
        ->assertSet('submitted', true);

    expect(ContactMessage::on('landlord')->where('email', 'tariq@cliententerprise.com')->exists())->toBeTrue();
});

test('job detail livewire form submits candidate application', function () {
    $job = JobPosting::query()->where('is_active', true)->first();
    if (! $job) {
        $job = JobPosting::create([
            'title' => 'Test Lead Engineer',
            'slug' => 'test-lead-engineer',
            'department' => 'Software Engineering',
            'location' => 'Cairo / Remote',
            'employment_type' => 'full_time',
            'description' => 'Test role description.',
            'is_active' => true,
        ]);
    }

    Livewire::test(JobDetailPage::class, ['slug' => $job->slug])
        ->set('candidate_name', 'Amr Hassan')
        ->set('candidate_email', 'amr.hassan@testcareer.com')
        ->set('candidate_phone', '+201011223344')
        ->set('portfolio_url', 'https://github.com/amrhassan-dev')
        ->set('cover_letter', 'Over 7 years architecting distributed PHP and Flutter systems.')
        ->call('submitApplication')
        ->assertSet('submitted', true);

    expect(JobApplication::on('landlord')->where('candidate_email', 'amr.hassan@testcareer.com')->exists())->toBeTrue();
});

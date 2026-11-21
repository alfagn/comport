<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\JobApplication;
use App\Modules\CMS\Models\JobPosting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.site')]
class JobDetailPage extends Component
{
    use WithFileUploads;

    public string $slug;

    #[Validate('required|string|min:3|max:100')]
    public string $candidate_name = '';

    #[Validate('required|email|max:120')]
    public string $candidate_email = '';

    #[Validate('nullable|string|max:40')]
    public string $candidate_phone = '';

    #[Validate('nullable|url|max:255')]
    public string $portfolio_url = '';

    #[Validate('nullable|string|max:3000')]
    public string $cover_letter = '';

    #[Validate('nullable|file|mimes:pdf,doc,docx|max:10240')]
    public mixed $resume = null;

    public bool $submitted = false;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function submitApplication(): void
    {
        $this->validate();

        /** @var JobPosting $job */
        $job = JobPosting::query()->where('slug', $this->slug)->firstOrFail();

        $resumePath = null;
        if ($this->resume) {
            $resumePath = $this->resume->store('resumes', 'public');
        }

        JobApplication::create([
            'job_posting_id' => $job->id,
            'candidate_name' => $this->candidate_name,
            'candidate_email' => $this->candidate_email,
            'candidate_phone' => $this->candidate_phone,
            'portfolio_url' => $this->portfolio_url,
            'cover_letter' => $this->cover_letter,
            'resume_path' => $resumePath,
            'status' => 'new',
        ]);

        $this->reset(['candidate_name', 'candidate_email', 'candidate_phone', 'portfolio_url', 'cover_letter', 'resume']);
        $this->submitted = true;
    }

    public function render(): View
    {
        /** @var JobPosting $job */
        $job = JobPosting::query()
            ->where('slug', $this->slug)
            ->where('is_active', true)
            ->firstOrFail();

        $otherJobs = JobPosting::query()
            ->where('id', '!=', $job->id)
            ->where('is_active', true)
            ->take(3)
            ->get();

        return view('modules.cms.livewire.job-detail-page', [
            'job' => $job,
            'otherJobs' => $otherJobs,
        ]);
    }
}

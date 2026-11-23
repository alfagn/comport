<?php

namespace App\Http\Controllers\Public;

use App\DTOs\JobApplicationData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\JobApplicationRequest;
use App\Models\JobPost;
use App\Services\JobApplicationService;
use Illuminate\Http\RedirectResponse;

/**
 * Receives applications from the public job page (CV upload included).
 */
class JobApplicationController extends Controller
{
    public function __construct(private JobApplicationService $applications) {}

    /** Validate and store an application, then bounce back with a thank-you flash. */
    public function store(JobApplicationRequest $request, string $slug): RedirectResponse
    {
        $job = JobPost::active()->where('slug', $slug)->firstOrFail();

        $data = JobApplicationData::fromArray($request->validated());
        $data->job_post_id = $job->id;

        $this->applications->storeFromPublic($data, $request->file('cv_file'));

        return redirect()
            ->to(route('jobs.show', $job->slug).'#job-apply-drawer')
            ->with('success', 'Thank you. Your application has been received; our hiring team reviews every application and replies within 3 business days.');
    }
}

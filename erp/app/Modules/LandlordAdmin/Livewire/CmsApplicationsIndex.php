<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\CMS\Models\JobApplication;
use App\Modules\CMS\Models\JobPosting;

/**
 * Class CmsApplicationsIndex
 *
 * Livewire component managing candidate job applications and resume submissions.
 */
class CmsApplicationsIndex extends BaseTable
{
    public bool $showDetailModal = false;

    public ?JobApplication $viewingApplication = null;

    public string $statusFilter = 'all';

    public string $jobFilter = 'all';

    public function view(int $id): void
    {
        $this->viewingApplication = JobApplication::on('landlord')->with('jobPosting')->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        $app = JobApplication::on('landlord')->findOrFail($id);
        $app->update(['status' => $newStatus]);

        if ($this->viewingApplication && $this->viewingApplication->id === $id) {
            $this->viewingApplication->refresh();
        }

        $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم تحديث حالة المرشح' : 'Applicant status updated to '.$newStatus);
    }

    public function delete(int $id): void
    {
        JobApplication::on('landlord')->where('id', $id)->delete();
        $this->showDetailModal = false;
        $this->toastSuccess('Application removed.');
    }

    public function render()
    {
        $jobs = JobPosting::on('landlord')->select('id', 'title')->get();

        $query = JobApplication::on('landlord')
            ->with('jobPosting')
            ->when($this->search !== '', function ($q) {
                $q->where('candidate_name', 'like', "%{$this->search}%")
                    ->orWhere('candidate_email', 'like', "%{$this->search}%");
            })
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->jobFilter !== 'all', fn ($q) => $q->where('job_posting_id', $this->jobFilter))
            ->latest();

        $applications = $query->paginate($this->perPage);

        return view('admin::livewire.cms-applications-index', [
            'applications' => $applications,
            'jobs' => $jobs,
            'totalApplications' => JobApplication::on('landlord')->count(),
            'newCount' => JobApplication::on('landlord')->where('status', 'new')->count(),
        ])->layout('layouts.admin', [
            'title' => 'Candidate Applications Inbox — Empetix CMS',
            'header' => app()->getLocale() === 'ar' ? 'طلبات التوظيف والسير الذاتية' : 'Candidate Job Applications & Resumes',
        ]);
    }
}

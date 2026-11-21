<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\CMS\Models\JobPosting;
use Illuminate\Support\Str;

/**
 * Class CmsCareersIndex
 *
 * Livewire component managing open career vacancies and job postings.
 */
class CmsCareersIndex extends BaseComponent
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $department = '';

    public string $location = 'Cairo, Egypt / Hybrid';

    public string $employment_type = 'full_time'; // full_time, part_time, remote, contract

    public string $salary_range = 'Competitive';

    public string $description = '';

    public string $requirements = '';

    public bool $is_active = true;

    public function create(): void
    {
        $this->reset(['editingId', 'title', 'slug', 'department', 'location', 'employment_type', 'salary_range', 'description', 'requirements', 'is_active']);
        $this->location = 'Cairo, Egypt / Hybrid';
        $this->employment_type = 'full_time';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $job = JobPosting::on('landlord')->findOrFail($id);
        $this->editingId = $job->id;
        $this->title = $job->title;
        $this->slug = $job->slug;
        $this->department = $job->department;
        $this->location = $job->location;
        $this->employment_type = $job->employment_type;
        $this->salary_range = (string) $job->salary_range;
        $this->description = $job->description;
        $this->requirements = (string) $job->requirements;
        $this->is_active = (bool) $job->is_active;
        $this->showModal = true;
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->slug ?: $this->title),
            'department' => $this->department,
            'location' => $this->location,
            'employment_type' => $this->employment_type,
            'salary_range' => $this->salary_range,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            JobPosting::on('landlord')->where('id', $this->editingId)->update($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم تحديث الشاغر الوظيفي!' : 'Job vacancy updated successfully!');
        } else {
            JobPosting::on('landlord')->create($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم إضافة الوظيفة بنجاح!' : 'Job vacancy opened successfully!');
        }

        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        $job = JobPosting::on('landlord')->findOrFail($id);
        $job->update(['is_active' => ! $job->is_active]);
        $this->toastSuccess('Vacancy status updated');
    }

    public function delete(int $id): void
    {
        JobPosting::on('landlord')->where('id', $id)->delete();
        $this->toastSuccess('Job posting removed');
    }

    public function render()
    {
        $jobs = JobPosting::on('landlord')->withCount('applications')->latest()->get();

        return view('admin::livewire.cms-careers-index', [
            'jobs' => $jobs,
            'totalJobs' => JobPosting::on('landlord')->count(),
            'activeJobs' => JobPosting::on('landlord')->where('is_active', true)->count(),
        ])->layout('layouts.admin', [
            'title' => 'Careers & Job Openings — Empetix CMS',
            'header' => app()->getLocale() === 'ar' ? 'الشواغر والوظائف المتاحة' : 'Careers & Job Openings Management',
        ]);
    }
}

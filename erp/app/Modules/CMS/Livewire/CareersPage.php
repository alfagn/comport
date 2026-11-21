<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\JobPosting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.site')]
class CareersPage extends Component
{
    #[Url]
    public string $department = 'all';

    public function setDepartment(string $dept): void
    {
        $this->department = $dept;
    }

    public function render(): View
    {
        $query = JobPosting::query()->where('is_active', true);

        if ($this->department !== 'all') {
            $query->where('department', $this->department);
        }

        $jobs = $query->latest()->get();

        return view('modules.cms.livewire.careers-page', [
            'jobs' => $jobs,
        ]);
    }
}

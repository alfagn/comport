<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\PortfolioProject;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.site')]
class WorkPage extends Component
{
    #[Url]
    public string $filter = 'all';

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function render(): View
    {
        $query = PortfolioProject::query()
            ->where('is_published', true);

        if ($this->filter !== 'all') {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->filter);
            });
        }

        $projects = $query->orderByDesc('is_featured')
            ->latest('completion_date')
            ->get();

        return view('modules.cms.livewire.work-page', [
            'projects' => $projects,
        ]);
    }
}

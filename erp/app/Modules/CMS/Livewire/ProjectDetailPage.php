<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\PortfolioProject;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class ProjectDetailPage extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        /** @var PortfolioProject $project */
        $project = PortfolioProject::query()
            ->where('slug', $this->slug)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedProjects = PortfolioProject::query()
            ->where('id', '!=', $project->id)
            ->where('is_published', true)
            ->take(2)
            ->get();

        return view('modules.cms.livewire.project-detail-page', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Projects\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\Projects\Models\Project;

/**
 * Class ProjectsIndex
 *
 * Livewire component managing software development projects, milestones, and task boards.
 */
class ProjectsIndex extends BaseTable
{
    public function render()
    {
        $projects = Project::on('tenant')
            ->with(['client', 'tasks'])
            ->withCount('tasks')
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('projects::livewire.projects-index', [
            'projects' => $projects,
        ])->layout('layouts.tenant', [
            'title' => 'Software Projects & Tasks',
            'header' => app()->getLocale() === 'ar' ? 'إدارة المشاريع والمهام البرمجية' : 'Software Projects & Engineering Tasks',
        ]);
    }
}

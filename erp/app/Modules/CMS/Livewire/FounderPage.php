<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\TeamMember;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class FounderPage extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        /** @var TeamMember $founder */
        $founder = TeamMember::query()
            ->where('slug', $this->slug)
            ->firstOrFail();

        $coFounders = TeamMember::query()
            ->where('is_founder', true)
            ->where('id', '!=', $founder->id)
            ->orderBy('sort_order')
            ->get();

        return view('modules.cms.livewire.founder-page', [
            'founder' => $founder,
            'coFounders' => $coFounders,
        ]);
    }
}

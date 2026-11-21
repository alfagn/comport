<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\TeamMember;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class TeamPage extends Component
{
    public function render(): View
    {
        $founders = TeamMember::query()
            ->where('is_founder', true)
            ->orderBy('sort_order')
            ->get();

        $teamMembers = TeamMember::query()
            ->where('is_founder', false)
            ->orderBy('sort_order')
            ->get();

        return view('modules.cms.livewire.team-page', [
            'founders' => $founders,
            'teamMembers' => $teamMembers,
        ]);
    }
}

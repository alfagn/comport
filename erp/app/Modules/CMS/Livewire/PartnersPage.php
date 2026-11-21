<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\Partner;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class PartnersPage extends Component
{
    public function render(): View
    {
        $partners = Partner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('modules.cms.livewire.partners-page', [
            'partners' => $partners,
        ]);
    }
}

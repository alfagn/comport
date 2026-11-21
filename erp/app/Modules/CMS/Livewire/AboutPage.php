<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\SiteSetting;
use App\Modules\CMS\Models\TeamMember;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class AboutPage extends Component
{
    public function render(): View
    {
        $founders = TeamMember::query()
            ->where('is_founder', true)
            ->orderBy('sort_order')
            ->get();

        $cairoHq = (string) SiteSetting::get('office_cairo', 'Building 14, District 5 Campus, New Cairo, Cairo, Egypt');
        $riyadhOffice = (string) SiteSetting::get('office_riyadh', 'Digital City, Building C3, Level 4, Riyadh, Saudi Arabia');
        $londonOffice = (string) SiteSetting::get('office_london', '1 Canada Square, Level 24, Canary Wharf, London, UK');

        return view('modules.cms.livewire.about-page', [
            'founders' => $founders,
            'cairoHq' => $cairoHq,
            'riyadhOffice' => $riyadhOffice,
            'londonOffice' => $londonOffice,
        ]);
    }
}

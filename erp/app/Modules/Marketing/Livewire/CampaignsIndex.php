<?php

declare(strict_types=1);

namespace App\Modules\Marketing\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\Marketing\Models\MarketingCampaign;

/**
 * Class CampaignsIndex
 *
 * Livewire component managing paid digital acquisition campaigns, clients, and performance metrics.
 */
class CampaignsIndex extends BaseTable
{
    public function render()
    {
        $campaigns = MarketingCampaign::on('tenant')
            ->with(['client', 'kpiLogs'])
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('marketing::livewire.campaigns-index', [
            'campaigns' => $campaigns,
        ])->layout('layouts.tenant', [
            'title' => 'Marketing Campaigns & Performance',
            'header' => app()->getLocale() === 'ar' ? 'إدارة الحملات الإعلانية ومؤشرات الأداء' : 'Marketing Campaigns & Performance KPIs',
        ]);
    }
}
